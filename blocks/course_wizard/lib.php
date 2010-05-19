<?php
//============B L O C K _ C O U R S E _ W I Z A R D  F U N C T I O N S=====================
/*Checks if user has a specific $capability in his/her courses.
 * if $return is true, returns true when first $capability is found.
 * if $return is false, builds array of user's courses that they have $capability for.
 * Otherwise, returns false.
 * 
 *  @param array $user The user object to check.
 *  @param string $capability The capability to check for.
 *  @param boolean $return Flag for return type.
 *  @return mixed
 */
function check_my_courses_for_capability($user, $capability, $return=FALSE) {
    
    //Get all the user's courses
	$fields  = 'id, category, sortorder, shortname, fullname, idnumber, newsitems, teacher, teachers, student, students, guest, startdate, visible, cost, enrol, summary, groupmode';
    $courses = get_my_courses($user->id, NULL, $fields);
 
    if(!empty($courses)) { 
        $capabilityFoundIn = array();

        foreach ($courses as $course) {
            //Get the context of the course
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
         
            //Check if $user has $capability for $course. 
            if(has_capability($capability, $context, $user->id)) {
                if($return) {
                    //If capability found, we're done
                    return true;
                }
                else {
                    //Build an array of courses user has $capability in
                    $capabilityFoundIn[$course->id] = $course;
                }
            }   
        }
        if(!empty($capabilityFoundIn)) {
            return $capabilityFoundIn;
        }
        else {
            //Couldn't find $capability in user's courses.
            return false;
        }
    }
    else {
        //User is enrolled in no courses, therefore can not have $capability.
        return false;
    }    
}

//============S H O W / H I D E  F U N C T I O N S===========================
// This function gets a list of the courses that a user is the teacher the second parameter tells whether or not to include meta-courses
    function get_courses_i_teach($userid, $inc_meta=true) {
//DEBUGGING: new code follows:
        global $CFG;
	$fields  = 'id, category, sortorder, shortname, fullname, idnumber, newsitems, teacher, teachers, student, students, guest, startdate, visible, cost, enrol, summary, groupmode';
        $courses = get_my_courses($userid, NULL, $fields);
        
        $iteach = array();
        if($inc_meta) {
            foreach($courses as $course) {
                $context = get_context_instance(CONTEXT_COURSE, $course->id);
                if(has_capability('moodle/course:update', $context, $userid)) {
                        $iteach[$course->id] = $course;
                }
            }        
        }
        else {
        	$courseids = array();
        	foreach ($courses as $course) {
        		$courseids[] = $course->id;
        	}
        	$queryids = implode(",", $courseids);
        	// get_my_courses() doesn't grab the metacourse field so we have to edit core or create our own query
        	// but were only querying once because we know which courses the user teaches.
            $courses = get_records_select('course', "id IN (" . $queryids . ") ", null, 'id, category, sortorder, shortname, fullname, idnumber, newsitems, teacher, teachers, student, students, guest, startdate, visible, cost, enrol, summary, groupmode, groupmodeforce, metacourse');
            foreach($courses as $course) {
                if(empty($course->metacourse)) {
                    $context = get_context_instance(CONTEXT_COURSE, $course->id);
                    if(has_capability('moodle/course:update', $context, $userid)) {
                        $iteach[$course->id] = $course;
                    }
                }    
            }        
        }
        
        return $iteach;
/*DEBUGGING: original code follows: (requires outdated SQL tables)
        global $CFG;
        
        $sql = "SELECT t.course, 1 FROM {$CFG->prefix}user_teachers t, {$CFG->prefix}course c WHERE t.userid = $userid AND c.id = t.course";
        if (!$inc_meta) {
            $sql .= ' AND c.metacourse != 1 ';
        }
        $sql .= ' ORDER BY c.shortname, c.fullname ';
        //print "SQL $sql <br/>";
        if ($teachers = get_records_sql($sql)) { //('user_teachers', 'userid', $userid, '', 'id, course')) {
            foreach ($teachers as $teacher) {
                $course[$teacher->course] = $teacher->course;
            }
        }
        if (empty($course)) {
            return $course;
        }

        $courseids = implode(',', $course);

        return get_records_list('course', 'id', $courseids, 'shortname, fullname');
*/
    }

