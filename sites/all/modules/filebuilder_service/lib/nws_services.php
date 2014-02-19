<?php

/**
 * @file
 * nws_services.php
 * Filedepot: File Management Service Module developed by Nextide www.nextide.ca
 * Drupal specific classes and interfaces for cross platform compatibility with the desktop client
 */
if (strpos(strtolower($_SERVER['PHP_SELF']), 'nws_services.php') !== false) {
  die('This file can not be used on its own!');
}

/**
 * Service request and response methods
 */
class NWSRequestWorker extends NWSRequestWorkerCommon
{

  private $uid            = 0;
  private $filedepotClass = NULL;
  
  /**
   * Helper function to download a category and all sub-categories (and files)
   * @param type $cid
   */
  private function downloadCategoryArchive($cid) {
    module_load_include('php', 'filedepot', 'filedepot_archiver.class');
    $fa = new filedepot_archiver();
    $fa->createAndCleanArchiveDirectory();
    $fa->addCheckedObjectArrays(array(), array("{$cid}" => array('id' => $cid, 'checked' => TRUE)));
    $fa->createArchive();
    $fa->close();
    $fa->download();
  }
  
  /**
   * Output a file for downloading by the desktop application. 
   * @param type $file_path                 NULL for error info download
   * @param type $file_size
   * @param type $mime_type
   * @param type $errno
   */
  private function downloadFileInternal($file_path, $file_name, $file_size, $mime_type, $errno = 0) {
    if (($file_path === NULL) || (file_exists($file_path) === FALSE)) {
      if ($errno == 0) {
        $errno = 1;
      }
    }

    $headers = array(
      'Content-Type: ' . $mime_type . '; name="' . $file_name . '"',
      'Content-Length: ' . $file_size,
      'Content-Disposition: attachment; filename="' . $file_name . '"',
      'Cache-Control: private',
      'Error-Code: ' . $errno,
      'FileSize: ' . $file_size,
    );

    // This has to be manually done so we can still show error header information
    foreach ($headers as $value) {
      //drupal_add_http_header($name, $value);
      header($value);
    }

    if ($errno == 0) {
      $fp = fopen($file_path, 'rb');
      while (!feof($fp)) {
        echo fread($fp, 1024);
        flush(); // this is essential for large downloads
      }
      fclose($fp);
    }
    else {
      echo "";
    }

    drupal_exit();
  }

  /**
   * Recursively gets the total size for all files under a specific folder (and under all subfolders)
   * In addition, the total number of files and subfolders are also retreived
   * @param type $categoryId                  [Input]     The category ID to retreive information for
   * @param type $totalsize                   [Output]    Total size in bytes will be stored here
   * @param type $filecount                   [Output]    Total number of all files will be stored here
   * @param type $foldercount                 [Output]    Total number of all folders will be stored here
   */
  private function recursivelyGetFolderSize($categoryId, &$totalsize, &$filecount, &$foldercount) {
    $res = db_query("SELECT cid FROM {filedepot_categories} WHERE pid = :pid", array(
      ':pid' => $categoryId,
      ));

    while ($A = $res->fetchAssoc()) {
      $foldercount = $foldercount + 1;
      $this->recursivelyGetFolderSize($A['cid'], $totalsize, $filecount, $foldercount);
    }

    // Get any files
    $file_res = db_query("SELECT size FROM {filedepot_files} WHERE cid = :cid", array(
      ':cid' => $categoryId,
      ));

    while ($A = $file_res->fetchAssoc()) {
      $filecount = $filecount + 1;
      $totalsize += $A['size'];
    }
  }

