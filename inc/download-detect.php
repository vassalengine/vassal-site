<?php

# NB: $version should already be defined before calling this script

$base_url = "http://sourceforge.net/projects/vassalengine/files/VASSAL-current/VASSAL-$version";
 
# Get the user's browser
$useragent = isset($_SERVER['HTTP_USER_AGENT']) ?
  $_SERVER['HTTP_USER_AGENT'] : '';

if (strstr($useragent, 'Win')) {
  $download_os = ' for Windows';
  $download_url = "$base_url/VASSAL-$version-windows.exe/download";
}
else if (strstr($useragent, 'Linux')) {
  $download_os = ' for Linux';
  $download_url = "$base_url/VASSAL-$version-linux.tar.bz2/download";
}
else if (strstr($useragent, 'Mac')) {
  $download_os = ' for Mac OS X';
  $download_url = "$base_url/VASSAL-$version-macosx.dmg/download";
}
else {
  $download_os = '';
  $download_url = "$base_url/VASSAL-$version-other.zip/download";
}

?>
