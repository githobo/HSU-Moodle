<?php
/*
 * Created on Dec 3, 2007
 * by Ray Thompson
 *
 */
 require_once($CFG->libdir.'/formslib.php');
 require_once($CFG->dirroot.'/blocks/course_wizard/lib.php');

class coursewizard_showhide_form extends moodleform {
    
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
    function teacherform($adminflag=NULL) {
    	global $CFG, $SESSION, $USER, $COURSE;
    	//Create the form
        $mform =& $this->_form;
        //If admin retrieve teacher from teachersearch() else retrieve teacher from session
        //use teacher to get the list of courses to set visibility for.
        if($adminflag) {
        	$teacher = $adminflag;
        } else {
        	$teacher = $SESSION->teacher;	
        }
        $courses = get_courses_i_teach($teacher);
        
        // Commenting out this section because i've replaced the method in which this block
        // handles course identification. The reason I comment instead of remove is that while
        // I can find no reason why this code should be useful once course identification has
        // been fixed, I hesitate to alter permanently alter uncommented code, just in case
        // I have not fully understood what I am altering. Although confidence runs high on
        // this modification . Take a look
        /** 
		$shortnames = array();
		$duplicates = array();
        foreach ($courses as $course) {
			if (in_array($course->shortname, $shortnames)) {
				$duplicates['shortname'][] = $course->shortname;
				$duplicates['id'][] = $course->id;
			}
			$shortnames[] = $course->shortname;
		}
		if ($duplicates) {
			$mform->addElement('header', NULL, NULL, NULL);
			$mform->addElement('html', 'You must resolve these conflicts in order to use the show/hide course wizard. The listed courses have duplicate shortnames. A course shortname must be unique.');
			$mform->addElement('header', 'courseDuplicates', 'Duplicate Shortnames Found');
			for ($i = 0; $i < sizeof($duplicates['id']); $i++) {
			  	$mform->addElement('static', $duplicates['id'][$i], $duplicates['shortname'][$i], '<a href="'.$CFG->wwwroot.'/course/edit.php?id='.$duplicates['id'][$i].'"/>edit </a>');
				$mform->setHelpButton($duplicates['id'][$i], array('courseshortname', get_string('shortname')), true);
			}
			
			$buttonarray = array();
			$buttonarray[] =& $mform->createElement('submit', 'submitbutton', 'Refresh');		
			$buttonarray[] =& $mform->createElement('cancel', 'cancelbutton', get_string('donebutton','block_course_wizard'));
			$mform->addGroup($buttonarray, 'buttonarr', '', array(' '), false);
			$mform->closeHeaderBefore('buttonarr');
		} else{
		**/
		//headder
	        $mform->addElement('header', 'coursesHeader', get_string('showhide','block_course_wizard').' '.get_string('mycourses', 'block_course_wizard'));
		
		    //course checkboxes
		    foreach($courses as $course) {
		        //Set up hidden / visible icons.
		        if($course->visible) {
		            $status = '<img class="icon hide" src="'.$CFG->pixpath.'/i/hide.gif" alt="Visible" /></a>'."\n".'</li>';    
		        } else {
		            $status = '<img class="icon show" src="'.$CFG->pixpath.'/i/show.gif" alt="Hidden" /></a>'."\n".'</li>';    
		        }
		        // Object keys should not have spaces. Replacing spaces with underscores
		        $mform->addElement('advcheckbox', str_replace(' ', '_', $course->shortname).$course->id, $status, $course->shortname.': '.$course->fullname.' '.substr($course->idnumber, -5, 5));
		        //Set default checkbox value to true if the course is visible
		        if($course->visible) {
		        	$mform->setDefault(str_replace(' ', '_', $course->shortname).$course->id, '1');
		        } else {
		        	$mform->setDefault(str_replace(' ', '_', $course->shortname).$course->id, '0');
		        }
		    }
		    //hidden fields
		    $mform->addElement('hidden', 'id');
	        $mform->addElement('hidden','course', $COURSE->id);
	        $mform->addElement('hidden','t', $adminflag);
		    $mform->addElement('hidden', 'sesskey', sesskey());
		    //Buttons
		    $buttonarray = array();
		    $buttonarray[] =& $mform->createElement('submit', 'submitbutton', get_string('setcoursevisibility','block_course_wizard'));
		    $buttonarray[] =& $mform->createElement('reset', 'resetbutton', get_string('resetbutton', 'block_course_wizard'));
		    $buttonarray[] =& $mform->createElement('cancel', 'cancelbutton', get_string('donebutton','block_course_wizard'));
		    $mform->addGroup($buttonarray, 'buttonarr', '', array(' '), false);   
		    //Headder close
		    $mform->closeHeaderBefore('buttonarr');
		        
		    //Show/hide instructions
			$mform->addElement('header', null, null, null);
		    $mform->addElement('html', '<div align="center">'.get_string('showhideinstructions','block_course_wizard').'</div>');
	    //} commented end of code for above stated modification
    }
}
?>
