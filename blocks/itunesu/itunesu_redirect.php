<?php // $Id: itunesu_redirect.php,v 1.2 2010/10/15 13:13:30 mchurch Exp $
    require_once('../../config.php');

    global $USER, $CFG;

    require_once($CFG->dirroot.'/lib/accesslib.php');
    require_once($CFG->dirroot.'/lib/weblib.php');
    require_once($CFG->dirroot.'/lib/moodlelib.php');
    require_once($CFG->dirroot.'/blocks/itunesu/itunes.php');
        
    if (!isloggedin()) {
      print_error('sessionerroruser', '' , $CFG->wwwroot);
    }
    
    $destination  = required_param('destination', SITEID, PARAM_INT); // iTunes U destination

    $name = fullname($USER);
    
    /* Create instance of the itunes class and initalized instance variables */
    $itunes = new itunes();
    $itunes->setUser($name, $USER->email, $USER->username, $USER->id);
    
    /* more work needs to be done with determining credentials */
    $itunes->setAdminCredential($CFG->block_itunesu_admincred);
    $itunes->setInstructorCredential($CFG->block_itunesu_insturctcred);
    $itunes->setStudentCredential($CFG->block_itunesu_studentcred);
    // Only give the user Admin credentials if they are an admin user in Moodle
    if (is_siteadmin($USER->id)) {
        $itunes->addAdminCredentials();
    // Give the user Instructor permissions if they teach any course in Moodle
    } else if (isteacherinanycourse()) {
        $itunes->addInstructorCredential(false);
    // Try to find the most recently-visited course and determine whether or not
    // the user is a student in it
    } else {
        $courseaccesses = array();
        $courseaccesses = $USER->currentcourseaccess;
        $timenow = time();
        $sortresult = arsort($courseaccesses);
        $isastudent = false;
        $lastcourseaccessed = array('courseid' => null, 'time' => null);
        foreach ($courseaccesses as $courseid => $accesstime) {
            if ($accesstime > $lastcourseaccessed['time']) {
                $lastcourseaccessed['time'] = $accesstime;
                $lastcourseaccessed['courseid'] = $courseid;
            }
            // Check to see if this course could be the most recently-visited course
            if ($timenow - $accesstime < LASTACCESS_UPDATE_SECS) {
                if (isstudent($courseid)) {
                    $isastudent = true;
                }
                break;
            }
        }
        // If they haven't accessed any courses in the last LASTACCESS_UPDATE_SECS seconds,
        // check the last class that they *have* accessed
        if (!$isastudent && !is_null($lastcourseaccessed['courseid'])) {
            $isastudent = isstudent($lastcourseaccessed['courseid']);
        }
        // Grant student access if the user is a student in at least one of the courses
        // that they have visited in the last LASTACCESS_UPDATE_SECS seconds, or if they
        // are a student in the last course they visited
        if ($isastudent) {
            $itunes->addStudentCredential(false);
        }
    }
    $itunes->setSiteDomain($CFG->block_itunesu_site_domain);
    $itunes->setSiteURL($CFG->block_itunesu_url);
    $itunes->setSharedSecret($CFG->block_itunesu_sharedsecret);
    $itunes->setDestination($destination);
    
    $itunes->invokeAction();
?>