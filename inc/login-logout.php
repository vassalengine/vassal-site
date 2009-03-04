<div id="vassal-login">
<?php

if (isset($_COOKIE['VASSAL_login'])) {
  $key = $_COOKIE['VASSAL_login'];

  require_once(dirname(__FILE__) . '/../sso/AuthDB.php');
  
  try {
    $auth = new AuthDB();
    $username = $auth->user_for_cookie($key);
    echo '<a href="/logout.php?returnto=', $_SERVER['REQUEST_URI'], '">Log out</a>';
  }
  catch (ErrorException $e) {
    echo '<a href="/login.php?returnto=', $_SERVER['REQUEST_URI'], '">Log in</a>';
  } 
}   
else {
  echo '<a href="/login.php?returnto=', $_SERVER['REQUEST_URI'], '">Log in</a>';
} 

?>
</div>
