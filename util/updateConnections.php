<?php

# bail if there's no input
if (!isset($_REQUEST['STATUS'])) {
  exit;
}

$now = time();

# FIXME: what encoding for status? seems to contain garbage sometimes
# FIXME: used to stripslashes(), was this necessary?
$status = $_REQUEST['STATUS'];

#$connections = fopen('connections/connectionStatus','w');
#fwrite($connections,$STATUS);
#fclose($connections);

# connect to the SQL server
require_once(dirname(__FILE__) . '/config.php');

$dbh = mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD);
if (!$dbh) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysql_error());
}

if (!mysql_select_db(SQL_DB, $dbh)) {
  throw new ErrorException('Cannot select database: ' . mysql_error());
}

# parse the status data
for ($line = strtok($status, "\n"); $line; $line = strtok("\n")) {
  list($module, $game, $player) = explode("\t", $line, 3);

  if (strpos($player, "\t") !== false) {
    throw new ErrorException('Status line contains junk: ' . $line);
  }

  # map module-room-player triples to the current time
  $query = sprintf(
    'INSERT INTO connections (module_name, game_room, player_name, time) ' .
    'VALUES ("%s", "%s", "%s", FROM_UNIXTIME(%d)) ' .
    'ON DUPLICATE KEY UPDATE time = FROM_UNIXTIME(%d)',
    mysql_real_escape_string($module),
    mysql_real_escape_string($game),
    mysql_real_escape_string($player),
    $now,
    $now
  );

  $r = mysql_query($query, $dbh);
  if (!$r) {
    throw new ErrorException('Replace failed: ' . mysql_error());
  }
}

?>
