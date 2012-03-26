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

require_once('Log.php');
$logger = &Log::singleton('file', '/var/log/bugs', 'one');

ini_set('display_errors', 1);

#
# Read bug report
#
if (!isset($_POST['version'])) die('Not a bug report.');

$time = date("M d H:i:s", $_SERVER['REQUEST_TIME']);
$version = $_POST['version'];
$email = $_POST['email'];
$summary = $_POST['summary'];
$description = $_POST['description'];
$log = file_get_contents($_FILES['log']['tmp_name']);

#
# Log bug report in case something goes wrong
#
$logger->info("$email\n$summary\n\n$description\n\n$log\n\n\n");

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
# FIXME: This is a hack to work around the fact that prior to BZ 3.7,
# the XML-RPC interface doesn't support adding attachments. We can
# use the much nicer XML-RPC code once we move to BZ 3.7.
#
$data = base64_encode(file_get_contents($_FILES['log']['tmp_name']));
$size = filesize($_FILES['log']['tmp_name']);

if (strlen($email) > 0) {
  # include the reporter's email, if given
  $description = "Reported by $email\n\n$description";
}

$bz_xml = <<<END
<?xml version="1.0"?>
<bugzilla version="4.0.5" urlbase="http://www.vassalengine.org/tracker" maintainer="uckelman@nomic.net" exporter="uckelman@nomic.net">
  <bug>
    <short_desc>ABR: $summary</short_desc>
    <long_desc is_private="0">
      <who name="Joel Uckelman">uckelman@nomic.net</who>
      <thetext>$description</thetext>
    </long_desc>
    <reporter name="Joel Uckelman">uckelman@nomic.net</reporter>
    <reporter_accessible>1</reporter_accessible>
    <cclist_accessible>1</cclist_accessible>
    <classification_id>1</classification_id>
    <classification>Unclassified</classification>
    <product>VASSAL</product>
    <component>unknown</component>
    <version>$version</version>
    <rep_platform>All</rep_platform>
    <op_sys>All</op_sys>
    <bug_status>NEW</bug_status>
    <priority>unspecified</priority>
    <bug_severity>normal</bug_severity>
    <target_milestone>---</target_milestone>
    <actual_time>0.00</actual_time>
    <assigned_to name="Joel Uckelman">uckelman@nomic.net</assigned_to>
    <attachment isobsolete="0" ispatch="0" isprivate="0">
      <attachid>1</attachid>
      <desc>the errorLog</desc>
      <filename>errorLog</filename>
      <type>text/plain</type>
      <size>$size</size>
      <data encoding="base64">$data</data>
    </attachment>
  </bug>
</bugzilla>
END;

$desc = array(
  0 => array('pipe', 'r'),
  1 => array('pipe', 'w'),
  2 => array('pipe', 'w')
);

$proc = proc_open('/usr/share/bugzilla/importxml.pl', $desc, $pipes);

if (is_resource($proc)) {
  fwrite($pipes[0], $bz_xml);
  fclose($pipes[0]);

#  echo stream_get_contents($pipes[1]);
  fclose($pipes[1]);

#  echo stream_get_contents($pipes[2]);
  fclose($pipes[2]);

  echo proc_close($proc);
}
else {
  echo 1;
}

/*

#
# Relay bug report on to our Bugzilla
#
$url = 'http://www.vassalengine.org/tracker/xmlrpc.cgi';

#
# Login to Bugzilla
#
require_once(dirname(__FILE__) . '/bug-config.php');

$params = array(
  'login'    => BZ_USERNAME,
  'password' => BZ_PASSWORD
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

*/

?>
