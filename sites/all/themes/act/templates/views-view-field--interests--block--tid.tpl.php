<?php
  global $user;
  $term = taxonomy_term_load($output);
  //$uid = (arg(0) == 'user' && is_numeric(arg(1))) ? arg(1) : '';
  $uid = $user->uid;
  print l('<span></span>'.$term->name, "user/$uid/interest/remove/$term->tid/nojs/", array('attributes' => array('class' => array('use-ajax')),'html' => TRUE));
?>
