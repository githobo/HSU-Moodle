<?php
/****
 * handles SOAP requests for CoursesService
 ****/

require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once(dirname(dirname(__FILE__)) . '/lib/soapClasses/CoursesServiceClass.php');

$server = new SoapServer(TURNINGTECH_WSDL_URL . '?service=course');
$server->setClass('CoursesService');
$server->handle();
?>