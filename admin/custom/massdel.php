<?php

    $starttime = microtime();

/// The following is a hack necessary to allow this script to work well 
/// from the command line.

    define('FULLME', 'cron');


/// Do not set moodle cookie because we do not need it here, it is better to emulate session
    $nomoodlecookie = true;

/// The current directory in PHP version 4.3.0 and above isn't necessarily the
/// directory of the script when run from the command line. The require_once()
/// would fail, so we'll have to chdir()

    if (!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'][0])) {
        chdir(dirname($_SERVER['argv'][0]));
    }

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->libdir.'/adminlib.php');

/// extra safety
    @session_write_close();

/// emulate normal session
    $SESSION = new object();
    $USER = get_admin();      /// Temporarily, to provide environment for this script

/// ignore admins timezone, language and locale - use site deafult instead!
    $USER->timezone = $CFG->timezone;
    $USER->lang = '';
    $USER->theme = '';
    course_setup(SITEID);

/// send mime type and encoding
    if (check_browser_version('MSIE')) {
        //ugly IE hack to work around downloading instead of viewing
        @header('Content-Type: text/html; charset=utf-8');
        echo "<xmp>"; //<pre> is not good enough for us here
    } else {
        //send proper plaintext header
        @header('Content-Type: text/plain; charset=utf-8');
    }

/// no more headers and buffers
    while(@ob_end_flush());



     $rs = get_recordset_sql("select id from mdl_course where idnumber like '2006%' or idnumber like '2005%' or idnumber like '200720%' or idnumber like '200730%' or idnumber like '2003%' or idnumber like '2004%' or idnumber like '2002%' or idnumber like '2001%' or idnumber like '1991%' or idnumber like '1992%'"); 
     while ($course = rs_fetch_next_record($rs)) {
            delete_course($course->id);
	}
            fix_course_sortorder(); //update course count in catagories
?>
