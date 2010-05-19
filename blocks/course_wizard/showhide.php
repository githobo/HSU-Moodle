<?php
require_once("../../config.php");
require_once("showhide_form.php");
require_once("teachersearch_form.php");
include_once("$CFG->dirroot/blocks/course_wizard/lib.php");
global $CFG, $SESSION;
$course = optional_param('course', 0, PARAM_INT);
$teacher = optional_param('t', 0, PARAM_INT);
$search = optional_param('s','', PARAM_ALPHANUM);
$s = $search;
$showhide = optional_param('sh',0, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);

if(empty($SESSION->visiblecourses)) {
    $SESSION->visiblecourses = array();
}
if(empty($SESSION->hiddencourses)) {
    $SESSION->hiddencourses = array();
}
if(empty($SESSION->teacher)) {
    $SESSION->teacher = 0;
}


// optional_param can't handle HTML arrays... boo!
if (!empty($_REQUEST['courses'])) {
    $temp = $_REQUEST['courses'];
}
else {
    $temp = '';
}
$courses = array();
if (!empty($temp)) {
    foreach($temp as $c) {
        $temp = clean_param($c, PARAM_INT);
        $courses[] = $temp;
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

if ($data = data_submitted()) {
    if (!confirm_sesskey($data->sesskey)) {
        error(get_string('confirmsesskeybad', 'error'));
    }
}

if ($showhide) {

// process data here
    $acourses = get_courses_i_teach($SESSION->teacher);

    $all_courses = array();
    if (!empty($acourses)) {
        foreach($acourses as $id => $c) {
            $all_courses[] = $id;
        }
    }

    // $courses is the visible courses and $hcourses is the hidden courses
    $hcourses = array_diff($all_courses,$courses);
    $uc->visible = 1;
    if (!empty($courses)) {
        foreach($courses as $c) {

            $uc->id = $c;

            unset($SESSION->hiddencourses[$c]);
            $SESSION->visiblecourses[$c] = $c;
            error($c);
        //update_record('course', $uc);
        }
    }

    $uc->visible = 0;
    if (!empty($hcourses)) {
        foreach($hcourses as $c) {

            $uc->id = $c;

            unset($SESSION->visiblecourses[$c]);
            $SESSION->hiddencourses[$c] = $c;
            error($c);

            update_record('course', $uc);
        }
    }

    //This is to clear the user's courses from the cache so that when they return to the main
    //page their courses will be refreshed to show visibility changes.
    unset($USER->mycourses[$doanything]);
    if (!$doanything) {
        redirect($CFG->wwwroot, get_string('visibilityset', 'block_course_wizard'));
    }

}
if ($doanything && !$teacher) {
//save the teacher we're looking at for this session.
    $mform = new coursewizard_teachersearch_form();
    $mform->teachersearch();
    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot);
    } else if ($fromform=$mform->get_data()) {
            print_header("$course->shortname: ".get_string('showhidecourses','block_course_wizard'), $course->fullname,
                build_navigation(get_string('showhidecourses', 'block_course_wizard')), $mform->focus());

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
                        redirect($CFG->wwwroot.'/blocks/course_wizard/showhide.php?course='.$course->id.'&t='.$teacher, 'Fetching courses...', 0);
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
            print_header("$course->shortname: ".get_string('showhidecourses','block_course_wizard'), $course->fullname,
                build_navigation(get_string('showhidecourses', 'block_course_wizard')), $mform->focus());
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

        $courses = get_courses_i_teach($teacher);
        $mform = new coursewizard_showhide_form();
        if ($doanything) {
            $mform->teacherform($teacher);
        } else {
            $mform->teacherform();
        }
        if ($mform->is_cancelled()) {
            redirect($CFG->wwwroot);
        } else if ($fromform=$mform->get_data()) {
                print_header("$course->shortname: ".get_string('showhidecourses','block_course_wizard'), $course->fullname,
                    build_navigation(get_string('showhidecourses', 'block_course_wizard')), $mform->focus());
                foreach($courses as $course) {
                    $visible = $course->visible;
                    // Object keys should not have spaces. Replacing spaces with underscores
                    $switch = $fromform->{str_replace(' ', '_',$course->shortname).$course->id};
                    if ($visible && confirm_sesskey() && (!$switch)) {
                        set_field('course','visible', '0', 'id', $course->id);
                    }

                    if ((!$visible) && confirm_sesskey() && $switch) {
                        set_field('course','visible', '1', 'id', $course->id);
                    }
                }

                $mform = new coursewizard_showhide_form();
                if ($doanything) {
                    $mform->teacherform($teacher);
                } else {
                    $mform->teacherform();
                }
                $mform->display();
                print_footer($course);
            } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            //setup strings for heading
                print_header("$course->shortname: ".get_string('showhidecourses','block_course_wizard'), $course->fullname,
                    build_navigation(get_string('showhidecourses', 'block_course_wizard')), $mform->focus());
                //notice use of $mform->focus() above which puts the cursor
                //in the first form field or the first field with an error.

                //call to print_heading_with_help or print_heading? then :

                //put data you want to fill out in the form into array $toform here then :
                $toform = array();
                $mform->set_data($toform);
                $mform->display();
                print_footer($course);
            }

    }

?>
