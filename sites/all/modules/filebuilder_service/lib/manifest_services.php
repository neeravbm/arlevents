<?php

/**
 * @file
 * manifestservices.php
 * Filedepot: File Management Service Module developed by Nextide www.nextide.ca
 * Drupal specific classes and interfaces for cross platform compatibility with the desktop client
 * Provides manifest processing (background process work) methods
 */
if (strpos(strtolower($_SERVER['PHP_SELF']), 'manifest_services.php') !== false) {
  die('This file can not be used on its own!');
}

/**
 * Returns the error string for an error number
 * @param type $type
 * @return string
 */
function php_errno_to_string($type) {
  switch ($type) {
    case E_ERROR: // 1 // 
      return 'E_ERROR';
    case E_WARNING: // 2 // 
      return 'E_WARNING';
    case E_PARSE: // 4 // 
      return 'E_PARSE';
    case E_NOTICE: // 8 // 
      return 'E_NOTICE';
    case E_CORE_ERROR: // 16 // 
      return 'E_CORE_ERROR';
    case E_CORE_WARNING: // 32 // 
      return 'E_CORE_WARNING';
    case E_CORE_ERROR: // 64 // 
      return 'E_COMPILE_ERROR';
    case E_CORE_WARNING: // 128 // 
      return 'E_COMPILE_WARNING';
    case E_USER_ERROR: // 256 // 
      return 'E_USER_ERROR';
    case E_USER_WARNING: // 512 // 
      return 'E_USER_WARNING';
    case E_USER_NOTICE: // 1024 // 
      return 'E_USER_NOTICE';
    case E_STRICT: // 2048 // 
      return 'E_STRICT';
    case E_RECOVERABLE_ERROR: // 4096 // 
      return 'E_RECOVERABLE_ERROR';
    case E_DEPRECATED: // 8192 // 
      return 'E_DEPRECATED';
    case E_USER_DEPRECATED: // 16384 // 
      return 'E_USER_DEPRECATED';
  }
  return "";
}

// Error handling function
function manifest_handle_error($errno, $errstr, $errfile, $errline, $errcontext) {
  // Get an instance
  $instance = ManifestOrchestrator::GetCurrentInstance();

  $error_title         = php_errno_to_string($errno);
  $watchdog_error_type = WATCHDOG_ERROR;
  $cancel_and_clean    = TRUE;
  if (($errno == E_WARNING) || ($errno == E_NOTICE) || ($errno == E_STRICT)) {
    $watchdog_error_type = WATCHDOG_WARNING;
    $cancel_and_clean    = FALSE;
  }

  if (strstr($errstr, "stat")) { # drupal issue
    return TRUE;
  }

  $instance->LogActivity("PHP {$error_title}: {$errstr} in file {$errfile} at line $errline with context $errcontext", $watchdog_error_type);
  if ($cancel_and_clean == TRUE) {
    $instance->CancelandClean();
  }
  return TRUE;
}

set_error_handler("manifest_handle_error", E_ALL);

/**
 * Processes the manifest file and passes the results to the various helper methods that perform further action on it
 */
class ManifestOrchestrator
{

  private $_EmailNotifications   = FALSE;
  private $_UniqueID             = 0;
  private $_JobID                = 0;
  private $_UploadPath           = NULL;
  private $_Workspace            = 0;
  private $_Uid                  = 0;
  private $_ManifestParser       = NULL;
  private $_LastTimeChecked      = 0;
  private $_FiledepotInstance    = NULL;
  private $_LastRequestedCommand = 'process';
  private static $_OrchestratorInstance = NULL;
  private static $_CurrentlyCancelling  = FALSE;
  private static $_CategoriesAdded      = Array();
  private static $_FilesAdded = Array();
  private static $_MovedFiles = Array();
  private static $_MovedCategories = Array();
  private static $_CategoryOrderChanged = Array();
  private static $_FileOrderChanged = Array();
  private static $_RenamedDirectories = Array();
  private static $_RenamedFiles = Array();

  /**
   * Lock or unlock to signify a commit is happening
   * @param type $lock
   */
  private function _WorkspaceCommitLock($lock = TRUE) {
    db_query("UPDATE {filebuilder_service_upload_job} SET in_progress = :prog WHERE jobid = :jobid AND workspace = :workspace", array(
      ':prog'      => ($lock === TRUE) ? 1 : 0,
      ':jobid'     => $this->_JobID,
      ':workspace' => $this->_Workspace,
    ));
  }

