<?php
#
# Tell the client his IP address.
#
header('Content-type: text/plain; charset=utf-8');
echo $_SERVER['REMOTE_ADDR'];
?>
