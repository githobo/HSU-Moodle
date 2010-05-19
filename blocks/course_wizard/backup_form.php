<?php
 require_once($CFG->libdir.'/formslib.php');
 require_once($CFG->dirroot.'/blocks/course_wizard/lib.php');

class coursewizard_backup_form extends moodleform {
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
    	$mform->addElement('static', 'backupinstructions', '',get_string('backupinstructions', 'block_course_wizard'));
    	$mform->addElement('select', 'backupcourse', get_string('backupcourse', 'block_course_wizard').':', $mycourses);
    	$mform->addRule('backupcourse', get_string('err_required', 'block_course_wizard'), 'required', null, 'server');
    	
    	$mform->addElement('hidden','course', $COURSE->id);
        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->addElement('hidden','t', $adminflag);
    	$this->add_action_buttons(true, get_string('backupcourse','block_course_wizard'));
    	
	}
}
?>
