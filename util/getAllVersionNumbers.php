<?php 
if ($dh = opendir("../ws")) {
    while (($file = readdir($dh)) !== false) {
        if (strstr($file,"vassal-")) {
	   $version = substr($file,7,$versions.strrchr(".jnlp",0)-5);
//           $version = substr($file,7,3);
           echo "$version\n";
        }
    }
    closedir($dh);
   }
?>