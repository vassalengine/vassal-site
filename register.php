<?php
require_once('sso/AuthDB.php');
require_once('sso/UserDB.php');
require_once('sso/config.php');
require_once('sso/ssolib.php');
require_once('sso/recaptchalib.php');
require_once('sso/EmailAddressValidator.php');

$title = 'Create Account';

# check whether this was a registration attempt
if (empty($_POST)) {
  print_top($title);
  print_form();
  print_bottom();
  exit;
}

# FIXME: Don't clear all the fields on error. This is frustrating
# for the user.

# sanitize the input
$username = isset($_POST['username']) ? addslashes($_POST['username']) : '';
$password = isset($_POST['password']) ? addslashes($_POST['password']) : '';
$retype_password = isset($_POST['retype_password']) ?
                         addslashes($_POST['retype_password']) : '';
$email = isset($_POST['email']) ? addslashes($_POST['email']) : '';
$retype_email = isset($_POST['retype_email']) ?
                      addslashes($_POST['retype_email']) : '';
$realname = isset($_POST['realname']) ? addslashes($_POST['realname']) : '';

try {
  # check for blank username
  if (empty($username)) {
    throw new ErrorException('Invalid username.');
  }

  # reject ridiculously long usernames
  if (strlen($username) > 32) {
    throw new ErrorException(
      'Username must be no more than 32 characters long.');
  }

  validate_password($password, $retype_password);

  # check for blank email
  if (empty($email)) {
    throw new ErrorException('Blank email.');
  }

  # check for email mismatch
  if ($email != $retype_email) {
    throw new ErrorException('Email mismatch.');
  }

  # check for bad email address
  $validator = new EmailAddressValidator;
  if (!$validator->check_email_address($email)) {
    throw new ErrorException('Bad email address.');
  }

  # check for blank realname
  if (empty($realname)) {
    throw new ErrorException('Blank realname.');
  }

  # reject ridiculously long realname 
  if (strlen($realname) > 64) {
    throw new ErrorException(
      'Real name must be no more than 64 characters long.');
  }

  # check that the captcha fields were filled in
  if (!isset($_POST['recaptcha_challenge_field'])) {
    throw new ErrorException('No reCAPTCHA challenge.');
  }

  if (!isset($_POST['recaptcha_response_field'])) {
    throw new ErrorException('No reCAPTCHA response.');
  }

# FIXME: Check the CAPTCHA POST vars to ensure that we're not getting 
# some ridiculously long crap.

  # check the CAPTCHA
  $resp = recaptcha_check_answer(
    RECAPTCHA_PRIVATE_KEY,
    $_SERVER['REMOTE_ADDR'],
    $_POST['recaptcha_challenge_field'],
    $_POST['recaptcha_response_field']
  );

  if (!$resp->is_valid) {
    throw new ErrorException(
      "The reCAPTCHA wasn't entered correctly. Go back and try it again. " .
      '(reCAPTCHA said: ' . $resp->error . ')');
  }

  # check that the username is not already taken
  $user = new UserDB();
  
  if ($user->exists($username)) {
    throw new ErrorException('The account "' . $username . '" already exists.');
  }

  # build confirmation key
  $key = rand_base64_key();

  # store confirmation information in the database
  $auth = new AuthDB();

  $query = sprintf(
    "INSERT INTO pending
      (id, username, password, email, realname)
      VALUES('%s', '%s', '%s', '%s', '%s')",
    mysql_real_escape_string($key),
    mysql_real_escape_string($username),
    mysql_real_escape_string($password),
    mysql_real_escape_string($email),
    mysql_real_escape_string($realname)
  );

  $auth->write($query);

  # send confirmation email
  $subject = 'vassalengine.org email address confirmation';
  $message = <<<END
Someone claiming to be "$realname", probably you, from IP address {$_SERVER['REMOTE_ADDR']}, has attempted to register the account "$username" with this email address at vassalengine.org.

To active this account, simply reply to this message, or open this link in your browser:

http://www.test2.nomic.net/confirm.php?key=$key

If you do not wish to activate this account, please disregard this message. If you think your email address is being maliciously associated with this account, or you have any other questions, please send them to webmaster@test2.nomic.net.

END;

  $message = wordwrap($message, 70);
  $headers =
    "From: webmaster@test2.nomic.net\r\n" .
    "Reply-To: confirm+$key@test2.nomic.net\r\n";

  if (!mail($email, $subject, $message, $headers)) {
    throw new ErrorException('Failed to send confirmation email.');
  }

  # success!
  print_top($title);
  print '<p>A confirmation email has been sent.
         Reply to it to activate your account.</p>'; 
  print_bottom();
  exit;
}
catch (ErrorException $e) {
  print_top($title);
  warn($e->getMessage());
  print_form();
  print_bottom();
  exit;
}

function print_form() {
  print <<<END
<script type="text/javascript">
<!--
  var RecaptchaOptions = { theme : 'white' };
-->
</script>
<form class="sso_form" action="register.php" method="post">
  <fieldset>
    <legend>Create an Account</legend>
    <p>Already have an account? <a href="login.php">Log in</a>.</p>
    <table>
      <tr>
        <th><label for="username">Username:</label></th>
        <td><input type="text" id="username" name="username" size="20"/></td>
      </tr>
      <tr>
        <th><label for="password">Password:</label></th>
        <td><input type="password" id="password" name="password" size="20"/></td
>
      </tr>
      <tr>
        <th><label for="retype_password">Retype password:</label></th>
        <td><input type="password" id="retype_password" name="retype_password" size="20"/></td
>
      </tr>
      <tr>
        <th><label for="email">Email address:</label></th>
        <td><input type="text" id="email" name="email" size="20"/></td
>
      </tr>
      <tr>
        <th><label for="retype_email">Retype email address:</label></th>
        <td><input type="text" id="retype_email" name="retype_email" size="20"/></td>
      </tr>
      <tr>
        <th><label for="realname">Real name:</label></th>
        <td><input type="text" id="realname" name="realname" size="20"/></td>
      </tr>
      <tr>
        <th><label>Type some words:</label></th>
      </tr>
      <tr>
        <td colspan="2" align="center">
END;

  echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY);

print <<<END
        </td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" name="create" id="create" value="Create account" /></td>
      </tr>
    </table>
  </fieldset>
</form>
END;
}

?>
