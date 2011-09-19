<?php

# This is a shim for phpBB's logout so that we can capture the Set-Cookie
# headers it sends.

define('IN_PHPBB', true);
$phpbb_root_path = '../../forum/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin(false);
$auth->acl($user->data);
$user->setup();

if ($user->data['is_registered']) {
  $user->session_kill();
  $user->session_begin(false);
}

?>
