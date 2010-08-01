<?php
#
# Obsolete. Used only for Web Start.
# 
header('Content-type: text/plain; charset=utf-8');

if ($dh = opendir("../ws")) {
  while (($file = readdir($dh)) !== false) {
    if (strncmp($file, 'vassal-', 7) == 0) {
	    echo substr($file, 7, strlen($file)-12), "\n";
    }
  }
  closedir($dh);
}
?>
