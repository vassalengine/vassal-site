<?php
require_once('sso/AuthDB.php');
require_once('sso/UserDB.php');
require_once('sso/ssolib.php');

$title = 'VASSAL Login';

$returnto = getval($_GET, 'returnto');

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
  set_cookies($cookies);

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
