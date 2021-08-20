<?php

header('Content-type: text/plain; charset=utf-8');

# connect to the SQL server
require_once(dirname(__FILE__) . '/vserver-config.php');

$dbh = mysqli_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
if (mysqli_connect_errno()) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysqli_connect_error());
}

# get the set of rows having the most recent timestamp
$query = 'SELECT module_name, game_room, player_name FROM connections ' .
         'WHERE time = (SELECT MAX(time) FROM connections)';

# TODO: Determine whether this join is faster than the subquery
#$query = 'SELECT c1.module_name, c1.game_room, c1.player_name '
#         'FROM connections AS c1 LEFT OUTER JOIN connections AS c2 ON '
#         'c1.time < c2.time '
#         'WHERE c2.module_name IS NULL';

$r = mysqli_query($dbh, $query);
if (!$r) {
  throw new ErrorException('SELECT failed: ' . mysqli_error($dbh));
}

# header('Content-type: text/html; charset=utf-8');

while (($row = mysqli_fetch_row($r))) {
  echo implode("\t", $row), "\n";
}

?>
