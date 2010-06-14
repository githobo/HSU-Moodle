<?php
include_once("config.php");

$dformat = "Ymd H:i:s";

// build an array of SQL commands to run
$sqls = array();
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'sessions;';
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'cache_text;';
$sqls[] = 'OPTIMIZE TABLE '.$CFG->prefix.'cache_filters;';

foreach($sqls as $sql) {
    if (execute_sql($sql, false)) {
        print date($dformat).' SUCCESS: '.$sql."\n";
    }
    else {
        print date($dformat).' FAILED: '.$sql."\n";
    }
}

?>
