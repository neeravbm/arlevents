<?php

/**
 * @file
 * nws_services.conf.php
 * Filedepot: File Management Service Module developed by Nextide www.nextide.ca
 * Configuration page for version info
 */
class NWSServiceConfigurationValues
{

  /**
   * Returns the SFTP path filedepot builder requires to change directory to on the server
   */
  public static function getSFTPUploadReturnPath() {
    $path = drupal_realpath("private://filebuilder_working_directory/");
    if (!file_exists($path)) {
      $umask = umask(0);
      mkdir($path, 0777);
      umask($umask);
    }

    $ftp_path_set = variable_get(FILEBUILDER_SERVICE_FTPPATH, TRUE);
    if ($ftp_path_set === TRUE) {
      return "";
    }
    else {
      return $path;
    }
  }

}

?>
