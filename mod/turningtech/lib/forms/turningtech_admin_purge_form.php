<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/lib/formslib.php');

/**
 * 
 * @author jacob
 *
 */
class turningtech_admin_purge_form extends moodleform {
  
  /**
   * 
   * @return unknown_type
   */
  function definition() {
    $mform =& $this->_form;
    $mform->addElement('header','turningtechadminpurgeheader',get_string('adminpurgeheader','turningtech'));
    $mform->addElement('static', 'description', get_string('instructions', 'turningtech'), get_string('purgecourseinstructions','turningtech'));
    $mform->addElement('checkbox', 'confirm', get_string('awareofdangers','turningtech'));
    $mform->addRule('confirm', get_string('youmustconfirm','turningtech'), 'required');

    $this->add_action_buttons();
  }
  
  /**
   * 
   * @param $data
   * @param $files
   * @return unknown_type
   */
  function validation($data, $files) {
    $errors = parent::validation($data, $files);
  }
}
?>