<?php
include_once "date.php";

$now = getGmtTimeMillis() - 3600 * 8 * 1000;

$STATUS=stripSlashes($_REQUEST['STATUS']);

$connections = fopen("connections/connectionStatus","w");
fwrite($connections,$STATUS);
fclose($connections);

$db = mysql_connect( "localhost");
mysql_select_db("test");

$line = strtok($STATUS,"\n");
while ($line) {
  list ($module, $game, $player) = split("\t",$line);
  $module = mysql_real_escape_string($module);
  $game = mysql_real_escape_string($game);
  $player = mysql_real_escape_string($player);

  mysql_query("DELETE FROM vassal_server_connections WHERE module_name = '$module' AND game_room = '$game' AND player_name = '$player'");
  mysql_query("INSERT INTO vassal_server_connections (module_name, game_room, player_name, time) values ('$module', '$game', '$player', '$now')");
  $line = strtok("\n");
}
?>