  /**
   * Called to get the next set of categories and files for a specified category id (lazy loading)
   * @param type $categoryId
   */
  private function getNextCategoriesFilesPrivate($categoryId) {
    global $user;

    $array_of_folders = array();
    $array_of_files = array();

    // Get permissions for this category
    //$permission_object = new filedepot_permission_object($cid);
    $perms = FALSE;
    if ($categoryId == 0) {
      $perms             = TRUE;
      $permission_object = filedepot_permission_object::createFullPermissionObject($categoryId);
    }
    else {
      $permission_object = $this->filedepotClass->getPermissionObject($categoryId);
      $perms             = $permission_object->canView();
    }


    if ($perms === TRUE) {
      $override_order = "ORDER BY " . (variable_get('filedepot_override_folderorder', 0) ? 'name,' : '') . ' folderorder';
      //$roverride_order = "ORDER BY " . (variable_get('filedepot_override_folderorder', 0) ? 'name DESC,' : '') . ' folderorder DESC' ;

      $sql = "SELECT DISTINCT cid,pid,name,description,folderorder,last_modified_date FROM {filedepot_categories} WHERE pid=:pid ";
      if (!empty($this->filedepotClass->allowableViewFoldersSql)) {
        $sql .= "AND cid in ({$this->filedepotClass->allowableViewFoldersSql}) ";
      }
      $sql .= $override_order;

      $qfolders = db_query($sql, array(':pid' => $categoryId));
      while ($A     = $qfolders->fetchAssoc()) {
        $repo_dir                    = new RepositoryDirectory();
        $repo_dir->DirectoryId       = (int) $A['cid'];
        $repo_dir->ParentDirectoryId = (int) $categoryId;
        $repo_dir->DirectoryName     = $A['name'];
        $repo_dir->LastModified      = $A['last_modified_date'];
        $repo_dir->FolderOrder       = (int) $A['folderorder'];
        $repo_dir->LastUpdated       = (int) $A['last_modified_date'];

        // Get permission object for this directory
        $tmp_perm_object            = $this->filedepotClass->getPermissionObject($repo_dir->DirectoryId);
        $nws_folderperm             = new NWSServiceFolderPermissionObject();
        $nws_folderperm->View       = $tmp_perm_object->canView();
        $nws_folderperm->Download   = $tmp_perm_object->canDownload();
        $nws_folderperm->Manage     = $tmp_perm_object->canManage();
        $nws_folderperm->Upload     = $tmp_perm_object->canUpload();
        $repo_dir->PermissionObject = $nws_folderperm;

        $array_of_folders[] = $repo_dir;
      }

      // Get files
      $oo         = variable_get('filedepot_override_folderorder', 0) ? 'file.title,' : '';
      $sql        = "SELECT file.fid as fid,file.cid,file.title,file.fname,file.date,file.version,file.submitter,file.status,file.fileorder,folderindex.folderprefix,";
      $sql .= "file.description,category.name as foldername,category.pid,category.nid,category.last_modified_date,status_changedby_uid as changedby_uid, size ";
      $sql .= "FROM {filedepot_files} file ";
      $sql .= "LEFT JOIN {filedepot_categories} category ON file.cid=category.cid ";
      $sql .= "LEFT JOIN {filedepot_folderindex} folderindex ON file.cid=folderindex.cid AND folderindex.uid = :uid ";
      $sql .= "WHERE file.cid={$categoryId} ORDER BY {$oo} file.date DESC, file.fid DESC ";
      $file_query = db_query($sql, array(':uid' => $user->uid));

      while ($A = $file_query->fetchAssoc()) {
        $repo_file                    = new RepositoryFile();
        $repo_file->FileId            = (int) $A['fid'];
        $repo_file->FileName          = $A['title'];
        $repo_file->FileSize          = (int) $A['size'];
        $repo_file->LastModified      = $A['date'];
        $repo_file->Submitter         = $A['submitter'];
        $repo_file->ParentDirectoryId = (int) $categoryId;
        $repo_file->FileOrder         = (int) $A['fileorder'];
        $nws_fileperm                 = new NWSServiceFilePermissionObject();
        $nws_fileperm->View           = $permission_object->canDownload();
        $nws_fileperm->Download       = $permission_object->canDownload();
        $nws_fileperm->Manage         = $permission_object->canManage();
        $repo_file->PermissionObject  = $nws_fileperm;

        $array_of_files[] = $repo_file;
      }
    }

    return array($array_of_folders, $array_of_files);
  }

  /**
   * Use this method to load any config values. This is called after AuthenticateRequest and before the command. 
   */
  protected function loadSiteConfig() {
    
  }

  public function getUID() {
    return $this->uid;
  }

  /**
   * Retreives a list of workspaces 
   * 
   * @return 	NWSServiceInitialRequestResponseObject					
   */
  protected function getAvailableWorkspaces() {
    $response                      = new NWSServiceInitialRequestResponseObject();
    $workspace_object              = new NWSWorkspaceObject();
    $workspace_object->Cid         = 0;
    $workspace_object->WorkspaceID = 1;
    $workspace_object->Name        = "Filedepot";
    $workspace_object->CreatedDate = 0;
    $workspace_object->HasAccess   = true;
    $workspace_object->Link        = "";
    $workspace_object->Host        = "";
    $workspace_object->ID          = 1;
    $response->WorkspaceObjects[]  = $workspace_object;
    $response->MultipleWorkspaces  = FALSE; // No workspaces as filedepot currently does not support more than one workspace
    $response->UploadPath          = NWSServiceConfigurationValues::getSFTPUploadReturnPath();//self::GetUploadPath();

    return $response;
  }

