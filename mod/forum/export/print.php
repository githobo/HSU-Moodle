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

	print_header(get_string('exportforum', 'forum'), '', '', '');
	
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
	
	forum_export_print_posts($posts, $forum, $course);
	
	echo '<script type="text/javascript">
	<!--
	
	var da = (document.all) ? 1 : 0;
	var pr = (window.print) ? 1 : 0;
	var mac = (navigator.userAgent.indexOf("Mac") != -1);
	
	if (window.addEventListener) {
	    window.addEventListener(\'load\', printWin, false);
	} else if (window.attachEvent) {
	    window.attachEvent(\'onload\', printWin);
	} else if (window.onload != null) {
	    var oldOnLoad = window.onload;
	    window.onload = function(e)
	    {
	        oldOnLoad(e);
	        printWin();
	    };
	} else {
	    window.onload = printWin;
	}
	
	function printWin()
	{
	    if (pr) {
	        // NS4+, IE5+
	        window.print();
	    } else if (!mac) {
	        // IE3 and IE4 on PC
	        VBprintWin();
	    } else {
	        // everything else
	        handle_error();
	    }
	}
	
	window.onerror = handle_error;
	window.onafterprint = function() {window.close()}
	
	function handle_error()
	{
	    window.alert(\'Your web browser does not support this function. Press control + P to print.\');
	    return true;
	}
	
	if (!pr && !mac) {
	    if (da) {
	        // This must be IE4 or greater
	        wbvers = "8856F961-340A-11D0-A96B-00C04FD705A2";
	    } else {
	        // this must be IE3.x
	        wbvers = "EAB22AC3-30C1-11CF-A7EB-0000C05BAE0B";
	    }
	
	    document.write("<OBJECT ID=\"WB\" WIDTH=\"0\" HEIGHT=\"0\" CLASSID=\"CLSID:");
	    document.write(wbvers + "\"> </OBJECT>");
	}
	
	// -->
	</script>';
	
	print_footer('none');
?>