<?php

	require_once('../../config.php');
		
	global $CFG, $COURSE, $USER;
	
	$userid = required_param('userid', PARAM_INT);
	
	$context = get_context_instance(CONTEXT_SYSTEM);
	
	if($userid != $USER->id) {
		print_error(get_string('usernomatch', 'block_course_organize'));
	}
	
	$site = get_site();
	
	require_login();
	
	print_header(strip_tags($site->fullname), $site->fullname,
			    build_navigation(get_string('organizecourses', 'block_course_organize')), '',
			    '<meta name="description" content="'. s(strip_tags($site->summary)) .'">', true, '', '');
			    
	if(!record_exists('block_course_organize', 'userid', $userid)) {
		$allow = false;
		if($config = get_field('config', 'value', 'name', 'course_organize_users')) {
			if($config === 'roles') $allow = user_allow_role();
			if($config === 'sitewide') $allow = true;
			if($allow) {
				$adduser = new object();
				$adduser->userid = $USER->id;
				
				if(!insert_record('block_course_organize', $adduser)) {
					print_error(get_string('noadd', 'block_course_organize'));
				}
			}
		} else {
			print_error(get_string('nouser', 'block_course_organize'));
		}
	}
	
	if($frm = data_submitted()) {
        
		$co_data = get_record('block_course_organize', 'userid', $userid);
		$co_data->classorder = base64_encode(serialize($frm->classOrder));
		
		if(!update_record('block_course_organize', $co_data)) {
			print_error(get_string('updateerror', 'block_course_organize'));
		}

		redirect($CFG->wwwroot, get_string('savesuccess', 'block_course_organize'));
		
	} else {
		
		$courses = get_my_courses($userid);
		if($courseorder = unserialize(base64_decode(get_field('block_course_organize', 'classorder', 'userid', $USER->id)))) {
			$neworder = array();
			$coursetally = count($courses);
			
			for($i = 0; $i < $coursetally; $i++) {
				if($courses[$courseorder[$i]]) {
					array_push($neworder, $courses[$courseorder[$i]]);
				}
			}
			
			foreach($courses as $course) {
				if(!in_array($course->id, $courseorder)) {
					array_push($neworder, $course);
				}
			}		
			
			$courses = $neworder;
			unset($neworder);
		}
		print_simple_box_start('center');
		include("course_organize.html");
		print_simple_box_end();
		
	}
	
	print_footer($COURSE);
	
?>