  /**
   * Create a new category
   * @param RepositoryAddDirectoryCommandObject   $add_dir_command                   The RepositoryAddDirectoryCommandObject object
   * @param Integer                               $server_parent_id                  ID of the parent that exists on the server
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds new category id ]
   */
  private function _CreateCategory($add_dir_command, $server_parent_id) {
    $result_obj = new WorkspaceActionResultItem();
    $node       = NULL;
    $cid        = 0;
    $res        = $this->_FiledepotInstance->createFolderNode($add_dir_command->Name, "", $server_parent_id, $node, $cid, TRUE);

    if ($res === TRUE) {
      $result_obj->Set(TRUE, "", WorkspaceActionResultCodes::$OK);
      $result_obj->Data = $cid;
      self::$_CategoriesAdded[] = $cid;
    }
    else {
      $result_obj->Set(false, "Creating category failed", WorkspaceActionResultCodes::$FAILURE_TO_CREATE);
    }

    return $result_obj;
  }

  /**
   * Add a file
   * @param RepositoryAddFileCommandObject        $add_file_command                  The RepositoryAddFileCommandObject object
   * @param Integer                               $server_parent_id                  ID of the parent that exists on the server
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds new file id ]
   */
  private function _AddFile($add_file_command, $server_parent_id) {
    global $user;

    $result_obj = new WorkspaceActionResultItem();
    $time       = time();

    // Get folder node
    $folder_nid = db_query("SELECT nid FROM {filedepot_categories} WHERE cid=:cid", array(':cid' => $server_parent_id))->fetchField();
    $node  = node_load($folder_nid);

    if (!$node) {
      $result_obj->Set(false, "", WorkspaceActionResultCodes::$NOT_FOUND);
      return;
    }

    $filedepot = $this->_FiledepotInstance;
    $file      = new stdClass();
    $file->uid = $user->uid;

    $obj                 = $filedepot->getPermissionObject($server_parent_id);
    $upload_direct       = $obj->hasPermission('upload_dir');
    $upload_moderated    = $obj->hasPermission('upload');
    $upload_new_versions = $obj->hasPermission('upload_ver');

    if ((!$upload_direct) && ($upload_moderated)) { // Admin's have all perms so test for users with upload moderated approval only
      $moderated           = TRUE;
      $private_destination = 'private://filedepot/' . $node->folder . '/submissions/';
    }
    else {
      $moderated           = FALSE;
      $private_destination = 'private://filedepot/' . $node->folder . '/';
    }
    // don't use submissions for this
    $moderated           = FALSE;
    $private_destination = 'private://filedepot/' . $node->folder . '/';

    // Clean
    $cleaned_name                 = check_plain($add_file_command->Name);
    $cleaned_path                 = check_plain($add_file_command->Path);
    $fullpath_private_destination = drupal_realpath($private_destination);
    $file->filename               = $cleaned_name;
    $file->uri                    = $private_destination . $cleaned_name;
    $file->filemime               = $add_file_command->Type; // or use 
    $file->timestamp              = $time;
    $file->status                 = FILE_STATUS_PERMANENT;
    $file->filesize               = $add_file_command->Size;

    // Best to call file_prepare_directory() - even if you believe directory exists
    file_prepare_directory($private_destination, FILE_CREATE_DIRECTORY);
    $source_file = $this->_UploadPath . $cleaned_path;
    $dest_file   = $fullpath_private_destination . "/{$cleaned_name}";

    if ((!file_exists($fullpath_private_destination)) || (!file_exists($source_file))) {
      $result_obj->Set(false, "Source does not exist", WorkspaceActionResultCodes::$NOT_FOUND);
    }
    elseif (file_exists($dest_file)) {
      $result_obj->Set(false, "Destination file already exists", WorkspaceActionResultCodes::$ALREADY_EXISTS);
    }
    else {
      // Attempt to move the file
      $res = rename($source_file, $dest_file);
      if ($res === FALSE) {
        $result_obj->Set(false, "Move (rename) file failed", WorkspaceActionResultCodes::$FAILURE_TO_CREATE);
      }
      else {
        $ext_parts                                    = explode(".", $cleaned_name);
        $extension                                    = end($ext_parts);
        $file->display                                = 1;
        $file->description                            = '';
        $file                                         = file_save($file);
        // Doing node_save changes the file status to permanent in the file_managed table
        $node->filedepot_folder_file[LANGUAGE_NONE][] = (array) $file; //the name of the field that requires the files
        node_save($node);

        if ($moderated === TRUE) {
          // not supported currently
        }
        else {
          // Update the file usage table
          file_usage_add($file, 'filedepot', 'node', $node->nid);
          file_usage_delete($file, 'file');

          // Create filedepot record for file and set status of file to 1 - online
          $query = db_insert('filedepot_files');
          $query->fields(array('cid', 'fname', 'title', 'description', 'version', 'drupal_fid', 'size', 'mimetype', 'extension', 'submitter', 'status', 'date'));
          $query->values(array(
            'cid'         => $node->folder,
            'fname'       => $cleaned_name,
            'title'       => $cleaned_name,
            'description' => '',
            'version'     => 1,
            'drupal_fid'  => $file->fid,
            'size'        => $file->filesize,
            'mimetype'    => $file->filemime,
            'extension'   => $extension,
            'submitter'   => $user->uid,
            'status'      => 1,
            'date'        => $time,
          ));
          $newfid       = $query->execute();
          if ($newfid > 0) {
            $query = db_insert('filedepot_fileversions');
            $query->fields(array('fid', 'fname', 'drupal_fid', 'version', 'notes', 'size', 'date', 'uid', 'status'));
            $query->values(array(
              'fid'        => $newfid,
              'fname'      => $cleaned_name,
              'drupal_fid' => $file->fid,
              'version'    => 1,
              'notes'      => '',
              'size'       => $file->filesize,
              'date'       => $time,
              'uid'        => $user->uid,
              'status'     => 1,
            ));
            $query->execute();

            $result_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
            $result_obj->Data = $newfid;
            self::$_FilesAdded[] = $newfid;
          }
          else {
            $result_obj->Set(false, "Invalid ID returned from insert new file record", WorkspaceActionResultCodes::$FAILURE_TO_CREATE);
          }
        }
      }
    }

    return $result_obj;
  }

