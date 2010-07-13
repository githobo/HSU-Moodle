<?php
require_once $CFG->dirroot.'/grade/lib.php';

/**
 * Class that abstracts communication with Moodle systems
 * @author jacob
 *
 */
class MoodleHelper {
  
  /**
   * authenticate a username/password pair
   * @param $username
   * @param $password
   * @return user object
   */
  public static function authenticateUser($username, $password) {
    return authenticate_user_login($username, $password);
  }
  
  /**
   * returns all courses for which the given user is
   * in the "teacher" role
   * @param $user
   * @return unknown_type
   */
  public static function getInstructorCourses($user) {
    $courses = array();
    $mycourses = get_my_courses($user->id);
    // iterate through courses and verify that this user is 
    // the instructor, not a student, for each course
    foreach($mycourses as $course) {
      $context = get_context_instance(CONTEXT_COURSE, $course->id);
      $role_users = get_role_users(TURNINGTECH_DEFAULT_TEACHER_ROLE, $context);
      foreach($role_users as $ru) {
        if($ru->id == $user->id) {
          $courses[] = $course;
          break;
        }
      }
    }
    return $courses;
  }
  
  /**
   * check if user is enrolled as student in course
   * @param $user
   * @param $course
   * @return unknown_type
   */
  public static function isUserStudentInCourse($user, $course) {
    $found = self::getClassRoster($course, FALSE, $user->id);
    return ($found ? TRUE : FALSE); 
  }
  
  /**
   * check if user is instructor for course
   * @param $user
   * @param $course
   * @return unknown_type
   */
  public static function isUserInstructorInCourse($user, $course) {
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    return has_capability('mod/turningtech:manage', $context, $user->id);
  }
  
  /**
   * determines whether the user has permission to view the course roster
   * @param $user
   * @param $course
   * @return unknown_type
   */
  public static function userHasRosterPermission($user, $course) {
    $allowed = FALSE;
    if($context = get_context_instance(CONTEXT_COURSE, $course->id)) {
      $allowed = has_capability('moodle/course:viewparticipants', $context, $user->id);
    }
    return $allowed;
  }
  
  /**
   * determines whether user has permission to create a new gradebook item in the given course
   * @param $user
   * @param $course
   * @return unknown_type
   */
  public static function userHasGradeItemPermission($user, $course) {
    $allowed = FALSE;
    if($context = get_context_instance(CONTEXT_COURSE, $course->id)) {
      $allowed = has_capability('moodle/grade:manage', $context, $user->id);
    }
    return $allowed;
  }
  /**
   * fetches the class roster
   * @param $course
   * @param $roles array of role ids
   * @param $userid optional id of user to quickly check if they are enrolled
   * @return unknown_type
   */
  public static function getClassRoster($course, $roles = FALSE, $userid = FALSE, $order = 'u.lastname', $asc = TRUE) {
    global $CFG;
    
    if(!$roles) {
      $roles = array(TURNINGTECH_DEFAULT_STUDENT_ROLE);
    }
    
    $sql =<<<EOF
SELECT u.id, u.firstname, u.lastname, u.username, u.email,
d.id AS devicemapid, d.deviceid, d.deleted, d.all_courses, d.courseid
FROM mdl_user u
LEFT JOIN {$CFG->prefix}device_mapping dall ON (dall.userid=u.id AND dall.deleted=0 AND dall.all_courses=1)
LEFT JOIN {$CFG->prefix}device_mapping dcrs ON (dcrs.userid=u.id AND dcrs.deleted=0 AND dcrs.all_courses=0 AND dcrs.courseid={$course->id})
LEFT JOIN {$CFG->prefix}device_mapping d ON (d.id = IFNULL(dcrs.id, dall.id))
LEFT JOIN {$CFG->prefix}role_assignments r ON r.userid=u.id
LEFT JOIN {$CFG->prefix}context c ON r.contextid=c.id
EOF;

    if(is_array($roles)) {
      $rolesql = 'r.roleid IN (' . implode(', ', $roles) . ')';
    }
    elseif(is_numeric($roles)) {
      $rolesql = 'r.roleid=' . $roles;
    }
    
    $where = "{$rolesql} AND u.deleted=0 AND c.contextlevel=" . CONTEXT_COURSE . " AND c.instanceid={$course->id} ";
    if($userid) {
      $where .= "AND u.id={$userid} ";
    }
    
    $orderby = "ORDER BY {$order} ";
    $orderby .= ($asc ? 'ASC' : 'DESC');
    $sql ="{$sql} WHERE {$where} {$orderby}";
    return get_records_sql($sql);
  }
  