  /**
   * Get the next unique ID value for the upload directory
   */
  protected function getUniqueUploadDir() {
    global $user;
    $unique_id = uniqid($this->workspaceId . "_");

    // Add to the database
    db_query("INSERT INTO {filebuilder_service_upload_job} (status, uniqueid, uid, workspace, lastupdate) VALUES(:status, :uniqueid, :uid, :workspace, :lastupdate) ", array(
      ':status'     => 'open',
      ':uniqueid'   => $unique_id,
      ':uid'        => $user->uid,
      ':workspace'  => $this->workspaceId,
      ':lastupdate' => time(),
    ));

    return $unique_id;
  }

  /**
   * Retreives the workspace disclaimer and image for that workspace
   * 
   * @return  NWSWorkspaceObject						A workspace object with the disclaimer and image
   */
  protected function getWorkspaceDisclaimer() {
    $response = new NWSWorkspaceObject(); // no workspaces
    return $response;
  }

  /**
   * Downloads the entire workspace
   *
   */
  protected function downloadWorkspace() {
    $this->downloadCategoryArchive(0);
  }

  /**
   * Download a category and all underneath files into an archive
   * @param Integer               $cid          Category ID to download
   * @param	Integer               $workspaceId	The workspace id
   */
  protected function downloadCategory($cid) {
    $this->downloadCategoryArchive($cid);
  }

  /**
   * Download the current version of the client software
   */
  public function downloadSoftwareUpdate($path) {
    $file_path = $path;//$this->GetUploadPath() . "downloads" . DIRECTORY_SEPARATOR . NWSServiceConfigurationValues::$BINARY_SOFTWARE_FILE;
    $this->downloadFileInternal($file_path, NextideFileBuilderControlConfig::$BINARY_SOFTWARE_FILE, filesize($file_path), "application/octet-stream");
  }

  /**
   * Requests the processing operating on the operation specified by the $uniqueDirectory cancel processing and clean up.
   * 
   * @param	String					$unique_directory	The unique directory name where the data and files are uploaded
   */
  protected function cancelUpload($unique_directory) {
    global $user;

    $res = db_query("SELECT uid FROM {filebuilder_service_upload_job} WHERE uniqueid = :uniqueid", array(
      ':uniqueid' => $unique_directory,
      ));

    $uid = 0;
    while ($A   = $res->fetchAssoc()) {
      $uid = $A['uid'];
      break;
    }

    if ($uid == 0) {
      $this->status = NWSStatus::$INVALID_PARAMETERS;
    }
    elseif ((user_access('administer filebuilder') === FALSE) && ($uid != $user->uid)) {
      $this->status = NWSStatus::$PERMISSION_DENIED;
    }
    else {
      db_query("UPDATE {filebuilder_service_upload_job} SET requested_command = 'cancel' WHERE uniqueid = :uniqueid", array(
        ':uniqueid' => $unique_directory,
      ));
    }
  }

  /**
   * The ping performs two actions - adds an entry in the database for this user and workspace, 
   * removes any entries for users that have not pinged in 10 minutes,
   * and returns a list of all users currently connected to the workspace
   * 
   * @return NWSPingResponseObject        
   */
  protected function ping() {
    global $user;

    // Declare variables
    $nws_response      = new NWSPingResponseObject();
    $already_logged_in = false;
    $time              = time();

    // Trim any old users who have a period of inactivity for more than 10 minutes
    db_query("DELETE FROM {filebuilder_service_active_users} WHERE workspace = :workspace AND last_ping < :time_interval ", array(
      ':workspace'     => $this->workspaceId,
      ':time_interval' => ($time - 600), // 600 = 10 minutes in seconds
    ));

    // Select any users logged in currently
    $res = db_query("SELECT a.last_ping, a.first_ping, a.uid, u.name FROM {filebuilder_service_active_users} a
                    LEFT JOIN {users} u ON a.uid = u.uid
                    WHERE a.workspace = :workspace ", array(
      ':workspace' => $this->workspaceId,
      ));

    while ($A = $res->fetchAssoc()) {
      if ($A['uid'] == $user->uid) {
        $already_logged_in = TRUE;
      }
      else {
        $nws_response->UsersCurrentlyLoggedIn[] = new NWSUserLoggedInObject($A['name'], (int) $A['first_ping']);
      }
    }

