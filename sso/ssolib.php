<?php

function rand_base64_key() {
  $key = base64_encode(pack('L6', mt_rand(), mt_rand(), mt_rand(),
                                  mt_rand(), mt_rand(), mt_rand()));
  return strtr($key, '+/=', '-_');
}

function getval($hash, $key) {
  return array_key_exists($key, $hash) ? $hash[$key]: '';
}

#
# Create a list of cookies for use in a 'Cookie:' header.
#
function cookies_list($cookies) {
  $str = '';

  $first = true;
  foreach ($cookies as $name => $value) {
    if ($first) $first = false;
    else $str .= '; ';

    $str .=  $name . '=' . $value;
  }

  return $str;
}

#
# Capture and parse cookies from an array of headers.
#
function extract_cookies($headers) {
  $cookies = array();

  foreach ($headers as $header) {
    if (!strncmp($header, 'Set-Cookie: ', 12)) {
      # knock off the header name and split on attributes
      $crumbs = explode('; ', substr($header, 12));

      # get the cookie name and value
      $tmp = explode('=', array_shift($crumbs));
      $name = trim($tmp[0]);
      $cookies[$name]['value'] = trim($tmp[1]);

      # get each attribute 
      foreach ($crumbs as $crumb) {
        $tmp = explode('=', $crumb);
        $cookies[$name][strtolower(trim($tmp[0]))] =
          sizeof($tmp) > 1 ? trim($tmp[1]) : null;
      }
    }
  }

  return $cookies;
}

#
# Write an array of cookies created by extract_cookies() as output.
#
function set_cookies($cookies) {
  foreach ($cookies as $name => $attr) {
    setrawcookie(
      $name,
      $attr['value'],
      array_key_exists('expires', $attr) ? strtotime($attr['expires']) : 0,
      $attr['path'],
      'www.test.nomic.net',
      false,
      array_key_exists('httponly', $attr)
    );
  }
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
      <li><a href="news.php">News</a></li>
      <li><a href="">Help</a></li>
    </ul>
  </div>

  <div id="content">
END;
}

function print_bottom() {
  print '</div>';
  virtual('/footer.shtml');
  print <<<END
</div>
</body>
</html>
END;
}

?>
