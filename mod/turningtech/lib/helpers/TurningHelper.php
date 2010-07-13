<?php
/**
 * handles communication with TurningPoint systems
 * @author jacob
 *
 */
class TurningHelper {
  
  /**
   * get an escrow instance.  This may be a new instance or one fetched
   * from the database, depending on the values handed in.
   * @param $course
   * @param $dto
   * @param $migrated
   * @return unknown_type
   */
  public static function getEscrowInstance($course, $dto, $grade_item, $migrated) {
    $instance = FALSE;
    $params = array(
      'deviceid' => $dto->deviceId,
      'courseid' => $course->id,
      'itemid' => $grade_item->id,
      'points_possible' => $dto->pointsPossible,
      'migrated' => ($migrated ? 'TRUE' : 'FALSE')
    );
    // check if this represents an item in the DB
    if($instance = Escrow::fetch($params)) {
      return $instance;
    }
    // otherwise, generate a new one
    $params['points_possible'] = $dto->pointsEarned;
    return Escrow::generate($params);
  }
  
  /**
   * looks up the device ID for the user in the given course.  If none can
   * be found, return FALSE
   * @param $user
   * @return unknown_type
   */
  public static function getDeviceIdByCourseAndStudent($course, $student){
    $params = array(
      'userid' => $student->id,
      'courseid' => $course->id,
      'all_courses' => 0,
      'deleted' => 0
    );
    $device = DeviceMap::fetch($params);
    // if no course-specific association exists, look for global
    if(!$device) {
      // do not search for a specific course
      unset($params['courseid']);
      $params['all_courses'] = 1;
      $device = DeviceMap::fetch($params);
    }
    return $device;
  }
  
  
  /**
   * Checks if there is a (user,course,device) association.  If so, returns
   * the user.  If not, checks if there is a global (user,device) association.
   * If no user is found, returns false.
   * @param $course
   * @param $deviceid
   * @return unknown_type
   */
  public static function getStudentByCourseAndDeviceId($course, $deviceid) {
    $params = array(
      'courseid' => $course->id,
      'deviceid' => $deviceid,
      'all_courses' => 0,
      'deleted' => 0
    );
    $map = DeviceMap::fetch($params);
    // if no course-specific map found, look for global
    if(!$map) {
      // do not search for specific course
      unset($params['courseid']);
      $params['all_courses'] = 1;
      $map = DeviceMap::fetch($params);
    }
    if($map) {
      return MoodleHelper::getUserById($map->getField('userid'));
    }
    return FALSE;
  }
  
  /**
   * checks whether the given device ID is in the correct format
   * @param $deviceid
   * @return unknown_type
   */
  public static function isDeviceIdValid($deviceid) {
    global $CFG;
    switch($CFG->turningtech_deviceid_format) {
      case TURNINGTECH_DEVICE_ID_FORMAT_HEX:
        return self::isDeviceIdValidHex($deviceid);
        break;
      case TURNINGTECH_DEVICE_ID_FORMAT_ALPHA:
        return self::isDeviceIdValidAlpha($deviceid);
        break;
      default:
        return FALSE;
    }
  }
  
  /**
   * checks if the given device ID is in valid hex form
   * @param $deviceid
   * @return unknown_type
   */
  public static function isDeviceIdValidHex($deviceid) {
    if(strlen($deviceid) >= TURNINGTECH_DEVICE_ID_FORMAT_HEX_MIN_LENGTH 
      && strlen($deviceid) <= TURNINGTECH_DEVICE_ID_FORMAT_HEX_MAX_LENGTH
      && ctype_xdigit($deviceid)) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * checks if the given device ID is in valid alphanumeric form
   * @param $deviceid
   * @return unknown_type
   */
  public static function isDeviceIdValidAlpha($deviceid) {
    if(strlen($deviceid) >= TURNINGTECH_DEVICE_ID_FORMAT_ALPHA_MIN_LENGTH && ctype_alnum($deviceid)) {
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * determines if the user needs to see a reminder.  If so, returns the reminder message.
   * @param $user
   * @param $course
   * @return unknown_type
   */
  public static function getReminderMessage($user, $course) {
    // ensure we only show 1 reminder per session
    if(isset($_SESSION['USER']->turningtech_reminder)) return NULL;
    // set flag so reminder is not shown
    $_SESSION['USER']->turningtech_reminder = 1;
    return get_string('remindermessage','turningtech');
  }
  
  /**
   * compiles a list of all students who do not have devices registered
   * @return unknown_type
   */
  public static function getStudentsWithoutDevices($course) {
    $students = array();
    
    $roster = MoodleHelper::getClassRoster($course);
    if(!empty($roster)) {
      foreach($roster as $r) {
        if(empty($r->devicemapid) && !isset($students[$r->id])) {
          $students[$r->id] = $r;
        }
      }
    }
    return $students;
  }
  
  /**
   * provides the URL of the responseware provider
   * @return unknown_type
   */
  public static function getResponseWareUrl($action = FALSE) {
    global $CFG;
    $url = $CFG->turningtech_responseware_provider;
    if($url[strlen($url)-1] != '/') {
      $url .= '/';
    }
    if($action) {
      switch($action) {
        case 'login':
          $url .= 'Login.aspx';
          break;
        case 'forgotpassword':
          $url .= 'ForgotPassword.aspx';
          break;
      }
    }
    return $url;
  }
  
  /**
   * 
   * @param $student
   * @return unknown_type
   */
//  public static function getReminderEmailBody($course) {
//    global $CFG;
//    $raw = "\n{$CFG->turningtech_reminder_email_body}\n";
//    return str_replace(
//      array('@coursename', '@courselink'),
//      array($course->fullname, "{$CFG->wwwroot}/course/view.php?id={$course->id}"),
//      $raw
//    );
//  }
}
?>
