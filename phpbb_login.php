<?php

# This is a shim for phpBB's login so that we can capture the Set-Cookie
# headers it sends.

$username = addslashes($_POST['username']);
$password = addslashes($_POST['password']);

if (empty($username)) {
  print 'Empty username.';
  exit;
} 

if (empty($password)) {
  print 'Empty password.';
  exit;
}

define('IN_PHPBB', true);
$phpbb_root_path = '../forum/';
$phpEx = 'php';
require($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin(false);
$auth->acl($user->data);
$user->setup();

if (!$user->data['is_registered']) {
  $autologin = true;
  $viewonline = true;

  $result = $auth->login($username, $password, $autologin, $viewonline);

  if ($result['status'] != LOGIN_SUCCESS) {
    print $result['error_msg'];
    exit;
  }
}

print 1;

?>
