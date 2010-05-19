<?php

function user_role_allow() {
	
	global $USER, $CFG, $SESSION;
	
	$LIKE = sql_ilike();
					
	$rolesuser = get_recordset_sql("SELECT DISTINCT roleid
							  FROM ".$CFG->prefix."role_assignments
							  WHERE userid = ".$USER->id);
	
	$rolesallow = recordset_to_array(get_recordset_sql("SELECT * FROM ".$CFG->prefix."config
							       						WHERE name ".$LIKE." 'course_organize_role%'"));
							       
	while($user_role = rs_fetch_next_record($rolesuser)) {
		foreach($rolesallow as $allow_role) {
			if($user_role->roleid == $allow_role->value) {
				return true;
			}
		}
	}
	
	return false;
	
}

?>
