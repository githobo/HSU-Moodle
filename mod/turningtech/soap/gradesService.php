<?php
/****
 * handles SOAP requests for GradesService
 ****/

require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once(dirname(dirname(__FILE__)) . '/lib/soapClasses/GradesServiceClass.php');

$server = new SoapServer(TURNINGTECH_WSDL_URL . '?service=grades');
$server->setClass('GradesService');
$server->handle();
?>