<?php

header('Content-type: text/plain; charset=utf-8');

$dir = '/var/www/html/site/util/motd_files';
$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';

$motdFile = realpath("$dir/$module");

# Make sure that the motd path does not take us above dir
if ($motdFile === false ||
    strncmp($motdFile, "$dir/", strlen($dir)+1) != 0 ||
    !file_exists($motdFile)) {
  $motdFile = "$dir/VASSAL";
}

readfile($motdFile);

?>
