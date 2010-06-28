<?php
// this file is intended to make it easier for teachers to figure out the admin settings
global $USER, $CFG;
require_once("../../../config.php");
require_once("$CFG->dirroot/course/lib.php");

$id = required_param('id', PARAM_INT);  // course id
$action = optional_param('action', 'mcp', PARAM_ALPHA);
require_login();

if (! $course = get_record("course", "id", $id)) {
    error("Course ID was incorrect");
}
$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('block/course_menu:viewcontrols', $coursecontext);

print_header("$course->fullname", "$course->fullname", build_navigation("Control Panel"));

//check capabilities to see which links to add
$editcourse = has_capability('moodle/course:update', $coursecontext);
$canemail = has_capability('block/quickmail:cansend', $coursecontext);
$managefiles = has_capability('moodle/course:managefiles', $coursecontext);
$viewreports = has_capability('moodle/site:viewreports', $coursecontext);
$viewpart = has_capability('moodle/course:viewparticipants', $coursecontext);
$mnggroup = has_capability('moodle/course:managegroups', $coursecontext);
$canassign = has_capability('moodle/role:assign', $coursecontext);
$canbackup = has_capability('moodle/site:backup', $coursecontext);
$canrestore = has_capability('moodle/site:restore', $coursecontext);
$canimport = has_capability('moodle/site:import', $coursecontext);
$viewscales = has_capability('moodle/course:managescales', $coursecontext);

//make the table rows
if ($editcourse) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/settings.gif"  alt="settings" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/course/edit.php?id='.$course->id.'">'.get_string("editcoursesettings").'</a>',
                           get_string("editcoursesettingsdesc", 'block_course_menu')
                     );
}
if ((is_dir($CFG->dirroot.'/blocks/quickmail')) && $canemail) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/email.gif" alt="quickmail" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/blocks/quickmail/email.php?id='.$course->id.'&instanceid='.$coursecontext->instanceid.'">'.get_string('emailstudents', 'block_course_menu').'</a>',
                           get_string('emaildesc', 'block_course_menu')
                     );
}
if ($managefiles) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/files.gif"  alt="course files" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/files/index.php?id='.$course->id.'">'.get_string('files').'</a>',
                           get_string('filesdesc', 'block_course_menu')
                     );
}
if (!empty($CFG->block_course_menu_passwordchange_url)) {
    $rows[] = array('<img src="../icons/userpwd.gif" alt="change password" align="texttop" class="icon"/>',
                           '<a href="'.$CFG->block_course_menu_passwordchange_url.'">'.get_string("password").'</a>',
                           get_string('passworddesc','block_course_menu')
                     );
}
if ($viewreports) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/stats.gif" alt="course access logs" align="texttop" class="icon"/>',
                           '<a href="'.$CFG->wwwroot.'/course/report.php?id='.$course->id.'">'.get_string("reports").'</a>',
                           get_string('logsdesc', 'block_course_menu')
                     );
}
if ($canassign) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/roles.gif" alt="roles" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/admin/roles/assign.php?contextid='.$coursecontext->id.'">'.get_string('assignroles','role').'</a>',
                           get_string('rolesdesc', 'block_course_menu')
                     );
}
if ($viewpart) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/users.gif" alt="participants" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'">'.get_string('participants').'</a>',
                           get_string('participantsdesc', 'block_course_menu')
                     );
}
if ($mnggroup) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/group.gif" alt="groups" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/group/index.php?id='.$course->id.'">'.get_string("groups").'</a>',
                           get_string('groupsdesc','block_course_menu')
                     );
}
if ($canbackup) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/backup.gif" alt="backup" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/backup/backup.php?id='.$course->id.'">'.get_string("backup").'</a>',
                           get_string('backupdesc', 'block_course_menu')
                     );
}
if ($canrestore) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/restore.gif"  alt="restore" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/files/index.php?id='.$course->id.'&wdir=/backupdata">'.get_string("restore").'</a>',
                           get_string('restoredesc', 'block_course_menu')
                     );
}
if ($canimport) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/restore.gif"  alt="restore" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/course/import.php?id='.$course->id.'">'.get_string('importdata').'</a>',
                           get_string('activitybrdesc', 'block_course_menu')
                     );
}
if ($viewscales) {
    $rows[] = array('<img src="'.$CFG->pixpath.'/i/scales.gif" alt= "grading scales" align="texttop" class="icon" />',
                           '<a href="'.$CFG->wwwroot.'/course/scales.php?id='.$course->id.'">'.get_string('scales').'</a>',
                           get_string('scalesdesc', 'block_course_menu')
                     );
}
$rows[] = array('<img src="'.$CFG->pixpath.'/i/guest.gif" alt= "edit profile" align="texttop" class="icon" />',
              '<a href="'.$CFG->wwwroot."/user/edit.php?id=".$USER->id.'&course='.$course->id.'">'.get_string('editmyprofile').'</a>',
              get_string('profiledesc', 'block_course_menu')
              );
              
//set up the table

require_once("$CFG->libdir/tablelib.php");

$main = new flexible_table('main');
$main->collapsible(false);
$main->set_attribute('width', '80%');
$main->set_attribute('border', '1');
$main->set_attribute('cellpadding', '4');
$main->set_attribute('cellspacing', '3');
$main->set_attribute('align', 'center');
$main->define_columns(array('icon', 'link', 'desc'));
$main->define_headers(NULL);
$main->setup();
foreach($rows as $row) {
    $main->add_data($row);
}

$main->print_html();
/*if (has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id))) {
    $tabs = array();
    $tabrows = array();

    $tabrows[] = new tabobject('mcp', "?action=mcp&amp;id=$course->id", get_string('main_control_panel', 'block_course_menu'));
    $tabrows[] = new tabobject('um', "?action=um&amp;id=$course->id", get_string('user_management', 'block_course_menu'));
    $tabrows[] = new tabobject('br', "?action=br&amp;id=$course->id", get_string('backup_restore', 'block_course_menu'));
    $tabrows[] = new tabobject('oc', "?action=oc&amp;id=$course->id", get_string('other_controls', 'block_course_menu'));
    if (!empty($CFG->block_course_menu_teachermanual_url) || !empty($CFG->block_course_menu_studentmanual_url)) {
        $tabrows[] = new tabobject('mm', "?action=mm&amp;id=$course->id", get_string('moodle_manuals', 'block_course_menu'));
    }
    $tabs[] = $tabrows;

    print_tabs($tabs, $action);
   
    if ($action != 'sc') {
        print '<table border="1" cellpadding="4" cellspacing="3" align="center" class="controls" width="80%">';
    }
    switch($action) {
        case 'mcp':
            include('mcp.html');
            break;
        case 'um':
            include('um.html');
            break;
        case 'br':
            include('br.html');
            break;
        case 'oc':
            include('oc.html');
            break;
        case 'mm':
            if (!empty($CFG->block_course_menu_teachermanual_url) || !empty($CFG->block_course_menu_studentmanual_url)) {
                include('mm.html');
            }
            else {
                // no manual location set default to regular control panel
                include('mcp.html');
            }
            break;
        case 'sc':
            include('studentcontrols.html');
            break;
    }

    if ($action != 'sc') {
        print '</table>';
    }
}
else {
   // students
   if (has_capability('moodle/course:view', get_context_instance(CONTEXT_COURSE, $course->id))){
       require("studentcontrols.html");
   }
   else { 
       print "Guests can't view this page";
   }
}*/

print_footer($course);

?>