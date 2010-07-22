<?php
include_once "date.php";
$start=$_REQUEST['start'];
$end=$_REQUEST['end'];

$db = mysql_connect( "localhost");
mysql_select_db("test");

$touchFile = "historyCleanup";
if (file_exists($touchFile)) {
  $nextClean = filemtime($touchFile) + 3600*24;
  if (time() > $nextClean) {
    $now = getGmtTimeMillis();
    $expiration = $now - 30*24*3600*1000;
    mysql_query("delete from vassal_server_connections where time < '$expiration'");
  }
}
else {
  touch ($touchFile);
}

if ($start && $end) {
  $start = mysql_real_escape_string($start);
  $end = mysql_real_escape_string($end);

  $cur = mysql_query("select module_name,game_room, player_name,time from vassal_server_connections where time >= $start and time < $end order by time");
}
else {
  $cur = mysql_query("select module_name,game_room, player_name,time from vassal_server_connections order by time");
}

if ($cur) {
  while ($row = mysql_fetch_row( $cur )) {
    echo $row[0]."\t".$row[1]."\t".$row[2]."\t".$row[3]."\n";
  }
}
