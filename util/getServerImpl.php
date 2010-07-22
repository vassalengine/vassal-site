<?php 
$module=$_REQUEST['module'];
?>
type = node
<? if ($module == "Server Test") { ?>
nodeHost = test.nomic.net
<? } else { ?>
<? //nodeHost = 63.144.41.3 ?>
nodeHost = test.nomic.net
<? } ?>
nodePort = 5050
