<?php 

///  For a given post, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");
    require_once($CFG->libdir.'/tablelib.php');

    $id   = required_param('id', PARAM_INT);
    $f    = required_param('f', PARAM_INT);        // Forum ID

    if (! $forum = get_record('forum', 'id', $f)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record('course', 'id', $forum->course)) {
        error("Course ID was incorrect");
    }

    if (! $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
/// Print header.    
	$strforums = get_string("modulenameplural", "forum");
	$viewposters_str = get_string('viewposters','forum');
    $navigation = "<a href=\"index.php?id=$course->id\">$strforums</a> ->
				   <a href=\"view.php?f=$f\">".format_string($forum->name,true)."</a> -> $viewposters_str";

    print_header_simple(format_string($forum->name), "",
                 "$navigation ", "", "", true, "", navmenu($course, $cm));
    
    require_capability('mod/forum:viewposters',$context);

    $rows = array();
    if (isset($f)) {
    	// As it stands viewposters is
		$userposts = get_records_sql(
			"SELECT userid,count(id) FROM {$CFG->prefix}forum_posts 
			 WHERE discussion IN 
			 (SELECT id FROM {$CFG->prefix}forum_discussions WHERE forum=$f) 
             GROUP BY userid;"); // ORDER BY count(id) DESC;
    
    	foreach ($userposts as $obj) {
    		$arr = (array)$obj;
    		$u = get_record('user','id',$arr['userid']);
    		$posts = $arr['count(id)'];
			$row = array("$u->firstname $u->lastname",$posts);
			$rows[] = $row;
    	}
    }
    
	$main = new flexible_table('main');
	$main->define_columns(array('User','Posts'));
	$main->define_headers(array('User','Posts'));
	$main->collapsible(false);
	$main->set_attribute('width', '75%');
	$main->set_attribute('border', '1');
	$main->set_attribute('cellpadding', '4');
	$main->set_attribute('cellspacing', '3');
	$main->set_attribute('align', 'center');
	$main->setup();
	
	foreach ($rows as $row) {
		$main->add_data($row);
	}
	
	print_heading('View Posters');
	$main->print_html();
    print_footer($course);
?>
