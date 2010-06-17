<?php
include_once("config.php");

$dformat = "Ymd H:i:s";

// build an array of SQL commands to run
$sqls = array();
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'sessions;';
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'cache_text;';
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'cache_filters;';
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'course_sections;';
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'forum_posts;';
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'log;';

foreach($sqls as $sql) {
    if (execute_sql($sql, false)) {
        print date($dformat).' SUCCESS: '.$sql."\n";
    }
    else {
        print date($dformat).' FAILED: '.$sql."\n";
    }
}

?>
