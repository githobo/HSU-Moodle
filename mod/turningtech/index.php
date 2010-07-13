<?php // $Id: index.php,v 1.9 2009/03/31 13:03:28 mudrd8mz Exp $

/**
 * This page lists all the instances of turningtech in a particular course
 *
 * @author  Your Name <your@email.address>
 * @version $Id: index.php,v 1.9 2009/03/31 13:03:28 mudrd8mz Exp $
 * @package mod/turningtech
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__) . '/lib/forms/turningtech_device_form.php');
require_once(dirname(__FILE__) . '/lib/forms/turningtech_responseware_form.php');
require_once(dirname(__FILE__) . '/lib/helpers/EncryptionHelper.php');
require_once(dirname(__FILE__) . '/lib/helpers/HttpPostHelper.php');

// set up javascript requirements
require_js(array('yui_yahoo','yui_event','yui_dom', 'yui_selector', 'yui_element'));
require_js($CFG->wwwroot . '/mod/turningtech/js/turningtech.js');

$id = required_param('id', PARAM_INT);   // course

if (! $course = get_record('course', 'id', $id)) {
    error(get_string('courseidincorrect', 'turningtech'));
}

require_course_login($course);

add_to_log($course->id, 'turningtech', 'view devices', "index.php?id=$course->id", '');

global $USER;
$context = get_context_instance(CONTEXT_COURSE, $course->id);

$title = get_string('modulename', 'turningtech');

// what is the "right" way to add CSS?
echo "<link rel='stylesheet' type='text/css' href='{$CFG->wwwroot}/mod/turningtech/css/style.css' />";

/// Print the header
$navlinks = array();
$navlinks[] = array('name' => $title, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple($title, '', $navigation, '', '', true, '', navmenu($course));
print_heading($title);

// determine if this is a student or instructor
if(MoodleHelper::isUserStudentInCourse($USER, $course)) {
  
  $rwid = NULL;
  $rwform = new turningtech_responseware_form("index.php?id={$id}");
  
  // process responseware form
  if($rwdata = $rwform->get_data()) {
    try {
      $rwid = doPostRW($CFG->turningtech_responseware_provider, $rwdata->username, $rwdata->password);
      $params = new stdClass();
      $params->userid = $USER->id;
      $params->all_courses = 1;
      $params->deviceid = $rwid;
      $map = DeviceMap::generate($params);
      if($map->save()) {
        turningtech_set_message(get_string('deviceidsaved','turningtech'));
      }
      else {
        turningtech_set_message(get_string('errorsavingdeviceid','turningtech'), 'error');
      }
    }
    catch(Exception $e) {
      turningtech_set_message(get_string('couldnotauthenticate','turningtech',$CFG->turningtech_responseware_provider));
    }
  }
  // process the edit form
  $editform = new turningtech_device_form("index.php?id={$id}");
  if($editform->is_cancelled()) {
    
  }
  elseif($data = $editform->get_data()) {
    $map = DeviceMap::generateFromForm($data);
    if($map->save()) {
      turningtech_set_message(get_string('deviceidsaved','turningtech'));
    }
    else {
      turningtech_set_message(get_string('errorsavingdeviceid','turningtech'), 'error');
    }

  }
  
  // show list of existing devices
  $device_list = turningtech_list_user_devices($USER, $course);
  // set up and display form for new device map
  $dto = new stdClass();
  $dto->userid = $USER->id;
  $dto->courseid = $course->id;
  $dto->deviceid = (empty($rwid) ? '' : $rwid);
  $dto->all_courses = 0;
  $editform->set_data($dto);
  
  // call the template to render
  require_once(dirname(__FILE__) . '/lib/templates/student_index.php');
  
}
else{
  // so user is a member of course, but not a student.  Let's make sure they have
  // permission to manage devices
  require_capability('mod/turningtech:manage', $context);
  $action = optional_param('action', 'deviceid');
//  if($action == 'email') {
//    $sent = turningtech_mail_reminder($course, FALSE);
//    if($sent === FALSE) {
//      turningtech_set_message(get_string('errorsendingemail','turningtech'), 'error');
//    }
//    else {
//      turningtech_set_message(get_string('emailhasbeensent','turningtech'));
//    }
//    $action = 'deviceid';
//  }
  
  echo turningtech_show_messages();
  
  // list actions
  turningtech_list_instructor_actions($USER, $course, $action);
  switch($action) {
    case 'deviceid':
      turningtech_list_course_devices($course);
      break;
    case 'sessionfile':
      turningtech_import_session_file($course);
      break;
    case 'purge':
      turningtech_import_purge_course_devices($course);
      break;
  }
}


/// Finish the page

print_footer($course);

?>
