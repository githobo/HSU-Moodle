<?php
require_once("../../config.php");
      require_login();
     $rs = get_recordset_sql("select id from mdl_course where idnumber like '200530%'"); 
     while ($course = rs_fetch_next_record($rs)) {
        if (can_delete_course($course->id)) {
            delete_course($course->id);
            fix_course_sortorder(); //update course count in catagories
        }
     }
?>
