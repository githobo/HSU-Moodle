<?php
if(!empty($_SERVER['GATEWAY_INTERFACE'])){
    error_log("should not be called from apache!");
    exit;
}
error_reporting(E_ALL);

require_once(dirname(dirname(dirname(__FILE__))).'/config.php'); // global moodle config file.
global $CFG;

$_TERM[20] = 'Spring';
$_TERM[30] = 'Summer';
$_TERM[40] = 'Fall';

// ensure errors are well explained
$CFG->debug=10;

$cats=array();

$misc = get_record('course_categories', 'name', 'LDAP Staging');
$unsorted = get_records('course', 'category', $misc->id);
foreach($unsorted as $course) {
    if (strlen($course->idnumber) == 9) {   # new PS style term codes - 4 characters instead of 6 (missing 2nd and 6th zero)
        # will need to update in 2100 :)
        $year = substr($course->idnumber, 0, 1) . "0" . substr($course->idnumber, 1, 2);
        $term = substr($course->idnumber, 3, 1) . "0";
    } else {
        $year = substr($course->idnumber,0,4);
        $term = substr($course->idnumber,4,2);
    }
    $crn = substr($course->idnumber,-5,5);
    if (!empty($term) && !empty($year) && is_numeric($term) && is_numeric($year)) {
        $cat = get_record('course_categories','name', "$_TERM[$term] $year");
        if(empty($cat)) {
            $cat->name = "$_TERM[$term] $year";
            $cat->description = '';
            $cat->parent = 0;
            $cat->sortorder = 999;
            $cat->coursecount = 1;
            $cat->visible = 1;
            $cat->timemodified = 0;
            $catid = insert_record('course_categories', $cat);
            $cat->id = $catid;
        }
        if (!empty($cat->id)) {
            $course->category = $cat->id;

            // unset the fields that break inserts/updates
            unset($course->password);
            unset($course->fullname);
            unset($course->summary);
            unset($course->shortname);
            unset($course->idnumber);
            unset($course->modinfo);
            unset($course->teacher);
            unset($course->teachers);
            unset($course->student);
            unset($course->students);
            if (!array_key_exists($cat->id, $cats)) {
                $cats[$cat->id] = $cat;
            }
            update_record('course', $course);
        }
    }
}
fix_course_sortorder();

foreach($cats as $id => $cat) {
    $sql = "SELECT min(sortorder) as min, max(sortorder) as max from {$CFG->prefix}course WHERE category=$cat->id";
    $min_max = get_record_sql($sql);
    $courses = get_records('course', 'category', $cat->id);
    $ecat = get_record('course_categories', 'name', "Extended Education $cat->name", 'parent', $cat->id);
    if(empty($ecat)) {
        $ecat->name = "Extended Education $cat->name";
        $ecat->description = '';
        $ecat->parent = $cat->id;
        $ecat->sortorder = 999;
        $ecat->coursecount = 1;
        $ecat->visible = 1;
        $ecat->timemodified = 0;
        $ecatid = insert_record('course_categories', $ecat);
        $ecat->id = $ecatid;
    }
    // sort the courses, on shortname then fullname
    uasort($courses, 'sort_by_shortname');
    $i = $min_max->max;
    foreach($courses as $course) {
        $course->sortorder = $i;
        // unset the fields that break inserts/updates
        unset($course->password);
        unset($course->summary);
        unset($course->modinfo);
        unset($course->teacher);
        unset($course->teachers);
        unset($course->student);
        unset($course->students);
        if (strstr($course->shortname, 'EENC')){
            // extended ed course
            $course->category = $ecat->id;
        }

        // see if we need to adjust the fullname
        unset($parts);
        $parts = array();
        $parts = explode(' ',$course->fullname);
        // I think the following if should be enough to avoid reformating strings that don't need reformatting. (may need revisiting)
        // updated for how peoplesoft does course codes
        if (is_numeric($parts[2]) && is_numeric($parts[3]) && !empty($parts[4])) {
            $course->fullname = "$parts[0] $parts[1]:";
            for ($i = 4; $i < count($parts); $i++) {   //append the rest of the fullname
                $course->fullname .= " $parts[$i]";
            }
            $course->fullname = addslashes($course->fullname);
            $term = substr($parts[2], 3, 1) . "0";
            $course->shortname =  "$course->shortname ".substr($_TERM[$term],0,2).substr($parts[2], 1, 2).' '.$parts[3];
            $course->shortname = addslashes($course->shortname);
            unset($course->idnumber);
        }
        else {
            unset($course->fullname);
        }
        update_record('course',$course);
        $i++;
    }
    fix_course_sortorder();
}

function sort_by_shortname($x,$y)
{
    if (strnatcasecmp($x->shortname,$y->shortname) == 0) {
        return strnatcasecmp($x->fullname,$y->fullname);
    }
    else {
        return strnatcasecmp($x->shortname,$y->shortname);
    }
} 
?>
