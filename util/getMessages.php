<?php

header('Content-type: text/plain; charset=utf-8');

# validate input
$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';

if (empty($module)) {
  throw new ErrorException('no module specified');
}

# connect to the SQL server
require_once(dirname(__FILE__) . '/vserver-config.php');

$dbh = mysqli_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
if (mysqli_connect_errno()) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysqli_connect_error());
}

# read messages for this module
$query = sprintf(
  'SELECT sender, UNIX_TIMESTAMP(time)*1000, content FROM messages ' .
  'WHERE module_name = "%s"',
  mysqli_real_escape_string($dbh, $module)
);

$r = mysqli_query($dbh, $query);
if (!$r) {
  throw new ErrorException('SELECT failed: ' . mysqli_error($dbh));
}

# send messages to the client
while (($row = mysqli_fetch_row($r))) {
  echo vsprintf("SENDER=%s&DATE=%s&CONTENT=%s\n", $row);
}

?>
