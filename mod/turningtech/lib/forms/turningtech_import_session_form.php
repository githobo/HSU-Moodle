<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/lib/formslib.php');
/**
 * form that allows user to import session file
 * @author jacob
 *
 */
class turningtech_import_session_form extends moodleform {
  
  /**
   * (non-PHPdoc)
   * @see docroot/lib/moodleform#definition()
   */
  function definition() {
    $mform =& $this->_form;
    
    $mform->addElement('header','turningtechimportheader',get_string('importformtitle','turningtech'));
    $mform->addElement('text','assignment_title', get_string('assignmenttitle','turningtech'));
    $this->set_upload_manager(new upload_manager('session_file'));
    $mform->addElement('file','session_file',get_string('filetoimport','turningtech'));
    $mform->addElement('checkbox','override',get_string('overrideallexisting','turningtech'));
    $mform->addRule(array('assignment_title', 'session_file'), null, 'required');
    // add submit/cancel buttons
    $this->add_action_buttons();
  }
  
  /**
   * (non-PHPdoc)
   * @see docroot/lib/moodleform#validation($data, $files)
   */
  function validation($data, $files) {
    // TODO: what kind of validation needs to be done?
  }
}
?>