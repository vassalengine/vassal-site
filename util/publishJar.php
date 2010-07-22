<?php
$file=$_REQUEST['file'];
$version=$_REQUEST['version'];
$versionedFile = str_replace(".jar","__V$version.jar",$file);
$dir = "/home/vassal2/public_html/ws"; 
if (copy("$dir/$versionedFile","$dir/$file")) {
  echo "$versionedFile copied to $file";
}
?>
