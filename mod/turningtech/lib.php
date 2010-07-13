<?php  // $Id: lib.php,v 1.12 2009/04/22 21:30:29 skodak Exp $

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

// the path to the WSDL definitions
define('TURNINGTECH_WSDL_URL', $CFG->wwwroot . '/mod/turningtech/wsdl/wsdl.php');

// the 2 types of device ID formats
define('TURNINGTECH_DEVICE_ID_FORMAT_HEX', 1);
// TODO: is this the correct length?
define('TURNINGTECH_DEVICE_ID_FORMAT_HEX_MIN_LENGTH', 6);
define('TURNINGTECH_DEVICE_ID_FORMAT_HEX_MAX_LENGTH', 8);
define('TURNINGTECH_DEVICE_ID_FORMAT_ALPHA', 2);
// TODO: is this the correct length?
define('TURNINGTECH_DEVICE_ID_FORMAT_ALPHA_MIN_LENGTH', 6);

// the type of gradebook items to use
define('TURNINGTECH_GRADE_ITEM_TYPE', 'turningtech');

// different modes of saving scores
define('TURNINGTECH_SAVE_NO_OVERRIDE', 1);
define('TURNINGTECH_SAVE_ONLY_OVERRIDE', 2);
define('TURNINGTECH_SAVE_ALLOW_OVERRIDE', 3);

// default user roles
define('TURNINGTECH_DEFAULT_STUDENT_ROLE', 5);
define('TURNINGTECH_DEFAULT_TEACHER_ROLE', 3);

// switch for enabling/disabling WS encryption
define('TURNINGTECH_ENABLE_ENCRYPTION', true);

// switch for enabling/disabling WS decryption
define('TURNINGTECH_ENABLE_DECRYPTION', true);

// switch for enabling/disabling WS encryption EncryptionHelperException messages also being output via error_log()
define('TURNINGTECH_ENABLE_ENCRYPTION_EXCEPTIONS_IN_ERROR_LOG', true);

// switch for enabling/disabling HttpPostHelperException and HttpPostHelperIOException messages also being output via error_log()
define('TURNINGTECH_ENABLE_POSTRW_EXCEPTIONS_IN_ERROR_LOG', true);

// default responseware provider
define('TURNINGTECH_DEFAULT_RESPONSEWARE_PROVIDER','http://www.rwpoll.com/');

// require all necessary libraries
require_once(dirname(__FILE__) . '/lib/IntegrationServiceProvider.php');
require_once(dirname(__FILE__) . '/lib/types/Escrow.php');
require_once(dirname(__FILE__) . '/lib/types/DeviceMap.php');
require_once(dirname(__FILE__) . '/lib/types/TurningSession.php');

/**
 * Library of functions and constants for module turningtech
 * This file should have two well differenced parts:
 *   - All the core Moodle functions, neeeded to allow
 *     the module to work integrated in Moodle.
 *   - All the turningtech specific functions, needed
 *     to implement all the module logic. Please, note
 *     that, if the module become complex and this lib
 *     grows a lot, it's HIGHLY recommended to move all
 *     these module specific functions to a new php file,
 *     called "locallib.php" (see forum, quiz...). This will
 *     help to save some memory when Moodle is performing
 *     actions across all modules.
 */

/**
 * returns a list of activity types provided by the module.  We return
 * an empty array because we don't want activities to be added.
 * @return unknown_type
 */