    // Finally add the user to the listing or update their last ping time
    if ($already_logged_in === FALSE) {
      db_query("INSERT INTO {filebuilder_service_active_users} (uid, workspace, first_ping, last_ping) VALUES(:uid, :workspace, :first_ping, :last_ping) ", array(
        ':uid'        => $user->uid,
        ':workspace'  => $this->workspaceId,
        ':first_ping' => $time,
        ':last_ping'  => $time,
      ));
    }
    else {
      db_query("UPDATE {filebuilder_service_active_users} SET last_ping = :last_ping WHERE uid = :uid AND workspace = :workspace ", array(
        ':last_ping' => $time,
        ':uid'       => $user->uid,
        ':workspace' => $this->workspaceId,
      ));
    }

    return $nws_response;
  }

  /**
   * Start processing the manifest data stored in the directory passed
   * 
   * @param	String					$unique_directory	The unique directory name where the data and files are uploaded
   * @param Boolean         $notification_on  True if notifications are to be sent, false if not
   * @return  Integer										The job ID
   */
  protected function putManifestFile($unique_directory, $notification_on) {
    //die(drupal_realpath('private://filebuilder_working_directory/'));
    module_load_include('php', 'filebuilder_service', 'lib/manifest_common');
    module_load_include('php', 'filebuilder_service', 'lib/manifest_services');

    background_process_start('filebuilder_service_manifest_start', $unique_directory, ($notification_on == 1) ? TRUE : FALSE);
    $this->status = NWSStatus::$OK;
  }

  /**
   * Gets the next set of category files to be displayed
   * 
   * @param	Integer					$workspaceId	The work space id	
   */
  protected function getNextCategoriesFiles($category_id) {
    $result           = $this->getNextCategoriesFilesPrivate($category_id);
    $array_of_folders = $result[0];
    $array_of_files   = $result[1];

    $tmp_array_folders = array();
    $count_i = 0;
    foreach ($array_of_folders as $repo_folder) {
      $result                                    = $this->getNextCategoriesFilesPrivate($repo_folder->DirectoryId);
      $array_of_folders[$count_i]->HasBeenLoaded = true;
      $tmp_array_folders                         = array_merge($tmp_array_folders, $result[0]);
      $array_of_files                            = array_merge($array_of_files, $result[1]);
      $count_i++;
    }

    $array_of_folders = array_merge($array_of_folders, $tmp_array_folders);
    return array($array_of_folders, $array_of_files);
  }

  /**
   * Returns a file as a byte stream to the requesting application
   * 
   * @param	Integer					$fid			File ID to retreive
   * @param	Integer					$workspaceId	Workspace Identifier
   */
  protected function downloadFile($fid) {
    $query = db_query("SELECT cid,drupal_fid,fname,title,mimetype,size FROM {filedepot_files} WHERE fid=:fid", array(':fid' => $fid));
    $rec   = $query->fetchAssoc();
    if ($rec === FALSE) {
      $this->downloadFileInternal(NULL, "", 0, "application/octet-stream", 1);
    }
    else {
      list($cid, $drupal_fid, $fname, $filetitle, $mimetype, $size) = array_values($rec);
      $filedepot = filedepot::getInstance();
      $filepath  = $filedepot->root_storage_path . "{$cid}/{$fname}";
      $perm_obj  = $filedepot->getPermissionObject($cid);
      if ($perm_obj->canDownload()) {
        $this->downloadFileInternal($filepath, $filetitle, $size, $mimetype);
      }
      else {
        $this->downloadFileInternal(NULL, "", 0, "application/octet-stream", 3);
      }
    }
  }

  /**
   * Recursively gets the size of a folder and all its subfolders
   * 
   * @param Integer         $cid     Category ID to retreive size for
   * @return ServerFolderInfoObject                 
   */
  protected function getFolderSize($cid) {
    if ($cid > 0) {
      $file_count   = 0;
      $folder_count = 0;
      $total_size   = 0;
      $this->recursivelyGetFolderSize($cid, $total_size, $file_count, $folder_count);
      $resp         = new ServerFolderInfoObject($total_size, $file_count, $folder_count);
      return $resp;
    }
    else {
      $this->status = NWSStatus::$INVALID_PARAMETERS;
    }
  }

  /**
   * Asks for the status of the current manifest job being processed
   * 
   * @param  String				   $unique_directory		The unique directory name
   * @return String										The status as a string
   */
  protected function getManifestStatus($unique_directory) {
    $res = db_query("SELECT jobid, status FROM {filebuilder_service_upload_job} WHERE uniqueid = :uniqueid", array(
      ':uniqueid' => $unique_directory,
      ));

    $manifest_status_object = new ManifestStatusObject();
    $jobid                  = 0;
    while ($A                      = $res->fetchAssoc()) {
      $manifest_status_object->Status = $A['status'];
      $jobid                          = $A['jobid'];
      break;
    }

    if (($manifest_status_object->Status == ManifestJobStatuses::$COMPLETED) || ($manifest_status_object->Status == ManifestJobStatuses::$CANCELLED) || ($manifest_status_object->Status == ManifestJobStatuses::$FAILED)) {
      $statistics = Array();
      $statistics[] = new JobStatisticsObject(0, WorkspaceActionResultCodes::$OK);
      $res2         = db_query("SELECT rid, status FROM {filebuilder_service_job_statistics} WHERE jobid = :jobid", array(':jobid' => $jobid));

      while ($A = $res2->fetchAssoc()) {
        $statistics[] = new JobStatisticsObject($A['rid'], $A['status']);
      }

      $manifest_status_object->JobStatistics = $statistics;
    }

    
    return $manifest_status_object;
  }

  /**
   * Gets the specified FTP / SFTP upload path
   */
  public static function getUploadPath() {
    $path = drupal_realpath("private://filebuilder_working_directory/");
	if (!file_exists($path)) {
	  $umask = umask(0);
	  mkdir($path, 0777);
	  umask($umask);
	}
	return $path;
  }

  /**
   * Peform a request to be authenticated
   * 
   * @return Boolean						True on success, False on failure
   */
  public function authenticateRequest() {
    global $user;

    $username      = NULL;
    $password      = NULL;
    $return_result = FALSE;

    if (NWSHelper::getAuthenticationParameters($username, $password) === TRUE) {
      // Decrypt the password, hash the username
      $username = Encryption::aESDecryptDecode($username);
      $password = Encryption::aESDecryptDecode($password);

      $auth_result = user_authenticate(trim($username), $password);
      if (($auth_result === FALSE) || ($auth_result === NULL)) {
        $this->status = NWSStatus::$INVALID_CREDENTIALS;
      }
      else {

        $user = user_load($auth_result);
        if (($user === FALSE) || (user_access('access filebuilder', $user) === FALSE)) {
          $this->status = NWSStatus::$PERMISSION_DENIED;
        }
        elseif ($user->status != 1) {
          $this->status = NWSStatus::$ACCOUNT_NOT_ACTIVE;
        }
        else {
          $this->status  = NWSStatus::$OK;
          $this->uid     = $user->uid;
          $return_result = TRUE;
        }
      }
    }
    else {
      $this->status = NWSStatus::$INVALID_PARAMETERS;
    }

    return $return_result;
  }

  /**
   * This method is called after common values have been gathered and set, right before the command switch.
   * Any generic init processing should be done here
   */
  public function initialProcessing() {
    $this->filedepotClass = filedepot::getInstance();
    $this->workspaceId    = 0; // For now
  }

  /**
   * This method is called after everything has been performed 
   */
  public function lastProcessing() {
    
  }

  /**
   * Encode in JSON format
   */
  public function jsonEncode($data) {
    return drupal_json_encode($data);
  }

}

