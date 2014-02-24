<?php
global $user;
print $output;
 if($user->uid == 0 && !empty($output)){ ?>
  <div class="anonymousaddtocart"> 
<?php 
   $login = l(t('login'),'user/login', array('query' => drupal_get_destination(),'html' => TRUE,));
   $register = l(t('register'),'user/register', array('query' => drupal_get_destination(),'html' => TRUE,));
   print $login_reg = t("Please !login or !register to register for this event.", array('!login' => $login, '!register' => $register));
 ?>
  </div>
<?php } ?>
