<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$filebuilder_private_path = drupal_realpath("private://filebuilder_working_directory");
?>

<?php if ($wizard_step == 1): ?>
  <?php
  $path = drupal_realpath("private://");
  if (empty($path)):
    ?>
    <h3>Wizard Step One - FTP / SFTP Sync</h3>
    Your drupal private file system is currently not set. To continue with this wizard you must set a private file system path. 
    <br />This can be changed <a target="_blank" href="<?php echo url('admin/config/media/file-system'); ?>">here</a>.

  <?php else: ?>
    <h3>Wizard Step One - FTP / SFTP Sync</h3>
    The FTP or SFTP account used to upload data from FileBuilder should optimally be configured to have its root directory point to your private://filebuilder_working_directory where private:// is the path to your drupal private files directory.
    <br /><br />
    Your drupal private file system is currently set to: <?php echo drupal_realpath("private://"); ?>
    <br />
    This can be changed <a target="_blank" href="<?php echo url('admin/config/media/file-system'); ?>">here</a>.
    <br /><br />
    Please choose the option that reflects your current situation:
    <br />
    - <a href="<?php echo url('admin/config/media/filebuilder_service/wizard/two_a'); ?>">My SFTP / FTP account does <b>not</b> point to my private://filebuilder_working_directory and I do not want it to / I don't know if it does</a>
    <br />
    - <a href="<?php echo url('admin/config/media/filebuilder_service/wizard/two_b'); ?>">I have configured / want to configure an SFTP / FTP account to point to private://filebuilder_working_directory</a>
  <?php endif; ?>
<?php elseif ($wizard_step == 2): ?>
  <h3>Wizard Step Two - FTP / SFTP Sync (continued)</h3>
  <?php if (!isset($_GET['e'])): ?>
    To ensure that your FileBuilder Service knows about your FTP / SFTP account, please perform the following steps:
  <?php else: ?>
    <b>Error: The requested file has NOT been uploaded to '<?php echo $filebuilder_private_path; ?>'<br />Please try again, performing the following steps:</b>
  <?php endif; ?>
  <br />
  <?php if ($ftp_path == TRUE): ?>
    If you need to configure your SFTP / FTP account to point to the directory '<?php echo $filebuilder_private_path; ?>', please do so now.
    <br />
  <?php endif; ?>
  <br />
  <ol>
    <li>Download <a href="<?php echo url('admin/config/media/filebuilder_service/wizard/target_file'); ?>" target="_blank">this file</a> and save it to your computer</li>
    <li>Open your FTP / SFTP client and connect to your FTP / SFTP account</li>
    <?php if ($ftp_path == FALSE): ?>
      <li>Navigate to '<?php echo $filebuilder_private_path; ?>/' in your FTP / SFTP client</li>  
    <?php endif; ?>
    <li>Upload the file you just downloaded</li>
    <?php 
      if ($ftp_path == TRUE) {
        $wizard_3_path = url('admin/config/media/filebuilder_service/wizard/three_b');
      }
      else {
        $wizard_3_path = url('admin/config/media/filebuilder_service/wizard/three_a');
      }
    ?>
    <li>Ensure the file exists at: '<?php echo $filebuilder_private_path; ?>/filebuilder.sync'</li>
    <li><a href="<?php echo $wizard_3_path; ?>">Press here to continue</a></li>
  </ol>
<?php elseif ($wizard_step == 3): ?>
  <h3>Wizard Step Three - Authentication Key Sync</h3>
  To ensure data security, FileBuilder requires a unique server specific key to be able to encrypt authentication details between FileBuilder and drupal.
  <br />
  This key must be downloaded, then imported into all instances of FileBuilder that are to connect to this drupal site.
  <br /><br />
  Upon first startup, FileBuilder will ask for the key file - later it can be added / modified in FileBuilder by navigating to "Configure", then "Authentication Key", and then "Attach New Authentication Key".
  <br />
  Once the keyfile has been imported into FileBuilder, in FileBuilder click the "Configure" button and set the URL to your drupal site and set your FTP / SFTP account settings.
  <br />
  <br />
  <?php echo l('Download Key File', 'filebuilder_service/export_key'); ?>
<?php endif; ?>
