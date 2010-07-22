<?php
$module=$_REQUEST['module'];
$dir = "motd_files/";
if (file_exists("$dir/$module")) {
  $motdFile = "$dir/$module";  
}
else {
  $motdFile = "$dir/VASSAL";
}
$fp = fopen($motdFile,"r");
fpassthru($fp);
fclose($fp);
?>