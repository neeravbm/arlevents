<?php

/**
 * Instance of the manifest parser - will parse manifests  
 * NOTE: Wrap in try{}catch block
 */
class ManifestParser
{

  private $_SiteMap     = NULL;
  private $_ManifestDir = NULL;

  public function __construct() {
    
  }

  /**
   * Attempt to load the manifest file
   */
  public function LoadManifest($manifestDir) {
    $manifest = "$manifestDir/manifest.xml";
    if (file_exists($manifest) === TRUE) {
      // Continue
      $this->_ManifestDir = $manifestDir;
      $this->_SiteMap     = new SimpleXMLElement($manifest, null, true);
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Recursively delete the contents of a directory
   */
  private function RmDirRecursive($dir) {
    $objects = @scandir($dir);
    foreach ($objects as $object) {
      if (($object != '.') && ($object != '..')) {
        $path = "{$dir}/$object";
        if (filetype($path) == "dir") {
          $this->RmDirRecursive($path);
        }
        else {
          @unlink($path);
        }
      }
    }

    @rmdir($dir);
  }

  /**
   * Remove the manifest file and directory and any supporting data
   */
  public function CleanUp() {
    $result = @unlink("{$this->_ManifestDir}/manifest.xml");

    // Delete all files recursively left over
    $this->RmDirRecursive($this->_ManifestDir);
  }

  /**
   * Parse the manifest file
   * 
   * @return ManifestResult					The parsing result
   */
  public function Parse() {

    $workspaceId           = 0;
    $uploadDir             = NULL;
    $commandsAddFilesArray = Array();
    $commandsAddDirArray = Array();
    $commandsDelFileArray = Array();
    $commandsDelDirArray = Array();
    $commandsReOrderDirArray = Array();
    $commandsMoveDirArray = Array();
    $commandsMoveFileArray = Array();
    $commandsReOrderFileArray = Array();
    $commandsRenameFileArray = Array();
    $commandsRenameDirArray = Array();
    $commandsReplaceDirArray = Array();
    $commandsReplaceFileArray = Array();

    // Loop over each parent element
    foreach ($this->_SiteMap->children() as $child) {

      $name = $child->getName();

      if ($name === "workspaceId") {
        $workspaceId = $child;
      }
      else if ($name === "uploadDir") {
        $uploadDir = $child;
      }
      else if ($name === "commands") {
        // parse file objects
        foreach ($child->children() as $command) {
          $commandName = $command->getName();
          $attributes  = $command->attributes();

          if ($commandName === "AddDirectory") {
            $addDir                     = new RepositoryAddDirectoryCommandObject();
            $addDir->RepositoryActionID = $attributes['actionId'];
            $addDir->Id                 = $attributes['id'];
            $addDir->LocalParent        = ($attributes['localParent'] == "true") ? true : false;
            $addDir->Name               = (string) $command; //$attributes['name'];
            $addDir->FolderOrder        = (int) $attributes['folderOrder'];
            $addDir->ParentId           = $attributes['parent'][0];
            $commandsAddDirArray[]      = $addDir;
          }
          else if ($commandName === "AddFile") {
            $addFile                     = new RepositoryAddFileCommandObject();
            $addFile->RepositoryActionID = $attributes['actionId'];
            $addFile->Path               = $attributes['path'];
            $addFile->Id                 = $attributes['id'];
            $addFile->LocalParent        = ($attributes['localParent'] == "true") ? true : false;
            $addFile->Name               = (string) $command; //$attributes['name'];
            $addFile->ParentId           = $attributes['parent'];
            $addFile->Size               = $attributes['size'];
            $addFile->Type               = $attributes['type'];
            $commandsAddFilesArray[]     = $addFile;
          }
          else if ($commandName === "DelDirectory") {
            $delDir                     = new RepositoryDeleteDirectoryCommandObject();
            $delDir->RepositoryActionID = $attributes['actionId'];
            $delDir->Id                 = $attributes['id'];
            $commandsDelDirArray[]      = $delDir;
          }
          else if ($commandName === "DelFile") {
            $delFile                     = new RepositoryDeleteFileCommandObject();
            $delFile->RepositoryActionID = $attributes['actionId'];
            $delFile->Id                 = $attributes['id'];
            $commandsDelFileArray[]      = $delFile;
          }
          else if ($commandName === "ReplaceDirectory") {
            $delDir                     = new RepositoryReplaceDirectoryCommandObject();
            $delDir->Id                 = $attributes['id'];
            $delDir->RepositoryActionID = $attributes['actionId'];
            $commandsReplaceDirArray[]  = $delDir;
          }
          else if ($commandName === "ReplaceFile") {
            $delFile                     = new RepositoryReplaceFileCommandObject();
            $delFile->Id                 = $attributes['id'];
            $delFile->RepositoryActionID = $attributes['actionId'];
            $commandsReplaceFileArray[]  = $delFile;
          }
          else if ($commandName == "ReOrderDirectory") {
            $reorderDir                     = new RepositoryChangeDirectoryOrderObject();
            $reorderDir->Id                 = $attributes['id'];
            $reorderDir->Order              = $attributes['order'];
            $reorderDir->RepositoryActionID = $attributes['actionId'];
            $reorderDir->Local              = ($attributes['local'] == "true") ? true : false;
            $commandsReOrderDirArray[]      = $reorderDir;
          }
          else if ($commandName == "ReOrderFile") {
            $reorderFile                     = new RepositoryChangeFileOrderObject();
            $reorderFile->Id                 = $attributes['id'];
            $reorderFile->Order              = $attributes['order'];
            $reorderFile->RepositoryActionID = $attributes['actionId'];
            $reorderFile->Local              = ($attributes['local'] == "true") ? true : false;
            $commandsReOrderFileArray[]      = $reorderFile;
          }
          else if ($commandName == "MoveDirectory") {
            $moveDir                     = new RepositoryMoveDirectoryObject();
            $moveDir->Id                 = $attributes['id'];
            $moveDir->RepositoryActionID = $attributes['actionId'];
            $moveDir->ParentId           = $attributes['parent'];
            $moveDir->IsLocalParent      = ($attributes['localParent'] == "true") ? true : false;
            $commandsMoveDirArray[]      = $moveDir;
          }
          else if ($commandName == "MoveFile") {
            $moveFile                     = new RepositoryMoveFileObject();
            $moveFile->Id                 = $attributes['id'];
            $moveFile->RepositoryActionID = $attributes['actionId'];
            $moveFile->ParentId           = $attributes['parent'];
            $moveFile->IsLocalParent      = ($attributes['localParent'] == "true") ? true : false;
            $commandsMoveFileArray[]      = $moveFile;
          }
          else if ($commandName == "RenameFile") {
            $renameFile                     = new RepositoryChangeFileNameObject();
            $renameFile->Id                 = $attributes['id'];
            $renameFile->RepositoryActionID = $attributes['actionId'];
            foreach ($command->children() as $subcommand) {
              $subName = $subcommand->getName();
              if ($subName == "NewName") {
                $renameFile->NewName = (string) $subcommand;
              }
              else if ($subName == "OldName") {
                $renameFile->OldName       = (string) $subcommand;
              }
            }
            $commandsRenameFileArray[] = $renameFile;
          }
          else if ($commandName == "RenameDirectory") {
            $renameDir                     = new RepositoryChangeDirectoryNameObject();
            $renameDir->Id                 = $attributes['id'];
            $renameDir->RepositoryActionID = $attributes['actionId'];
            foreach ($command->children() as $subcommand) {
              $subName = $subcommand->getName();
              if ($subName == "NewName") {
                $renameDir->NewName = (string) $subcommand;
              }
              else if ($subName == "OldName") {
                $renameDir->OldName       = (string) $subcommand;
              }
            }
            $commandsRenameDirArray[] = $renameDir;
          }
        }
      }
    }

    // Finished parsing
    $this->_SiteMap = NULL;

    $manifestResult                       = new ManifestResult();
    $manifestResult->AddDirectoryCommands = $commandsAddDirArray;

    // Sort the add directory commands
    usort($manifestResult->AddDirectoryCommands, array("ManifestParser", "CompareDirectoryCommands"));

    $manifestResult->AddFileCommands                    = $commandsAddFilesArray;
    $manifestResult->DeleteDirectoryCommands            = $commandsDelDirArray;
    $manifestResult->DeleteFileCommands                 = $commandsDelFileArray;
    $manifestResult->RepositoryRenameDirectoryCommands  = $commandsRenameDirArray;
    $manifestResult->RepositoryRenameFileCommands       = $commandsRenameFileArray;
    $manifestResult->RepositoryChangeDirectoryCommands  = $commandsReOrderDirArray;
    $manifestResult->RespositoryChangeFileCommands      = $commandsReOrderFileArray;
    $manifestResult->RepositoryMoveDirectoryCommands    = $commandsMoveDirArray;
    $manifestResult->RepositoryMoveFileCommands         = $commandsMoveFileArray;
    $manifestResult->RepositoryReplaceDirectoryCommands = $commandsReplaceDirArray;
    $manifestResult->RepositoryReplaceFileCommands      = $commandsReplaceFileArray;
    $manifestResult->WorkspaceId                        = $workspaceId;

    return $manifestResult;
  }

  /**
   * This is called by usort, to sort the directory commands in the order they were created on the client
   * Compares RepositoryAddDirectoryCommandObject $a, $b
   */
  private static function CompareDirectoryCommands($a, $b) {
    $aId = (int) $a->Id;
    $bId = (int) $b->Id;
    if ($aId === $bId) {
      return 0;
    }
    else {
      return ($aId > $bId) ? +1 : -1;
    }
  }

}

/**
 * Potential statuses
 */
class ManifestJobStatuses
{

  public static $OPEN                       = "open";
  public static $PROCESSING                 = "processing";
  public static $COMPLETED                  = "completed";
  public static $REVERTING                  = "reverting";
  public static $CANCELLED                  = "cancelled";
  public static $FAILED 			    = "failed";
  public static $FAILED_PARSING             = "failed_parsing";
  public static $FAILED_NO_SUCH_JOB         = "failed_no_such_job";
  public static $FAILED_NO_SUCH_DIR         = "failed_no_such_dir";
  public static $INSUFFICIENT_PRIVS         = "insufficient_priviledges";
  public static $FAILED_PROCESSING_COMMANDS = "failed_processing_commands"; // failed and reason is given by WorkspaceActionResultCodes error code

}

/**
 * Result codes indicating if anything went wrong
 */
class WorkspaceActionResultCodes
{

  public static $OK                = 200;
  public static $FAILURE_TO_REMOVE = 400;
  public static $NOT_FOUND         = 404;
  public static $FORBIDDEN         = 403;
  public static $FAILURE_TO_CREATE = 401;
  public static $ALREADY_EXISTS    = 300;
  public static $INVALID_DATA      = 301;
  public static $NOT_SUPPORTED     = 302;

}

/**
 * Interface to force compliance across platforms for the workspace manager class
 */
interface WorkspaceManagerBaseInterface
{

  /**
   * Delete a file by file id
   * 
   * @param	Integer						$fileId					ID of the file to delete
   * @return  WorkspaceActionResultItem							ResultItem object holding information about the status of the request
   */
  public static function DeleteFile($fileId);

  /**
   * Delete a category by category id
   * 
   * @param	Integer						$catId						ID of the category to delete
   * @return  WorkpaceActionResultItem								
   */
  public static function DeleteCategory($catId);

  /**
   * Save a file
   * @param	RepositoryAddFileCommandObject				$fileCommandObject			Containing information about the file to upload
   * @param	String										$dirPath					Path to the directory containing the file
   * @param	Integer										$catId						Parent category ID
   * @return  WorkspaceActionResultItem							ResultItem object holding information about the status of the request
   */
  public static function SaveFile(RepositoryAddFileCommandObject $fileCommandObject, $dirPath, $catId);

  /**
   * Create a new category
   * 
   * @param	int			$wkspace			Id of the workspace
   * @param	int			$catpid				Parent category id
   * @param	String		$catname			Name of the category
   * @param	String		$catdesc			Optional description
   * @return  WorkspaceActionResultItem							ResultItem object holding information about the status of the request
   */
  public static function CreateCategory($wkspace, $catpid, $catname, $catdesc = "");
}

/**
 * Contains information about the result of the action performed
 */
class WorkspaceActionResultItem
{

  /**
   * True if the result was a success, false on failure
   */
  public $Succeeded = false;

  /**
   * What went wrong (if applicable - error message)
   */
  public $ErrorMessage = NULL;

  /**
   * Error code if applicable
   */
  public $ResultCode = 0;

  /**
   * Any supporting data that needs to be returned
   */
  public $Data = NULL;

  /**
   * Set the public fields quickly
   */
  public function Set($succeeded, $errorMessage, $resultCode) {
    $this->Succeeded    = $succeeded;
    $this->ResultCode   = $resultCode;
    $this->ErrorMessage = $errorMessage;
  }

}

/**
 * Contains the items parsed from the manifest
 */
class ManifestResult
{

  // Private Members
  private $_AddedCategoryIDMappings = Array(); // local => server

  /**
   * The workspace id
   */
  public $WorkspaceId;

  /**
   * An array of commands containing information about new directories to create
   * NOTE: The directory commands are sorted by ID lower to higher, so they are replicated in the order they were created. 
   * This means that they can be looped through i=0, i < length and there will be no risk of not having the parent already created
   */
  public $AddDirectoryCommands;

  /**
   * An array of commands containing information about uploaded files to add
   */
  public $AddFileCommands;

  /**
   * An array of commands containing information about deleting existing directories
   */
  public $DeleteDirectoryCommands;

  /**
   * An array of commands containing information about deleting existing files
   */
  public $DeleteFileCommands;

  /**
   * An array of commands containing information about deleting existing directories
   */
  public $RepositoryReplaceDirectoryCommands;

  /**
   * An array of commands containing information about deleting existing files
   */
  public $RepositoryReplaceFileCommands;

  /**
   * An array of commands containing information about changing the order of server directories
   */
  public $RepositoryChangeDirectoryCommands;

  /**
   * An array of commands containing information about changing the order of server directories
   */
  public $RespositoryChangeFileCommands;

  /**
   * An array of commands containing information about moving a directory on the server
   */
  public $RepositoryMoveDirectoryCommands;

  /**
   * An array of commands containing information about moving a file on the server
   */
  public $RepositoryMoveFileCommands;

  /**
   * An array of file rename objects
   * @var type 
   */
  public $RepositoryRenameFileCommands;

  /**
   * An array of commands for directories  to be renamed
   * @var type 
   */
  public $RepositoryRenameDirectoryCommands;

  /**
   * Call this to retreive the next RepositoryAddDirectoryCommandObject in the list.
   * This method will check to make sure the object being returned has a parent that has been already processed and added. 
   * 
   * @return                RepositoryAddDirectoryCommandObject   OR NULL if no more entries
   */
  public function GetNextAddDirectoryCommand() {
    $addDirCommand = NULL;
    $count         = count($this->AddDirectoryCommands);
    $i             = 0;
    while ($i < $count) {
      $addDirCommand = $this->AddDirectoryCommands[$i];

      if ($addDirCommand->LocalParent == FALSE) {
        break;
      }
      else {
        $parentId = (int) $addDirCommand->ParentId;
        if (in_array($parentId, $this->_AddedCategoryIDMappings)) {
          break;
        }
        else {
          $i++;
          continue;
        }
      }
    }

    if ($addDirCommand !== NULL) {
      unset($this->AddDirectoryCommands[$i]);
      $this->AddDirectoryCommands = array_values($this->AddDirectoryCommands);
    }

    return $addDirCommand;
  }

  /**
   * Register that a directory could not be created due to some error
   * This is vital as it removes said directory from the list - this will ensure that the program will not go into an endless loop
   *
   * @param	Integer	$localDirId		The ID of this directory (local, not server)
   */
  public function RegisterDirectoryCreateError($localID) {
    $localID = (int) $localID;
    for ($i = 0; $i < count($this->AddDirectoryCommands); $i++) {
      $cmd = $this->AddDirectoryCommands[$i];
      $id  = (int) $cmd->Id;
      if ($id == $localID) {
        unset($this->AddDirectoryCommands[$i]);
        break;
      }
    }
  }

  /**
   * Register a directory has been created
   * @param type $localDirId            The local ID for the directory
   */
  public function RegisterDirectoryCreated($localDirId) {
    $this->_AddedCategoryIDMappings[] = (int) $localDirId;
  }

}

/**
 * AddFile command object
 */
class RepositoryAddFileCommandObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * True if the parent directory is local, false if server
   */
  public $LocalParent;

  /**
   * The ID of the parent directory
   */
  public $ParentId;

  /**
   * The name of the file
   */
  public $Name;

  /**
   * The path name of the file (the file id)
   */
  public $Path;

  /**
   * The size of the file
   */
  public $Size;

  /**
   * The MIME type of the file
   */
  public $Type;

  /**
   * ID of the file (local ID)
   */
  public $Id;

}

/**
 * Add directory object data
 */
class RepositoryAddDirectoryCommandObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * True if the parent directory is local, false if server
   */
  public $LocalParent;

