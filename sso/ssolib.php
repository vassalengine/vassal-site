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
# Login to MediaWiki.
#
function mediawiki_login($url, $username, $password) {
  $params = array(
    'format'     => 'php',
    'action'     => 'login',
    'lgname'     => $username,
    'lgpassword' => $password,
    'lgdomain'   => 'test'
  );

  $request = http_build_query($params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  $reply = unserialize($content);
  $reply = $reply['login'];

  if ($reply['result'] != 'Success') {
    if ($reply['result'] == 'Illegal') {
      throw new ErrorException('MediaWiki login failed: Invalid username.');
    }
    else if ($reply['result'] == 'NotExists') {
      throw new ErrorException('MediaWiki login failed: Invalid username.');
    }
    else if ($reply['result'] == 'WrongPass') {
      throw new ErrorException('MediaWiki login falied: Invalid password.');
    }
    else if ($reply['result'] == 'WrongPluginPass') {
      throw new ErrorException('MediaWiki login failed: Invalid password.');
    }
    else {
      throw new ErrorException('MediaWiki login failed: ' . $reply['result']);
    }
  }

  return extract_cookies($http_response_header);
}

#
# Logout of MediaWiki
#
function mediawiki_logout($url) {
  $params = array(
    'format'     => 'php',
    'action'     => 'logout',
  );

  $request = http_build_query($params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n" .
                  'Cookie: ' . cookies_list($_COOKIE) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  return extract_cookies($http_response_header);
}

#
# Login to phpBB.
#
function phpbb_login($url, $username, $password) {
  $params = array(
    'username' => $username,
    'password' => $password,
  );

  $request = http_build_query($params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  if ($content != '1') {
    throw new ErrorException('phpBB login failed.');
  }

  return extract_cookies($http_response_header);
}

#
# Logout of phpBB.
#
function phpBB_logout($url) {
  $opts = array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Cookie: ' . cookies_list($_COOKIE) . "\r\n"
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  return extract_cookies($http_response_header);
}

#
# Login to Bugzilla.
#
function bugzilla_login($url, $username, $password) {
  $params = array(
    'login'    => $username,
    'password' => $password
    #  'remember' => true
  );

  $request = xmlrpc_encode_request('User.login', $params);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: text/xml\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  $reply = xmlrpc_decode($content);
  if (xmlrpc_is_fault($reply)) {
    throw new ErrorException(
      $reply['faultString'] . ' (' . $reply['faultCode'] . ')');
  }

  return extract_cookies($http_response_header);
}

#
# Logout of Bugzilla.
#
function bugzilla_logout($url) {
  $request = xmlrpc_encode_request('User.logout', null);

  $opts = array(
    'http' => array(
      'method' => 'POST',
      'header' => "Content-type: text/xml\r\n" .
                  'Content-Length: ' . strlen($request) . "\r\n" .
                  'Cookie: ' . cookies_list($_COOKIE) . "\r\n",
      'content' => $request
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);

  return extract_cookies($http_response_header);
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
