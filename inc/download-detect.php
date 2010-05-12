<?php

# NB: $version should already be defined before calling this script

$base_url = 'http://downloads.sourceforge.net/vassalengine';
 
# Get the user's browser
$useragent = $_SERVER['HTTP_USER_AGENT'];

if (strstr($useragent, 'Win')) {
  $download_os = ' for Windows';
  $download_url = "$base_url/VASSAL-$version-windows.exe";
}
else if (strstr($useragent, 'Linux')) {
  $download_os = ' for Linux';
  $download_url = "$base_url/VASSAL-$version-linux.zip";
}
else if (strstr($useragent, 'Mac')) {
  $download_os = ' for Mac OS X';
  $download_url = "$base_url/VASSAL-$version-macosx.dmg";
}
else {
  $download_os = '';
  $download_url = "$base_url/VASSAL-$version-other.zip";
}

?>