function turningtech_get_types() {
  return array();
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $turningtech An object from the form in mod_form.php
 * @return int The id of the newly inserted turningtech record
 */
function turningtech_add_instance($turningtech) {

  $turningtech->timecreated = time();

  # You may have to add extra stuff in here #

  return insert_record('turningtech', $turningtech);
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $turningtech An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function turningtech_update_instance($turningtech) {

  $turningtech->timemodified = time();
  $turningtech->id = $turningtech->instance;

  # You may have to add extra stuff in here #

  return update_record('turningtech', $turningtech);
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function turningtech_delete_instance($id) {

  if (! $turningtech = get_record('turningtech', 'id', $id)) {
    return false;
  }

  $result = true;

  # Delete any dependent records here #

  if (! delete_records('turningtech', 'id', $turningtech->id)) {
    $result = false;
  }

  return $result;
}


/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function turningtech_user_outline($course, $user, $mod, $turningtech) {
  return $return;
}


/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function turningtech_user_complete($course, $user, $mod, $turningtech) {
  return true;
}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in turningtech activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function turningtech_print_recent_activity($course, $isteacher, $timestart) {
  return false;  //  True if anything was printed, otherwise false
}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
//function turningtech_cron () {
//  $courses = get_records('course');
//  $total = 0;
//  foreach($courses as $course) {
//    if($valid = turningtech_mail_reminder($course)) {
//      $total += $valid;
//    }
//  }
//  mtrace('sent reminder email to ' . $total . ' students');
//  return TRUE;
//}

/**
 * 
 * @return unknown_type
 */
//function turningtech_mail_reminder($course, $cron = TRUE) {
//  global $CFG;
//  $total = 0;
//  $users = TurningHelper::getStudentsWithoutDevices($course);
//  $total += count($users);
//  
//  if(empty($users) || !empty($CFG->noemailever)) {
//    return 0;
//  }
//  
//  $mailer =& get_mailer();
//  $supportuser = generate_email_supportuser();
//  $mailer->Sender = $supportuser->email;
//  $mailer->From = $CFG->noreplyaddress;
//  $mailer->FromName = fullname($supportuser);
//  $mailer->Subject = $CFG->turningtech_reminder_email_subject;
//  $mailer->IsHTML(TRUE);
//  $mailer->Body = TurningHelper::getReminderEmailBody($course);
//  $mailer->Encoding = 'quoted-printable';
//  
//  $count = 0;
//  foreach($users as $id=>$user) {
//    // ensure user actually can be emailed
//    if(empty($user->email) || 
//      (isset($user->auth) && $user->auth=='nologin') ||
//      over_bounce_threshold($user)) {
//      
//        continue;
//    }
//    $mailer->AddBCC($user->email, "{$user->firstname} {$user->lastname}");
//    $count++;
//  }
//  
//  if($count > 0) {
//    if(!$mailer->Send()) {
//      if($cron) {
//        mtrace('--ERROR-- failed to send reminder email: ' . $mailer->ErrorInfo);
//      }
//      return FALSE;
//    }
//  }
//  
//  return $total;
//}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of turningtech. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $turningtechid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function turningtech_get_participants($turningtechid) {
  return false;
}


/**
 * This function returns if a scale is being used by one turningtech
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $turningtechid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function turningtech_scale_used($turningtechid, $scaleid) {
  $return = false;

  //$rec = get_record("turningtech","id","$turningtechid","scale","-$scaleid");
  //
  //if (!empty($rec) && !empty($scaleid)) {
  //    $return = true;
    //}

    return $return;
  }


  /**
   * Checks if scale is being used by any instance of turningtech.
   * This function was added in 1.9
   *
   * This is used to find out if scale used anywhere
   * @param $scaleid int
   * @return boolean True if the scale is used by any turningtech
   */
  function turningtech_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('turningtech', 'grade', -$scaleid)) {
      return true;
    } else {
      return false;
    }
  }


  /**
   * Execute post-install custom actions for the module
   * This function was added in 1.9
   *
   * @return boolean true if success, false on error
   */
  function turningtech_install() {
    return true;
  }


  /**
   * Execute post-uninstall custom actions for the module
   * This function was added in 1.9
   *
   * @return boolean true if success, false on error
   */
  function turningtech_uninstall() {
    return true;
  }


  //////////////////////////////////////////////////////////////////////////////////////
  /// Any other turningtech functions go here.  Each of them must have a name that
  /// starts with turningtech_
  /// Remember (see note in first lines) that, if this section grows, it's HIGHLY
  /// recommended to move all funcions below to a new "localib.php" file.

  /**
   * generates device ID format options
   * @return unknown_type
   */
  function turningtech_get_device_id_format_options() {
    return array(
    TURNINGTECH_DEVICE_ID_FORMAT_HEX => get_string('deviceidformathex', 'turningtech'),
    TURNINGTECH_DEVICE_ID_FORMAT_ALPHA => get_string('deviceidformatalpha', 'turningtech')
    );
  }

  /**
   * outputs a table of students and their device IDs
   * @param $course
   * @return unknown_type
   */
  function turningtech_list_course_devices($course) {
    global $CFG;

    $table = new stdClass();
    
    $sort = optional_param('sort', 'name', PARAM_ALPHA);
    $asc = optional_param('asc', true, PARAM_BOOL);
    $order = FALSE;
    switch($sort) {
      case 'uid':
      	$order = 'u.username';
      	break;
      case 'device': 
        $order = 'd.deviceid'; 
        break;
      default:
        $order = 'u.lastname';
        break;
    }
    
    $href = "index.php?id={$course->id}&sort=name";
    $class = '';
    if($sort == 'name' && $asc) {
      $href .= "&asc=0";
      $class = 'asc';
    }
    else if($sort == 'name') {
      $class = 'desc';
    }
    $student_col = "<a href='{$href}' class='{$class}'>" . get_string('student','grades') . "</a>\n";
    
    $href = "index.php?id={$course->id}&sort=device";
    $class = '';
    if($sort == 'device' && $asc) {
      $href .= "&asc=0";
      $class = 'asc';
    }
    else if($sort == 'device') {
      $class = 'desc';
    }
    $device_col = "<a href='{$href}' class='{$class}'>" . get_string('deviceid','turningtech') . "</a>\n";
    
    $href = "index.php?id={$course->id}&sort=uid";
    $class = '';
    if($sort == 'uid' && $asc) {
      $href .= "&asc=0";
      $class = 'asc';
    }
    else if($sort == 'uid') {
      $class = 'desc';
    }
    $id_col = "<a href='{$href}' class='{$class}'>User ID</a>\n";
    
    //$table->head = array(get_string('student', 'grades'), get_string('deviceid', 'turningtech'));
    $table->head = array($student_col, $id_col, $device_col);
    $table->align = array('center', 'center', 'center');
    
    $roster = MoodleHelper::getClassRoster($course, FALSE, FALSE, $order, $asc);
    //echo "Roster: <pre>" . print_r($roster, TRUE) . "</pre>\n";
    if(!empty($roster)) {
      foreach($roster as $student) {
        $studentcell = '';
        $idcell ='';
        $devicecell = '';
        $studentcell = "<a href='{$CFG->wwwroot}/user/view.php?id={$student->id}&course={$course->id}'>{$student->firstname} {$student->lastname}</a>\n";
        $idcell = "<a href='{$CFG->wwwroot}/user/view.php?id={$student->id}&course={$course->id}'>{$student->username}</a>\n";
        if(empty($student->deviceid)) {
          $devicecell = "<a href='edit_device.php?course={$course->id}&student={$student->id}'>";
          $devicecell .= get_string('nodevicesregistered', 'turningtech') . "</a>\n";
        }
        else {
          $device = DeviceMap::fetch(array('id' => $student->devicemapid));
          $devicecell = $device->displayLink();
        }
        $table->data[] = array($studentcell, $idcell, $devicecell);
      }
      print_table($table);
    }
    else {
      echo "<p class='empty-roster'>" . get_string('nostudentsfound', 'turningtech') . "</p>\n";
    }
  }  
  
  
  /**
   * display a list of a user's device IDs for the given course
   * @param $user
   * @param $course
   * @return string
   */
  function turningtech_list_user_devices($user, $course) {
    $output = '';
    // show list of registered device IDs
    $table = new stdClass();
    $table->head = array(get_string('deviceid', 'turningtech'), get_string('devicetype', 'turningtech'));
    $table->align = array('center', 'center');

    $str_all_courses = get_string('allcourses', 'turningtech');
    $str_this_course = get_string('justthiscourse', 'turningtech');

    /// Get all the appropriate data
    $devices = DeviceMap::getAllDevices($user, $course);
    $currentDevice = FALSE;
    if(count($devices)) {
      foreach($devices as $device) {
        if(!$currentDevice) {
          $currentDevice = $device;
        }
        elseif($currentDevice->isAllCourses() && !$device->isAllCourses()) {
          $currentDevice = $device;
        }
        $table->data[] = array($device->displayLink(), ($device->isAllCourses() ? $str_all_courses : $str_this_course));
      }
      $output .= print_table($table, TRUE);
      if($currentDevice) {
        $output .= "<p id='current-device'>" . get_string('usingdeviceid', 'turningtech', $currentDevice->displayLink()) . "</p>";
      }
    }
    else {
      $output .= "<p class='no-devices'>" . get_string('nodevicesregistered', 'turningtech') . "</p>\n";
    }
    return $output;
  }
  
  /**
   * return an array of link data
   * @param $user
   * @param $course
   * @param $action
   * @return unknown_type
   */
  function turningtech_get_instructor_actions($user, $course, $action='deviceid') {
    global $CFG;
    $links = array(
      'deviceid' => array(
      	'text' => get_string('deviceids', 'turningtech'),
        'href' => "{$CFG->wwwroot}/mod/turningtech/index.php?id={$course->id}&action=deviceid"
      ),
      'sessionfile' => array(
        'text' => get_string('importsessionfile', 'turningtech'),
        'href' => "{$CFG->wwwroot}/mod/turningtech/index.php?id={$course->id}&action=sessionfile"
      ),
      'export' => array(
        'text' => get_string('exportparticipantlist','turningtech'),
        'href' => "{$CFG->wwwroot}/mod/turningtech/export_roster.php?id={$course->id}"
      ),
      'purge' => array(
        'text' => get_string('purgedeviceids','turningtech'),
        'href' => "{$CFG->wwwroot}/mod/turningtech/index.php?id={$course->id}&action=purge"
      )//,
//      'email' => array(
//        'text' => get_string('sendemailreminder','turningtech'),
//        'href' => "{$CFG->wwwroot}/mod/turningtech/index.php?id={$course->id}&action=email"
//      )
    );
    $links[$action]['classes'] = array('active');
    return $links;
  }
  
  /**
   * create an unordered list of links
   * @param $links
   * @return unknown_type
   */
  function turningtech_ul($links, $id = '') {
    $count = count($links);
    $i = 1;
    $output = "<ul " . (!empty($id) ? "id={$id}" : '') . ">\n";
    
    foreach($links as $id=>$link) {
      if(!isset($link['classes'])) {
        $link['classes'] = array();
      }
      if($i == 1) {
        $link['classes'][] = "first";
      }
      if($i == $count) {
        $link['classes'][] = "last";
      }
      $class = '';
      if(count($link['classes'])) {
        $class = "class='".implode(' ', $link['classes']) . "'";
      }
      $output .= "<li {$class}>\n";
      $output .= "<a href='{$link['href']}'>{$link['text']}</a>\n";
      $output .= "</li>\n";
      
      $i++;
    }
    $output .= "</ul>\n";
    return $output;
  }
  
  /**
   * list instructor actions
   * @param $user
   * @param $course
   * @return unknown_type
   */
  function turningtech_list_instructor_actions($user, $course, $action='deviceid') {
    global $CFG;
    
    $actions = turningtech_get_instructor_actions($user, $course, $action);
    $output = turningtech_ul($actions, 'turningtech-actions');
    
    echo $output;
  }
  
  /**
   * figure out the correct name for the exported filename
   * @param $course
   * @return unknown_type
   */
  function turningtech_generate_export_filename($course) {
    $course = $course->shortname;
    $date = date('m-d-Y');
    $time = date('H-i-A');
    $extension = 'tpl';
    return "{$course}_{$date}_{$time}.{$extension}";
  }
  
  
  /**
   * displays form for importing session file
   * @param $course
   * @return unknown_type
   */
  function turningtech_import_session_file($course) {
    require_once(dirname(__FILE__) . '/lib/forms/turningtech_import_session_form.php');
    global $CFG;
    $default_url = "index.php?id={$course->id}&action=sessionfile";
    $importform = new turningtech_import_session_form($default_url);
    if($importform->is_cancelled()) {
      // cancel form
      redirect($default_url);
    }
    elseif($data = $importform->get_data()) {
      // process data
      $dir = turningtech_file_dir($course) . "/csv";
      if ($importform->save_files($dir) && $newfilename = $importform->get_new_filename()) {
        $session = new TurningSession();
        $session->setActiveCourse($course);
        try {
          $session->importSession($data->assignment_title, "{$dir}/{$newfilename}", isset($data->override));
        }
        catch(Exception $e) {
          print_error('couldnotparsesessionfile', 'turningtech', $default_url);
        }
      }
      else {
        print_error('errorsavingsessionfile','turningtech', $default_url);
      }
      
    }
    echo turningtech_show_messages();
    // display form
    $importform->display();
  }
  
  /**
   * purges all device maps for the given course
   * @param $course
   * @return unknown_type
   */
  function turningtech_import_purge_course_devices($course) {
    require_once(dirname(__FILE__) . "/lib/forms/turningtech_purge_course_form.php");
    $default_url = "index.php?id={$course->id}&action=purge";
    $mform = new turningtech_purge_course_form($default_url);
    if($data = $mform->get_data()) {
      $purged = DeviceMap::purgeCourse($course);
      if($purged === FALSE) {
        // error
        turningtech_set_message(get_string('couldnotpurge','turningtech'),'error');
      }
      else {
        turningtech_set_message(get_string('alldevicesincoursepurged','turningtech'));
        turningtech_set_message(get_string('purgedinthiscourse','turningtech',$purged));
      }
    }
    else {
      // show warning messages
      turningtech_set_message(get_string('purgecoursewarning','turningtech'));
      turningtech_set_message(get_string('purgecoursedescription','turningtech'));
    }
    echo turningtech_show_messages();
    $mform->display();
  }
  
  /**
   * fetches all messages and optionally clears them
   * @param $clear
   * @return unknown_type
   */
  function turningtech_get_messages($clear = FALSE) {
    $messages = turningtech_set_message();
    if($clear) {
      unset($_SESSION['turning_messages']);
    }
    return $messages;
  }
  
  /**
   * add a message that will be displayed
   * @param $message
   * @param $type
   * @return unknown_type
   */
  function turningtech_set_message($message = '', $type = 'message') {
    if ($message) {
      if (!isset($_SESSION['turning_messages'])) {
        $_SESSION['turning_messages'] = array();
      }
      if (!isset($_SESSION['turning_messages'][$type])) {
        $_SESSION['turning_messages'][$type] = array();
      }
      $_SESSION['turning_messages'][$type][] = $message;
    }
    return isset($_SESSION['turning_messages']) ? $_SESSION['turning_messages'] : array();    
  }
  
  /**
   * display all status messages
   * @return unknown_type
   */
  function turningtech_show_messages() {
    $output = '';
    $messages = turningtech_get_messages(TRUE);
    foreach($messages as $type=>$message) {
      $output .= "<div class='messages {$type}'>";
      $output .= "<ul class='{$type}'>\n";
      foreach($message as $item) {
        $output .= "<li>{$item}</li>\n";
      }
      $output .= "</ul>\n";
      $output .= "</div>\n";
    }
    return $output;
  }
  
  /**
   * Creates a directory file name, suitable for make_upload_directory()
   * @param $course
   * @return unknown_type
   */
  function turningtech_file_dir($course) {
    global $CFG;
    return "{$course->id}/{$CFG->moddata}/turningtech";
  }
  
  /**
   * conducts search and builds table of results
   * @param $data
   * @return unknown_type
   */
  function turningtech_admin_search_results($data) {
    global $CFG;
    
    $users = MoodleHelper::adminStudentSearch($data->searchstring);
    if(!empty($users) && count($users)) {
      $table = new stdClass();
      $table->head = array(get_string('student', 'grades'), get_string('deviceid', 'turningtech'));
      $table->align = array('center', 'center');
      foreach($users as $user) {
        if(empty($user->deviceid)) {
          $devicecell = "<a href='admin_device.php?student={$user->id}'>" . get_string('nodevicesregistered','turningtech') . "</a>\n";
        }
        else {
          $device = DeviceMap::fetch(array('id' => $user->devicemapid));
          $devicecell = $device->displayLink(TRUE);
        }
        $usercell = "<a href='{$CFG->wwwroot}/user/view.php?id={$user->id}&course=1'>{$user->firstname} {$user->lastname}</a>";
        $table->data[] = array($usercell, $devicecell);
      }
      return $table;
    }
    return FALSE;
  }

  function turningtech_role_unassign($userid, $context) {
  	if ($context->contextlevel == CONTEXT_COURSE) {
  		$student = get_record('user', 'id', $userid);
		$course = get_record('course', 'id', $context->instanceid);
		if ($student && $course) {
			TurningHelper::getDeviceIdByCourseAndStudent($course, $student);
		}
		DeviceMap::purgeMappings($course, $student);
  	}
  }
  ?>