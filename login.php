<?php
require_once('sso/AuthDB.php');
require_once('sso/UserDB.php');
require_once('sso/ssolib.php');

$title = 'VASSAL Login';

$returnto = isset($_GET['returnto']) ? urldecode($_GET['returnto']) : '';

# check whether this was a login attempt
if (empty($_POST)) {
  print_top($title);
  print_form($returnto);
  print_bottom();
  exit;
}

# sanitize the input
$username = isset($_POST['username']) ? addslashes($_POST['username']) : '';
# NB: password does NOT need to be sanitized, it's never used in a query
$password = isset($_POST['password']) ? $_POST['password'] : '';

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

  $slpassword = addslashes($password);

  # MediaWiki login
  $url = 'http://localhost/mediawiki/api.php';
  $cookies += mediawiki_login($url, $username, $password);

  # phpBB login
  $url = 'http://localhost/phpbb_login.php';
  $cookies += phpbb_login($url, $username, $slpassword);

  # Bugzilla login
  $url = 'http://www.vassalengine.org/tracker/xmlrpc.cgi';
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
    'www.vassalengine.org',
    false,
    true
  );

  # return to front page
  # * by default; 
  # * if the returnto is the logout page, since returning there would
  #   immediately log us out
  # * if the returnto is the account confirmation page, since going
  #   back there makes no sense
  if (empty($returnto) ||
      strpos($returnto, 'logout.php')   === 0 ||
      strpos($returnto, '/logout.php')  === 0 || 
      strpos($returnto, 'confirm.php')  === 0 || 
      strpos($returnto, '/confirm.php') === 0) {
    $returnto = '/index.php';
  }

  # go back where we came from
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
  if (!empty($returnto)) $action .= '?returnto=' . urlencode($returnto);

  print <<<END
<form class="sso_form" action="$action" method="post">
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
