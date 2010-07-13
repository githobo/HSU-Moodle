<?php
/****
 * handles SOAP requests for FunctionalCapabilityService
 ****/

require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once(dirname(dirname(__FILE__)) . '/lib/soapClasses/FunctionalCapabilityServiceClass.php');

$server = new SoapServer(TURNINGTECH_WSDL_URL . '?service=func');
$server->setClass('FunctionalCapabilityService');
$server->handle();
?>