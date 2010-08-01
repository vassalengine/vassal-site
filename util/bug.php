<?php

/*
 * $Id: bug.php 4183 2008-10-02 18:39:18Z uckelman $
 *
 * Copyright (c) 2008-2010 by Joel Uckelman
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License (LGPL) as published by the Free Software Foundation.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Library General Public License for more details.
 *
 * You should have received a copy of the GNU Library General Public
 * License along with this library; if not, copies are available
 * at http://www.opensource.org.
 */

#
# This script exists to collect bug reports from VASSAL.tools.BugDialog
# and redirect them on to our bug tracker of choice.
#

ini_set('display_errors', 1);

#
# Read bug report
#
if (!array_key_exists('version', $_POST)) die('Not a bug report.');

$time = date("M d H:i:s", $_SERVER['REQUEST_TIME']);
$version = $_POST['version'];
$email = $_POST['email'];
$summary = $_POST['summary'];
$description = $_POST['description'];
$log = file_get_contents($_FILES['log']['tmp_name']);

#
# Log bug report in case something goes wrong
#
$fh = fopen('bug_log', 'ab');
fwrite($fh, "$time\n$email\n$summary\n\n$description\n\n$log\n\n\n");
fclose($fh);

#
# Relay bug report on to bug tracker at SourceForge 
#
$url = 'http://sourceforge.net/tracker/index.php';

$param = array(
  'group_id'          => '90612',
  'atid'              => '594231',
  'func'              => 'postadd',
  'category_id'       => '100',
  'artifact_group_id' => '100',
  'summary'           => "ABR: $summary",
  'details'           => "$email\n\n$description",
  'file_description'  => 'the errorLog',
  'input_file'        => ('@' . $_FILES['log']['tmp_name']),
  'submit'            => 'Add Artifact'
);

$headers = array(
  'Expect:'  // avoid lighttpd bug in version used by SF
);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
#curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.19.4 (x86_64-redhat-linux-gnu) libcurl/7.19.4 NSS/3.12.3 zlib/1.2.3 libidn/1.9 libssh2/1.0');

$result = curl_exec($ch);
if (curl_errno($ch) != 0) { 
  die('curl: ' . curl_error($ch));
}

#
# Relay bug report on to our Bugzilla
#
$url = 'http://www.vassalengine.org/tracker/xmlrpc.cgi';

#
# Login to Bugzilla
#
$params = array(
  'login'    => 'username',
  'password' => 'password'
);

$request = xmlrpc_encode_request('User.login', $params);

$opts = array(
  'http' => array(
    'method' => 'POST',
    'header' => "Content-Type: text/xml\r\n" .
                'Content-Length: ' . strlen($request) . "\r\n",
    'content' => $request
  )
);

$ctx = stream_context_create($opts);
$content = file_get_contents($url, 0 , $ctx);
$reply = xmlrpc_decode($content);

if (xmlrpc_is_fault($reply)) {
  die("xmlrpc: {$response['faultString']} ({$response['faultCode']})");
}

$cookies = extract_cookies($http_response_header);

#
# Submit new bug to Bugzilla
#
$params = array(
  'product'     => 'VASSAL',
  'component'   => 'unknown',
  'summary'     => "ABR: $summary",
  'version'     => 'unspecified',
  'description' => "$email\n\n$description",
  'op_sys'      => 'All',
  'platform'    => 'All',
  'priority'    => 'none',
  'severity'    => 'medium'
);

$request = xmlrpc_encode_request('Bug.create', $params);

$opts = array(
  'http' => array(
    'method' => 'POST',
    'header' => "Content-Type: text/xml\r\n" .
                'Content-Length: ' . strlen($request) . "\r\n" .
                'Cookie: ' . implode('; ', $cookies) . "\r\n",
    'content' => $request
  )
);

$ctx = stream_context_create($opts);
$content = file_get_contents($url, 0 , $ctx);
$reply = xmlrpc_decode($content);

if (xmlrpc_is_fault($reply)) {
  die("xmlrpc: {$response['faultString']} ({$response['faultCode']})\n");
}

echo 0;

function extract_cookies($headers) {
  $cookies = array();

  foreach ($headers as $header) {
    if (!strncmp($header, 'Set-Cookie: ', 12)) {
      # knock off the header name and split on attributes
      $crumbs = explode('; ', substr($header, 12));

      # get the cookie name and value
      $cookies[] = $crumbs[0];
    }
  }

  return $cookies;
}

?>