//============C R O S S L I S T   F U N C T I O N S======================

function course_wizard_crosslist($children, $teacher, $parent=0) {
    global $CFG;
    if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    }
    print get_string('crosslisting','block_course_wizard').'<br />';
    if ($parent !== 0) {
        $meta = crosslist_courses($children, $teacher, $parent);
        $meta = $parent;
    }
    else {
        if (!empty($children)) {
            $meta = crosslist_courses($children, $teacher);
        }
    }
    redirect($CFG->wwwroot.'/blocks/course_wizard/crosslist.php?action=rename&amp;meta='.$meta.'&amp;crosslist='.implode(',',$children));
}

/*
$crosslist is an array of idnumbers corresponding to courses that should be crosslisted
$parent may or may not be populated with an idnumber if it is then this course is assumed 
    to have content and will become the parent course, if the value is empty then a new
    meta course (parent) will be created and all of the crosslisted courses will be enrolled
$destinationcat is the category number where the course will be created. If left out the category
	will default to 1 during the new course creation process.
*/
function crosslist_courses($crosslist, $teacher, $parent='', $destinationcat=null) {
    global $USER;
    if (empty($parent)) {
        $default = get_record('course', 'id', $crosslist[0]);
        if (!empty($default)) {
            $coursevals->shortname = 'CL: '.$default->shortname;
            $fullname = array();
            foreach($crosslist as $cl) {
                // build the new fullname based off all the children, course and number and section
                $cl = get_record('course','id',$cl);
                $fullname[] = substr($cl->fullname,0,9).' '.substr($cl->fullname,-9);
            }
            $coursevals->fullname = implode(', ',$fullname);
            if($destinationcat) {
            	$coursevals->category = $destinationcat;
            }
        }
        else {
            $coursevals->shortname = "$USER->username: crosslist";
        }
        $meta = crosslist_create_course($coursevals);
    } else {
        $parent = get_record('course', 'id', $parent);
        $meta = $parent->id;
        unset($parent->modinfo);
        unset($parent->sortorder);
        unset($parent->password);
        unset($parent->summary);
        unset($parent->format);
        unset($parent->showgrades);
        unset($parent->newsitems);
        unset($parent->teacher);
        unset($parent->teachers);
        unset($parent->student);
        unset($parent->students);
        unset($parent->guest);
        unset($parent->startdate);
        unset($parent->enrolperiod);
        unset($parent->numsections);
        unset($parent->marker);
        unset($parent->maxbytes);
        unset($parent->showreports);
        unset($parent->visible);
        unset($parent->hiddensections);
        unset($parent->groupmode);
        unset($parent->groupmodeforce);
        unset($parent->lang);
        unset($parent->theme);
        unset($parent->cost);
        unset($parent->timecreated);
        unset($parent->timemodified);
		
		// php4 and php5 clone objects differently this function is a simple fix.
        $child = php4_clone($parent);
        $parent->idnumber = 'meta';
        $parent->metacourse = 1;
        
        //unset any fields we don't want to change for the parent course
        $category = $parent->category;
        unset($parent->category);
        unset($parent->fullname);
        unset($parent->shortname);
        if(!update_record('course', $parent)) {
            add_to_log(1,'crosslist', 'crosslist', 'crosslist.php', "Could not update parent course record id: $meta");
        }
        unset($child->id);
        $child->fullname = addslashes("$child->fullname: c");
        $child->shortname = addslashes("$child->shortname: c");
        unset($child->sortorder);
        $child->visible = 0;
        
        /*//Why is this needed? Maybe for a different aproach.
        if ($newcourse = crosslist_create_course($child, false)) {
            $crosslist[] = $newcourse;
        }
        else {
            add_to_log(1,'crosslist', 'crosslist', 'crosslist.php', 'Could not create child course');
        } */  
    }
    // $meta is the id for our metacourse... now we just need to enroll each entry in $crosslist
    foreach($crosslist as $cl) {
        // set the child courses to hidden & make sure they are in the same category
        $hidden->id = $cl;
        $hidden->visible = 0;
        //why do they need to be in same category? We don't want this.
        //$hidden->category = $category;
        update_record('course', $hidden);
        
        if (!add_to_metacourse ($meta, $cl)) {
            add_to_log(1,'crosslist', 'crosslist', 'crosslist.php', "Could not enroll id: $cl in meta: $id");
        }
    }
    return $meta;
}

