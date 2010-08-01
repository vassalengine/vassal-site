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
require_once(dirname(__FILE__) . '/config.php');

$dbh = mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD);
if (!$dbh) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysql_error());
}

if (!mysql_select_db(SQL_DB, $dbh)) {
  throw new ErrorException('Cannot select database: ' . mysql_error());
}

# store the message
$query = sprintf(
  'INSERT INTO messages (module_name, sender, content) ' .
  'VALUES ("%s", "%s", "%s")',
  mysql_real_escape_string($module),
  mysql_real_escape_string($sender),
  mysql_real_escape_string($content)
);

$r = mysql_query($query, $dbh);
if (!$r) {
  throw new ErrorException('INSERT failed: ' . mysql_error());
}

?>
