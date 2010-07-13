<?php
require_once(dirname(__FILE__) . '/TurningModel.php');


/**
 * represents a user/course/deviceId mapping
 * @author jacob
 *
 */
class DeviceMap extends TurningModel {

  // user
  protected $userid;
  // device ID
  protected $deviceid;
  // course
  protected $courseid;
  // is this just for a single course, or all courses?
  protected $all_courses;
  // has this been deleted?
  protected $deleted;
  
  public $tablename = 'device_mapping';
  public $classname = 'DeviceMap';
  
  /**
   * constructor
   * @return unknown_type
   */
  public function __construct() {
    parent::__construct();
  }
  
  /**
   * override parent's save so that we can check the grade escrow to see
   * if we need to update the gradebook
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/types/TurningModel#save()
   */
  public function save() {
    $result = parent::save();
    if($result) {
      IntegrationServiceProvider::migrateEscowGrades($this);
    }
    return $result;
  }
  
  /**
   * fetch an instance
   * @param $params
   * @return unknown_type
   */
  public static function fetch($params) {
    $params = (array) $params;
    if(!empty($params['deviceid'])) {
      $params['deviceid'] = "'{$params['deviceid']}'";
    }
    return parent::fetchHelper('device_mapping', 'DeviceMap', $params);
  }
  
  /**
   * generator function
   * @param $params
   * @return unknown_type
   */
  public static function generate($params) {
    return parent::generateHelper('DeviceMap', $params);
  }
  
  /**
   * helper function for building a new DeviceMap by turningtech_device_form
   * @param $data
   * @return unknown_type
   */
  public static function generateFromForm($data) {
    $params = array();
    $params['deviceid'] = strtoupper($data->deviceid);
    $params['userid'] = $data->userid;
    $params['all_courses'] = $data->all_courses;
    if(!$params['all_courses']) {
      $params['courseid'] = $data->courseid;
    }
    
    // check if we're updating an existing device map
    if($data->devicemapid) {
      $params['id'] = $data->devicemapid;
    }
    else {
      // if the user enters one of their own device IDs, edit that existing devicemap
      if($map = self::fetch(array('deviceid' => $params['deviceid'], 'userid' => $params['userid'], 'deleted'=>0))) {
        $params['id'] = $map->getId();
      }
      else {
        // make sure the user only ever has 1 all-courses device ID and
        // 1 course-specific ID for this course
        if($data->all_courses) {
          // if user already has an all-courses device ID, edit that record instead of creating
          // a new one
          if($map = self::fetch(array('userid' => $params['userid'], 'all_courses' => 1, 'deleted' => 0))) {
            $params['id'] = $map->getId();
          }
        }
        else {
          // if user already has course-specific map for this course, edit that record instead of creating
          // a new one
          if($map = self::fetch(array('userid' => $params['userid'], 'courseid' => $params['courseid'], 'deleted' => 0))) {
            $params['id'] = $map->getId();
          }
        }
      }
    }
    return self::generate($params);
  }
  
  
  
  /**
   * get all devices associated with the user.  If $course is specified, only find
   * those that apply to that course.
   * @param $user
   * @param $course
   * @return unknown_type
   */
  public static function getAllDevices($user, $course = FALSE) {
    $devices = array();
    $conditions = array();
    $conditions[] = 'deleted = 0';
    $conditions[] = 'userid = ' . $user->id;
    if($course) {
      $conditions[] = '(all_courses = 1 OR courseid = ' . $course->id . ')';
    }
    
    $sql = implode(' AND ', $conditions);
    if($records = get_records_select('device_mapping', $sql)) {
      foreach($records as $record) {
        $device = new DeviceMap();
        parent::setProperties($device, $record);
        $devices[] = $device;
      }
    }
    return $devices;
  }
  
  /**
   * 
   * @return unknown_type
   */
  public function isAllCourses() {
    return $this->all_courses;
  }
  
	/**
   * build a DTO for this item
   * @return unknown_type
   */
  public function getData() {
    $data = new stdClass();
    $data->all_courses = $this->all_courses;
    $data->courseid = $this->courseid;
    $data->deleted = ($this->deleted ? 1 : 0);
    $data->deviceid = $this->deviceid;
    $data->userid = $this->userid;
    if(isset($this->id)) {
      $data->id = $this->id;
    }
    if(isset($this->created)) {
      $data->created = $this->created;
    }
    return $data;
  }
  
  /**
   * display a link to the form for editing this device map
   * @return unknown_type
   */
  public function displayLink($admin = FALSE) {
    global $CFG, $COURSE;
    
    $url = $CFG->wwwroot . '/mod/turningtech/';
    if($admin) {
      $url .= "admin_device.php?id={$this->id}";
    }
    else {
      $url .= "edit_device.php?id={$this->id}";
      $course = $COURSE->id;
      if(!$this->all_courses) {
        $course = $this->courseid;
      }
      if(isset($course)) {
        $url .= '&course=' . $course;
      }
    }
    return "<a href='{$url}'>{$this->deviceid}</a>";
  }
  