  /**
   * Delete a specified category
   * @param RepositoryDeleteDirectoryCommandObject $del_dir_command
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds parent id ]
   */
  private function _DeleteCategory($del_dir_command) {
    global $user;

    $response_obj = new WorkspaceActionResultItem();

    $query = db_query("SELECT pid FROM {filedepot_categories} WHERE cid=:cid", array(
      ':cid' => $del_dir_command->Id,
      ));

    $res    = $query->fetchAssoc();
    $result = FALSE;
    $pid    = 0;
    if ($res !== FALSE) {
      $pid    = $res['pid'];
      $result = $this->_FiledepotInstance->deleteFolder($del_dir_command->Id);
    }

    if ($result === TRUE) {
      $response_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
      $response_obj->Data = $pid;
    }
    else {
      $response_obj->Set(false, "Failed to delete category", WorkspaceActionResultCodes::$FAILURE_TO_REMOVE);
    }

    return $response_obj;
  }

  /**
   * Delete a specified file
   * @param RepositoryDeleteFileCommandObject $del_file_command
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds parent id ]
   */
  private function _DeleteFile($del_file_command) {
    global $user;

    $response_obj = new WorkspaceActionResultItem();
    $cid          = 0;
    $result       = $this->_FiledepotInstance->deleteFile($del_file_command->Id, $cid);
    if ($result === TRUE) {
      $response_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
      $response_obj->Data = $cid;
    }
    else {
      $response_obj->Set(false, "Failed to delete file", WorkspaceActionResultCodes::$FAILURE_TO_REMOVE);
    }

    return $response_obj;
  }

  /**
   * 
   * @param RepositoryMoveFileObject              $move_file_command
   * @param Integer                               $server_parent_id                  ID of the parent that exists on the server
   * @param Boolean $revert [Optional] Defaults to FALSE - Set to TRUE to not add to the cancel list in case of cancel (only set to true if cancelling)
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds old parent id ]
   */
  private function _MoveFile($move_file_command, $server_parent_id, $revert = FALSE) {
    global $user;

    $response_obj = new WorkspaceActionResultItem();
    $cid          = 0;
    $result       = $this->_FiledepotInstance->moveFile($move_file_command->Id, $server_parent_id, $cid);

    if ($result === TRUE) {
      $response_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
      $response_obj->Data = $cid;

      if ($revert === FALSE) {
        self::$_MovedFiles[] = Array(
          'fid'    => $move_file_command->Id,
          'newpid' => $server_parent_id,
          'oldpid' => $cid,
        );
      }
    }
    else {
      $response_obj->Set(false, "Failed to move file", WorkspaceActionResultCodes::$FAILURE_TO_MODIFY);
    }

    return $response_obj;
  }

