<?php

header('Content-type: text/plain; charset=utf-8');

# validate input
$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';

if (empty($module)) {
  throw new ErrorException('no module specified');
}

# connect to the SQL server
require_once(dirname(__FILE__) . '/config.php');

$dbh = mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD);
if (!$dbh) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysql_error());
}

if (!mysql_select_db(SQL_DB, $dbh)) {
  throw new ErrorException('Cannot select database: ' . mysql_error());
}

# read messages for this module
$query = sprintf(
  'SELECT sender, UNIX_TIMESTAMP(time)*1000, content FROM messages ' .
  'WHERE module_name = "%s"',
  mysql_real_escape_string($module)
);

$r = mysql_query($query, $dbh);
if (!$r) {
  throw new ErrorException('SELECT failed: ' . mysql_error());
}

# send messages to the client
while (($row = mysql_fetch_row($r))) {
  echo vsprintf("SENDER=%s&DATE=%s&CONTENT=%s\n", $row);
}

?>
