<?php
require_once(dirname(dirname(__FILE__)).'/lib.php');
require_once(dirname(__FILE__).'/soapClasses/CoursesServiceClass.php');
require_once(dirname(__FILE__).'/soapClasses/FunctionalCapabilityServiceClass.php');
require_once(dirname(__FILE__).'/soapClasses/GradesServiceClass.php');

/**
 * 
 * @param $testname
 * @param $request
 * @param $response
 * @return unknown_type
 */
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

/**
 * 
 * @param $client
 * @param $name
 * @param $request
 * @return unknown_type
 */
function runTest($client, $name, $request) {
  $response = NULL;
  
  try {
    $response = $client->$name($request);
  } catch (SoapFault $e) {
    $response = $e;
  }
  
  showTestResult($name, $request, $response);
}


$coursesClient = new CoursesService();
$funcClient = new FunctionalCapabilityService();
$gradesClient = new GradesService();

require(dirname(__FILE__) . '/tests/tests.php');
?>
