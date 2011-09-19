<?php

# This is a shim for phpBB's login so that we can capture the Set-Cookie
# headers it sends.

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($username)) {
  print 'Empty username.';
  exit;
} 

if (empty($password)) {
  print 'Empty password.';
  exit;
}

define('IN_PHPBB', true);
$phpbb_root_path = '../../forum/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
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
