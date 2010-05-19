<?php
 require_once($CFG->libdir.'/formslib.php');
 require_once($CFG->dirroot.'/blocks/course_wizard/lib.php');

class coursewizard_restore_form extends moodleform {
	function definition() {
		global $COURSE, $CFG;
    	//Create the form
        $mform =& $this->_form;        
	}
	function courseselect($adminflag=NULL) {
		global $CFG, $SESSION, $USER, $COURSE;
		
		//Create the form
        $mform =& $this->_form;
        
        // If adminflag is not null it is the teacherid passed in by an admin
        if($adminflag) {
        	$teacher = $adminflag;
        } else {
        	$teacher = $SESSION->teacher;	
        }
        
        // Get all courses I teach
    	$acourses = get_courses_i_teach($teacher);
    	
    	$mycourses = array();
    	foreach ($acourses as $course) {
    		$mycourses[$course->id] = $course->shortname.': '.$course->fullname;
    	}
    	
    	// Form Elements
    	$mform->addElement('header','selectcourse', get_string('selectcourse', 'block_course_wizard'));
    	$mform->addElement('static', 'restoreinstructions', '',get_string('restoreinstructions', 'block_course_wizard'));
    	$mform->addElement('select', 'restorecourse', get_string('restorecourse', 'block_course_wizard').':', $mycourses);
    	$mform->addRule('restorecourse', get_string('err_required', 'block_course_wizard'), 'required', null, 'server');
    	$mform->addElement('static', 'uploadinstructions', '',get_string('uploadinstructions', 'block_course_wizard'));
    	$mform->addElement('checkbox', 'uploadbackup', get_string('uploadbackup', 'block_course_wizard').':');
    	
    	$mform->addElement('hidden','course', $COURSE->id);
        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->addElement('hidden','t', $adminflag);
    	$this->add_action_buttons(true, get_string('restorecourse','block_course_wizard'));
    	
	}	
}
?>
