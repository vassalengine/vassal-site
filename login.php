<?php
require_once('sso/AuthDB.php');
require_once('sso/UserDB.php');
require_once('sso/ssolib.php');

$title = 'VASSAL Login';

$returnto = $_GET['returnto'];

# check whether this was a login attempt
if (empty($_POST)) {
  print_top($title);
  print_form($returnto);
  print_bottom();
  exit;
}

# sanitize the input
$username = addslashes($_POST['username']);
$password = addslashes($_POST['password']);

try {
  # check for blank username
  if (empty($username)) {
    throw new ErrorException('Invalid username.');
  }

  # check for blank password
  if (empty($password)) {
    throw new ErrorException('Blank password.');
  }

  # authenticate with LDAP server
  $user = new UserDB();
  $user->auth($username, $password);

  $cookies = array();

  # MediaWiki login
  $url = 'http://www.test.nomic.net/wiki/api.php';
  $cookies += mediawiki_login($url, $username, $password);

  # phpBB login
  $url = 'http://www.test.nomic.net/phpbb_login.php';
  $cookies += phpbb_login($url, $username, $password);

  # Bugzilla login
  $url = 'http://www.test.nomic.net/bugzilla/xmlrpc.cgi';
  $cookies += bugzilla_login($url, $username, $password);

  # write out the cookies captured from the logins 
  put_cookies($cookies);

  # FIXME: loop in case we have a cookie collision

  # set our login cookie
  $key = rand_base64_key();
  $expires = time() + (60 * 60 * 24 * 30);

  $auth = new AuthDB();
  $auth->create_cookie($username, $key, $expires);

  setrawcookie(
    'VASSAL_login',
    $key,
    $expires,
    '/',
    'www.test.nomic.net',
    false,
    true
  );

  # go back where we came from
  if (empty($returnto)) $returnto = '/index.php';
  header("Location: $returnto");
  exit;
}
catch (ErrorException $e) {
  print_top($title);
  warn($e->getMessage());
  print_form($returnto);
  print_bottom();
  exit;
}

#
# Login to MediaWiki.
#
function mediawiki_login($url, $username, $password) {
  $params = array(
    'format'     => 'php',
    'action'     => 'login',
    'lgname'     => $username,
    'lgpassword' => $password,
    'lgdomain'   => 'test'
  );
 
  $request = http_build_query($params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  $reply = unserialize($content);
  $reply = $reply['login'];

  if ($reply['result'] != 'Success') {
    if ($reply['result'] == 'Illegal') {
      throw new ErrorException('MediaWiki login failed: Invalid username.');
    }
    else if ($reply['result'] == 'NotExists') {
      throw new ErrorException('MediaWiki login failed: Invalid username.');
    }
    else if ($reply['result'] == 'WrongPass') {
      throw new ErrorException('MediaWiki login falied: Invalid password.');
    }
    else if ($reply['result'] == 'WrongPluginPass') {
      throw new ErrorException('MediaWiki login failed: Invalid password.');
    }
    else {
      throw new ErrorException('MediaWiki login failed: ' . $reply['result']);
    }
  }

  return get_cookies($http_response_header);
}

#
# Login to phpBB.
#
function phpbb_login($url, $username, $password) {
  $params = array(
    'username' => $username,
    'password' => $password,
  );
 
  $request = http_build_query($params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  if ($content != '1') {
    throw new ErrorException('phpBB login failed.');
  }

  return get_cookies($http_response_header);
}

#
# Login to Bugzilla.
#
function bugzilla_login($url, $username, $password) {
  $params = array(
    'login'    => $username,
    'password' => $password
    #  'remember' => true
  );

  $request = xmlrpc_encode_request('User.login', $params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: text/xml\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  $reply = xmlrpc_decode($content);
  if (xmlrpc_is_fault($reply)) {
    throw new ErrorException(
      $reply['faultString'] . ' (' . $reply['faultCode'] . ')');
  }

  return get_cookies($http_response_header);
}

#
# Capture and parse cookies from an array of headers.
#
function get_cookies($headers) {
  $cookies = array();

  foreach ($headers as $header) {
    if (!strncmp($header, 'Set-Cookie: ', 12)) {
      # knock off the header name and split on attributes
      $crumbs = explode('; ', substr($header, 12));
      
      # get the cookie name and value
      $tmp = explode('=', array_shift($crumbs));
      $name = trim($tmp[0]);
      $cookies[$name]['value'] = trim($tmp[1]);
 
      # get each attribute 
      foreach ($crumbs as $crumb) {
        $tmp = explode('=', $crumb);
        $cookies[$name][strtolower(trim($tmp[0]))] =
          sizeof($tmp) > 1 ? trim($tmp[1]) : null;
      }
    }
  }

  return $cookies;
}

#
# Write an array of cookies created by get_cookies() as output.
#
function put_cookies($cookies) {
  foreach ($cookies as $name => $attr) {
    setrawcookie(
      $name,
      $attr['value'],
      array_key_exists('expires', $attr) ? strtotime($attr['expires']) : 0,
      $attr['path'],
      'www.test.nomic.net',
      false,
      array_key_exists('httponly', $attr)
    );
  }
}



function print_form($returnto) {
  $action = 'login.php';
  if (!empty($returnto)) $action .= "?returnto=$returnto";

  print <<<END
<form class="login_form" action="$action" method="post">
  <fieldset>
    <legend>Login</legend>
    <p>Don't have an account? <a href="register.php">Create an account</a>.</p>
    <table>
      <tr>
        <th><label for="username">Username:</label></th>
        <td><input type="text" id="username" name="username" size="20"/></td>
      </tr>
      <tr>
        <th><label for="password">Password:</label></th>
        <td><input type="password" id="password" name="password" size="20"/></td>
      </tr>
      <tr>
        <td></td>
        <td><a href="sendpw.php">I forgot my password!</a></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" name="login" id="login" value="Log in" /></td>
      </tr>
    </table>
  </fieldset>
</form>
END;
}

?>
