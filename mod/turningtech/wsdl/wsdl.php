<?php
// load moodle config to get base URL
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
// the URL of the module
$url = $CFG->wwwroot . '/mod/turningtech';
// the WSDL file to read, depending on the request
$filename = '';

// get the service
$service = $_GET['service'];
// set filename depending on request
switch($service) {
  case 'course':
    $filename = 'CoursesService.wsdl';
    break;
  case 'func':
    $filename = 'FunctionalCapabilityService.wsdl';
    break;
  case 'grades':
    $filename = 'GradesService.wsdl';
    break;
  default:
    echo "expecting parameter 'service' with value 'course', 'func', or 'grades'\n";
    die;
}

$contents = file_get_contents($filename);

header('Content-type: text/xml');
echo str_replace('@URL', $url, $contents);
?>
