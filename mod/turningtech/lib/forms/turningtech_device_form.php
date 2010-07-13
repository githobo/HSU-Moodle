<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/lib/formslib.php');

/**
 * form class for creating/editing DeviceMaps
 * @author jacob
 *
 */
class turningtech_device_form extends moodleform {
  
  /**
   * (non-PHPdoc)
   * @see docroot/lib/moodleform#definition()
   */
  function definition() {
    $mform =& $this->_form;
    $mform->addElement('header', 'turningtechdevicemapheader', get_string('deviceid', 'turningtech'));
    $mform->addElement('hidden', 'devicemapid');
    $mform->addElement('hidden', 'userid');
    $mform->addElement('hidden', 'courseid');
    $mform->addElement('text', 'deviceid', get_string('deviceid', 'turningtech'));
    $mform->addRule('deviceid', NULL, 'required');
    
    // radio buttons for "just this course" and "all courses"
    $radioarray = array();
    $radioarray[] = &MoodleQuickForm::createElement('radio', 'all_courses', '', get_string('justthiscourse','turningtech'), 0);
    $radioarray[] = &MoodleQuickForm::createElement('radio', 'all_courses', '', get_string('allcourses', 'turningtech'), 1);
    $mform->addGroup($radioarray, 'all_courses_options', get_string('appliesto', 'turningtech'), array(' '), false);
    
    // submit/delete buttons
    //$this->add_action_buttons();
    $mform->addElement('submit','submitbutton', get_string('register','turningtech'));
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/lib/moodleform#validation($data, $files)
   */
  function validation($data, $files) {
    $errors = parent::validation($data, $files);
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