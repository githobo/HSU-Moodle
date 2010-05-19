<?php // $Id: crosslist.php, Author: Jeff Graham
/* 
This script will "crosslist" courses by using a meta course. The admins can choose
to crosslist courses for a specific instructor, which will direct them to the same
screen that instructor would see if they logged in. Instructors will see a list of 
all of the courses they teach. In the top course will be ALL courses including meta
courses. This is what will be the parent course they can also choose to create a new
course to "enroll" the other courses into. In the bottom section they will see a list
of all non-meta courses this is a mult-select box use 'ctrl' or 'command' aka 'Apple'
click to select multiple courses that will either be enrolled into the parent or into
the new course. If the teacher chooses a course as a parent course then that course is 
backed up and restored into a new course then all of the child courses are enrolled into 
the newly created course. This is useful if an instructor has already started content 
development. If no parent course is created, a new one is created to enroll the selected 
child courses into.


*/
require_once('../../config.php');
global $CFG, $USER, $COURSE, $SESSION;
require_once($CFG->dirroot.'/backup/backup_scheduled.php');
require_once($CFG->dirroot.'/backup/lib.php');
require_once($CFG->dirroot.'/backup/backuplib.php');
require_once($CFG->dirroot.'/backup/restorelib.php');
require_once($CFG->libdir."/tablelib.php");
require_once($CFG->libdir."/xmlize.php");
require_once($CFG->libdir."/datalib.php");
require_once("crosslist_form.php");
require_once("teachersearch_form.php");
include_once("$CFG->dirroot/blocks/course_wizard/lib.php");
$course = optional_param('course', 0, PARAM_INT);
$teacher = optional_param('t', 0, PARAM_INT);
$data = optional_param('data', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$parent = optional_param('parent', 0, PARAM_INT);
$c = optional_param('c', 0, PARAM_INT);
$crosslist = optional_param('cl',0, PARAM_INT);
// optional_param can't handle HTML arrays... boo!
if (!empty($_REQUEST['children'])) {
    $temp = $_REQUEST['children'];
}
else {
    $temp = '';
}
$children = array();
if (!empty($temp)) {
    foreach($temp as $child) {
        $temp = clean_param($child, PARAM_INT);
        if ($parent != $temp) {
        // only add if  it is not the same
            $children[] = $temp;
        }
        else {
        // may be used to check if the child was set as the parent
            $childisparent = true;
        }
    }
}
require_login();
$course = get_record('course', 'id', $course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);

$doanything = has_capability('moodle/site:doanything', $context, $USER->id);

if(!$doanything && !check_my_courses_for_capability($USER, 'moodle/course:update', true)) {
    error('This page is for admins or teachers only!');
}

if(!$doanything) {
    $teacher = $USER->id;
}

if (!$site = get_site()) {
    redirect("index.php");
}

if ($doanything && !$teacher) {
//save the teacher we're looking at for this session.
    $mform = new coursewizard_teachersearch_form();
    $mform->teachersearch();
    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot);
    } else if ($fromform=$mform->get_data()) {
            print_header("$course->shortname: ".get_string('crosslistcourses','block_course_wizard'), $course->fullname,
                build_navigation(get_string('crosslistcourses','block_course_wizard')), $mform->focus());

            // show a list of instructors w/search to
            $s = $fromform->searchfield;

            if(!empty($s)) {
                $search = ' WHERE u.username = \''.$s.'\'';
                $sql = "SELECT DISTINCT u.id, u.lastname, u.firstname FROM {$CFG->prefix}user u $search ORDER by u.lastname, u.firstname LIMIT 0, 100";

                $users = get_records_sql($sql);
                if (!empty($users)) {
                    foreach ($users as $user) {
                    //If user can update at least 1 of their courses, they are an instructor.
                        if (check_my_courses_for_capability($user, 'moodle/course:update')) {
                            $teachers = $user;
                        }
                    }
                    if(!$teachers) {
                        $mform = new coursewizard_teachersearch_form();
                        $mform->teachersearch('none');
                        $mform->display();
                    } else {
                        $teacher = $teachers->id;
                        redirect($CFG->wwwroot.'/blocks/course_wizard/crosslist.php?course='.$course->id.'&t='.$teacher, 'Fetching courses...', 0);
                    }
                } else {
                    $mform = new coursewizard_teachersearch_form();
                    $mform->teachersearch('notfound');
                    $mform->display();
                }
            }
            print_footer($course);
        } else {
        // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
        // or on the first display of the form.
        //setup strings for heading
            print_header("$course->shortname: ".get_string('crosslistcourses','block_course_wizard'), $course->fullname,
                build_navigation(get_string('crosslistcourses', 'block_course_wizard')), $mform->focus());
            //notice use of $mform->focus() above which puts the cursor
            //in the first form field or the first field with an error.

            //call to print_heading_with_help or print_heading? then :

            //put data you want to fill out in the form into array $toform here then :
            $toform = array();
            $mform->set_data($toform);
            $mform->display();
            print_footer($course);
        }
} else if ($doanything || $teacher) {
        if (!$doanything) {
            $SESSION->teacher = $teacher;
        }

        $mform = new coursewizard_crosslist_form();
        if ($doanything) {
            $mform->courseselect($teacher);
        } else {
            $mform->courseselect();
        }
        if ($mform->is_cancelled()) {
            redirect($CFG->wwwroot);
        } else if ($fromform=$mform->get_data()) {
                print_header("$course->shortname: ".get_string('crosslistcourses','block_course_wizard'), $course->fullname,
                    build_navigation(get_string('crosslistcourses', 'block_course_wizard')), $mform->focus());

                if ($fromform->selectparent==0) {
                    $newcourseid = crosslist_courses($fromform->selectchild, $fromform->t, null, $fromform->selectcat);
                    redirect($CFG->wwwroot.'/course/edit.php?id='.$newcourseid,'Returning to parent course.',0);
                } else {
                // Let's make sure we aren't grabbing old restore information
                    if (isset($SESSION->course_header)) {
                        unset ($SESSION->course_header);
                    }
                    if (isset($SESSION->info)) {
                        unset ($SESSION->info);
                    }
                    if (isset($SESSION->backupprefs)) {
                        unset ($SESSION->backupprefs);
                    }
                    if (isset($SESSION->restore)) {
                        unset ($SESSION->restore);
                    }
                    if (isset($SESSION->import_preferences)) {
                        unset ($SESSION->import_preferences);
                    }

                    // If this form has already been executed
                    if (empty($SESSION->cancontinue)) {
                        error("Multiple restore execution not allowed!");
                    }

                    // Let's grab the course information
                    $pcourse = get_record('course', 'id', $fromform->selectparent);

                    // Build the course preferences to prepare it for backup
                    print 'Configuring course...';
                    $prefs = wizard_backup_course_configure($pcourse);
                    print 'Done<br />';

                    // Execute the backup using the course preferences
                    print 'Backing up course...';
                    schedule_backup_course_execute($prefs);
                    print 'Done<br />';

                    // Restore the backup we just created
                    print 'Restoring content of backup into course...<br />';
                    print '(This may take some time depending on the size of your course) ...';
                    $backuppath = $CFG->dataroot.'/'.$fromform->selectparent.'/backupdata/'.$prefs->backup_name;
                    $oldsummary = wizard_import_backup_file_silently($backuppath,$pcourse->id, $fromform->selectcat);
                    print 'Done<br />';

                    // Crosslist the courses
                    print 'Crosslisting into a new course...';
                    // Grab the new course id by searching the database for the unique summary we created
                    $newcourseid = get_record_select('course', "summary = 'meta$pcourse->id'", 'id');
                    // Re-insert the summary
                    $status = array('id'=> $newcourseid->id, 'summary'=>$oldsummary);
                    update_record('course', $status);
                    $parentcourseid = crosslist_courses($fromform->selectchild, $fromform->t, $newcourseid->id);
                    print 'Done';

                    // Were done. Let's clean up and unset the $SESSION structures
                    if (isset($SESSION->course_header)) {
                        unset ($SESSION->course_header);
                    }
                    if (isset($SESSION->info)) {
                        unset ($SESSION->info);
                    }
                    if (isset($SESSION->backupprefs)) {
                        unset ($SESSION->backupprefs);
                    }
                    if (isset($SESSION->restore)) {
                        unset ($SESSION->restore);
                    }
                    if (isset($SESSION->import_preferences)) {
                        unset ($SESSION->import_preferences);
                    }
                    if (isset($SESSION->cancontinue)) {
                        unset($SESSION->cancontinue);
                    }

                    print_continue($CFG->wwwroot.'/course/edit.php?id='.$newcourseid->id);
                }
                print_footer($course);
            } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            //setup strings for heading
                print_header("$course->shortname: ".get_string('crosslistcourses','block_course_wizard'), $course->fullname,
                    build_navigation(get_string('crosslistcourses', 'block_course_wizard')), $mform->focus());
                //notice use of $mform->focus() above which puts the cursor
                //in the first form field or the first field with an error.

                //call to print_heading_with_help or print_heading? then :
                //set a variable to make sure that this form is only executed once
                $SESSION->cancontinue = true;
                //put data you want to fill out in the form into array $toform here then :
                $toform = array();
                $mform->set_data($toform);
                $mform->display();
                print_footer($course);
            }
    }
?>

