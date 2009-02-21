<?php

# This is a shim for phpBB's logout so that we can capture the Set-Cookie
# headers it sends.

define('IN_PHPBB', true);
$phpbb_root_path = '../forum/';
$phpEx = 'php';
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_kill(false);

?>
