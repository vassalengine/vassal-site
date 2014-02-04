<?php
require_once('sso/ssolib.php');

$title = 'Reset Password';

# check whether this is an inital attempt to reset
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  # sanitize the input
  $key = isset($_GET['key']) ? addslashes($_GET['key']) : ''; 

  if (empty($key)) {
    print_top($title);
    warn('No key.');
    print_bottom();
    exit;
  }

  print_top($title);
  print_form($key);
  print_bottom();
  exit;
}

# check whether this is input from the reset form
if (empty($_POST)) {
  print_top($title);
  warn('No key.');
  print_bottom();
  exit;
}

# sanitize the input
$password = isset($_POST['password']) ? $_POST['password'] : '';
$retype_password = isset($_POST['retype_password']) ?
                         $_POST['retype_password'] : '';

# check for blank key
if (!isset($_POST['key'])) {
  print_top($title);
  warn('No key.');
  print_bottom();
  exit;
}

# ensure that key is set because we use it in the catch below
$key = $_POST['key']

try {
  # check for blank password
  if (empty($password)) {
    throw new ErrorException('Blank password.');
  }

  # check for password mismatch
  if ($password != $retype_password) {
    throw new ErrorException('Password mismatch.');
  }

  $pwlen = strlen($password);

  # reject ridiculously short passwords
  if ($pwlen < 6) {
    throw new ErrorException('Password must be at least 6 characters long.');
  }

  # reject ridiculously long passwords
  if ($pwlen > 128) {
    throw new ErrorException(
      'Password must be no more than 128 characters long.');
  }

  require_once('sso/AuthDB.php');
  require_once('sso/UserDB.php');

  # get data for key from the registration database
  $auth = new AuthDB();

  $key = mysql_real_escape_string($key);

  $query = sprintf(
    "SELECT username FROM resetpw WHERE id='%s'",
    $key
  );

  $row = $auth->read($query);
  if (!$row) {
    throw new ErrorException('No rows.');
  }

  extract($row);

  # set new password in LDAP
  $user = new UserDB();
  $user->modify($username, array('userPassword' => $password));

  # remove row from the registration database
  $query = sprintf(
    "DELETE FROM pending WHERE id='%s'",
    $key
  );

  $auth->write($query);

  # success!
  print_top($title);
  print '<p>Your password has been reset.</p>';
  print_bottom();
  exit;
}
catch (ErrorException $e) {
  print_top($title);
  warn($e->getMessage());
  print_form($key);
  print_bottom();
  exit;
}


# FIXME: should redirect to front page after some seconds:
# <meta http-equiv="refresh" content="5;URL=index.html"/>


function print_form($key) {
  print <<<END
<form class="sso_form" action="resetpw.php" method="post">
  <fieldset>
    <legend>Reset Password</legend>
    <input type="hidden" id="key" name="key" value="$key"/>
    <table>
      <tr>
        <th><label for="password">Password:</label></th>
        <td><input type="password" id="password" name="password" size="20"/></td>
      </tr>
      <tr>
        <th><label for="retype_password">Retype password:</label></th>
        <td><input type="password" id="retype_password" name="retype_password" size="20"/></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" name="resetpw" id="resetpw" value="Reset password"/></td>
      </tr>
    </table>
  </fieldset>
</form>
END;
}

?>
