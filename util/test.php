<?php
include_once "date.php";

$now = getGmtTimeMillis();

echo $now;

$now = $now  - 3600 * 8 * 1000;;
echo "  $now";

?>

