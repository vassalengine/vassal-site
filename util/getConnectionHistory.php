<?php

header('Content-type: text/plain; charset=utf-8');

# validate input
$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : null;
$end = isset($_REQUEST['end']) ? $_REQUEST['end'] : null;

# either both start and end must be set, or neither
if ($start !== null || $end !== null) {
  
  if (!is_numeric($start) || $start != (int) $start) {
    throw new ErrorException('start is not an integer');
  }

  if (!is_numeric($end) || $end != (int) $end) {
    throw new ErrorException('end is not an integer');
  }

  # start, end are in milliseconds, timestamps are in seconds, so we convert
  $start = (int)($start/1000);
  $end = (int)($end/1000);

  if ($start < 0) {
    throw new ErrorException('start is negative');
  }

  if ($end <= $start) {
    throw new ErrorException('end <= start');
  }
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

# build the query
$query = 'SELECT module_name, game_room, player_name, UNIX_TIMESTAMP(time)*1000 FROM connections';

if ($start !== null) {
  # add the time interval, if we have one
  $query .= sprintf(
    ' WHERE time BETWEEN FROM_UNIXTIME(%s) AND FROM_UNIXTIME(%s)',
    $start,
    # BETWEEN wants [start,end], we use [start,end), so adjust end
    $end + 1
  );  
}

# get the rows and send them
$r = mysql_query($query, $dbh);
if (!$r) {
  throw new ErrorException('SELECT failed: ' . mysql_error());
}

while (($row = mysql_fetch_row($r))) {
  echo implode("\t", $row), "\n";
}

?>
