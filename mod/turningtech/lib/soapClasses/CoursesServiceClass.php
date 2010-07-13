<?php
/**
 * SOAP server class for courses service
 * @author jacob
 *
 */

// require the main library for this module
require_once(dirname(dirname(dirname(__FILE__))) . '/lib.php');

// require parent class
require_once(dirname(__FILE__) . '/AbstractSoapServiceClass.php');

class CoursesService extends SoapService{
  
  /**
   * constructor
   * @return void
   */
  public function CoursesService() {
    parent::SoapService();
  }
  
  /**
   * 
   * @param $request
   * @return array of courseSiteView
   */
  public function getTaughtCourses($request) {
    $instructor = NULL;
    $courses = NULL;
    
    $instructor = $this->authenticateRequest($request);
    $courses = $this->service->getCoursesByInstructor($instructor);
    if($courses === FALSE) {
      $this->throwFault('CourseException', get_string('couldnotgetlistofcourses', 'turningtech'));
    }
    
    return $courses;
  }
  
  /**
   * 
   * @param $request
   * @return array of courseParticipantDTO
   */
  public function getClassRoster($request) {
    $instructor = NULL;
    $course = NULL;
    $roster = NULL;
    
    $instructor = $this->authenticateRequest($request);
    $course = $this->getCourseFromRequest($request);
    
    if($this->service->userHasRosterPermission($instructor, $course)) {
      $roster = $this->service->getClassRoster($course);
      if($roster === FALSE) {
        $this->throwFault("CourseException", get_string('couldnotgetroster', 'turningtech', $request->siteId));
      }
      return $roster;
    }
    else {
      $this->throwFault("SiteConnectException", get_string('norosterpermission', 'turningtech'));
    }
  }
}
?>