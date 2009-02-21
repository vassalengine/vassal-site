<?php 

require_once('sso/ssolib.php');

$title = 'Log out';

$returnto = getval($_GET, 'returnto');

# expire the cookie if we have one
$key = getval($_COOKIE, 'VASSAL_login');
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
  $url = 'http://www.test.nomic.net/wiki/api.php';
  $cookies += mediawiki_logout($url);

  # phpBB logout
  $url = 'http://www.test.nomic.net/phpbb_logout.php';
  $cookies += phpbb_logout($url);

  # Bugzilla logout
  $url = 'http://www.test.nomic.net/bugzilla/xmlrpc.cgi';
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


function mediawiki_logout($url) {
  $params = array(
    'format'     => 'php',
    'action'     => 'logout',
  );
 
  $request = http_build_query($params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n" .
                  'Cookie: ' . cookies_list($_COOKIE) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  return extract_cookies($http_response_header);
}

function phpBB_logout($url) {
  $opts = array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Cookie: ' . cookies_list($_COOKIE) . "\r\n"
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  return extract_cookies($http_response_header);
}

function bugzilla_logout($url) {
  $request = xmlrpc_encode_request('User.logout', null);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: text/xml\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n" .
                  'Cookie: ' . cookies_list($_COOKIE) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  return extract_cookies($http_response_header);
}

?>
