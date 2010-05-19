<?php
/*
 * Created on Dec 3, 2007
 * by Ray Thompson
 *
 */
 require_once($CFG->libdir.'/formslib.php');
 require_once($CFG->dirroot.'/blocks/course_wizard/lib.php');

class coursewizard_teachersearch_form extends moodleform {
    
    function definition() {
    	global $COURSE, $CFG;
    	//Create the form
        $mform =& $this->_form;
        
    }
    function teachersearch($notfound=NULL) {
    	global $COURSE, $CFG;
    	//Create the form
        $mform =& $this->_form;
        
        $mform->addElement('header','specifyuser', get_string('specifyuser', 'block_course_wizard'));
        $mform->addElement('text', 'searchfield', get_string('searchfields', 'block_course_wizard'));
        $mform->addRule('searchfield', get_string('err_required', 'block_course_wizard'), 'required', null, 'server');
        // @notfound - User not found
        // @none - No editable courses found
        if ($notfound=='notfound') {
        	$mform->addElement('static', 'usernotfound', '',get_string('usernotfound', 'block_course_wizard'));
        }
        if ($notfound=='none') {
        	$mform->addElement('static', 'nocoursefound', '',get_string('nocoursefound', 'block_course_wizard'));
        }
        $mform->addElement('hidden','course', $COURSE->id);
        $mform->addElement('hidden', 'sesskey', sesskey());
        $this->add_action_buttons(true, get_string('showuser','block_course_wizard'));
    }
}
?>
