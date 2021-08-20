<?php

# bail if there's no input
if (!isset($_REQUEST['STATUS'])) {
  throw new ErrorException('No input');
}

# reject input from anywhere but our game server
if ($_SERVER['REMOTE_ADDR'] != '62.210.178.7') {
  throw new ErrorException('You are not our game server.');
}

$now = time();

# FIXME: what encoding for status? seems to contain garbage sometimes
# FIXME: used to stripslashes(), was this necessary?
$status = $_REQUEST['STATUS'];

# connect to the SQL server
require_once(dirname(__FILE__) . '/vserver-config.php');

$dbh = mysqli_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
if (mysqli_connect_errno()) {
  throw new ErrorException('Cannot connect to MySQL server: ' . mysqli_connect_error());
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
    mysqli_real_escape_string($dbh, $module),
    mysqli_real_escape_string($dbh, $game),
    mysqli_real_escape_string($dbh, $player),
    $now,
    $now
  );

  $r = mysqli_query($dbh, $query);
  if (!$r) {
    throw new ErrorException('INSERT failed: ' . mysqli_error($dbh));
  }
}

?>