  /**
   * searches for users by username
   * @param $str
   * @return unknown_type
   */
  public static function adminStudentSearch($str) {
    global $CFG;
    $str = strtolower($str);
    $select =<<<EOF
SELECT u.id, u.firstname, u.lastname, u.username, u.email,
d.id AS devicemapid, d.deviceid, d.deleted, d.all_courses, d.courseid
FROM mdl_user u
LEFT JOIN {$CFG->prefix}device_mapping d ON (d.userid=u.id AND d.deleted=0)
LEFT JOIN {$CFG->prefix}role_assignments r ON r.userid=u.id
LEFT JOIN {$CFG->prefix}context c ON r.contextid=c.id
EOF;
    $where = array();
    $where[] = 'r.roleid=' . TURNINGTECH_DEFAULT_STUDENT_ROLE;
    $where[] = 'c.contextlevel=' . CONTEXT_COURSE;
    $where[] = 'lower(u.username) LIKE "%'.$str.'%"';
    $wheresql = implode(' AND ', $where); 
    $sql ="{$select} WHERE {$wheresql} ORDER BY u.username";
    return get_records_sql($sql);
  }
  
  /**
   * fetches a user object by id
   * @param $id
   * @return unknown_type
   */
  public static function getUserById($id) {
    return get_complete_user_data('id', $id);
  }
  
  /**
   * creates a gradebook item
   * @param $course
   * @param $title
   * @param $points
   * @return unknown_type
   */
  public static function createGradebookItem($course, $title, $points) {
    // contains possible error DTO
    $dto = new stdClass();
    
    if(self::getGradebookItemByCourseAndTitle($course, $title)) {
      echo "gradebook item already exists";
      $dto->itemTitle = $title;
      $dto->errorMessage = get_string('gradebookitemalreadyexists', 'turningtech');
      return $dto;
    }
    
    // create new grade item
    $grade_item = new grade_item(array('courseid' => $course->id), FALSE);
    // set parent category
    $data = $grade_item->get_record_data();
    $parent_category = grade_category::fetch_course_category($course->id);
    $data->parentcategory = $parent_category->id;
    // set points
    $data->grademax = unformat_float($points);
    $data->grademin = unformat_float(0.0);
    // set title
    $data->itemname = $title;
    
    grade_item::set_properties($grade_item, $data);
    $grade_item->outcomeid = null;
    
    $grade_item->itemtype = TURNINGTECH_GRADE_ITEM_TYPE;
    $grade_item->insert();
    
    return FALSE;
  }
  
  /**
   * fetches a list of all gradebook items in the course
   * @param $course
   * @return unknown_type
   */
  public static function getGradebookItemsByCourse($course) {
    $gtree = new grade_tree($course->id, false, false);
    $items = array();
    
    foreach($gtree->top_element['children'] as $item) {
      // do not include courses, categories, etc
      if($item['object']->itemtype == TURNINGTECH_GRADE_ITEM_TYPE) {
        $items[] = $item['object'];
      }
    }
    
    return $items;
  }
  
  /**
   * fetches a gradebook item
   * @param $course
   * @param $title
   * @return unknown_type
   */
  public static  function getGradebookItemByCourseAndTitle($course, $title) {
    return grade_item::fetch(array('itemname'=>$title, 'courseid'=>$course->id, 'itemtype' => TURNINGTECH_GRADE_ITEM_TYPE));
  }
  
  /**
   * fetch a gradebook item
   * @param $id
   * @return unknown_type
   */
  public static function getGradebookItemById($id) {
    return grade_item::fetch(array('id' => $id, 'itemtype' => TURNINGTECH_GRADE_ITEM_TYPE));
  }
  
  /**
   * get a record from the gradebook
   * @param $student
   * @param $grade_item
   * @return unknown_type
   */
  public static function getGradeRecord($student, $grade_item) {
    return new grade_grade(array('userid' => $student->id, 'itemid' => $grade_item->id));
  }
  
  /**
   * check if the specified user already has a grade for the given item
   * @param $studentid
   * @param $gradeitemid
   * @return unknown_type
   */
  public static function gradeAlreadyExists($user, $grade_item) {
    $grade = self::getGradeRecord($user, $grade_item);
    return !empty($grade->id);
  }
}
?>