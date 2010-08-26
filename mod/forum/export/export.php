<?php
	require_once('lib.php');
	
	$forumid = required_param('f', PARAM_INT);
	$action = required_param('action', PARAM_ACTION);
	$sortorder = required_param('sort', PARAM_INT);
	$userid = optional_param('uid', 0, PARAM_INT);
	
	if (!$forum = get_record('forum', 'id', $forumid)) {
		print_error('Couldn\'t find the forum', 'forum');
	}
	
	if (!$cm = get_coursemodule_from_instance('forum', $forumid)) {
		print_error('Course module incorrect');
	}
	
	if (!$course = get_record('course', 'id', $cm->course)) {
		print_error('Course incorrect');
	}

	$context = get_context_instance(CONTEXT_MODULE, $cm->id);
	require_capability('mod/forum:viewposters', $context);
	
	$navigation = build_navigation(get_string('export', 'forum'), $cm);
	
	if ($form = data_submitted()) {
		//form submitted
		
		//determine the sort order
		switch ($sortorder) { 
	        case FORUM_SORT_FIRSTNAME:
	            $forumsort = "u.firstname";
	            break;
	        case FORUM_SORT_LASTNAME:
	            $forumsort = "u.lastname";
	            break;
	        case FORUM_SORT_OLDCREATED:
	            $forumsort = "p.created ASC";
	            break;
	        case FORUM_SORT_NEWCREATED:
	            $forumsort = "p.created DESC";
	            break;
	        case FORUM_SORT_NEWESTREPLY: 
	            // fall through
	        default:
	            $forumsort = "d.timemodified DESC";
	    }
	    
	    $posts = forum_export_get_posts($forumid, $cm, $userid, $forumsort);

	    forum_export_download($posts, $forum, $course, $action);
		
	} else {
		//form wasn't submitted let's set it up

		$users = array();
		if (empty($forum->anonymous)) {
			$users = forum_export_get_forum_posters($forumid);
		}
		
		print_header(get_string('exportforum', 'forum'), get_string('exportforum', 'forum'), $navigation);
		
		print_container_start(true, 'forumexport');
		
		print_heading(get_string('exportforum', 'forum'), 'center');
		
		$options = array();
		if (!empty($users)) {
			$options[0] = get_string('allusers', 'forum');
			foreach($users as $user) {
				$options[$user->id] = fullname($user);
			}
			popup_form("export.php?f=$forumid&amp;sort=$sortorder&amp;action=$action&amp;uid=", $options, 'userselect', $userid, '', '', '', false, 'self', get_string('chooseuser', 'forum'));
			echo '<br /><br />';
		}
		
		$buttonstring = get_string('download', 'forum', $action);
		if ($action == 'print') {
			$buttonstring = get_string('print', 'forum');
			$url = '/mod/forum/export/print.php?f='.$forumid.'&amp;sort='.$sortorder.'&amp;action='.$action.'&amp;uid='.$userid;
			button_to_popup_window($url, 'export', $buttonstring);
		} else {
			$buttonoptions = array('f' => $forumid, 'action' => $action, 'sort' => $sortorder, 'uid' => $userid);
			print_single_button('export.php', $buttonoptions, $buttonstring, 'post');	
		}
		
		
		print_container_end();
		
		print_footer($course);
	}
	    
?>