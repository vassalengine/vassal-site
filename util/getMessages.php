<?php
include_once "date.php";

$module=$_REQUEST['module'];
if ($module) {
  $module = str_replace("/","%2F",$module);
  cleanMessages($module);
  if (file_exists("msgs/$module")) {
    $msgFile = fopen("msgs/$module","r");
    fpassthru($msgFile);
  }
}

function cleanMessages(&$module) {
  $now = getGmtTimeMillis();
  $expiration = $now - 14*3600*24*1000;
  $filename = "msgs/$module";
  if (is_writeable($filename)) {
    $msgs = file($filename);
    $msgFile = fopen("msgs/$module","w");
    foreach ($msgs as $msg) {
      $dateToEnd = strstr($msg,"DATE=");
      $date = substr($dateToEnd,5,strpos($dateToEnd,"&")-5);
      if ($date >= $expiration) {
	fwrite($msgFile,$msg);
      }
    }
    fclose($msgFile);
  }
}
?>
