7.x-1.x RELEASE NOTE

The FileBuilder Service module provides an web service interface allowing the FileBuilder desktop client to manage the online Filedepot repository. 
 
The FileBuilder Service module is provided by Nextide www.nextide.ca and written by Tim Patrick (_timpatrick)

Dependencies
---------------------
 * filedepot
 * background_process
 
Requirements
------------
 PHP 5.3+ (openssl_random_psuedo_bytes function requireD) and PHP JSON library enabled.

 As of PHP 5.2.0, the JSON extension is bundled and compiled into PHP by default.
 
 - (Recommended) An SFTP server with a username configured to point to: private://filebuilder_working_directory/ where "private" is the path to your drupal private filesystem. This directory must be writeable by 
 the SFTP user AND the webserver (drupal).
  OR
 - An FTP server with the root configured to point to private://filebuilder_working_directory/ where "private" is the path to your drupal private filesystem. This directory must be writeable by 
 the FTP user AND the webserver (drupal).
 - PHP mcrypt extension enabled
 - PHP openSSL 
 
 
Permissions
--------------------------------------
There are two permissions that can be set - "Access FileBuilder Utility" and "Administer FileBuilder Utility". Only users who have been given the permission "Access FileBuilder Utility" may login using the
desktop client, and only those with the "Administer FileBuilder Utility" can administer the FileBuilder service module. 
All folder permissions are inherited from Filedepot. 

Sync Wizard
--------------------------------------
To allow for the easier syncing of FileBuilder with Filedepot, a wizard is available by navigating to "admin/config/media/filebuilder_service" on your drupal site.
This will guide you through the steps of setting up your SFTP / FTP connection to sync to this module and FileBuilder to sync with drupal. 

Please Note: 
The username and password to login to the FileBuilder desktop client is your drupal username and password. 
 
Uploading 
---------------------------------------
FileBuilder uploads data using an SFTP or FTP connection. 
Data is uploaded to "private://filebuilder_working_directory/" where private:// is the path to your drupal private files directory.
The SFTP or FTP account's root directory should point to the resolved path of "private://filebuilder_working_directory/" optimally.
Please use the Sync Wizard to perform the SFTP / FTP sync. 

Interfacing with the desktop client
---------------------------------------
To interface with the desktop client, once the module has been installed, navigate to "admin/config/media/filebuilder_service". The client requires a unique server specific key to be able to encrypt 
authentication details between the desktop client and the service module. You can generate this key by clicking the "Generate New Key" link, and once generated, can download the keyfile by clicking the 
"Download Key File" link. This keyfile can then be imported into all instances of the FileBuilder desktop client that are to connect to this service. Upon first startup, the desktop client will 
ask for the key file. It can also be added by navigating to "Configure", then "Authentication Key", and then "Attach New Authentication Key" in the FileBuilder deskop client. 