  /**
   * 
   * @param RepositoryMoveDirectoryObject              $move_directory_command
   * @param Integer                                    $server_parent_id             ID of the parent that exists on the server
   * @param Boolean $revert [Optional] Defaults to FALSE - Set to TRUE to not add to the cancel list in case of cancel (only set to true if cancelling)
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds old parent id ]
   */
  private function _MoveCategory($move_directory_command, $server_parent_id, $revert = FALSE) {
    $response_obj = new WorkspaceActionResultItem();
    $old_pid      = 0;
    $result       = $this->_FiledepotInstance->moveCategory($move_directory_command->Id, $server_parent_id, $old_pid);

    if ($result == 0) {
      if (($revert === FALSE)) {
        self::$_MovedCategories[] = Array(
          'cid'    => $move_directory_command->Id,
          'newpid' => $server_parent_id,
          'oldpid' => $old_pid,
        );
      }
      $response_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
    }
    else {
      $response_obj->Set(false, "ErrorCode: {$result}", WorkspaceActionResultCodes::$INVALID_DATA);
    }

    return $response_obj;
  }

  /**
   * Change the ordering of a specified category
   * @param Integer   $category_id            Id of the category to change order for
   * @param Integer   $order                  Order as an increment of 10
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds parent id ]
   */
  private function _ChangeCategoryOrder($category_id, $order) {
    global $user;

    $response_obj   = new WorkspaceActionResultItem();
    $pid            = 0;
    $original_order = 0;
    $result         = $this->_FiledepotInstance->setSingleFolderOrder($category_id, $order, $pid, $original_order);

    if ($result === TRUE) {
      $response_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
      $response_obj->Data = $pid;

      self::$_CategoryOrderChanged[] = Array(
        'cid'   => $category_id,
        'order' => $original_order,
      );
    }
    else {
      $response_obj->Set(false, "Failed to change file order", WorkspaceActionResultCodes::$FORBIDDEN);
    }

    return $response_obj;
  }

  /**
   * Change the ordering of a specified file
   * @param Integer   $file_id                Id of the file to change order for
   * @param Integer   $order                  Order as an increment of 10
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds parent id ]
   */
  private function _ChangeFileOrder($file_id, $order) {
    $response_obj = new WorkspaceActionResultItem();
    //$response_obj->Set(false, "", WorkspaceActionResultCodes::$NOT_SUPPORTED);
    $response_obj->Set(true, "", WorkspaceActionResultCodes::$NOT_SUPPORTED);
    /* */

    return $response_obj;
  }

  /**
   * Rename a category
   * @param RepositoryChangeDirectoryNameObject $rename_dir_object
   * @param Boolean $revert [Optional] Defaults to FALSE - Set to TRUE to not add to the cancel list in case of cancel (only set to true if cancelling)
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds parent id ]
   */
  private function _RenameCategory($rename_dir_object, $revert = FALSE) {
    global $user;

    $response_obj = new WorkspaceActionResultItem();
    $pid          = 0;
    $result       = $this->_FiledepotInstance->renameCategory($rename_dir_object->Id, $rename_dir_object->NewName, $pid);

    if ($result === TRUE) {
      $response_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
      $response_obj->Data = $pid;

      if ($revert === FALSE) {
        self::$_RenamedDirectories[] = $rename_dir_object;
      }
    }
    else {
      $response_obj->Set(false, "Failed to rename category", WorkspaceActionResultCodes::$FORBIDDEN);
    }

    return $response_obj;
  }

  /**
   * Rename a file
   * @param RepositoryChangeFileNameObject $rename_file_object
   * @param Boolean $revert [Optional] Defaults to FALSE - Set to TRUE to not add to the cancel list in case of cancel (only set to true if cancelling)
   * @return  WorkspaceActionResultItem                                              ResultItem object holding information about the status of the request [ Data member holds parent id ]
   */
  private function _RenameFile($rename_file_object, $revert = FALSE) {
    global $user;

    $response_obj = new WorkspaceActionResultItem();
    $pid          = 0;
    $result       = $this->_FiledepotInstance->renameFile($rename_file_object->Id, $rename_file_object->NewName, $pid);

    if ($result === TRUE) {
      $response_obj->Set(true, "", WorkspaceActionResultCodes::$OK);
      $response_obj->Data = $pid;

      if ($revert === FALSE) {
        self::$_RenamedFiles[] = $rename_file_object;
      }
    }
    else {
      $response_obj->Set(false, "Failed to rename file", WorkspaceActionResultCodes::$FORBIDDEN);
    }

    return $response_obj;
  }

