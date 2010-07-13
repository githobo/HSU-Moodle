<?php
/**
 * Class that delegates requests for Moodle and TurningPoint operations
 */
require_once(dirname(__FILE__) . '/AbstractServiceProvider.php');
require_once(dirname(__FILE__) . '/helpers/MoodleHelper.php');
require_once(dirname(__FILE__) . '/helpers/TurningHelper.php');
require_once(dirname(__FILE__) . '/helpers/EncryptionHelper.php');

class IntegrationServiceProvider extends ServiceProvider {
  
  /**
   * constructor
   * @return unknown_type
   */
  public function IntegrationServiceProvider() {
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getUserByAESAuth()
   */
  public function getUserByAESAuth($AESusername, $AESpassword) {
    global $USER;
    list($username, $password) = decryptWebServicesStrings(array($AESusername, $AESpassword));
    $USER = MoodleHelper::authenticateUser($username, $password);
    return $USER;
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getClassRoster()
   */
  public function getClassRoster($course) {
    
    $roster = array();
    if($participants = MoodleHelper::getClassRoster($course)) {
      foreach($participants as $participant) {
        $roster[] = $this->generateCourseParticipantDTO($participant, $course);
      }
    }
    
    return $roster;
  }
  
  /**
   * check if a user is enrolled as a student in the course
   * @param $user
   * @param $course
   * @return unknown_type
   */
  public function isUserStudentInCourse($user, $course) {
    return MoodleHelper::isUserStudentInCourse($user, $course);
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getCourseById()
   */
  public function getCourseById($siteId) {
    // technically, this should live in moodleHelper... but it's already
    // abstracted away into oblivion
    return get_record('course', 'id', $siteId);
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#userHasRosterPermission()
   */
  public function userHasRosterPermission($user, $course) {
    // delegate to moodle helper
    return MoodleHelper::userHasRosterPermission($user, $course);
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getCoursesByInstructor()
   */
  public function getCoursesByInstructor($instructor) {
    $moodle_courses = MoodleHelper::getInstructorCourses($instructor);
    $courses = array();
    foreach($moodle_courses as $c) {
      $courses[] = $this->generateCourseSiteView($c);
    }
    return $courses;
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getUserCapabilities()
   */
  public function getUserCapabilities($user) {
    $cap = array();
    $dto = new stdClass();
    $dto->description = get_string('getcoursesforteacherdesc','turningtech');
    $dto->name = 'getCoursesForTeacher';
    $dto->permissions = array();
    $cap[] = $dto;
    return $cap;
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#createGradebookItem()
   */
  public function createGradebookItem($course, $title, $points) {
    global $USER;
    
    // holds any error messages
    $dto = new stdClass();
    
    if(!MoodleHelper::userHasGradeItemPermission($USER, $course)) {
      $dto->itemTitle = $title;
      $dto->errorMessage = get_string('nogradeitempermission','turningtech');
      return $dto;
    }
    
    return MoodleHelper::createGradebookItem($course, $title, $points);
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getGradebookItemsByCourse()
   */
  public function getGradebookItemsByCourse($course) {
    $items = MoodleHelper::getGradebookItemsByCourse($course);
    //echo "<pre>" . print_r($items, TRUE) . "</pre>";
    for($i=0; $i<count($items); $i++) {
      $items[$i] = $this->generateGradebookItemView($items[$i]);
    }
    
    return $items;
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#createGradebookItemInstance()
   */
  public function createGradebookItemInstance($course, $title) {
    return MoodleHelper::getGradebookItemByCourseAndTitle($course, $title);
  }
  
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getStudentByCourseAndDeviceId()
   */
  public function getStudentByCourseAndDeviceId($course, $deviceId) {
    return TurningHelper::getStudentByCourseAndDeviceId($course, $deviceId);
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#getDeviceIdByCourseAndStudent()
   */
  public function getDeviceIdByCourseAndStudent($course, $student) {
    return TurningHelper::getDeviceIdByCourseAndStudent($course, $student);
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#saveGradebookItem()
   */
  public function saveGradebookItem($course, $dto, $mode = TURNINGTECH_SAVE_NO_OVERRIDE) {
    // prepare the error just in case
    $error = new stdClass();
    $error->deviceId = $dto->deviceId;
    $error->itemTitle = $dto->itemTitle;
    
    // get the gradebook item for this transaction
    $grade_item = MoodleHelper::getGradebookItemByCourseAndTitle($course, $dto->itemTitle);
    if(!$grade_item) {
      $error->errorMessage = get_string('couldnotfindgradeitem', 'turningtech', $dto);
      return $error;
    }
    
    // see if there is a student associated with this device id
    $student = $this->getStudentByCourseAndDeviceId($course, $dto->deviceId);
    if(!$student) {
      // no device association for this device, so save in escrow
      $escrow = TurningHelper::getEscrowInstance($course, $dto, $grade_item, FALSE);
      // check if we can't override an existing entry
      if(($mode == TURNINGTECH_SAVE_NO_OVERRIDE) && $escrow->getId()) {
        $error->errorMessage = get_string('cannotoverridegrade', 'turningtech');
      }
      // inversely, check if we're trying to override a grade but none was found
      elseif(($mode == TURNINGTECH_SAVE_ONLY_OVERRIDE) && !$escrow->getId()) {
        $error->errorMessage = get_string('existingitemnotfound', 'turningtech');
      }
      // otherwise we don't care and the escrow item can be saved
      else {
        $escrow->setField('points_earned', $dto->pointsEarned);
        $escrow->setField('points_possible', $dto->pointsPossible);
        if($escrow->save()) {
          $error->errorMessage = get_string('gradesavedinescrow', 'turningtech');
        }
        else {
          $error->errorMessage = get_string('errorsavingescrow', 'turningtech');
        }
      }
    }
    else {
      // we have a student, so we can write directly to the gradebook.  First
      // we need to check if we can't/must override existing grade
      $exists = MoodleHelper::gradeAlreadyExists($student, $grade_item);
      if(($mode == TURNINGTECH_SAVE_NO_OVERRIDE) && $exists) {
        $error->errorMessage = get_string('cannotoverridegrade', 'turningtech');
      }
      elseif(($mode == TURNINGTECH_SAVE_ONLY_OVERRIDE) && !$exists) {
        $error->errorMessage = get_string('existingitemnotfound', 'turningtech');
      }
      else {
        // save the grade
        if($grade_item->update_final_grade($student->id, $dto->pointsEarned, 'gradebook')) {
          // everything is fine, no error to return. Save an escrow entry just to record
          // the transaction
          $escrow = Escrow::generate(
            array(
            	'deviceid' => $dto->deviceId, 
            	'courseid' => $course->id, 
            	'itemid' => $grade_item->id,
              'points_possible' => $dto->pointsPossible,
              'points_earned' => $dto->pointsEarned,
              'migrated' => TRUE
            )
          );
          $escrow->save();
          $error = FALSE;
        }
        else {
          echo "<p>grade not saved successfully, creating escrow entry</p>\n";
          // could not save in gradebook.  Create escrow item and save it
          $escrow = TurningHelper::getEscrowInstance($course, $dto, $grade_item, FALSE);
          $escrow->setField('points_earned', $dto->pointsEarned);
          $escrow->save();
          $error->errorMessage = get_string('errorsavinggradeitemsavedinescrow', 'turningtech');
        }
      }
    }
    
    return $error;
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/mod/turningtech/lib/ServiceProvider#addToExistingScore()
   */
  public function addToExistingScore($course, $dto) {
    // prepare the error just in case
    $error = new stdClass();
    $error->deviceId = $dto->deviceId;
    $error->itemTitle = $dto->itemTitle;
    
    // get the gradebook item for this transaction
    $grade_item = MoodleHelper::getGradebookItemByCourseAndTitle($course, $dto->itemTitle);
    if(!$grade_item) {
      $error->errorMessage = get_string('couldnotfindgradeitem', 'turningtech', $dto);
      return $error;
    }
    
    // see if there is a student associated with this device id
    $student = $this->getStudentByCourseAndDeviceId($course, $dto->deviceId);
    if(!$student) {
      // no device association for this device, so save in escrow
      $escrow = TurningHelper::getEscrowInstance($course, $dto, $grade_item, FALSE);
      // verify this is an existing item
      if(!$escrow->getId()) {
        $error->errorMessage = get_string('existingitemnotfound', 'turningtech');
      }
      else {
        $escrow->setField('points_earned', ($escrow->getField('points_earned') + $dto->pointsEarned));
        if($escrow->save()) {
          $error->errorMessage = get_string('gradesavedinescrow', 'turningtech');
        }
        else {
          $error->errorMessage = get_string('errorsavingescrow', 'turningtech');
        }
      }
    }
    else {
      $grade = MoodleHelper::getGradeRecord($student, $grade_item);
      if(!$grade) {
        $error->errorMessage = get_string('existingitemnotfound', 'turningtech');
      }
      else {
        $grade_item->update_final_grade($student->id, ($grade->finalgrade + $dto->pointsEarned), 'gradebook');
        $error = FALSE;
      }
    }
    
    return $error;
  }
  
  /**
   * check the escrow table to see if there are any entries that correspond to
   * the given device map.  If so, move them into the database
   * @param $devicemap
   * @return unknown_type
   */
  public static function migrateEscowGrades($devicemap) {
    $conditions = array();
    $conditions['deviceid'] = "'{$devicemap->getField('deviceid')}'";
    $conditions['migrated'] = 0;
    if(!$devicemap->isAllCourses()) {
      $conditions['courseid'] = $devicemap->getField('courseid');
    }
    $sql = TurningModel::buildWhereClause($conditions);
    $items = get_records_select('escrow', $sql);
    if($items) {
      foreach($items as $item) {
        $escrow = Escrow::generate($item);
        self::doGradeMigration($devicemap, $escrow);
      }
    }
  }
  
  /**
   * add a new entry to the gradebook for escrow item using information provided
   * by the device map.
   * @param $devicemap
   * @param $escrow
   * @return unknown_type
   */
  public static function doGradeMigration($devicemap, $escrow) {
    if($grade_item = MoodleHelper::getGradebookItemById($escrow->getField('itemid'))) {
      $grade_item->update_final_grade($devicemap->getField('userid'), $escrow->getField('points_earned'), 'gradebook');
      $escrow->setField('migrated', 1);
      $escrow->save();
    }
  }
  
  
  /**
   * create fake gradebook item
   * @return unknown_type
   */
  private function generateGradebookItemView($gradeitem) {
    $item = new stdClass();
    $item->itemTitle = $gradeitem->itemname;
    $item->points = $gradeitem->grademax;
    return $item;
  }
  
  
  /**
   * generates a fake course
   * @return CourseSiteView
   */
  private function generateCourseSiteView($course) {
    $view = new stdClass();
    $view->id = $course->id;
    $view->title = $course->fullname;
    $view->type = $course->category;
    return $view;
  }
  
  /**
   * translates a Moodle user into a course participant DTO
   * @return CourseParticipantDTO
   */
  private function generateCourseParticipantDTO($participant, $course) {
    $dto = new stdClass();
    $dto->deviceId = NULL;
    if(!empty($participant->deviceid)) {
      $dto->deviceId = $participant->deviceid;
    }
    $dto->email = $participant->email;
    $dto->firstName = $participant->firstname;
    $dto->lastName = $participant->lastname;
    $dto->loginId = $participant->username;
    $dto->userId = $participant->id;
    
    return $dto;
  }
  
  
	/**
   * generate fake DTO
   * @return functionalCapabilityDto
   */
  private function _generateFakeCapabilityDto() {
    $dto = new stdClass();
    $dto->description = $this->_generateRandomString();
    $dto->name = $this->_generateRandomString();
    $dto->permissions = $this->_generateRandomString();
    return $dto;
  }
  
  /**
   * spits out a random string
   * @param $length
   * @return string
   */
  private function _generateRandomString($length = 0) {
    $str = md5(uniqid(rand(), TRUE));
    return ($length ? substr($str, 0, $length) : $str);
  }
  
}
?>