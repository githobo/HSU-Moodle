<?php

	require_once('../../config.php');
	require_once($CFG->dirroot.'/mod/forum/lib.php');
	
	global $CFG, $USER, $COURSE;
	
	$remove = optional_param('remove', PARAM_INT);
	$add = optional_param('add', PARAM_INT);
	$searchtext = optional_param('searchtext', PARAM_RAW);
	
	define("MAX_USERS_SHOW", 100);
	
	$strsearchresults = get_string('searchresults');
	
	$site = get_site();

	print_header(strip_tags($site->fullname), $site->fullname,
			    build_navigation(get_string('manageusers', 'block_course_organize')), '',
			    '<meta name="description" content="'. s(strip_tags($site->summary)) .'">', true, '', '');
	
	$context = get_context_instance(CONTEXT_SYSTEM);
	
	if(!has_capability('moodle/site:doanything', $context)) {
		print_error(get_string('notadmin', 'block_course_organize'));
	} else {
		
		if($frm = data_submitted()) {
		
			if($add && !empty($frm->availableusers)) {
				foreach($frm->availableusers as $adduser) {
					if(!$adduser = clean_param($adduser, PARAM_INT)) {
						continue;
					} 
					
					$organize = new object();
					$organize->userid = $adduser;
					if(!record_exists('block_course_organize', 'userid', $organize->userid)) {
						if(!insert_record('block_course_organize', $organize)) {
							print_error(get_string('newuserfail', 'block_course_organize', $adduser));
						}
					} else {
						add_to_log($COURSE->id, 'courseorganize', 'adduserexists');
					}
					
				}
				
			}else if($remove && !empty($frm->currentusers)) {
				foreach($frm->currentusers as $removeuser) {
					if(!$removeuser = clean_param($removeuser, PARAM_INT)) {
						continue;
					}
					
					if(delete_records('block_course_organize', 'userid', $removeuser)) {
						add_to_log($COURSE->id, 'courseorganize', 'removeuser');
					}
				}
			}
			
			if($frm->searchtype == 'current') {
				$searchcurrent = true;
				$searchavailable = false;
			} else if($frm->searchtype == 'available') {
				$searchavailable = true;
				$searchcurrent = false;
			} else {
				$searchcurrent = false;
				$searchavailable = false;
			}
			
		} else {
			$searchcurrent = false;
			$searchavailable = false;
		}
					
		
		if(!empty($searchtext)) $searchtext = trim($searchtext);
		
		if ($searchtext !== '') {   
	        $LIKE      = sql_ilike();
	        $FULLNAME  = sql_fullname();
	
	        $searchavailable ? $availablesql = " AND ($FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%') "
	        				 : $availablesql = " ";
													
			$searchcurrent ? $currentsql = " AND ($FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%') "
						   : $currentsql = " ";
	    }	

		$select  = "username <> 'guest' AND deleted = 0 AND confirmed = 1";
		
		$availableusers = get_recordset_sql('SELECT id, firstname, lastname, email
											 FROM '.$CFG->prefix.'user
											 WHERE '.$select.' '.$availablesql.'
											 ORDER BY lastname, firstname');
		
		$countavailable = $availableusers->_numOfRows;
		
		$select = " co.userid = u.id ";
		
		$currentusers = get_recordset_sql('SELECT * 
										   FROM '.$CFG->prefix.'block_course_organize co, '.$CFG->prefix.'user u
										   WHERE '.$select.' '.$currentsql.'
										   ORDER BY u.firstname');
		
		$countcurrent = $currentusers->_numOfRows;
		
		print_simple_box_start('center');
		include("manageusers.html");
		print_simple_box_end();
	}	
	print_footer($COURSE);
?>
