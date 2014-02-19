<h3>Sync Wizard</h3>
This wizard will guide you through the steps of getting FileBuilder synced with Filedepot. 
 <br />
<a href="<?php echo url('admin/config/media/filebuilder_service/wizard/one'); ?>">Run Sync Wizard</a>

<br />
<br />
<h3>Manage Authentication Key</h3>
This key is used to encrypt authentication data between the desktop client and this service. 
<br />
<br />
<?php
  if ($key !== NULL) {
    echo $export_key_link . ' | ';
  }
  else {
    echo "No key set yet (required to allow client communication):<br />";
  }
  
  echo $generate_key_link;
  
?>
 
