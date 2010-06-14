<?php
require_once("../../config.php");
      require_login();
      $rs = get_recordset_sql("select id from mdl_course where idnumber='20082020506' "); 
     while ($course = rs_fetch_next_record($rs)) {
        if (can_delete_course($course->id)) {
            delete_course($course->id);
            fix_course_sortorder(); //update course count in catagories
        }
     }
?>
