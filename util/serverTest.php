<?php 
$module=$_REQUEST['module'];
?>
type = node
nodeHost = 63.144.41.3
<? if ($module == "Server Test") { ?>
nodePort = 5060
<? } else { ?>
nodePort = 5050
<? } ?>
