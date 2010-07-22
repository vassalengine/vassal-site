<?php
include_once "date.php";

$module = $_REQUEST['module'];
$content = stripslashes($_REQUEST['content']);
$sender = $_REQUEST['sender'];

if ($module) {
  $module = str_replace("/","%2F",$module);
  $now = getGmtTimeMillis();
  $msgFile = fopen("msgs/$module","a");
  $msg = "SENDER=$sender&DATE=$now&CONTENT=$content\n";
  fwrite($msgFile,$msg);
  fclose($msgFile);
}
?>
