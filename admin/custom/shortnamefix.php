<?php
    if(!empty($_SERVER['GATEWAY_INTERFACE'])){
        error_log("should not be called from apache!");
        exit;
    }
    error_reporting(E_ALL);

    require_once(dirname(dirname(dirname(__FILE__))).'/config.php'); // global moodle config file.
    global $CFG;

    // ensure errors are well explained
    $CFG->debug=10;
    /*  Created specifically for HSU to address: Bug #69
        Reason: Duplicate shortnames are not permitted by moodle.
        This script fixes the majority of the course shortname duplications that was created by the organize_course script. 
        The organize_course script was also modified to not cause this problem in the future.
        Less then 100 courses will still have duplicate shortnames that can be fixed manually. */
    $courses = get_records('course', NULL, NULL, NULL, 'id, shortname, idnumber, startdate');
    foreach($courses as $course) {
        //$course->shortname = $course->shortname .'1';
        if ($course->idnumber != NULL) {
        	if (strlen($course->idnumber) == 9) { 
		        $year = substr($course->idnumber, 0, 1) . "0" . substr($course->idnumber, 1, 2);
		        $term = substr($course->idnumber, 3, 1) . "0";
		    } else {
		        $year = substr($course->idnumber,0,4);
		        $term = substr($course->idnumber,4,2);
		    }
		    $crn = substr($course->idnumber,-5,5);
		    
		    $searchterms = array('Sp06', 'Sp07', 'Sp08', 'Sp09', 'Su06', 'Su07', 'Su08', 'Su09',
		    				'Fa06', 'Fa07', 'Fa08', 'Fa09', 'Spring', 'Summer', 'Fall', 'spring',
		    				'summer', 'fall', 'Spr ');
		    				
		    foreach($searchterms as $term) {
		    	if($where = strpos($course->shortname, $term)) {
		    		$course->shortname = substr_replace($course->shortname, $term.' '.$crn, $where);
		    	}
		    }
		    
		    $course->shortname = addslashes($course->shortname);
		    update_record('course', $course);
        }
    }
?>