/**
 * Helper methods
 */
class NWSHelper implements NWSPlatformLink
{

  /**
   * Reads the request headers and fills in the values with the username and password
   * 
   * @param	String			&$username			Reference to a variable where the username will be stored
   * @param	String			&$password			Reference to a variable where the password will be stored
   * 
   * @return  Boolean								True on success, False on failure
   */
  public static function getAuthenticationParameters(&$username, &$password) {

    // Get request parameters
    $username = self::getRequestData(NWSConstants::$REQUEST_USERNAME);
    $password = self::getRequestData(NWSConstants::$REQUEST_PASSWORD);

    if (($username === NULL) or ($password === NULL)) {
      return false;
    }
    else {
      return true;
    }
  }

  /**
   * Returns the request data associated with the key
   * 
   * @param	String			$key				The key to search for
   * 
   * @return  Mixed								Data on success, NULL on failure
   */
  public static function getRequestData($key) {
    $returnValue = NULL;

    if (isset($_REQUEST[$key])) {
      $returnValue = $_REQUEST[$key];
    }

    return $returnValue;
  }

  /**
   * Attempts to authenticate a username and password
   * 
   * @param	String	$username			The username to authenticate (md5 hashed)
   * @param	String	$password			The password to authenticate (md5 hashed)
   * @param	Integer	$uid&				By reference variable, to hold the user id
   * 
   * @return 	Integer						Account status or -1 on failure to authenticate
   */
  public static function authenticate($username, $password, &$uid) {
    return -1;
  }

}

?>