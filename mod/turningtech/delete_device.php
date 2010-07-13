<?php
/***
 * displays the confirmation form for deleting a device ID map
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$devicemapid = required_param('id', PARAM_INT);
$courseid = optional_param('course', NULL, PARAM_INT);
$course = NULL;
$devicemap = NULL;
if(!$devicemap = DeviceMap::fetch(array('id' => $devicemapid))) {
  error(get_string('couldnotfinddeviceid', 'turningtech', $devicemapid));
}

// has the form been confirmed?
$confirm = optional_param('confirm', 0, PARAM_BOOL);

// figure out which course we're dealing with
if(empty($courseid)) {
  if(!$devicemap->isAllCourses()) {
    $courseid = $devicemap->getField('courseid');
  }
  else {
    error(get_string('courseidincorrect', 'turningtech'));
  }
}

if (! $course = get_record('course', 'id', $courseid)) {
  error(get_string('courseidincorrect', 'turningtech'));
}

// make sure user is enrolled
require_course_login($course);

// verify user has permission to delete this devicemap
if($USER->id != $devicemap->getField('userid')) {
  // current user is not the owner of the devicemap.  So
  // verify current user is a teacher
  $context = get_context_instance(CONTEXT_COURSE, $course->id);
  if(!has_capability('mod/turningtech:manage', $context)) {
    error(get_string('notpermittedtoeditdevicemap', 'turningtech'));
  }
}

echo "<link rel='stylesheet' type='text/css' href='{$CFG->wwwroot}/mod/turningtech/css/style.css' />";



if($confirm && confirm_sesskey()) {
  $devicemap->delete();
  turningtech_set_message(get_string('deviceiddeleted', 'turningtech'));
  redirect("{$CFG->wwwroot}/mod/turningtech/index.php?id={$course->id}");
}
else {
  // build breadcrumbs
  $title = get_string('modulename', 'turningtech');
  $heading = get_string('editdevicemap', 'turningtech');
  $navlinks = array();
  $navlinks[] = array('name' => $title, 'link' => "{$CFG->wwwroot}/mod/turningtech/index.php?id={$course->id}", 'type' => 'activity');
  $navlinks[] = array('name' => $heading, 'link' => '', 'type' => 'activity');
  $navigation = build_navigation($navlinks);

  print_header_simple($title, '', $navigation, '', '', true, '', navmenu($course));
  print_heading($heading);
  
  $optionyes = array(
  	'id' => $devicemap->getField('id'), 
  	'course' => $course->id, 
  	'confirm' => 1, 
  	'sesskey' => sesskey()
  );
  $optionno = array('id' => $course->id);
  notice_yesno(
    get_string('deletedevicemap', 'turningtech', $devicemap->getField('deviceid')),
    'delete_device.php',
    'index.php',
    $optionyes,
    $optionno,
    'POST',
    'GET'
  );
  
  print_footer($course);
}

?>