  /**
   * verify that the given device ID is not already in use
   * @param $data array with the following keys
   *  - userid
   *  - courseid
   *  - all_courses
   *  - deviceid
   * @return unknown_type
   */
  public static function isAlreadyInUse($data) {
    // do a quick preliminary check to see if the user is
    // editing an attribute of the device map besides the device ID
    if($data['devicemapid']) {
      // this is an existing device map
      $conditions['id'] = $data['devicemapid'];
      $conditions['deviceid'] = "'{$data['deviceid']}'";
      if(record_exists_select('device_mapping', parent::buildWhereClause($conditions))) {
        // the device ID is technically in use, but it is in use by THIS devicemap
        return FALSE;
      }
    }
    
    $conditions = array();
    $conditions['deviceid'] = "'{$data['deviceid']}'";
    $conditions['deleted'] = 0;
    if($data['all_courses']) {
      // device cannot be in use in ANY course or already be listed as
      // an all-courses device by someone else.  So, we just query to see
      // if the device id is listed AT ALL
      $sql = parent::buildWhereClause($conditions);
      if(!empty($data['userid'])) {
        $sql .= ' AND userid != ' . $data['userid'];
      }
      return record_exists_select('device_mapping', $sql);
    }
    else {
      // check if this is already in use as a course-specific mapping
      $conditions['all_courses'] = 0;
      $conditions['courseid'] = $data['courseid'];
      $sql = parent::buildWhereClause($conditions);
      if(!empty($data['userid'])) {
        $sql .= ' AND userid != ' . $data['userid'];
      }
      if(record_exists_select('device_mapping', $sql)) {
        return TRUE;
      }
      
      // now check if it's in use as an all-courses map by someone else
      // in this course
      unset($conditions['courseid']);
      $conditions['all_courses'] = 1;
      $sql = parent::buildWhereClause($conditions);
      $sql .= ' AND userid != ' . $data['userid'];
      $maps = get_records_select('device_mapping', $sql);
      if($maps && is_array($maps)) {
        foreach($maps as $map) {
          $user = new stdClass();
          $user->id = $map->userid;
          $course = new stdClass();
          $course->id = $data['courseid'];
          if(MoodleHelper::isUserStudentInCourse($user, $course)) {
            return TRUE;
          }
        }
      }
    }
    return FALSE;
  }
  
  /**
   * mark the device map as deleted
   * @return unknown_type
   */
  public function delete() {
    set_field('device_mapping', 'deleted', 1, 'id', $this->id);
    $this->deleted=1;
  }
  
  /**
   * purge all course-based device IDs for this course/user.
   * @param $course
   * @param $user
   * @return count of updated fields
   */
  public static function purgeMappings($course, $user) {
    global $db;
  	
	if ((!$course || !isset($course->id))) {
        turningtech_set_message(get_string('couldnotpurge','turningtech'),'error');
        return FALSE;
	}
	else if ((!$user || !isset($user->id))) {
        turningtech_set_message(get_string('couldnotpurge','turningtech'),'error');
        return FALSE;
	}

	$table = 'device_mapping';
    $field = 'deleted';
    $value = 1;

    $field1 = 'all_courses';
    $value1 = 0;
    $field2 = 'courseid';
    $value2 = $course->id;
    $field3 = 'userid';
    $value3 = $user->id;

    $rs = set_field($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3);
    return $db->Affected_Rows();
  }

  /**
   * purge all device IDs in this course
   * @param $course
   * @return unknown_type
   */
  public static function purgeCourse($course) {
    return self::purge($course);
  }
  
  /**
   * purge all all-courses device IDs
   * @return unknown_type
   */
  public static function purgeGlobal() {
    return self::purge();
  }
  
  /**
   * helper function for purging device ids
   * @param $course
   * @return unknown_type
   */
  private static function purge($course=FALSE) {
    global $db;
    
    $table = 'device_mapping';
    $field = 'deleted';
    $value = 1;
    
    $field1 = '';
    $value1 = '';
    $field2 = '';
    $value2 = '';
    $field3 = '';
    $value3 = '';
    if($course && isset($course->id)) {
      $field1 = 'all_courses';
      $value1 = 0;
      $field2 = 'courseid';
      $value2 = $course->id;
    }
    else if($course === FALSE) {
      $field1 = 'all_courses';
      $value1 = 1;
    }
    $rs = set_field($table, $field, $value, $field1, $value1, $field2, $value2, $field3, $value3);
    return $db->Affected_Rows();
  }
}
?>