  /**
   * The ID of the parent directory
   */
  public $ParentId;

  /**
   * The name of the file
   */
  public $Name;

  /**
   * The Id of the directory (local id)
   */
  public $Id;

  /**
   * The folder order
   */
  public $FolderOrder;

}

/**
 * Information about changing the order of a directory on the server
 */
class RepositoryChangeDirectoryOrderObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The id of the directory
   */
  public $Id;

  /**
   * The order to display as
   */
  public $Order;

  /**
   * True if local, false otherwise 
   */
  public $Local;

}

/**
 * Information about changing the order of a file on the server
 */
class RepositoryChangeFileOrderObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The id of the directory
   */
  public $Id;

  /**
   * The order to display as
   */
  public $Order;

  /**
   * True if local, false otherwise 
   */
  public $Local;

}

/**
 * Information about changing the name of a file on the server
 */
class RepositoryChangeFileNameObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The id of the file
   * @var type 
   */
  public $Id;

  /**
   * The new name
   * @var type 
   */
  public $NewName;

  /**
   * The old name
   * @var type 
   */
  public $OldName;

}

/**
 * Information about changing the name of a directory on the server
 */
class RepositoryChangeDirectoryNameObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The id of the directory
   * @var type 
   */
  public $Id;

  /**
   * The new name
   * @var type 
   */
  public $NewName;

  /**
   * The old name
   * @var type 
   */
  public $OldName;

}

