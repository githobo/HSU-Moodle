<?php
global $CFG;


class block_course_organize extends block_base {
	
	function init() {
		$this->title = get_string("courseorganize", "block_course_organize");
		$this->content_type = BLOCK_TYPE_TEXT;
		$this->version = 2007112119;
	}
	
	function has_config() {
		return false;
	}
	
	function instance_allow_multiple() {
		return false;
	}
	
	function hide_header() {
		return false;
	}
	
	function get_content() {
		
		global $CFG, $USER, $COURSE;
		
		include_once($CFG->dirroot.'/blocks/course_organize/lib.php');
		
		$context = get_context_instance(CONTEXT_SYSTEM);
		
		if($config = get_record("config", "name", "course_organize_users")) {
			
			$sitewide = false;
			$roles = false;
			$allow = false;
			
			if($config->value === 'sitewide') {
				$sitewide = true;
			} else if($config->value === 'roles') {
				$roles = true;
				$allow = user_role_allow();
			}
			
		} else {
			$config = new object();
			$config->name = 'course_organize_users';
			$config->value = 0;
			
			if(insert_record('config', $config)) {
				$sitewide = false;
				$allow = false;
			}
		}
		
		if((record_exists('block_course_organize', 'userid', $USER->id) || $sitewide || $allow) || has_capability('moodle/site:doanything', $context, $USER->id)) {
			$this->content = new stdClass;
 
            if(has_capability('moodle/site:doanything', $context, $USER->id)) {
                $this->content->text = '<a href="'.$CFG->wwwroot.'/blocks/course_organize/manageroles.php">'.get_string('manageroles', 'block_course_organize').'</a><br />';
                if(!$sitewide && !$roles){
					$this->content->text .=	'<a href="'.$CFG->wwwroot.'/blocks/course_organize/manageusers.php">'.get_string('manageusers', 'block_course_organize').'</a>';
                }
            }
            else {
                $this->content->text = '<a href="'.$CFG->wwwroot.'/blocks/course_organize/course_organize.php?userid='.$USER->id.'">Organize Courses</a>';
            }
            $this->content->footer = ' ';
		} 
		
		return $this->content;
	}
	
	function applicable_formats() {
		return array('all' => true);
	}
}
?>
