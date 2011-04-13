<?php

require_once('sso/AuthDB.php');
require_once('sso/UserDB.php');
require_once('sso/ssolib.php');
require_once('sso/EmailAddressValidator.php');

$title = 'Modify Account';

$key = isset($_COOKIE['VASSAL_login']) ? $_COOKIE['VASSAL_login'] : '';

try {
  if (empty($key)) {
    throw new ErrorException('No key.');
  }

  # check cookie
  $auth = new AuthDB();
  $username = $auth->user_for_cookie($key);

  # check whether this was a modification attempt
  if (empty($_POST)) {
    print_top($title);
    print_form();
    print_bottom();
    exit;
  }

  # sanitize the input
  $password = isset($_POST['password']) ? $_POST['password'] : '';
  $retype_password = isset($_POST['retype_password']) ?
                           $_POST['retype_password'] : '';

  $email = isset($_POST['email']) ? addslashes($_POST['email']) : '';
  $retype_email = isset($_POST['retype_email']) ?
                        addslashes($_POST['retype_email']) : '';
  $realname = isset($_POST['realname']) ? addslashes($_POST['realname']) : '';

  # check for password mismatch
  if ($password != $retype_password) {
    throw new ErrorException('Password mismatch.');
  }

  if (!empty($password)) {
    # reject password == username
    if ($password == $username) {
      throw new ErrorException('Password must differ from username.');
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

     # reject passwords with problematic characters
    if (preg_match('/[\']/', $password)) {
      throw new ErrorException('Password must not contain single quotes.');
    }
  }

  # check for email mismatch
  if ($email != $retype_email) {
    throw new ErrorException('Email mismatch.');
  }

  # build the attributes array
  $attr = array();

  if (!empty($password)) {
    $attr['userPassword'] = $password;
  }

  if (!empty($realname)) {
    $attr['cn'] = $realname;
  }

  if (empty($attr) && empty($email)) {
    throw new ErrorException('No changes.');
  }

  if (!empty($attr)) {
    # set new attributes in LDAP
    $user = new UserDB();
    $user->modify($username, $attr);
  }

  if (!empty($email)) {
    # check for bad email address
    $validator = new EmailAddressValidator;
    if (!$validator->check_email_address($email)) {
      throw new ErrorException('Bad email address.');
    }

    # build confirmation key
    $key = rand_base64_key();

    # store confirmation information in the database
    $query = sprintf(
      "INSERT INTO confirmemail
       (id, username, email)
       VALUES('%s', '%s', '%s')",
      mysql_real_escape_string($key),
      mysql_real_escape_string($username),
      mysql_real_escape_string($email)
    );

    $auth->write($query);

    # send confirmation email
    $subject = 'vassalengine.org email address confirmation';
    $message = <<<END
Someone claiming to be "$realname", probably you, from IP address {$_SERVER['REMOTE_ADDR']}, has attempted to associate the account "$username" at vassalengine.org with this email address.

To confirm this email address, simply open this link in your browser:

http://www.vassalengine.org/confirm_email.php?key=$key

If you do not wish to switch to this email address, please disregard this message. If you are not requesting to change the email address associated with this account, or you have any other questions, please contact webmaster@vassalengine.org.

END;

    $message = wordwrap($message, 70);
    $headers =
      "From: webmaster@vassalengine.org\r\n" .
      "Reply-To: confirm+$key@vassalengine.org\r\n";

    if (!mail($email, $subject, $message, $headers)) {
      throw new ErrorException('Failed to send confirmation email.');
    }
  }

  # success!
  print_top($title);
  print '<p>Your settings have been updated.</p>';
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
<form class="sso_form" action="modify.php" method="post">
  <fieldset>
    <legend>Modify Account</legend>
    <table>
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
        <td></td>
        <td><input type="submit" name="modify" id="modify" value="Modify account" /></td>
      </tr>
    </table>
  </fieldset>
</form>
END;
}

?>