/**
 * Information about moving a directory on the server
 */
class RepositoryMoveDirectoryObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The id of the directory
   */
  public $Id;

  /**
   * The ID of the parent directory to move to
   */
  public $ParentId;

  /**
   * If true, the directory being moved to is going to be created by the manifest (doesn't already exist)
   */
  public $IsLocalParent;

}

/**
 * Information about moving a file on the server
 */
class RepositoryMoveFileObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The id of the directory
   */
  public $Id;

  /**
   * The ID of the parent directory to move to
   */
  public $ParentId;

  /**
   * If true, the directory being moved to is going to be created by the manifest (doesn't already exist)
   */
  public $IsLocalParent;

}

/**
 * Information about a server directory to be deleted
 */
class RepositoryDeleteDirectoryCommandObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The Id of the directory
   */
  public $Id;

}

/**
 * Information about a server file to be deleted
 */
class RepositoryDeleteFileCommandObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The Id of the file
   */
  public $Id;

}

/**
 * Information about a server directory to be replace deleted
 */
class RepositoryReplaceDirectoryCommandObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The Id of the directory
   */
  public $Id;

}

/**
 * Information about a server file to be replace deleted
 */
class RepositoryReplaceFileCommandObject
{

  /**
   * The action ID (used to identify this action)
   */
  public $RepositoryActionID;

  /**
   * The Id of the file
   */
  public $Id;

}

?>