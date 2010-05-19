<?php
global $CFG;


class block_course_wizard extends block_base {
    function init() {
        $this->title = get_string('coursewizard','block_course_wizard');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2005090606;
        
    }
    
    function has_config() {
        return true;
    }
    
    function print_config() {
        global $CFG, $THEME;
        print_simple_box_start('center', '', $THEME->cellheading);
        include($CFG->dirroot.'/blocks/'.$this->name().'/config_global.html');
        print_simple_box_end();
        return true;
    }
    
    function applicable_formats() {
        // Default case: the block can be used in courses and site index, but not in activities
        return array('site' => true, 'mod' => false);
    }
  
    function config_save($config) {
        foreach ($config as $name => $value) {
            if (is_array($value)) {
                $value = implode(',',$value);
            }
            set_config($name, $value);
        }
        return true;    
    }
    
    function instance_allow_multiple() {
        return false;
    }
    
    function hide_header() {
        return false;
    }

    function get_content() {
        global $CFG, $USER, $COURSE;
        
        include_once("$CFG->dirroot/lib/datalib.php");
        include_once("$CFG->dirroot/blocks/course_wizard/lib.php");
                
        if($this->content !== NULL) {
            return $this->content;
        }   

        //if($COURSE->id == SITEID) {                
            $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
            if(has_capability('moodle/site:doanything', $context, $USER->id)) {
                $visible = true;
            }
            else {
                //Visible if user has capibility to update at least one of their courses.             
                $visible = check_my_courses_for_capability($USER, 'moodle/course:update', true);
            }
            //Display the course wizard if user has capability 'doanything' or has the capability to update any
            //of the courses they're enrolled in.           
            if($visible) {
//DEBUGGING
//    echo "this->instance->pageid: ".$this->instance->pageid."<br>";
//    echo "<font size=5>Course global object:<br>";
//    print_object($COURSE);
//    echo "</font>";
//END DEBUGGING                
                $this->content = new stdClass;
                //Check which links to include
		        if (!empty($CFG->block_course_wizard_menu_items)) {
		            $menuitems = explode(',', $CFG->block_course_wizard_menu_items);
		        } else {
		            $menuitems = array('cwback', 'cwcross', 'cwshow', 'cwrest');
		            set_config('block_course_wizard_menu_items', implode(',', $menuitems));
		        }
		        //Check which links are hidden to an instructor
		        if (!empty($CFG->block_course_wizard_visibility)) {
		            $visibility = explode(',', $CFG->block_course_wizard_visibility);
		        } else {
		            $visibility = array('cwback', 'cwcross', 'cwshow', 'cwrest');
		            set_config('block_course_wizard_visibility', implode(',', $visibility));
		        }
		        if (!isset($this->config)) {
		            $this->config->cwback = 'cwback';
		            $this->config->cwcross = 'cwcross';
		            $this->config->cwshow = 'cwshow';
		            $this->config->cwrest = 'cwrest';
		        }
		        if (!has_capability('moodle/site:doanything', $context)) {
		        	$menuitems = array_intersect($menuitems, $visibility);
		        }
                 if (in_array('cwback', $menuitems) && !empty($this->config->cwback)) {
                	$this->content->text = '<a href="'.$CFG->wwwroot.'/blocks/course_wizard/backup.php?course='.$this->instance->pageid.'">'.get_string('backupcourse','block_course_wizard').'</a>';
                }   $this->content->text .= helpbutton('course_wizard_backup', 'Backup Course Help', 'block_course_wizard', true, false, '', true). '<br />';
                if (in_array('cwcross', $menuitems) && !empty($this->config->cwcross)) {
                	$this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/course_wizard/crosslist.php?course='.$this->instance->pageid.'">'.get_string('crosslistcourses','block_course_wizard').'</a>';
                	$this->content->text .= helpbutton('course_wizard_crosslist', 'Crosslist Courses Help', 'block_course_wizard', true, false, '', true). '<br />';
                }
                if (in_array('cwshow', $menuitems) && !empty($this->config->cwshow)) {
                	$this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/course_wizard/showhide.php?course='.$this->instance->pageid.'">'.get_string('showhidecourses','block_course_wizard').'</a>';               
                	$this->content->text .= helpbutton('course_wizard_showhide', 'Show Hide Courses Help', 'block_course_wizard', true, false, '', true). '<br />';
                }
                if (in_array('cwrest', $menuitems) && !empty($this->config->cwrest)) {
                	$this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/course_wizard/restore.php?course='.$this->instance->pageid.'">'.get_string('restorecourse','block_course_wizard').'</a>';
                	$this->content->text .= helpbutton('course_wizard_restore', 'Restore Course Help', 'block_course_wizard', true, false, '', true). '<br />';
                }
                if(!has_capability('moodle/site:doanything', $context, $USER->id)) {
                	//Javascript Checks the DOM for all coursebox's with a 3rd childnode containing the dimmed class.
	                $this->content->text .= "<script language=\"JavaScript\">
										    <!-- Start
										    // javascript for hiding/displaying team member roles
										    var divflag = \"false\";
										    var beenDone = false;
										    function showHiddenMyCourses() {
										    	var divs = document.getElementsByTagName('div');
												var tables = document.getElementsByTagName('table');
											    for(i=0;i<divs.length;i++) {
											    	if(divs[i].className.match('coursebox') || divs[i].className.match('hideCat')) {
											       		if(divs[i].childNodes[0].childNodes[0].childNodes[0].className.match('dimmed') || divs[i].className.match('hideCat')) {
											       			if(!beenDone) {
											       				var showhidebutton = document.createElement('input');
													        	showhidebutton.id = 'showhidebutton';
													        	showhidebutton.type = \"button\";
													        	showhidebutton.value = \"".get_string('showbutton', 'block_course_wizard')."\";
													        	showhidebutton.addEventListener('click', function(e) {this.value=showHiddenMyCourses(); return false;}, false);
													        	showhidebutton.style.display = 'inline';
													        	divs[i].parentNode.insertBefore(showhidebutton,divs[i]);
													        	beenDone = true;
											       			}
											       			if(!divflag) {
													            divs[i].style.display = 'inherit';
													        } else {
													        	divs[i].style.display = 'none';
													        }
											       		}
											    	}
											    }
										        var hidewarninglink = document.getElementById('hidewarninglink');
										        var showhidebutton = document.getElementById('showhidebutton');
										        if (!divflag) {
										        	divflag = true;
										        	hidewarninglink.style.display = 'none';
										        	showhidebutton.value = \"".get_string('hidebutton', 'block_course_wizard')."\"
										           	return \"".get_string('hidebutton', 'block_course_wizard')."\";
										        } else {
										        	divflag = false;
										        	hidewarninglink.style.display = 'inherit';
										           	return \"".get_string('showbutton', 'block_course_wizard')."\";
										        }
											}
										    showHiddenMyCourses();
											//  End --></script><br />";
					// Get all the user's courses and check if they are visible
					$fields  = 'id, category, sortorder, shortname, fullname, idnumber, newsitems, teacher, teachers, student, students, guest, startdate, visible, cost, enrol, summary, groupmode';
        			$courses = get_my_courses($USER->id, NULL, $fields);
					if (!empty($courses)) {
		            	$hidden = false;
		            	foreach ($courses as $course) {
							if (!$course->visible) {
								$hidden = true;
							}
		            	}
						// If Hidden courses are found display the hidden warning link in the block
						if($hidden) {
							$this->content->text .= "<a id=\"hidewarninglink\" href=\"#hidewarninglink\" onClick=\"this.value=showHiddenMyCourses();\">".get_string('hiddencoursewarning', 'block_course_wizard')." </a>";
						}   
					}
				}
					
//DEBUGGING form lib form
//    $this->content->text .= '<br/><a href="'.$CFG->wwwroot.'/blocks/course_wizard/showhide_form_test.php?course='.$this->instance->pageid.'">Show/Hide form (using forms lib)</a>';
//END DEBUGGING                 
 //               $this->content->text .= '<br/><a href="'.$CFG->wwwroot.'/blocks/course_wizard/crosslist.php">'.get_string('crosslistcourses','block_course_wizard').'</a>';
 //               $this->content->text .= helpbutton('course_wizard_crosslist', 'Crosslist Courses Help', 'moodle', true, false, get_string('crosslisthelp','block_course_wizard'), true);
                $this->content->footer = '';      
            }
        //} 
        return $this->content;
    }
    
}

?>
