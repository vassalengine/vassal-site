<?php

define('MIME_FORM', 'application/x-www-form-urlencoded');
define('MIME_XML',  'text/xml');

function rand_base64_key() {
  $key = base64_encode(pack('L6', mt_rand(), mt_rand(), mt_rand(),
                                  mt_rand(), mt_rand(), mt_rand()));
  return strtr($key, '+/=', '-_');
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

  extract(do_http_post($url, MIME_FORM, $request));
  $reply = unserialize($content);
  $reply = $reply['login'];

  $set_cookies = array();

  # As of MW 1.15.3, we must confirm the returned token
  if ($reply['result'] == 'NeedToken') {
    $params['lgtoken'] = $reply['token'];
    $request = http_build_query($params);

    $set_cookies = extract_cookies($header);

    # convert extract_cookies format to $_COOKIES format for sending
    $cookies = array();
    foreach ($set_cookies as $name => $attr) {
      $cookies[$name] = $attr['value'];
    }

    extract(do_http_post($url, MIME_FORM, $request, $cookies));
    $reply = unserialize($content);
    $reply = $reply['login'];
  }

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

  return array_merge($set_cookies, extract_cookies($header));
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

  extract(do_http_post($url, MIME_FORM, $request, $_COOKIE)); 

  return extract_cookies($header);
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

  extract(do_http_post($url, MIME_FORM, $request));

  if ($content != '1') {
    throw new ErrorException("phpBB login failed: $content");
  }

  return extract_cookies($header);
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
  # NB: No content is returned, don't check for it.

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

  extract(do_http_post($url, MIME_XML, $request)); 

  $reply = xmlrpc_decode($content);
  if (xmlrpc_is_fault($reply)) {
    throw new ErrorException('bugzilla: ' .
      $reply['faultString'] . ' (' . $reply['faultCode'] . ')');
  }

  return extract_cookies($header);
}

#
# Logout of Bugzilla.
#
function bugzilla_logout($url) {
  $request = xmlrpc_encode_request('User.logout', null);

  extract(do_http_post($url, MIME_XML, $request, $_COOKIE)); 

  return extract_cookies($header);
}

#
# Do a HTTP POST with the given parameters, and return the result.
#
function do_http_post($url, $type, $data, $cookies = false) {

  $header = 'Content-Type: ' . $type . "\r\n" .
            'Content-Length: ' . strlen($data) . "\r\n";

  if ($cookies !== false) {
    $header .= 'Cookie: ' . cookies_list($cookies) . "\r\n";
  }

  $opts = array(
    'http' => array(
      'method'  => 'POST',
      'header'  => $header,
      'content' => $data
    )
  );

  $ctx = stream_context_create($opts);
  $content = file_get_contents($url, 0 , $ctx);
  if (!$content) {
    throw new ErrorException("Failed to open $url");
  }

  return array(
    'header'  => $http_response_header,
    'content' => $content
  );
}

#
# Create a list of cookies for use in a 'Cookie:' header.
# Expects cookies to be a map of name-value pairs (as $_COOKIES is).
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
      'www.test2.nomic.net',
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
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="stylesheet" type="text/css" href="/css/sso.css"/>
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>$title</title>
</head>
<body>
END;

  include(dirname(__FILE__) . '/../inc/header.php');
  echo '<div id="content">';
}

function print_bottom() {
  echo '</div>';
  include(dirname(__FILE__) . '/../inc/footer.shtml');
  print <<<END
</body>
</html>
END;
}

?>
