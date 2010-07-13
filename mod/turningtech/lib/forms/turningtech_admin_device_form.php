<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/lib/formslib.php');

/**
 * form class for creating/editing DeviceMaps
 * @author jacob
 *
 */
class turningtech_admin_device_form extends moodleform {
  
  /**
   * (non-PHPdoc)
   * @see docroot/lib/moodleform#definition()
   */
  function definition() {
    $mform =& $this->_form;
    $mform->addElement('header', 'turningtechdevicemapheader', get_string('deviceid', 'turningtech'));
    $mform->addElement('hidden', 'devicemapid');
    $mform->addElement('hidden', 'adminform', 1);
    $mform->addElement('hidden', 'userid');
    $mform->addElement('text', 'deviceid', get_string('deviceid', 'turningtech'));
    $mform->addRule('deviceid', NULL, 'required');
    
    // radio buttons for "just this course" and "all courses"
    $radioarray = array();
    $radioarray[] = &MoodleQuickForm::createElement('radio', 'all_courses', '', get_string('justthiscourse','turningtech'), 0);
    $radioarray[] = &MoodleQuickForm::createElement('radio', 'all_courses', '', get_string('allcourses', 'turningtech'), 1);
    $mform->addGroup($radioarray, 'all_courses_options', get_string('appliesto', 'turningtech'), array(' '), false);
    
    $coursearray = array();
    $studentcourses = get_my_courses($this->_customdata['studentid']);
    foreach($studentcourses as $studentcourse) {
      $coursearray[] = &MoodleQuickForm::createElement('radio', 'courseid', '', $studentcourse->fullname, $studentcourse->id);
    }
    $mform->addGroup($coursearray, 'select_course', get_string('selectcourse','turningtech'), array(' '), false);
    // submit/delete buttons
    $this->add_action_buttons();
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/lib/moodleform#validation($data, $files)
   */
  function validation($data, $files) {
    $errors = parent::validation($data, $files);
    if(!$data['all_courses'] && empty($data['courseid'])) {
      $errors['select_course'] = get_string('mustselectcourse', 'turningtech');
      return $errors; // return here because continuing validation is pointless and causes errors
    }
    if(!empty($data['deviceid'])) {
      if(!TurningHelper::isDeviceIdValid($data['deviceid'])) {
        $errors['deviceid'] = get_string('deviceidinwrongformat', 'turningtech');
      }
      elseif(DeviceMap::isAlreadyInUse($data)) {
        $errors['deviceid'] = get_string('deviceidalreadyinuse', 'turningtech');
      }
    }
    
    return $errors;
  }
}
?>