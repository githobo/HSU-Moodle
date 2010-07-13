<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/lib/formslib.php');

/**
 * provides a form that allows students to enter their responseware username and password
 * to get their device ID
 * @author jacob
 *
 */
class turningtech_responseware_form extends moodleform {
  
  function definition() {
    $mform =& $this->_form;
    
    $mform->addElement('header','responsewareheader',get_string('responsewareheadertext','turningtech'));
    $link = "<a href='" . TurningHelper::getResponseWareUrl('forgotpassword') . "'>" . get_string('forgotpassword','turningtech') . "</a>";
    //$mform->addElement('static','createaccountlink', '', $link);
    $mform->addElement('text','username', get_string('responsewareuserid','turningtech'));
    $mform->addElement('password','password',get_string('responsewarepassword','turningtech'));
    $mform->addElement('static','forgotpasswordlink', '', $link);
    $mform->addElement('submit','submitbutton',get_string('register','turningtech'));
    
  }
  
  function validation($data, $files) {
    $errors = parent::validation($data, $files);
    if(empty($data['username'])) {
      $errors['username'] = get_string('mustprovideid', 'turningtech');
    }
    if(empty($data['password'])) {
      $errors['password'] = get_string('mustprovidepassword','turningtech');
    }
    return $errors;
  }
}
?>