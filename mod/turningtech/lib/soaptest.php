<?php

function showTestResult($testname, $request, $response) {
?>
<h2><?php print $testname; ?></h2>
<dl>
  <dt>Request</dt>
  <dd><pre><?php print_r($request); ?></pre></dd>
  <dt>Response</dt>
  <dd><pre><?php print_r($response); ?></pre></dd>
</dl>
<?
}

function runTest($client, $name, $request) {
  $response = NULL;
  
  try {
    $response = $client->$name($request);
  } catch (SoapFault $e) {
    $response = $e;
  }
  
  showTestResult($name, $request, $response);
}

ini_set('soap.wsdl_cache_enabled', '0');

$url = "http://" . $_SERVER['HTTP_HOST'];

$path = split('/', $_SERVER['REQUEST_URI']);
// move up 2 directories
array_pop($path);
array_pop($path);
// add on path to WSDL provider
$path[] = 'wsdl';
$path[] = 'wsdl.php?service=';

$wsdl_url = $url . implode('/', $path);

$soap_params = array('trace' => 1);

$coursesClient = new SoapClient($wsdl_url.'course', $soap_params);
$funcClient = new SoapClient($wsdl_url.'func', $soap_params);
$gradesClient = new SoapClient($wsdl_url.'grades', $soap_params);

require(dirname(__FILE__) . '/tests/tests.php');
?>
