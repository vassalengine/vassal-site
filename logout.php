<?php

require_once('sso/ssolib.php');

$title = 'Log out';

$key = $_COOKIE['VASSAL_login'];
if (!empty($key)) {

  require_once('sso/AuthDB.php');

  try {
    $auth = new AuthDB();
    $auth->expire_cookie($key);

    
  }
  catch (ErrorException $e) {
    print_top($title);
    warn($e->getMessage());
    print_bottom();
    exit;
  }
}

print_top($title);
print '<p>You are now logged out.</p>';
print_bottom();

?>
