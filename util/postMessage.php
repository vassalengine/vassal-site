<?php

$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';
$content = isset($_REQUEST['content']) ? $_REQUEST['content'] : '';
$sender = isset($_REQUEST['sender']) ? $_REQUEST['sender'] : '';

if (empty($module)) {
  throw new ErrorException('no module specified');
}

if (empty($content)) {
  throw new ErrorException('no content specified');
}

if (empty($sender)) {
  throw new ErrorException('no sender specified');
}

# FIXME: used to stripslashes() on content, was this necessary?

# connect to the SQL server
require_once(dirname(__FILE__) . '/vserver-config.php');

$dbh = mysqli_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
if (mysqli_connect_errno()) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysqli_connect_error());
}

# store the message
$query = sprintf(
  'INSERT INTO messages (module_name, sender, content) ' .
  'VALUES ("%s", "%s", "%s")',
  mysqli_real_escape_string($dbh, $module),
  mysqli_real_escape_string($dbh, $sender),
  mysqli_real_escape_string($dbh, $content)
);

$r = mysqli_query($dbh, $query);
if (!$r) {
  throw new ErrorException('INSERT failed: ' . mysqli_error($dbh));
}

?>
