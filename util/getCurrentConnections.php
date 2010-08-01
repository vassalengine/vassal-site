<?php

header('Content-type: text/plain; charset=utf-8');

# connect to the SQL server
require_once(dirname(__FILE__) . '/config.php');

$dbh = mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD);
if (!$dbh) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysql_error());
}

if (!mysql_select_db(SQL_DB, $dbh)) {
  throw new ErrorException('Cannot select database: ' . mysql_error());
}

# get the set of rows having the most recent timestamp
$query = 'SELECT module_name, game_room, player_name FROM connections ' .
         'WHERE time = (SELECT MAX(time) FROM connections)';

# TODO: Determine whether this join is faster than the subquery
#$query = 'SELECT c1.module_name, c1.game_room, c1.player_name '
#         'FROM connections AS c1 LEFT OUTER JOIN connections AS c2 ON '
#         'c1.time < c2.time '
#         'WHERE c2.module_name IS NULL';

$r = mysql_query($query, $dbh);
if (!$r) {
  throw new ErrorException('SELECT failed: ' . mysql_error());
}

# header('Content-type: text/html; charset=utf-8');

while (($row = mysql_fetch_row($r))) {
  echo implode("\t", $row), "\n";
}

?>
