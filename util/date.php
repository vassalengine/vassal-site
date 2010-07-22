<?php
function getGmtTimeMillis() {
  $gmt_offset = 8;
  $now = (time() + 3600 * $gmt_offset)*1000;
  return $now;
}
?>
