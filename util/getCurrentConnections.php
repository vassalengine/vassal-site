<?php
$connections = fopen("connections/connectionStatus","r");
fpassthru($connections);
fclose($connections);
?>
