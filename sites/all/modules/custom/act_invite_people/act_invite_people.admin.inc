<?php
/*
 * Send mail message setting form 
 */
 
 function admin_send_mail_template_setting(){
	 $form = array();
   $token_types = array('user' => 'user');
   $form['invite_sendmail'] = array(
     '#type' => 'fieldset',
     '#description' => t("Invitation message setting by administrator."),
   );
  
  $form['invite_sendmail']['invite_subject'] = array(
    '#type' => 'textfield',
    '#title' => t('Enter your subject here'),
    '#default_value' => variable_get('invite_subject', 'Invitation Subject'),
    '#description' => t("Please enter invitation subject"),
    '#required' => TRUE,
  );
  
  $form['invite_sendmail']['invite_message'] = array(
    '#type' => 'textarea',
    '#title' => t('Enter your text here'),
    '#default_value' => variable_get('invite_message', 'Invitation Message'),
    '#description' => t("Please enter invitation message"),
    '#required' => TRUE,
  );
  
  if (module_exists('token')) {
    $form['invite_sendmail']['token_tree'] = array(
      '#theme' => 'token_tree',
      '#token_types' => $token_types,
    );
  }
  
  return system_settings_form($form);
}
