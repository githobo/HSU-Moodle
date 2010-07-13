<?php
require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
require_once("lib.php");
require_once("lib/forms/turningtech_admin_device_form.php");

admin_externalpage_setup('editusers');

$devicemapid = optional_param('id', NULL, PARAM_INT);
$studentid = optional_param('student', NULL, PARAM_INT);

$devicemap = NULL;
$student = NULL;

// we need either an existing devicemap or a student
if($devicemapid) {
  if(!$devicemap = DeviceMap::fetch(array('id' => $devicemapid))) {
    error(get_string('couldnotfinddeviceid', 'turningtech', $devicemapid));
  }
  $studentid = $devicemap->getField('userid');
}

if($studentid) {
  $student = get_record('user','id',$studentid);
}

if(empty($student)) {
  error(get_string('studentidincorrect','turningtech'));
}


// build action URL
$url = "admin_device.php";
$params = array();
if(!empty($devicemapid)) {
  $params[] = "id={$devicemapid}";
}
if(!empty($studentid)) {
  $params[] = "student={$studentid}";
}
if(count($params)) {
  $url .= '?' . implode('&', $params);
}

// url of return page
$redirect_url = "admin.php";

// set up form
$deviceform = new turningtech_admin_device_form($url, array('studentid' => $studentid));
if($deviceform->is_cancelled()) {
  redirect($redirect_url);
}
else if ($data = $deviceform->get_data()) {
  $map = DeviceMap::generateFromForm($data);
  if($map->save()) {
    turningtech_set_message(get_string('deviceidsaved','turningtech'));
    redirect($redirect_url);
  }
  else {
    error(get_string('errorsavingdeviceid', 'turningtech'), $redirect_url);
  }
}

// if showing the form, set up data
$dto = new stdClass();
if(!empty($devicemap)) {
  $dto = $devicemap->getData();
  $dto->devicemapid = $dto->id;
  unset($dto->id);
}
else {
  $dto->userid = $student->id;
}
$deviceform->set_data($dto);

//--------- page output ----------------------
admin_externalpage_print_header();

$deviceform->display();

print_footer();

?>