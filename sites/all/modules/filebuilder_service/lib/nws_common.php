<?php

/**
 * @file
 * nws-common.php
 * Filedepot: File Management Service Module developed by Nextide www.nextide.ca
 * Common classes and interfaces for cross platform compatibility with the desktop client
 */
if (strpos(strtolower($_SERVER['PHP_SELF']), 'nws_common.php') !== false) {
  die('This file can not be used on its own!');
}

/**
 * Constants for the NWS
 */
class NWSConstants
{

  public static $REQUEST_USERNAME = "un";
  public static $REQUEST_PASSWORD = "pw";
  public static $REQUEST_COMMAND  = "com";
  public static $REQUEST_ID       = "id";
  public static $UNIQUE_DIR       = "uniq";
  public static $REQUEST_ID2      = "id2";
  public static $WORKSPACE_ID     = "workspace_id";

}

/**
 * Commands for the NWS
 */
class NWSCommands
{

  public static $GET_AVAILABLE_WORKSPACES  = "GetAvailableWorkspaces";
  public static $GET_WORKSPACE_DISCLAIMER  = "GetWorkspaceDisclaimer";
  public static $GET_UNIQUE_UPLOAD_DIR     = "GetUniqueUploadDir";
  public static $PUT_MANIFEST_FILE         = "PutManifestFile";
  public static $GET_MANIFEST_STATUS       = "GetManifestStatus";
  public static $GET_NEXT_CATEGORIES_FILES = "GetNextCategoriesFiles";
  public static $DOWNLOAD_FILE             = "DownloadFile";
  public static $CANCEL_UPLOAD             = "CancelUpload";
  public static $DOWNLOAD_WORKSPACE        = "DownloadWorkspace";
  public static $DOWNLOAD_FOLDER           = "DownloadFolder";
  public static $GET_FOLDER_SIZE           = "GetFolderSize";
  public static $DOWNLOAD_SOFTWARE_UPDATE  = "DownloadSoftwareUpdate";
  public static $PING                      = "Ping";

  //public static $GET_FOLDER_SIZE           = "GetFolderSize";
}

/**
 * Statuses available
 */
class NWSStatus
{

  public static $OK                      = 0;
  public static $INVALID_PARAMETERS      = -1;
  public static $INVALID_CREDENTIALS     = -2;
  public static $ACCOUNT_NOT_ACTIVE      = -3;
  public static $INVALID_COMMAND         = -4;
  public static $INVALID_WORKSPACE_ID    = -5;
  public static $FILE_NOT_FOUND          = -6;
  public static $FAILED                  = -7;
  public static $PERMISSION_DENIED       = -8;
  public static $WORKSPACE_COMMIT_LOCKED = -9;

}

/**
 * Output formats available
 */
class NWSOutputFormats
{

  public static $JSON = "JSON";

}

/**
 * Contains information about the service request
 */
class NWServiceRequestObject
{

  private $_Username;
  private $_Password;
  private $_ArrayGET;
  private $_ArrayPOST;

}

/**
 * Contains permissions (for a folder)
 */
class NWSServiceFolderPermissionObject
{

  public $View     = FALSE;
  public $Download = FALSE;
  public $Upload   = FALSE;
  public $Manage   = FALSE;

}

class NWSServiceFilePermissionObject
{

  public $View     = FALSE;
  public $Download = FALSE;
  public $Manage   = FALSE;

}

/**
 * First request to the server upon client startup (called upon login)
 */
class NWSServiceInitialRequestResponseObject
{

  public $MultipleWorkspaces = TRUE; // If false, then this indicates no workspacess
  public $WorkspaceObjects   = Array(); // An array of workspace objects or NULL if no workspaces
  public $VersionInfo = NULL;
  public $UploadPath  = NULL; // The SFTP upload path of the server

  public function __construct() {
    $this->VersionInfo = NULL;//NWSServiceConfigurationValues::$VERSION_INFO;
  }

}

/**
 * Contains information about the ping response
 */
class NWSPingResponseObject
{

  /**
   * An array of NWSUserLoggedInObject objects
   */
  public $UsersCurrentlyLoggedIn = Array();

}

