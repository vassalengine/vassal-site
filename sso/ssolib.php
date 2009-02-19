<?php

function rand_base64_key() {
  $key = base64_encode(pack('L6', mt_rand(), mt_rand(), mt_rand(),
                                  mt_rand(), mt_rand(), mt_rand()));
  return strtr($key, '+/=', '-_');
}

function warn($err) {
  print '<div class="errorbox"><h2>Error:</h2>' . $err . '</div>';
}

function print_top($title) {
  print <<<END
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="style.css"/>
  <link rel="icon" type="image/png" href="VASSAL.png"/>
  <title>$title</title>
</head>
<body>
<div id="vassal-page">
  <div id="vassal-header">
    <div id="vassal-logo">
      <a href="/index.php"><img src="images/header.png"/></a>
    </div>
  </div>
  <div id="vassal-navigation">
    <ul id="vassal-nav-list">
      <li><a href="download.html">Download</a></li>
      <li><a href="">Modules</a></li>
      <li><a href="">FAQ</a></li>
      <li><a href="">Documentation</a></li>
      <li><a href="forum/">Forum</a></li>
      <li><a href="">News</a></li>
      <li><a href="">Help</a></li>
    </ul>
  </div>

  <div id="content">
END;
}

function print_bottom() {
  print <<<END
  </div>
  <div id="vassal-footer">
    <ul id="vassal-footer-list">
      <li><a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=90612&amp;type=2" width="125" height="37" alt="SourceForge.net Logo" /></a></li>
      <li><a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml11" alt="Valid XHTML 1.1" height="31" width="88" /></a></li>
      <li><a href="http://jigsaw.w3.org/css-validator/"><img style="width: 88px; height: 31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!" /></a></li>
    </ul>
  </div> 
</div>
</body>
</html>
END;
}

?>
