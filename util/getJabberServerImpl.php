<?php 
$module=$_REQUEST['module'];
?>
type = jabber
<? if ($module == "Server Test") { ?>
jabberHost = test.nomic.net
<? } else { ?>
<? //nodeHost = 63.144.41.3 ?>
jabberHost = test.nomic.net
<? } ?>
jabberPort = 5222
