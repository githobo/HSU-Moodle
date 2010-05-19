<?php
	
	global $CFG, $USER, $COURSE;

	require_once('../../config.php');
	require_once($CFG->dirroot.'/mod/forum/lib.php');
	
	$site = get_site();

	print_header(strip_tags($site->fullname), $site->fullname,
			    build_navigation(get_string('manageusers', 'block_course_organize')), '',
			    '<meta name="description" content="'. s(strip_tags($site->summary)) .'">', true, '', '');
	
	$context = get_context_instance(CONTEXT_SYSTEM);
	
	if(!has_capability('moodle/site:doanything', $context)) {
		
		print_error('notadmin', 'block_course_organize');
		
	} else if($frm = data_submitted()) {
		
		if(!record_exists('config', 'name', 'course_organize_users')) {
			$coconfig = new object();
			$coconfig->name = 'course_organize_users';
			$coconfig->value = '';
			insert_record('config', $coconfig);
		} else {
			$coconfig = get_record('config', 'name', 'course_organize_users');
		}
		
		$LIKE = sql_ilike();
		$deleteroles = "name ".$LIKE." 'course_organize_role%'";
		delete_records_select('config', $deleteroles);
		
		if($frm->users == 'sitewide') {
			
			if($coconfig = get_record('config', 'name', 'course_organize_users')) {
				$coconfig->value = 'sitewide';
				if(!(update_record('config',$coconfig))) {
					print_error('updatefailed', 'block_course_organize');
				}
			} else {
				$coconfig->value = 'sitewide';
				if(!(insert_record('config', $coconfig))) {
					print_error('insertfailed', 'block_course_organize');
				}
			}
			
		} else if($frm->users == 'roles') {
			
			if($frm->setroles) {
				count($frm->role) > 1 ? $usersql = explode(' OR roleid = ', $frm->role) : $usersql = ' '.$frm->role[0].' ';
				$sql = "SELECT DISTINCT userid FROM ".$CFG->prefix."role_assignments
						WHERE userid NOT IN
						    (SELECT userid 
							 FROM ".$CFG->prefix."block_course_organize)
						AND (roleid = ".$usersql.")";
				$userids = get_records_sql($sql);
				$total = count($userids);
				foreach($userids as $userid) {
					$newuser = new object();
					$newuser->userid = $userid->userid;
					$newuser->classorder = "";
					if(!(insert_record('block_course_organize', $newuser))) {
						add_to_log($COURSE->id, 'courseorganize', 'adduserfail');
					}
				}
				$coconfig->value = 'select';
				if(!(update_record('config', $coconfig))) {
					print_error('updatefailed', 'block_course_organize');
				}
			} else {
				$total = count($frm->role);
				for($x = 0; $x < $total; $x++) {
					$roleconfig->name = 'course_organize_role'.($x + 1);
					$roleconfig->value = $frm->role[$x];
					if(!insert_record('config', $roleconfig)) {
						add_to_log($COURSE->id, 'courseorganize', 'addrolefail');
					}
				}
				$coconfig->value = 'roles';
				if(!(update_record('config', $coconfig))) {
					print_error('error', 'block_course_organize');
				}
			}
			
		} else {
			
			$coconfig->value = 'select';
			if(!update_record('config', $coconfig)) {
				add_to_log($COURSE->id, 'courseorganize', 'updatefail');
			}
			
		}
		
		redirect($CFG->wwwroot, get_string('savesuccess', 'block_course_organize'));
					
	} else {
		
		$sql = "SELECT * FROM ".$CFG->prefix."role
			    WHERE shortname <> 'admin' AND shortname <> 'guest'
				ORDER BY sortorder ASC"; // Wouldn't need a guest organizing courses and the admin never gets to.
			    
		if($roles = array_values(get_records_sql($sql))) {
			$numroles = count($roles);
			$role_cols = ceil($numroles / 4);
		} else {
			$roles = 0;
			$role_cols = 0;
			$numroles = 0;
		}
		
		$coconfig = new object();
		
		if($coconfig->users = get_field('config', 'value', 'name', 'course_organize_users')) {
			if($coconfig->users === 'roles') {
				if(!($coconfig->roles = get_records('config', 'name', 'course_organize_roles'))) {
					$coconfig->users = 'select';
				}
			}
		} else {
			$coconfig->users = 'select';
		}
		
		print_simple_box_start('center');
		include('manageroles.html');
		print_simple_box_end();
		
		print_footer();
	
	}
			
?>