  /**
   * Checks to see if the user has set the cancel flag
   * This only checks every 60 seconds
   */
  private function _CanProceed() {
    $time = time();
    if (self::$_CurrentlyCancelling == TRUE) {
      return FALSE;
    }

    if (($this->_LastTimeChecked + 60) < $time) {
      $res = db_query("SELECT requested_command FROM {filebuilder_service_upload_job} WHERE jobid = :jobid", array(
        ':jobid' => $this->_JobID,
        ));

      $A = $res->fetchAssoc();
      if ($A === FALSE) {
        // do nothing
      }
      else {
        $this->_LastRequestedCommand = $A['requested_command'];
        $this->_LastTimeChecked      = $time;
      }
    }

    if ($this->_LastRequestedCommand == 'cancel') {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * If the cancel flag has been set by the user, will start the cancelling process
   */
  private function _IfCancelCleanAndExit() {
    if ($this->_CanProceed() === FALSE) {
      $this->CancelAndClean();
      exit();
    }
  }

  /**
   * Cancels the upload (reverts any changes that can be reverted) and cleans up
   */
  public function CancelAndClean($error = FALSE) {
    global $user;
    if (self::$_CurrentlyCancelling === TRUE) {
      return; // to prevent endless loops
    }
    else {
      self::$_CurrentlyCancelling = TRUE;
    }

    if ($this->_EmailNotifications === TRUE) {
      # to do send mail
      if ($error === TRUE) {
        $due_to_error = " due to error: ID";
        drupal_mail('filebuilder_service', FILEBUILDER_SERVICE_MAIL_NOTIFICATION_ERROR, $user->mail, language_default(), array());
      }
      else {
        $due_to_error = ": ID";
        drupal_mail('filebuilder_service', FILEBUILDER_SERVICE_MAIL_NOTIFICATION_CANCEL, $user->mail, language_default(), array());
      }
    }

    $this->LogActivity("Cancelling commit{$due_to_error} {$this->_UniqueID}");

    // Finally unlock
    $this->_WorkspaceCommitLock(false);
    $this->UpdateUploadJob(ManifestJobStatuses::$CANCELLED);
    // Clean up
    $this->_ManifestParser->CleanUp();
  }

  /**
   * Update the database entry for this job with its new status
   * @param type $status
   */
  public function UpdateUploadJob($status) {
    db_query("UPDATE {filebuilder_service_upload_job} SET status = :status, lastupdate = :lastupdate WHERE jobid = :jobid", array(
      ':status'     => $status,
      ':lastupdate' => time(),
      ':jobid'      => $this->_JobID,
    ));
  }

  /**
   * Write an entry to the filedepot builder service log
   * @param type $logentry
   * @param type $severity                [Optional] Defaults to WATCHDOG_NOTICE, can be one of drupals WATCHDOG constants
   */
  public function LogActivity($logentry, $severity = NULL) {
    if ($severity === NULL) {
      $severity = WATCHDOG_NOTICE;
    }

    watchdog('filebuilder_service', $logentry, array(), $severity);
  }

  /**
   * Used by the error handler to have access to a current instance
   * @return          ManifestOrchestrator instance
   */
  public static function GetCurrentInstance() {
    if (self::$_OrchestratorInstance === NULL) {
      return new ManifestOrchestrator('(no unique id)', FALSE);
    }
    else {
      return self::$_OrchestratorInstance;
    }
  }

  public function __construct($uniqueId, $emailNotification) {
    $this->_UniqueID           = $uniqueId;
    $this->_EmailNotifications = $emailNotification;
    self::$_OrchestratorInstance = $this;
  }

  /**
   * Start processing
   */
  public function Process() {
    global $user;

    // Check to see if there is a matching job for this ID
    $res = db_query("SELECT jobid, uid, workspace FROM {filebuilder_service_upload_job} WHERE uniqueid = :uniqueid", array(
      ':uniqueid' => $this->_UniqueID,
      ))->fetchAssoc();
    if ($res === FALSE) {
      $this->LogActivity("No upload job with unique id {$this->_UniqueID}", WATCHDOG_ERROR);
      return;
    }

    // Set variables
    $cids_to_update = array();
    $hash_local_dir_mappings = array(); // [localid] => created_id
    $hash_local_file_mappings = array(); // [localid] => created_id
    $job_statistics_array = array(); // => array([0] => action_id, [1] => status)
    // Set parameters
    $this->_Uid               = $res['uid'];
    $this->_Workspace         = $res['workspace'];
    $this->_JobID             = $res['jobid'];
    $this->_UploadPath        = NWSRequestWorker::GetUploadPath() . DIRECTORY_SEPARATOR . "{$this->_UniqueID}" . DIRECTORY_SEPARATOR;
    $this->_FiledepotInstance = filedepot::getInstance();

    $this->LogActivity("Processing new commit: {$this->_UniqueID}");
    $this->UpdateUploadJob(ManifestJobStatuses::$PROCESSING);
    $this->_WorkspaceCommitLock(true);

    try {
      $this->_ManifestParser = new ManifestParser();
      if ($this->_ManifestParser->LoadManifest($this->_UploadPath) !== TRUE) {
        $this->UpdateUploadJob(ManifestJobStatuses::$FAILED_NO_SUCH_DIR);
        $this->LogActivity("Directory {$this->_UploadPath} does not exist", WATCHDOG_ERROR);
        return;
      }

      $manifestResult = $this->_ManifestParser->Parse();
      $this->_IfCancelCleanAndExit();

      // Replace file commands
      foreach ($manifestResult->RepositoryReplaceFileCommands as $replace_file_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $del_file_command     = new RepositoryDeleteFileCommandObject();
        $del_file_command->Id = $replace_file_command->Id;
        $result_item          = $this->_DeleteFile($del_file_command);
        if ($result_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("ReplaceFile failed to delete server file - code: {$result_item->ResultCode} FileId: {$replace_file_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($replace_file_command->RepositoryActionID, $result_item->ResultCode);
        }
        else {
          $job_statistics_array[] = array($replace_file_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      // Replace directory now
      foreach ($manifestResult->RepositoryReplaceDirectoryCommands as $replace_dir_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $del_dir_command     = new RepositoryDeleteDirectoryCommandObject();
        $del_dir_command->Id = $replace_dir_command->Id;
        $result_item         = $this->_DeleteCategory($del_dir_command);
        if ($result_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("ReplaceFile failed to delete server category - code: {$result_item->ResultCode} DirectoryId: {$del_dir_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($replace_dir_command->RepositoryActionID, $result_item->ResultCode);
        }
        else {
          $job_statistics_array[] = array($replace_dir_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      // Execute the manifest commands
      // Add directories
      while (($add_dir_command = $manifestResult->GetNextAddDirectoryCommand()) !== NULL) {
        $this->_IfCancelCleanAndExit();

        // Declarations
        $server_parent_id = 0;

        // This is required as some directories may reference a local parent id and not the new server id. 
        $bypass = FALSE;
        if ($add_dir_command->LocalParent === TRUE) {
          // Find the server id in the mapping
          $local_parent_id = (int) $add_dir_command->ParentId;
          if (array_key_exists($local_parent_id, $hash_local_dir_mappings)) {
            $server_parent_id = $hash_local_dir_mappings[$local_parent_id];
          }
          else {
            $server_parent_id = 0;
          }
        }
        else {
          $bypass           = TRUE;
          $server_parent_id = $add_dir_command->ParentId;
        }

        if (($bypass === FALSE) && ($server_parent_id === 0)) {
          $this->LogActivity("AddDirectory did not find a server parent id (from local id): DirectoryId {$add_dir_command->Id}", WATCHDOG_WARNING);
          $manifestResult->RegisterDirectoryCreateError($add_dir_command->Id);
          $job_statistics_array[] = array($add_dir_command->RepositoryActionID, WorkspaceActionResultCodes::$NOT_FOUND);
          continue;
        }

        $result_action_item = $this->_CreateCategory($add_dir_command, $server_parent_id);
        if ($result_action_item->Succeeded === TRUE) {
          $local                           = (int) $add_dir_command->Id;
          $hash_local_dir_mappings[$local] = $result_action_item->Data;
          $manifestResult->RegisterDirectoryCreated($add_dir_command->Id);
          $cids_to_update[]                = $server_parent_id;
          $job_statistics_array[]          = array($add_dir_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
        else {
          $this->LogActivity("AddDirectory failed to create directory - code: {$result_action_item->ResultCode} DirectoryId: {$add_dir_command->Id}", WATCHDOG_WARNING);
          $manifestResult->RegisterDirectoryCreateError($add_dir_command->Id);
          $job_statistics_array[] = array($add_dir_command->RepositoryActionID, $result_action_item->ResultCode);
        }
      }

      // Add files
      foreach ($manifestResult->AddFileCommands as $add_file_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $server_parent_id = 0;

        if ($add_file_command->LocalParent === TRUE) {
          // Find the server id in the mapping
          $local_parent_id = (int) $add_file_command->ParentId;
          if (array_key_exists($local_parent_id, $hash_local_dir_mappings)) {
            $server_parent_id = $hash_local_dir_mappings[$local_parent_id];
          }
          else {
            $server_parent_id = 0;
          }
        }
        else {
          $server_parent_id = $add_file_command->ParentId;
        }

        // Was anything found
        if ($server_parent_id === 0) {
          // Error
          $this->LogActivity("AddFile failed to find server parent directory (local parent) FileId: {$add_file_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($add_file_command->RepositoryActionID, WorkspaceActionResultCodes::$NOT_FOUND);
          continue;
        }

        // And now add
        $result_action_item = $this->_AddFile($add_file_command, $server_parent_id);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("AddFile failed to save file - code: {$result_action_item->ResultCode} FileId: {$add_file_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($add_file_command->RepositoryActionID, $result_action_item->ResultCode);
        }
        else {
          $job_statistics_array[] = array($add_file_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
          $cids_to_update[] = $server_parent_id;
        }
      }

      // Delete files now
      foreach ($manifestResult->DeleteFileCommands as $del_file_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $result_action_item = $this->_DeleteFile($del_file_command);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("DeleteFile failed to delete server file - code: {$result_action_item->ResultCode} FileId: {$del_file_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($del_file_command->RepositoryActionID, $result_action_item->ResultCode);
        }
        else {
          $cids_to_update[]       = $result_action_item->Data;
          $job_statistics_array[] = array($del_file_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      // Del directories now
      foreach ($manifestResult->DeleteDirectoryCommands as $del_dir_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $result_action_item = $this->_DeleteCategory($del_dir_command);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("DeleteCategory failed to delete server category - code: {$result_action_item->ResultCode} DirectoryId: {$del_dir_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($del_dir_command->RepositoryActionID, $result_action_item->ResultCode);
        }
        else {
          $cids_to_update[]       = $result_action_item->Data;
          $job_statistics_array[] = array($del_dir_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      // Move Files (first)
      foreach ($manifestResult->RepositoryMoveFileCommands as $move_file_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $server_parent_id = 0;
        if ($move_file_command->IsLocalParent === TRUE) {
          // Find the server id in the mapping
          $local_parent_id = (int) $move_file_command->ParentId;
          if (array_key_exists($local_parent_id, $hash_local_dir_mappings)) {
            $server_parent_id = $hash_local_dir_mappings[$local_parent_id];
          }
          else {
            $server_parent_id = 0;
          }
        }
        else {
          $server_parent_id = $move_file_command->ParentId;
        }

        // Was anything found
        if ($server_parent_id === 0) {
          // Error
          $this->LogActivity("MoveFile failed to find server category to move into (local parent) FileId: {$move_file_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($move_file_command->RepositoryActionID, WorkspaceActionResultCodes::$NOT_FOUND);
          continue;
        }

        // Send move command
        $result_action_item = $this->_MoveFile($move_file_command, $server_parent_id);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("MoveFile failed - code: {$result_action_item->ResultCode} FileId: {$move_file_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($move_file_command->RepositoryActionID, $result_action_item->ResultCode);
        }
        else {
          $cids_to_update[]       = $server_parent_id;
          $cids_to_update[]       = $result_action_item->Data;
          $job_statistics_array[] = array($move_file_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      // Move Directories
      foreach ($manifestResult->RepositoryMoveDirectoryCommands as $move_dir_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $server_parent_id = 0;
        if ($move_dir_command->IsLocalParent === TRUE) {
          // Find the server id in the mapping
          $local_parent_id = (int) $move_dir_command->ParentId;
          if (array_key_exists($local_parent_id, $hash_local_dir_mappings)) {
            $server_parent_id = $hash_local_dir_mappings[$local_parent_id];
          }
          else {
            $server_parent_id = 0;
          }
        }
        else {
          $server_parent_id = $move_dir_command->ParentId;
        }

        // Was anything found
        if ($server_parent_id === 0) {
          // Error
          $this->LogActivity("MoveDirectory failed to find server category to move to (local parent) DirectoryId: {$move_dir_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($move_dir_command->RepositoryActionID, WorkspaceActionResultCodes::$NOT_FOUND);
          continue;
        }

        // Send move command
        $result_action_item = $this->_MoveCategory($move_dir_command, $server_parent_id);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("MoveDirectory failed - code: {$result_action_item->ResultCode} DirectoryId: {$move_dir_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($move_dir_command->RepositoryActionID, $result_action_item->ResultCode);
        }
        else {
          $cids_to_update[]       = $server_parent_id;
          $cids_to_update[]       = $result_action_item->Data;
          $job_statistics_array[] = array($move_dir_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      // Reorder now
      foreach ($manifestResult->RepositoryChangeDirectoryCommands as $change_dir_order_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $result_action_item = $this->_ChangeCategoryOrder($change_dir_order_command->Id, $change_dir_order_command->Order);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("Change Directory Ordering failed - code: {$result_action_item->ResultCode} DirectoryId: {$change_dir_order_command->Id}", WATCHDOG_WARNING);
        }
        else {
          $cids_to_update[] = $result_action_item->Data;
        }
      }

      // Reorder files now
      foreach ($manifestResult->RespositoryChangeFileCommands as $change_file_order_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $result_action_item = $this->_ChangeFileOrder($change_file_order_command->Id, $change_file_order_command->Order);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("Change File Ordering failed - code: {$result_action_item->ResultCode} FileId: {$change_file_order_command->Id}", WATCHDOG_WARNING);
        }
        else {
          $cids_to_update[] = $result_action_item->Data;
        }
      }

      // Rename files
      foreach ($manifestResult->RepositoryRenameFileCommands as $rename_file_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $result_action_item = $this->_RenameFile($rename_file_command);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("Rename file failed - code: {$result_action_item->ResultCode} FileId: {$rename_file_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($rename_file_command->RepositoryActionID, $result_action_item->ResultCode);
        }
        else {
          $cids_to_update[]       = $result_action_item->Data;
          $job_statistics_array[] = array($rename_file_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      // Rename directories
      foreach ($manifestResult->RepositoryRenameDirectoryCommands as $rename_dir_command) {
        // Check to see if a cancel flag has been set
        $this->_IfCancelCleanAndExit();

        $result_action_item = $this->_RenameCategory($rename_dir_command);
        if ($result_action_item->Succeeded === FALSE) {
          // Notify of errors
          $this->LogActivity("Rename directory failed - code: {$result_action_item->ResultCode} Directory: {$rename_dir_command->Id}", WATCHDOG_WARNING);
          $job_statistics_array[] = array($rename_dir_command->RepositoryActionID, $result_action_item->ResultCode);
        }
        else {
          $cids_to_update[]       = $result_action_item->Data;
          $job_statistics_array[] = array($rename_dir_command->RepositoryActionID, WorkspaceActionResultCodes::$OK);
        }
      }

      $this->_IfCancelCleanAndExit();

      $this->_ManifestParser->CleanUp();

      /**
       * Update categories to mark last modified timestamp on them
       */
      $cids_to_update = array_unique($cids_to_update);
      foreach ($cids_to_update as $cid_to_update) {
        $cid_to_update = (int) $cid_to_update;
        if ($cid_to_update > 0) {
          $this->_FiledepotInstance->updateCategoryLastUpdatedDate($cid_to_update);
        }
      }

      /**
       * Update the job statistics table
       */
      $failed_status_count = 0;
      foreach ($job_statistics_array as $job_statistic) {
        $action_id = $job_statistic[0];
        $status    = $job_statistic[1];
        if ($status != WorkspaceActionResultCodes::$OK) {
          $failed_status_count++;
        }
        db_query("INSERT INTO {filebuilder_service_job_statistics} (jobid, rid, status) VALUES(:jobid, :rid, :status);", array(
          ':jobid'  => $this->_JobID,
          ':rid'    => $action_id,
          ':status' => $status,
        ));
      }

      if ($failed_status_count >= count($job_statistics_array)) {
        $this->UpdateUploadJob(ManifestJobStatuses::$FAILED);
        $this->LogActivity("Failed commit: {$this->_UniqueID}");
        if ($this->_EmailNotifications === TRUE) {
          drupal_mail('filebuilder_service', FILEBUILDER_SERVICE_MAIL_NOTIFICATION_ERROR, $user->mail, language_default(), array());
        }
      }
      else {
        $this->UpdateUploadJob(ManifestJobStatuses::$COMPLETED);
        $this->LogActivity("Finished commit: {$this->_UniqueID}");
        if ($this->_EmailNotifications === TRUE) {
          # to do send mail
          drupal_mail('filebuilder_service', FILEBUILDER_SERVICE_MAIL_NOTIFICATION_SUCCESS, $user->mail, language_default(), array());
        }
      }

      // end
    }
    catch (Exception $e) {
      $this->UpdateUploadJob(ManifestJobStatuses::$FAILED_PARSING);
      $this->LogActivity("Parsing manifest failed: {$e}", WATCHDOG_ERROR);
    }

    $this->_WorkspaceCommitLock(false);
  }

}

?>