/* creates a course, defaults recieved from LDAP course template
$coursevals as a template/shell course
$meta as a flag to determine if this is a meta course or not
*/
function crosslist_create_course($coursevals, $meta=true) {
    global $CFG;
    // override defaults with template course
    if(!empty($CFG->enrol_ldap_template)){
        $course = get_record("course", 'shortname', $CFG->enrol_ldap_template);
        $templateid = $course->id;
        unset($course->id); // so we are clear to reinsert the record
        unset($course->sortorder);
    }
    else {
       add_to_log(1,'crosslist', 'crosslist', 'crosslist.php', 'Could not find template course looking at $CFG->enrol_ldap_template');
       if (empty($coursevals)) {
           // exit out returning an invalid courseid
           add_to_log(1,'crosslist', 'crosslist', 'crosslist.php', 'Could not find template course and no course defaults supplied');
           return false;
       }
    }
    
    // override with required ext data
    if ($meta) {
        $course->metacourse = 1;
    }
    if (empty($coursevals->idnumber)) {
        $course->idnumber = 'meta';
    }
    else {
        $course->idnumber = $coursevals->idnumber;
    }
    
    if(!empty($coursevals->fullname)) {
        $course->fullname = addslashes($coursevals->fullname);
    }
    else {
        add_to_log(1,'crosslist', 'crosslist', 'crosslist.php', 'Creating new course and fullname not set');
        $course->fullname = 'crosslist';
    }
    
    if (!empty($coursevals->shortname)) {
        $course->shortname = addslashes($coursevals->shortname);
    }
    else {
        add_to_log(1, 'crosslist', 'crosslist', 'crosslist.php', 'Creating new course and shortname not set');
        $course->shortname = 'crosslist';
    }
    
    if (   empty($course->idnumber)
        || empty($course->fullname)
        || empty($course->shortname) ) { 
        // we are in trouble!
        add_to_log(1, 'crosslist', 'crosslist', 'crosslist.php', 'Cannot create requested course: missing required data');
        add_to_log(1, 'crosslist', 'crosslist', 'crosslist.php', var_export($course, true));
        return false;
    }

    if(!empty($coursevals->summary)){ // optional
        $course->summary   = addslashes($coursevals->summary);
    }
    if(!empty($coursevals->category)) { // optional ... but ensure it is set!
        $course->category   = $coursevals->category;
    } 
    if (empty($course->category)){ // must be avoided as it'll break moodle
        $course->category = 1; // the misc 'catch-all' category
    }

    // define the sortorder (yuck)
    $sort = get_record_sql('SELECT MAX(sortorder) AS max, 1 FROM ' . $CFG->prefix . 'course WHERE category=' . $course->category);
    $sort = $sort->max;
    $sort++;
    $course->sortorder = $sort; 

    // override with local data
    $course->startdate = time();
    $course->timecreated = time();
    $course->visible     = 1;

    // store it and log
    //print "about to craete new course<br/>";
    //print_object($course);
    if ($newcourseid = insert_record("course", $course)) {  // Set up new course
        if (!empty($templateid)) {
            // add the course section entries
            $sections = get_records('course_sections', 'course', $templateid);
            foreach ($sections as $section) {
                $section->course = $newcourseid;
                insert_record('course_sections', $section);
            }
            // add the blocks
            $blocks = get_records('block_instance', 'pageid', $templateid);

            foreach($blocks as $block) {
                $block->pageid = $newcourseid;
                unset($block->id);
                insert_record('block_instance', $block);
            }
        }
        else {
            add_to_log(1, 'crosslist', 'crosslist', 'crosslist.php', "Could not create sections for new course id: $course->idnumber since no template was set");
        }
        fix_course_sortorder(); 
        add_to_log($newcourseid, "course", "new", "view.php?id=$newcourseid", "crosslist course creation");


    } else {
        add_to_log(1, 'crosslist', 'crosslist', 'crosslist.php', 'Could not create new course for crosslist');
        return false;
    }
    return $newcourseid;   
}