class ServerFolderInfoObject
{

  public $FileSize    = 0;
  public $FileCount   = 0;
  public $FolderCount = 0;

  public function __construct($fileSize, $fileCount, $folderCount) {
    $this->FileSize    = $fileSize;
    $this->FileCount   = $fileCount;
    $this->FolderCount = $folderCount;
  }

}

class JobStatisticsObject
{

  public $RepositoryActionID = 0;
  public $Status             = 0;

  public function __construct($repositoryActionId, $status) {
    $this->RepositoryActionID = (int) $repositoryActionId;
    $this->Status             = (int) $status;
  }

}

class ManifestStatusObject
{

  public $JobStatistics = NULL;
  public $Status        = "";

}

/**
 * Information about a logged in user
 */
class NWSUserLoggedInObject
{

  public $Username     = NULL;
  public $DateLoggedIn = NULL; // UNIX timestamp

  public function __construct($username, $dateLoggedIn) {
    $this->Username     = $username;
    $this->DateLoggedIn = $dateLoggedIn;
  }

}

/**
 * Contains the response information
 */
class NWServiceResponseObject
{

  public $STATUS;
  public $DATA = NULL;
  // Must be an integer
  public $UID;

  /**
   * Initialize the object, setting the status and data to be returned to the requester
   */
  public function __construct($status, $data = "", $uid = 0) {
    $this->STATUS = $status;
    $this->DATA   = $data;
    $this->UID    = (int) $uid;
  }

}

/**
 * Object representation of a workspace
 */
class NWSWorkspaceObject
{

  public $ID          = 0;
  public $Name        = NULL;
  public $Host        = NULL;
  public $Url         = NULL;
  public $Link        = NULL;
  public $Description = NULL;
  public $Disclaimer  = NULL;
  public $Image       = NULL;
  public $ImagePath   = NULL;
  public $Cid         = 0;
  public $WorkspaceID = 0;
  public $LastUpdated = NULL;
  public $HasAccess   = FALSE;

}

/**
 * Represents a category
 */
class RepositoryDirectory
{

  public $DirectoryId       = 0;
  public $ParentDirectoryId = 0;
  public $DirectoryName     = NULL;
  public $LastModified      = NULL;
  public $HasBeenLoaded     = false; /* If true, it signals to the receiving app that the contents of this directory have been loaded as well */
  public $FolderOrder       = 0;
  public $PermissionObject  = NULL;
  public $LastUpdated       = NULL;

}

/**
 * Represents a file
 */
class RepositoryFile
{

  public $FileId            = 0;
  public $FileName          = NULL;
  public $FileType          = NULL;
  public $FileSize          = 0;
  public $LastModified      = NULL;
  public $ParentDirectoryId = 0;
  public $Submitter         = NULL;
  public $FileOrder         = 0;
  public $PermissionObject  = NULL;

}

/**
 * Possible output formats
 */
class OutputFormats
{

  public static $NO_OUTPUT = "NoOutput";
  public static $JSON      = "JSON";

}

class Encryption
{

  /**
   * Returns the current encryption key
   */
  private static function getEncryptionKey() {
    return base64_decode(variable_get(FILEBUILDER_SERVICE_GENERATEDKEY, base64_encode(""))); //base64_decode(NWSServiceConfigurationValues::$UPASS_HASHKEY);
  }

  /**
   * Encrypt a string using AES encryption and base encode 64 the result
   * @param type $sr
   */
  public static function dESEncryptEncode($in_data) {
    $block = mcrypt_get_block_size('des', 'ecb');
    if (($pad   = $block - (strlen($str) % $block)) < $block) {
      $in_data .= str_repeat(chr($pad), $pad);
    }

    return base64_encode(
        mcrypt_decrypt(MCRYPT_DES, self::getEncryptionKey(), $in_data, MCRYPT_MODE_ECB)
    );
  }

