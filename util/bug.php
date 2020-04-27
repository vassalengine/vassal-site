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

list($header, $reply) = do_request($url, 'User.login', $params);
die_on_failure($reply);

$token = $reply['token'];

#
# Submit new bug to Bugzilla
#
$params = array(
  'token'       => $token,
  'product'     => 'VASSAL',
  'component'   => 'unknown',
  'summary'     => "ABR: $summary",
  'version'     => $version,
  'description' => "$email\n\n$description",
  'op_sys'      => 'All',
  'platform'    => 'All',
  'priority'    => 'unspecified',
  'severity'    => 'normal'
);

$reply = do_request($url, 'Bug.create', $params)[1];
die_on_failure($reply);

$params = array(
  'token'        => $token,
  'ids'          => array($reply['id']),
  'data'         => $log,
  'file_name'    => 'errorLog',
  'summary'      => 'the errorLog',
  'content_type' => 'text/plain'
);

$reply = do_request($url, 'Bug.add_attachment', $params)[1];
die_on_failure($reply);

echo 0;

function do_request($url, $method, $params) {
  $request = xmlrpc_encode_request($method, $params);

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
  return array($http_response_header, xmlrpc_decode($content));
}

function die_on_failure($reply) {
  if (xmlrpc_is_fault($reply)) {
    global $logger;
    $logger->crit("xmlrpc: {$reply['faultString']} ({$reply['faultCode']})\n");
    exit(1);
  }
}

?>
