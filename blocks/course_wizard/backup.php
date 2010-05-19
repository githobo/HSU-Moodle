<?php
/*
 * Created on Jun 3, 2008
 *
 * This script will backup a course.
 * The admins can choose to backup courses for a specific 
 * instructor, which will direct them to the same screen that the 
 * instructor would see if they logged in. Instructors will see a list 
 * of all of the courses they teach.
 * 
 */
 
	require_once('../../config.php');
    global $CFG, $USER, $COURSE, $SESSION;
    require_once("backup_form.php");
    require_once("teachersearch_form.php");
    include_once("$CFG->dirroot/blocks/course_wizard/lib.php");
	$course = optional_param('course', 0, PARAM_INT);
    $teacher = optional_param('t', 0, PARAM_INT);
    $data = optional_param('data', 0, PARAM_INT);
    $action = optional_param('action', '', PARAM_ALPHA);
    $parent = optional_param('parent', 0, PARAM_INT);
    $c = optional_param('c', 0, PARAM_INT);
    
    require_login();
    $course = get_record('course', 'id', $course);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $doanything = has_capability('moodle/site:doanything', $context, $USER->id);
    
    if(!$doanything && !check_my_courses_for_capability($USER, 'moodle/course:update', true)) {
        error('This page is for admins or teachers only!');
    }

    if(!$doanything) {
    	$teacher = $USER->id;
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }

    if ($doanything && !$teacher) {
	   	//save the teacher we're looking at for this session.
	    $mform = new coursewizard_teachersearch_form();
	    $mform->teachersearch();
	    if ($mform->is_cancelled()){
		    redirect($CFG->wwwroot);
		} else if ($fromform=$mform->get_data()){
			print_header("$course->shortname: ".get_string('backupcourse','block_course_wizard'), $course->fullname,
	             build_navigation(get_string('backupcourse','block_course_wizard')), $mform->focus());
	
			// show a list of instructors w/search to
	        $s = $fromform->searchfield;
	      
	        if(!empty($s)) {
	            $search = ' WHERE u.username = \''.$s.'\'';
	            $sql = "SELECT DISTINCT u.id, u.lastname, u.firstname FROM {$CFG->prefix}user u $search ORDER by u.lastname, u.firstname LIMIT 0, 100";
	
	            $users = get_records_sql($sql);
			    if (!empty($users)) { 
		            	foreach ($users as $user) {
						    //If user can update at least 1 of their courses, they are an instructor.       
						    if (check_my_courses_for_capability($user, 'moodle/course:update')) {
								$teachers = $user;    
						    }         
						}
						if(!$teachers) {
							$mform = new coursewizard_teachersearch_form();
        					$mform->teachersearch('none');
		    				$mform->display();
						} else {
							$teacher = $teachers->id;
							redirect($CFG->wwwroot.'/blocks/course_wizard/backup.php?course='.$course->id.'&t='.$teacher, 'Fetching courses...', 0);
						}
		        } else {
		    		$mform = new coursewizard_teachersearch_form();
        			$mform->teachersearch('notfound');
		    		$mform->display();
		        }
	        }
			print_footer($course);
		} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.
		    //setup strings for heading
		    print_header("$course->shortname: ".get_string('backupcourse','block_course_wizard'), $course->fullname,
                 build_navigation(get_string('backupcourse', 'block_course_wizard')), $mform->focus());
		    //notice use of $mform->focus() above which puts the cursor 
		    //in the first form field or the first field with an error.
		
		    //call to print_heading_with_help or print_heading? then :
		    
		    //put data you want to fill out in the form into array $toform here then :
			$toform = array();
		    $mform->set_data($toform);
		    $mform->display();
		    print_footer($course);
		}
    } else if ($doanything || $teacher) {
    	if (!$doanything) {
    		$SESSION->teacher = $teacher;	
    	}
    	
    	$mform = new coursewizard_backup_form();
    	if ($doanything) {
        	$mform->courseselect($teacher);
    	} else {
    		$mform->courseselect();
    	}
        if ($mform->is_cancelled()){
		    redirect($CFG->wwwroot);
		} else if ($fromform=$mform->get_data()) {
			print_header("$course->shortname: ".get_string('backupcourse','block_course_wizard'), $course->fullname,
                 build_navigation(get_string('backupcourse', 'block_course_wizard')), $mform->focus());
			
			redirect($CFG->wwwroot.'/backup/backup.php?id='.$fromform->backupcourse,'Initializing backup process...',0);
			print_object($fromform);

			print_footer($course);
		} else {
		// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		// or on the first display of the form.
		    //setup strings for heading
		    print_header("$course->shortname: ".get_string('backupcourse','block_course_wizard'), $course->fullname,
                 build_navigation(get_string('backupcourse', 'block_course_wizard')), $mform->focus());
		    //notice use of $mform->focus() above which puts the cursor 
		    //in the first form field or the first field with an error.
		
		    //call to print_heading_with_help or print_heading? then :
		    //put data you want to fill out in the form into array $toform here then :
			$toform = array();
		    $mform->set_data($toform);
		    $mform->display();
		    print_footer($course);
		}
    }
?>
