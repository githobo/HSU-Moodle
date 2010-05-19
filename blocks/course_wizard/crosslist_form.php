<?php
 require_once($CFG->libdir.'/formslib.php');
 require_once($CFG->dirroot.'/blocks/course_wizard/lib.php');

class coursewizard_crosslist_form extends moodleform {
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
        
        // Get categories
        $categories = get_categories();
        $cat = array();
        foreach($categories as $category) {
        	$cat[$category->id] = $category->name;
        }
        
        // Get meta courses and non-metacourses
    	$acourses = get_courses_i_teach($teacher);
    	$nonmetacourses = get_courses_i_teach($teacher, false);
    	
    	$parent = array();
    	$parent[] = 'Create a New Course';
    	foreach ($acourses as $course) {
    		$parent[$course->id] = $course->shortname.': '.$course->fullname;
    	}
    	$children = array();
    	foreach ($nonmetacourses as $course) {
    		$children[$course->id] = $course->shortname.': '.$course->fullname;
    	}
    	
    	// Form Elements
    	$mform->addElement('header','parentcourse', get_string('parentcourse', 'block_course_wizard'));
    	$mform->addElement('static', 'crosslistcategory', '',get_string('crosslistcategory', 'block_course_wizard'));
    	$mform->addElement('select', 'selectcat', get_string('selectcat', 'block_course_wizard'), $cat);
    	$mform->addElement('static', 'crosslistpinstructions', '',get_string('crosslistpinstructions', 'block_course_wizard'));
    	$mform->addElement('select', 'selectparent', get_string('selectparent', 'block_course_wizard'), $parent);
    	
    	$mform->addElement('header','childcourses', get_string('childcourses', 'block_course_wizard'));
    	$mform->addElement('static', 'crosslistcinstructions', '',get_string('crosslistcinstructions', 'block_course_wizard'));
    	$select =& $mform->addElement('select', 'selectchild', get_string('selectchild', 'block_course_wizard'), $children);
    	$select->setMultiple(true);
    	$mform->addRule('selectchild', get_string('err_required', 'block_course_wizard'), 'required', null, 'server');
    	$mform->addElement('hidden','course', $COURSE->id);
        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->addElement('hidden','t', $adminflag);
    	$this->add_action_buttons(true, get_string('crosslist','block_course_wizard'));
    	
	}	
}
?>
