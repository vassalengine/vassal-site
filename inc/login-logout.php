<div id="vassal-login">
<?php

$returnto = urlencode($_SERVER['REQUEST_URI']);

if (isset($_COOKIE['VASSAL_login'])) {
  $key = $_COOKIE['VASSAL_login'];

  require_once(dirname(__FILE__) . '/../sso/AuthDB.php');
  
  try {
    $auth = new AuthDB();
    $username = $auth->user_for_cookie($key);
    $op = 'out';
  }
  catch (ErrorException $e) {
    $op = 'in';
  } 
}   
else {
  $op = 'in';
} 

echo "<a href=\"/log$op.php?returnto=$returnto\">Log $op</a>";

?>
</div>