/** php4 and php5 clone objects differently this function is a simple fix.
* $object the object to be cloned
* $meta as a flag to determine if this is a meta course or not
*	@param object $object the object to be cloned
*	@return object $object the cloned object
*/
function php4_clone($object) {
	if (version_compare(phpversion(), '5.0') < 0) {
		return $object;
	} else {
		return @clone($object);
	}
}

/** This function sets up a course to be backed up. The majority of this code
 * is copied from schedule_backup_course_configure. 
 * This code has been modified for the course_wizard block.
 * $course is the course object to be used
 * $starttime specifies when this should occur
 * 	@param object $course the course to be backed up
 * 	@return object $status the preferences for the backup
 */

function wizard_backup_course_configure($course,$starttime = 0) {  

    global $CFG;
    
    $status = true;

    schedule_backup_log($starttime,$course->id,"    checking parameters");

    //Check the required variable
    if (empty($course->id)) {
        $status = false;
    }
    // Get scheduled backup preferences
    $backup_config =  backup_get_config();
	
	// HSU: Include all modules
	$backup_config->backup_sche_modules = 1;
    
    //Checks backup_config pairs exist
    if ($status) {
        if (!isset($backup_config->backup_sche_modules)) {
            $backup_config->backup_sche_modules = 1;
        }
        if (!isset($backup_config->backup_sche_withuserdata)) {
            $backup_config->backup_sche_withuserdata = 1;
        }
        if (!isset($backup_config->backup_sche_metacourse)) {
            $backup_config->backup_sche_metacourse = 1;
        }
        if (!isset($backup_config->backup_sche_users)) {
            $backup_config->backup_sche_users = 1;
        }
        if (!isset($backup_config->backup_sche_logs)) {
            $backup_config->backup_sche_logs = 0;
        }
        if (!isset($backup_config->backup_sche_userfiles)) {
            $backup_config->backup_sche_userfiles = 1;
        }
        if (!isset($backup_config->backup_sche_coursefiles)) {
            $backup_config->backup_sche_coursefiles = 1;
        }
        if (!isset($backup_config->backup_sche_messages)) {
            $backup_config->backup_sche_messages = 0;
        }
        if (!isset($backup_config->backup_sche_active)) {
            $backup_config->backup_sche_active = 0;
        }
        if (!isset($backup_config->backup_sche_weekdays)) {
            $backup_config->backup_sche_weekdays = "0000000";
        }
        if (!isset($backup_config->backup_sche_hour)) {
            $backup_config->backup_sche_hour = 00;
        }
        if (!isset($backup_config->backup_sche_minute)) {
            $backup_config->backup_sche_minute = 00;
        }
        if (!isset($backup_config->backup_sche_destination)) {
            $backup_config->backup_sche_destination = "";
        }
        if (!isset($backup_config->backup_sche_keep)) {
            $backup_config->backup_sche_keep = 1;
        }
    }

    if ($status) {
       //Checks for the required files/functions to backup every mod
        //And check if there is data about it
        $count = 0;
        if ($allmods = get_records("modules") ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = "$CFG->dirroot/mod/$modname/backuplib.php";
                $modbackup = $modname."_backup_mods";
                $modcheckbackup = $modname."_check_backup_mods";
                if (file_exists($modfile)) {
                   include_once($modfile);
                   if (function_exists($modbackup) and function_exists($modcheckbackup)) {
                       $var = "exists_".$modname;
                       $$var = true;
                       $count++;
                       
                       // PENNY NOTES: I have moved from here to the closing brace inside 
                       // by two sets of ifs()
                       // to avoid the backup failing on a non existant backup.
                       // If the file/function/whatever doesn't exist, we don't want to set this
                       // this module in backup preferences at all.
                       //Check data
                       //Check module info
                       $var = "backup_".$modname;
                       if (!isset($$var)) {
                           $$var = $backup_config->backup_sche_modules;
                       }
                       //Now stores all the mods preferences into an array into preferences
                       $preferences->mods[$modname]->backup = $$var;

                       //Check include user info
                       $var = "backup_user_info_".$modname;
                       if (!isset($$var)) {
                           $$var = $backup_config->backup_sche_withuserdata;
                       }
                       //Now stores all the mods preferences into an array into preferences
                       $preferences->mods[$modname]->userinfo = $$var;
                       //And the name of the mod
                       $preferences->mods[$modname]->name = $modname;
                   }
                }
            }
        }

        // now set instances
        if ($coursemods = get_course_mods($course->id)) {
            foreach ($coursemods as $mod) {
                if (array_key_exists($mod->modname,$preferences->mods)) { // we are to backup this module
                    if (empty($preferences->mods[$mod->modname]->instances)) {
                        $preferences->mods[$mod->modname]->instances = array(); // avoid warnings
                    }
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->backup = $preferences->mods[$mod->modname]->backup;
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->userinfo = $preferences->mods[$mod->modname]->userinfo;
                    // there isn't really a nice way to do this...
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->name = get_field($mod->modname,'name','id',$mod->instance);
                }
            }
        }

        // finally, clean all the $preferences->mods[] not having instances. Nothing to backup about them
        foreach ($preferences->mods as $modname => $mod) {
            if (!isset($mod->instances)) {
                unset($preferences->mods[$modname]);
            }
        }
    }
    
	//HSU: The following options are set based upon the needs of HSU
	//TODO: Create a backup ad restore wizard which allows customization of this process
	$backup_config->backup_sche_users = 2;
	$backup_config->backup_sche_coursefiles = 1;
	$backup_config->backup_sche_metacourse = 1;
	
	
    //Convert other parameters
    if ($status) {
        $preferences->backup_metacourse = $backup_config->backup_sche_metacourse;
        $preferences->backup_users = $backup_config->backup_sche_users;
        $preferences->backup_logs = $backup_config->backup_sche_logs;
        $preferences->backup_user_files = $backup_config->backup_sche_userfiles;
        $preferences->backup_course_files = $backup_config->backup_sche_coursefiles;
        $preferences->backup_messages = $backup_config->backup_sche_messages;
        $preferences->backup_course = $course->id;
        $preferences->backup_destination = $backup_config->backup_sche_destination;
        $preferences->backup_keep = $backup_config->backup_sche_keep;
    }

    //Calculate the backup string
    if ($status) {
        schedule_backup_log($starttime,$course->id,"    calculating backup name");

        //Calculate the backup word
        //Take off some characters in the filename !!
        $takeoff = array(" ", ":", "/", "\\", "|");
        $backup_word = str_replace($takeoff,"_",moodle_strtolower(get_string("backupfilename")));
        //If non-translated, use "backup"
        if (substr($backup_word,0,1) == "[") {
            $backup_word= "backup";
        }

        //Calculate the date format string
        $backup_date_format = str_replace(" ","_",get_string("backupnameformat"));
        //If non-translated, use "%Y%m%d-%H%M"
        if (substr($backup_date_format,0,1) == "[") {
            $backup_date_format = "%%Y%%m%%d-%%H%%M";
        }

        //Calculate the shortname
        $backup_shortname = clean_filename($course->shortname);
        if (empty($backup_shortname) or $backup_shortname == '_' ) {
            $backup_shortname = $course->id;
        }

        //Calculate the final backup filename
        //The backup word
        $backup_name = $backup_word."-";
        //The shortname
        $backup_name .= moodle_strtolower($backup_shortname)."-";
        //The date format
        $backup_name .= userdate(time(),$backup_date_format,99,false);
        //The extension
        $backup_name .= ".zip";
        //And finally, clean everything
        $backup_name = clean_filename($backup_name);

        //Calculate the string to match the keep preference
        $keep_name = $backup_word."-";
        //The shortname
        $keep_name .= moodle_strtolower($backup_shortname)."-";
        //And finally, clean everything
        $keep_name = clean_filename($keep_name);

        $preferences->backup_name = $backup_name;
        $preferences->keep_name = $keep_name;
    }

    //Calculate the backup unique code to allow simultaneus backups (to define
    //the temp-directory name and records in backup temp tables
    if ($status) {
        $backup_unique_code = time();
        $preferences->backup_unique_code = $backup_unique_code;
    }

    //Calculate necesary info to backup modules
    if ($status) {
        schedule_backup_log($starttime,$course->id,"    calculating modules data");
        if ($allmods = get_records("modules") ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modbackup = $modname."_backup_mods";
                //If exists the lib & function
                $var = "exists_".$modname;
                if (isset($$var) && $$var) {
                    //Add hidden fields
                    $var = "backup_".$modname;
                    //Only if selected
                    if ($$var == 1) {
                        $var = "backup_user_info_".$modname;
                        //Call the check function to show more info
                        $modcheckbackup = $modname."_check_backup_mods";
                        schedule_backup_log($starttime,$course->id,"      $modname");
                        $modcheckbackup($course->id,$$var,$backup_unique_code);
                    }
                }
            }
        }
    }

    //Now calculate the users
    if ($status) {
        schedule_backup_log($starttime,$course->id,"    calculating users");
        //Decide about include users with messages, based on SITEID
        if ($preferences->backup_messages && $preferences->backup_course == SITEID) {
            $include_message_users = true;
        } else {
            $include_message_users = false;
        }
        user_check_backup($course->id,$backup_unique_code,$preferences->backup_users,$include_message_users);  
    }

    //Now calculate the logs
    if ($status) {
        if ($preferences->backup_logs) {
            schedule_backup_log($starttime,$course->id,"    calculating logs");
            log_check_backup($course->id);
        }
    }

    //Now calculate the userfiles
    if ($status) {
        if ($preferences->backup_user_files) {
            schedule_backup_log($starttime,$course->id,"    calculating user files");
            user_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }
 
    //Now calculate the coursefiles
    if ($status) {
       if ($preferences->backup_course_files) {
            schedule_backup_log($starttime,$course->id,"    calculating course files");
            course_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }

    //If everything is ok, return calculated preferences
    if ($status) {
        $status = $preferences;
    }

    return $status;
}

/** this function will restore an entire backup.zip into the specified course
     * using standard moodle backup/restore functions, but silently. Modified for 
     * course_wizard block.
     * @param string $pathtofile the absolute path to the backup file.
     * @param int $destinationcourse the course id to restore to.
     * @param int $destinationcat the category to restore to.
     * @param boolean $emptyfirst whether to delete all coursedata first.
     * @param boolean $userdata whether to include any userdata that may be in the backup file.
     */
function wizard_import_backup_file_silently($pathtofile,$destinationcourse, $destinationcat,$emptyfirst=false,$userdata=false) {
    global $CFG,$SESSION,$USER, $restore; // is there such a thing on cron? I guess so..

    if (empty($USER)) {
        $USER = get_admin();
        $USER->admin = 1; // not sure why, but this doesn't get set
    }

    define('RESTORE_SILENTLY',true); // don't output all the stuff to us.
        
    $debuginfo = 'import_backup_file_silently: ';
    $cleanupafter = false;
    $errorstr = ''; // passed by reference to restore_precheck to get errors from.

    if (!$course = get_record('course','id',$destinationcourse)) {
        mtrace($debuginfo.'Course with id $destinationcourse was not a valid course!');
        return false;
    }

    // first check we have a valid file.
    if (!file_exists($pathtofile) || !is_readable($pathtofile)) {
        mtrace($debuginfo.'File '.$pathtofile.' either didn\'t exist or wasn\'t readable');
        return false;
    }

    // now make sure it's a zip file
    require_once($CFG->dirroot.'/lib/filelib.php');
    $filename = substr($pathtofile,strrpos($pathtofile,'/')+1);
    $mimetype = mimeinfo("type", $filename);
    if ($mimetype != 'application/zip') {
        mtrace($debuginfo.'File '.$pathtofile.' was of wrong mimetype ('.$mimetype.')' );
        return false;
    }

    // restore_precheck wants this within dataroot, so lets put it there if it's not already..
    if (strstr($pathtofile,$CFG->dataroot) === false) {
        // first try and actually move it..
        if (!check_dir_exists($CFG->dataroot.'/temp/backup/',true)) {
            mtrace($debuginfo.'File '.$pathtofile.' outside of dataroot and couldn\'t move it! ');
            return false;
        }
        if (!copy($pathtofile,$CFG->dataroot.'/temp/backup/'.$filename)) {
            mtrace($debuginfo.'File '.$pathtofile.' outside of dataroot and couldn\'t move it! ');
            return false;
        } else {
            $pathtofile = 'temp/backup/'.$filename;
            $cleanupafter = true;
        }
    } else {
        // it is within dataroot, so take it off the path for restore_precheck.
        $pathtofile = substr($pathtofile,strlen($CFG->dataroot.'/'));
    }
    if (!backup_required_functions()) {
        mtrace($debuginfo.'Required function check failed (see backup_required_functions)');
        return false;
    }
    
    @ini_set("max_execution_time","3000");
    raise_memory_limit("192M");
    if (!$backup_unique_code = restore_precheck($destinationcourse,$pathtofile,$errorstr,true)) {
        mtrace($debuginfo.'Failed restore_precheck (error was '.$errorstr.')');
        return false;
    }
    
    // add on some extra stuff we need...
    $SESSION->restore->restoreto = 2;
    $SESSION->restore->course_id = 0; 
    $SESSION->restore->deleting = $emptyfirst;
    $SESSION->restore->user_files = 0;
    $SESSION->restore->restore_course_files = 1;
    $SESSION->restore->course_startdateoffset = 0;
    $SESSION->restore->metacourse = 1;
    $SESSION->restore->backup_version = $SESSION->info->backup_backup_version;
    $SESSION->restore->original_wwwroot = $SESSION->info->original_wwwroot;
    $SESSION->restore->messages = 0;
    $SESSION->restore->logs = 0;
    $SESSION->restore->backup_unique_code = $backup_unique_code;
    
    restore_setup_for_check($SESSION->restore,$backup_unique_code);

    
    
    // maybe we need users (defaults to 2 in restore_setup_for_check)
    if (!empty($userdata)) {
        $SESSION->restore->users = 1;
    }

    // we also need modules...
    if ($allmods = get_records("modules")) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            //Now check that we have that module info in the backup file
            if (isset($SESSION->info->mods[$modname]) && $SESSION->info->mods[$modname]->backup == "true") {
                $SESSION->restore->mods[$modname]->restore = true;
                $SESSION->restore->mods[$modname]->userinfo = $userdata;
            }
        }
    }
    
    /*------------ DEBUG Info ----------------------------
    print '<p style="color: red;">RESTORE...</p><br />';
    print_object($SESSION->restore);
    print '<p style="color: red;">RESTORE...info</p><br />';
    print_object($SESSION->info);
    print '<p style="color: red;">RESTORE...header</p><br />';
    print_object($SESSION->course_header);
    */
    
    
    //Hack to create somthing unique within the newly created course to find later
    $oldsummary = $SESSION->course_header->course_summary;
    
    
    $SESSION->course_header->course_summary = 'meta'.$SESSION->course_header->course_id;
    $SESSION->restore->restore_restorecatto = $destinationcat;
    $SESSION->course_header->course_idnumber = 'meta';
    
    if ($SESSION) {
        $info = $SESSION->info;
        $course_header = $SESSION->course_header;
        $restore = $SESSION->restore;
    }
    
    if (!$newid=restore_execute($restore,$info,$course_header,$errorstr)) {
        mtrace($debuginfo.'Failed restore_execute (error was '.$errorstr.')');
        return;
    }    
    return $oldsummary;
}

?>