  /**
   * Encrypt a string using AES encryption and base encode 64 the result
   * @param type $sr
   */
  public static function aESEncryptEncode($in_data, $encryption_key=NULL) {
    // Java block size is 16 bytes
    $block = 16; //mcrypt_get_block_size('des', 'ecb');
    if (($pad   = $block - (strlen($in_data) % $block)) < $block) {
      $in_data .= str_repeat(chr($pad), $pad);
    }
    
    if ($encryption_key === NULL) {
      $encryption_key = self::getEncryptionKey();
    }

    return base64_encode(
        mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encryption_key, $in_data, MCRYPT_MODE_ECB)
    );
  }

  /**
   * Decrypts and decodes a 
   * @param type $in_data
   * @return type
   */
  public static function aESDecryptDecode($in_data, $encryption_key = NULL) {
    if ($encryption_key === NULL) {
      $encryption_key = self::getEncryptionKey();
    }
      
    
    $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $encryption_key, base64_decode($in_data), MCRYPT_MODE_ECB);

    # Strip padding out.
    // Java block size is 16 bytes
    $block = 16; //mcrypt_get_block_size('des', 'ecb') * 2;
    $pad   = ord($str[($len   = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match(
        '/' . chr($pad) . '{' . $pad . '}$/', $str
      )
    ) {
      return substr($str, 0, strlen($str) - $pad);
    }

    return $str;
  }

  public static function pkcs5Pad($text, $blocksize) {
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
  }

  /**
   * Decrypts and decodes a 
   * @param type $in_data
   * @return type
   */
  public static function dESDecryptDecode($in_data) {
    $str = mcrypt_encrypt(MCRYPT_DES, self::getEncryptionKey(), base64_decode($in_data), MCRYPT_MODE_ECB);

    # Strip padding out.
    $block = mcrypt_get_block_size('des', 'ecb');
    $pad   = ord($str[($len   = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match(
        '/' . chr($pad) . '{' . $pad . '}$/', $str
      )
    ) {
      return substr($str, 0, strlen($str) - $pad);
    }

    return $str;
  }

}

/**
 * Provides a common interface for differing platforms
 */
interface NWSPlatformLink
{

  public static function getAuthenticationParameters(&$username, &$password);

  public static function getRequestData($key);

  public static function authenticate($username, $password, &$returnData);
}

/**
 * Provides a common abstraction for differing platforms - these are the worker methods
 * Note on implementing. On error, fill $this->_Error with the error message and $this->status with the status code. If non boolean return, return NULL on error. 
 */
abstract class NWSRequestWorkerCommon
{

  protected $data         = NULL;
  protected $outputFormat = NULL;
  protected $error        = false;
  protected $status;
  protected $workspaceId  = 0;

  // ABSTRACT HELPER METHODS

  /**
   * Use this method to load any config values. This is called after authenticateRequest and before the command. 
   */
  protected abstract function loadSiteConfig();

  /// ABSTRACT PRIVATE REQUEST METHODS

  /**
   * Retreives a list of workspaces
   * 
   * @return 	NWSServiceInitialRequestResponseObject					
   */
  protected abstract function getAvailableWorkspaces();

  /**
   * Get the next unique ID value for the upload directory
   */
  protected abstract function getUniqueUploadDir();

  /**
   * Retreives the workspace disclaimer and image for that workspace
   * 
   * @return  NWSWorkspaceObject						A workspace object with the disclaimer and image
   */
  protected abstract function getWorkspaceDisclaimer();

  /**
   * Downloads the entire workspace
   *
   */
  protected abstract function downloadWorkspace();

  /**
   * Download a category and all underneath files into an archive
   * @param Integer               $cid          Category ID to download
   * @param	Integer               $workspaceId	The workspace id
   */
  protected abstract function downloadCategory($cid);

  /**
   * Download the current version of the client software
   */
  public abstract function downloadSoftwareUpdate($path);

  /**
   * Requests the processing operating on the operation specified by the $uniqueDirectory cancel processing and clean up.
   * 
   * @param	String					$uniqueDirectory	The unique directory name where the data and files are uploaded
   * @param Integer         $workspaceId      Workspace ID
   */
  protected abstract function cancelUpload($uniqueDirectory);

  /**
   * The ping performs two actions - adds an entry in the database for this user and workspace, 
   * removes any entries for users that have not pinged in 10 minutes,
   * and returns a list of all users currently connected to the workspace
   * 
   * @return NWSPingResponseObject        
   */
  protected abstract function ping();

  /**
   * Start processing the manifest data stored in the directory passed
   * 
   * @param	String					$uniqueDirectory	The unique directory name where the data and files are uploaded
   * @return  Integer										The job ID
   */
  protected abstract function putManifestFile($uniqueDirectory, $notificationOn);

  /**
   * Gets the next set of category files to be displayed
   * 
   * @param	Integer					$workspaceId	The work space id	
   */
  protected abstract function getNextCategoriesFiles($categoryId);

  /**
   * Returns a file as a byte stream to the requesting application
   * 
   * @param	Integer					$fid			File ID to retreive
   * @param	Integer					$workspaceId	Workspace Identifier
   */
  protected abstract function downloadFile($fid);

  /**
   * Recursively gets the size of a folder and all its subfolders
   * 
   * @param Integer         $cid     Category ID to retreive size for
   * @return ServerFolderInfoObject                 
   */
  protected abstract function getFolderSize($cid);

  /**
   * Asks for the status of the current manifest job being processed
   * 
   * @param  String				   $uniqueDirectory		The unique directory name
   * @return String										The status as a string
   */
  protected abstract function getManifestStatus($uniqueDirectory);

  /**
   * Start a program as a background process
   * (Linux only - Windows simply leaves the process open for debugging)
   * 
   * @param Mixed $args           An array of arguments or a single argument as a string
   */
  public function execInBackground($path, $cmd, $exe, $args = "") {
    //
    /*
      if (substr(php_uname(), 0, 7) == "Windows"){
      // not to be used in production -- for debugging
      exec("C:/wamp/bin/php/php5.3.8/php.exe C:\\wamp\\www\\nextide\\ccc\\nexfile\\service\\manifest-worker.php $args ");
      } else {
      chdir($path);
      $arg_string = "";

      if (is_array($args)) {
      foreach ($args as $arg) {
      $arg_string .= escapeshellarg($arg) . " ";
      }
      }
      else {
      $arg_string = escapeshellarg($args);
      }

      COM_errorLog("$cmd" . $exe . " " . $arg_string . " > /dev/null &");
      exec("$cmd" . $exe . " " . $arg_string . " > /dev/null &");
      } */
    if (substr(php_uname(), 0, 7) == "Windows") {
      // not to be used in production -- for debugging
      exec("C:/wamp/bin/php/php5.3.8/php.exe C:\\wamp\\www\\nextide\\ccc\\nexfile\\service\\manifest-worker.php $args ");
    }
    else {
      chdir($path);
      $arg_string = "";

      if (is_array($args)) {
        foreach ($args as $arg) {
          $arg_string .= escapeshellarg($arg) . " ";
        }
      }
      else {
        $arg_string = escapeshellarg($args);
      }
      exec("$cmd" . $exe . " " . $arg_string . " > /dev/null &");
      //exec("$cmd" . $exe . " " . escapeshellarg($args) . " > /dev/null &");   
    }
  }

  /**
   * Constructor
   * 
   * @param	Mixed		$data				Any required data
   * @param	Constant	$outputType			The format to return the request result
   */
  public function __construct($data, $outputFormat) {
    $this->data         = $data;
    $this->outputFormat = $outputFormat;
    $this->status       = NWSStatus::$OK;
  }

  /**
   * Peform a request to be authenticated
   * 
   * @return Boolean						True on success, False on failure
   */
  public abstract function authenticateRequest();

  /**
   * This method is called after common values have been gathered and set, right before the command switch.
   * Any generic init processing should be done here
   */
  public abstract function initialProcessing();

  /**
   * This method is called after everything has been performed 
   */
  public abstract function lastProcessing();

  /**
   * Encode in JSON format
   */
  public abstract function jsonEncode($data);

  public abstract function getUID();

  /**
   * Reads the request command using getRequestData(COMMAND) and returns the packaged result based on the resulting command
   * 
   * @return 	Mixed						The response data formed based on the type requested
   */
  public function getRequestResponse() {

    $responseObject = NULL;

    // Authenticate
    if ($this->authenticateRequest() === FALSE) {
      $responseObject = new NWServiceResponseObject($this->status, null);
    }
    else {
      // Perform config load
      $this->loadSiteConfig();

      //
      $responseObject = NULL;
      $resultData     = NULL;

      // Get the command
      $command = NWSHelper::getRequestData(NWSConstants::$REQUEST_COMMAND);

      // Is the workspace ID sent
      $workspaceId = NWSHelper::getRequestData(NWSConstants::$WORKSPACE_ID);
      if ($workspaceId !== NULL) {
        $this->workspaceId = (int) $workspaceId;
      }

      $this->initialProcessing();
      switch ($command) {
        case NWSCommands::$PING:
          $resultData         = $this->ping();
          break;
        case NWSCommands::$DOWNLOAD_SOFTWARE_UPDATE:
          $this->downloadSoftwareUpdate();
          $this->outputFormat = OutputFormats::$NO_OUTPUT;
          break;
        case NWSCommands::$GET_FOLDER_SIZE:
          $cid                = NWSHelper::getRequestData(NWSConstants::$REQUEST_ID);
          $resultData         = $this->getFolderSize($cid);
          break;
        case NWSCommands::$DOWNLOAD_FOLDER:
          $cid                = NWSHelper::getRequestData(NWSConstants::$REQUEST_ID);
          $this->downloadCategory($cid);
          break;
        case NWSCommands::$DOWNLOAD_WORKSPACE:
          $this->downloadWorkspace();
          $this->outputFormat = OutputFormats::$NO_OUTPUT;
          break;
        case NWSCommands::$DOWNLOAD_FILE:
          $fid                = NWSHelper::getRequestData(NWSConstants::$REQUEST_ID);
          $this->downloadFile($fid);
          $this->outputFormat = OutputFormats::$NO_OUTPUT;
          break;
        case NWSCommands::$GET_AVAILABLE_WORKSPACES:
          // Request a list of all available workspaces				
          $resultData         = $this->getAvailableWorkspaces();
          break;
        case NWSCommands::$GET_WORKSPACE_DISCLAIMER:
          $resultData         = $this->getWorkspaceDisclaimer();
          break;
        case NWSCommands::$GET_UNIQUE_UPLOAD_DIR:
          $resultData         = $this->getUniqueUploadDir();
          break;
        case NWSCommands::$CANCEL_UPLOAD:
          $uniqueDirectory    = NWSHelper::getRequestData(NWSConstants::$UNIQUE_DIR);
          $resultData         = $this->cancelUpload($uniqueDirectory);
          break;
        case NWSCommands::$PUT_MANIFEST_FILE:
          $uniqueDirectory    = NWSHelper::getRequestData(NWSConstants::$UNIQUE_DIR);
          $notificationOn     = (int) NWSHelper::getRequestData(NWSConstants::$REQUEST_ID);
          $resultData         = $this->putManifestFile($uniqueDirectory, $notificationOn);
          break;
        case NWSCommands::$GET_MANIFEST_STATUS:
          $uniqueDirectory    = NWSHelper::getRequestData(NWSConstants::$UNIQUE_DIR);
          $resultData         = $this->getManifestStatus($uniqueDirectory);
          break;
        case NWSCommands::$GET_NEXT_CATEGORIES_FILES:
          $cid                = NWSHelper::getRequestData(NWSConstants::$REQUEST_ID);
          $resultData         = $this->getNextCategoriesFiles($cid);
          break;
        default:
          $this->status       = NWSStatus::$INVALID_COMMAND;
          break;
      }


      $responseObject = new NWServiceResponseObject($this->status, $resultData, $this->getUID());
    }

    // What format
    $output = "";
    $this->lastProcessing();

    switch ($this->outputFormat) {
      case OutputFormats::$NO_OUTPUT:
        // do nothing
        break;
      default:
        $output = $this->jsonEncode($responseObject);
        break;
    }


    return $output;
  }

}

?>