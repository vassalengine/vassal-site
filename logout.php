<?php 

require_once('sso/ssolib.php');

$title = 'Log out';

$returnto = isset($_GET['returnto']) ? urldecode($_GET['returnto']) : '';

# expire the cookie if we have one
$key = isset($_COOKIE['VASSAL_login']) ? $_COOKIE['VASSAL_login'] : '';
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

try {
  $cookies = array();

  # MediaWiki logout
  $url = 'http://www.test.nomic.net/mediawiki/api.php';
  $cookies += mediawiki_logout($url);

  # phpBB logout
  $url = 'http://www.test.nomic.net/phpbb_logout.php';
  $cookies += phpbb_logout($url);

  # Bugzilla logout
  $url = 'http://www.test.nomic.net/tracker/xmlrpc.cgi';
  $cookies += bugzilla_logout($url);

  set_cookies($cookies);  
}
catch (ErrorException $e) {
  print_top($title);
  warn($e->getMessage());
  print_bottom();
  exit;
}

# go back where we came from, if asked
if (!empty($returnto)) {
  header("Location: $returnto");
}
else {
  print_top($title);
  print '<p>You are now logged out.</p>';
  print_bottom();
}

?>
