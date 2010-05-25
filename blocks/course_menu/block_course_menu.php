<?php

class block_course_menu extends block_base {
    function init() {
        $this->title = get_string('blockname','block_course_menu');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2005041005;
    }
    
    function has_config() {
        return true;
    }
    
    function preferred_width() {
        return 200;
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
        return array('course-view' => true, 'mod' => false);
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
    
    function instance_allow_config() {
        return true;
    }
  
    function get_content() {
        $this->course = get_record('course', 'id', $this->instance->pageid);
        global $USER, $CFG, $THEME;
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        require_once($CFG->dirroot.'/course/lib.php');
    
        if($this->content !== NULL) {
            return $this->content;
        }

        if ($this->course->format == 'topics') {
            $format = 'topic';
        }
        else {
            $format = 'week';
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
    
        $sections = get_all_sections($this->course->id);
        $sectionname = get_string('name'.$this->course->format);
        $sectiongroup = $this->course->format;
        $coursecontext = get_context_instance(CONTEXT_COURSE, $this->course->id);

        if (empty($CFG->block_course_menu_groupsections)) {
            $sectiongroup = 'tree';
        }
        
        get_all_mods($this->course->id, $mods, $modnames, $modnamesplural, $modnamesused);
        
        $tab = chr(9);
        $tab2 = $tab.$tab;
        $cr = chr(13);
        
        //check to see which links to display
        if (!empty($CFG->block_course_menu_menu_items)) {
            $menuitems = explode(',', $CFG->block_course_menu_menu_items);
        } else {
            $menuitems = array('cp', 'gb', 'lb', 'cal', 'tt');
            set_config('block_course_menu_menu_items', implode(',', $menuitems));
        }
        
        if (!isset($this->config)) {
            $this->config->cp = 'cp';
            $this->config->gb = 'gb';
            $this->config->lb = 'lb';
            $this->config->cal = 'cal';
            $this->config->tt = 'tt';
        }
        //start the script for YUI treeview
        $this->content->text .= require_js(array('yui_yahoo', 'yui_event', 'yui_dom-event', 'yui_treeview'));
        $this->content->text .= '<div id="treediv"></div>'.$cr;
        $this->content->text .= '<script type = "text/javascript">'. $cr;
        $this->content->text .= $tab.'var tree;'.$cr;
        $this->content->text .= $tab.'function treeInit() {'.$cr;
        $this->content->text .= $tab2.'tree = new YAHOO.widget.TreeView("treediv");'.$cr;
                    
        $this->content->text .= $tab2.'var root = tree.getRoot();'.$cr;
        $this->content->text .= $tab2.'var myobj = {label: "'.$this->course->shortname. '", href: "' .  $CFG->wwwroot . '/course/view.php?id='.$this->course->id.'" };'.$cr;
        $this->content->text .= $tab2.'var crsTitle = new YAHOO.widget.TextNode(myobj, root, true);' . $cr;
        $this->content->text .= $tab2.'crsTitle.labelStyle = "icon-crs";'.$cr;
        if (in_array('cp', $menuitems) && !empty($this->config->cp) && has_capability('block/course_menu:viewcontrols', $coursecontext)) {

            // control panel            
            $this->content->text .= $tab2.'var myobj = { label: "' . get_string('controlpanel','block_course_menu') . '", href: "' . $CFG->wwwroot . '/blocks/course_menu/controls/controls.php?id='.$this->course->id.'" };'.$cr;
            $this->content->text .= $tab2.'var tmpNode = new YAHOO.widget.TextNode(myobj, crsTitle, false);' . $cr;
            $this->content->text .= $tab2.'tmpNode.labelStyle = "icon-cfg";' .$cr;
            //edit course settings link
            if (has_capability('moodle/course:update', $coursecontext)) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('editcoursesettings') .'", href: "' . $CFG->wwwroot . '/course/edit.php?id='.$this->course->id. '" };'.$cr;
                $this->content->text .= $tab2.'var editCon = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'editCon.labelStyle = "icon-edt";'.$cr;
            }
            //quickmail link
            if ((is_dir($CFG->dirroot.'/blocks/quickmail')) && has_capability('block/quickmail:cansend', $coursecontext)) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('blockname', 'block_quickmail') .'", href: "' . $CFG->wwwroot . '/blocks/quickmail/email.php?id='.$this->course->id. '" };'.$cr;
                $this->content->text .= $tab2.'var qckMail = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'qckMail.labelStyle = "icon-eml";'.$cr;
            }
            //files link
            if (has_capability('moodle/course:managefiles', $coursecontext)) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('files') .'", href: "' . $CFG->wwwroot . '/files/index.php?id='.$this->course->id. '" };'.$cr;
                $this->content->text .= $tab2.'var filesLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'filesLnk.labelStyle = "icon-fls";'.$cr;
            }
            //password change link
            if (!empty($CFG->block_course_menu_passwordchange_url)) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('password') .'", href: "' . $CFG->block_course_menu_passwordchange_url. '" };'.$cr;
                $this->content->text .= $tab2.'var passLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'passLnk.labelStyle = "icon-psw";'.$cr;
            }
            //reports link
            if (has_capability('moodle/site:viewreports', $coursecontext)) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('reports') .'", href: "' . $CFG->wwwroot .'/course/report.php?id='.$this->course->id. '" };'.$cr;
                $this->content->text .= $tab2.'var reportsLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'reportsLnk.labelStyle = "icon-rep";'.$cr;
            }
            //user management subheading
            $viewpart = has_capability('moodle/course:viewparticipants', $coursecontext);
            $mnggroup = has_capability('moodle/course:managegroups', $coursecontext);
            $canassign = has_capability('moodle/role:assign', $coursecontext);
            //user management menu
            //assign roles link
            if ($canassign) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('assignroles', 'role') . '", href: "' . $CFG->wwwroot . '/admin/roles/assign.php?contextid='.$coursecontext->id. '" };'.$cr;
                $this->content->text .= $tab2.'var assignLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'assignLnk.labelStyle = "icon-rls";'.$cr;
            }
            //participants link
            if ($viewpart) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('participants') . '", href: "' . $CFG->wwwroot . '/user/index.php?id='.$this->course->id. '" };'.$cr;
                $this->content->text .= $tab2.'var particLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'particLnk.labelStyle = "icon-usr";'.$cr;
            }
            //groups link
            if ($mnggroup) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('groups') . '", href: "' . $CFG->wwwroot . '/group/index.php?id='.$this->course->id. '" };'.$cr;
                $this->content->text .= $tab2.'var grpsLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'grpsLnk.labelStyle = "icon-grp";'.$cr;
            }
            //backup and restore subheading
            $canbackup = has_capability('moodle/site:backup', $coursecontext);
            $canrestore = has_capability('moodle/site:restore', $coursecontext);
            $canimport = has_capability('moodle/site:import', $coursecontext);
            //backup and restore menu
            //backup link
            if ($canbackup) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('backup') . '", href: "' . $CFG->wwwroot . '/backup/backup.php?id='.$this->course->id. '" };'.$cr;
                $this->content->text .= $tab2.'var backupLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'backupLnk.labelStyle = "icon-bkp";'.$cr;
            }
            //restore link
            if ($canrestore) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('restore') . '", href: "' . $CFG->wwwroot . '/files/index.php?id='.$this->course->id.'&wdir=/backupdata" };'.$cr;
                $this->content->text .= $tab2.'var restoreLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'restoreLnk.labelStyle = "icon-rst";'.$cr;
            }
            //import link
            if ($canimport) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('import') . '", href: "' . $CFG->wwwroot . '/course/import.php?id='.$this->course->id.'" };'.$cr;
                $this->content->text .= $tab2.'var importLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'importLnk.labelStyle = "icon-rst";'.$cr;
            }
            //other controls subheading
            $viewscales = has_capability('moodle/course:managescales', $coursecontext);
            //other controls menu
            //scales link
            if ($viewscales) {
                $this->content->text .= $tab2.'var myobj = { label: "' . get_string('scales') . '", href: "' . $CFG->wwwroot . '/course/scales.php?id='.$this->course->id.'" };'.$cr;
                $this->content->text .= $tab2.'var scalesLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
                $this->content->text .= $tab2.'scalesLnk.labelStyle = "icon-scl";'.$cr;
            }
            //edit profile link
            $this->content->text .= $tab2.'var myobj = { label: "' . get_string('editmyprofile') . '", href: "' . $CFG->wwwroot . '/user/edit.php?id='.$USER->id.'&amp;course='.$this->course->id.'" };'.$cr;
            $this->content->text .= $tab2.'var profileLnk = new YAHOO.widget.TextNode(myobj, tmpNode, false);' . $cr;
            $this->content->text .= $tab2.'profileLnk.labelStyle = "icon-pfl";'.$cr;
            //            
        }
                
        
        //add the gradebook link
        if (in_array('gb', $menuitems) && !empty($this->config->gb) && $this->course->showgrades && has_capability('moodle/grade:view', $coursecontext)) {
            $this->content->text .= $tab2.'var myobj  = { label: "' . get_string('gradebook', 'grades') . '", href: "' . $CFG->wwwroot . '/grade/index.php?id='.$this->course->id.'" };'.$cr;
            $this->content->text .= $tab2.'var tmpNode2 = new YAHOO.widget.TextNode(myobj, crsTitle, false);'.$cr;
            $this->content->text .= $tab2.'tmpNode2.labelStyle = "icon-grd";'.$cr;
        }

        if ($sectiongroup !== 'tree') {
            $this->content->text .= $tab2.'var myobj = { label: "' . ucwords($sectiongroup) . '", href: "' . $CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;topic=all" };'.$cr;
            $this->content->text .= $tab2.'var tmpNode3 = new YAHOO.widget.TextNode(myobj, crsTitle, false);'.$cr;
        }
        if ($this->course->format !== 'social') {
            $this->content->text .= $this->print_course_sections($this->course,$sections,$this->course->format,$mods,$modnamesused,$sectiongroup);
        }

        // library resources link
        if (in_array('lb', $menuitems) && !empty($this->config->lb) && has_capability('block/course_menu:viewcontrols', $coursecontext)) {
            $this->content->text .= $tab2.'var myobj = { label: "' . get_string('librarylinktitle', 'block_course_menu') . '", href: "' . $CFG->block_course_menu_libraryresources_url . '" };'.$cr;
            $this->content->text .= $tab2.'var tmpNode4 = new YAHOO.widget.TextNode(myobj, crsTitle, false);'.$cr;
            $this->content->text .= $tab2.'tmpNode4.labelStyle = "icon-lb";'.$cr;
        }
        
        //print the calendar link
        if (in_array('cal', $menuitems) && !empty($this->config->cal)) {
            $this->content->text .= $tab2.'var myobj = { label: "' . get_string('calendar','calendar') . '", href: "' . $CFG->wwwroot.'/calendar/view.php?view=upcoming&amp;course=' . $this->course->id . '" };'.$cr;
            $this->content->text .= $tab2.'var tmpNode4 = new YAHOO.widget.TextNode(myobj, crsTitle, false);'.$cr;
            $this->content->text .= $tab2.'tmpNode4.labelStyle = "icon-cal";'.$cr;
            
            $this->content->text .= $tab2.'var myobj = { label: "' . get_string('showallsections', 'block_course_menu') . '", href: "' . $CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&amp;'.$format.'=all" };'.$cr;
            $this->content->text .= $tab2.'var tmpNode5 = new YAHOO.widget.TextNode(myobj, crsTitle, false);'.$cr;
            $this->content->text .= $tab2.'tmpNode5.labelStyle = "icon-shw";'.$cr;
        }
        //trouble ticket link
        
        if (in_array('tt', $menuitems) && !empty($this->config->tt) && has_capability('block/course_menu:viewcontrols', $coursecontext)) {
            $this->content->text .= $tab2.'var myobj = { label: "' . get_string('troubletickettitle', 'block_course_menu') . '", href: "' . $CFG->wwwroot.'/blocks/course_menu/ticket.php?id='.$this->course->id . '" };'.$cr;
            $this->content->text .= $tab2.'var tmpNode6 = new YAHOO.widget.TextNode(myobj, crsTitle, false);'.$cr;
            $this->content->text .= $tab2.'tmpNode6.labelStyle = "icon-tkt";'.$cr;
        }
        
        $this->content->text .= $tab2.'tree.draw();'.$cr;
        $this->content->text .= $tab.'}'.$cr;
        $this->content->text .= $tab.'treeInit();</script>'.$cr;
        $this->content->text .= $this->get_scriptless_content();
        
    }
    
    function get_scriptless_content() {
        $this->course = get_record('course', 'id', $this->instance->pageid);
        global $CFG;
        
        $coursecontext = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $sections = get_all_sections($this->course->id);  
        if ($this->course->format == 'topics') {
            $format = 'topic';

        }
        else {
            $format = 'week';
        }
        //check to see which links to display
        if (!empty($CFG->block_course_menu_menu_items)) {
            $menuitems = explode(',', $CFG->block_course_menu_menu_items);
        } else {
            $menuitems = array('cp', 'gb', 'lb', 'cal', 'tt');
            set_config('block_course_menu_menu_items', implode(',', $menuitems));
        }        
     
        $text = '<noscript>';
        $text .= '<table align="center" width="100%">';
        
        if (in_array('cp', $menuitems) && !empty($this->config->cp) && has_capability('block/course_menu:viewcontrols', $coursecontext)) {
            $text .='<tr ><td align="center"><img src="'.$CFG->wwwroot.'/blocks/course_menu/icons/configure.gif" alt="Control Panel" /></td>';
            $text .='<td valign="top"><a href="'.$CFG->wwwroot.'/blocks/course_menu/controls/controls.php?id='.$this->course->id.'">Control Panel</a></td></tr>';
        } 
        
        if (in_array('gb', $menuitems) && !empty($this->config->gb) && $this->course->showgrades && has_capability('moodle/grade:view', $coursecontext)) {
            $text .='<tr ><td align="center"><img src="'.$CFG->pixpath.'/i/grades.gif" alt="'.get_string('gradebook','grades').'" /></td>';
            $text .='<td valign="top"><a href='.$CFG->wwwroot.'/grade/index.php?id='.$this->course->id.'">'.get_string('gradebook','grades').'</a></td></tr>';

        }


        if (!empty($sections)) {
            foreach($sections as $section) {
                if ($section->visible && $section->section > 0 && $section->section <= $this->course->numsections) {
                    $summary = shorten_text($section->summary);
                    if (empty($summary)) {
                      $summary = get_string("name{$this->course->format}").' '.$section->section;
                    }
                    $summary = truncate_description($summary);
                    $summary = clean_text($summary);
                    $text .='<tr ><td align="center"><img src="../blocks/course_menu/icons/file.gif" alt="'.str_replace('"','&quot;',$summary).'" /></td>';
                    $text .='<td valign="top"><a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&'.$format.'='.$section->section.'">'.$summary.'</a></td></tr>';
                }
            }
        }

        // output a link to library resources
        if (in_array('lb', $menuitems) && !empty($this->config->lb)) {
            $text .='<tr ><td align="center"><img src="../blocks/course_menu/icons/book_open.png" alt="'.get_string('libraryresourcesurl', 'block_course_menu').'" /></td>';
            $text .='<td valign="top"><a href="'.$CFG->block_course_menu_libraryresources_url.'">'.get_string('librarylinktitle', 'block_course_menu').'</a></td></tr>';
        }

        // output a link to the calendar
        if (in_array('cal', $menuitems) && !empty($this->config->cal)) {
            $text .='<tr ><td align="center"><img src="../blocks/course_menu/icons/cal.gif" alt="'.get_string('calendar','calendar').'" /></td>';
            $text .='<td valign="top"><a href="'.$CFG->wwwroot.'/calendar/view.php?view=upcoming&amp;course='.$this->course->id.'">'.get_string('calendar', 'calendar').'</a></td></tr>';
        }
        // output a link to show all topics/weeks
        $text .='<tr ><td align="center"><img src="../blocks/course_menu/icons/viewall.gif" alt="'.get_string('showallsections','block_course_menu').'" /></td>';
        $text .='<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'&'.$format.'=all" alt="'.get_string("showallsections",'block_course_menu').'">'.get_string("showallsections",'block_course_menu').'</a></td></tr>';
        //output a link to show the ticket
        if (in_array('tt', $menuitems) && !empty($this->config->tt) && has_capability('block/course_menu:viewcontrols', $coursecontext)) {
            $text .='<tr ><td align="center"><img src="../blocks/course_menu/icons/bug.gif" alt="'.get_string('troubletickettitle','block_course_menu').'" /></td>';
            $text .='<td><a href="'.$CFG->wwwroot.'/blocks/course_menu/ticket.php?id='.$this->course->id.'" alt="'.get_string("troubletickettitle",'block_course_menu').'">'.get_string("troubletickettitle",'block_course_menu').'</a></td></tr>';
        }
        $text .= '</table></noscript>'; 
        return $text;
    }
    
    function print_course_section($course, $section, $mods, $modnamesused, $num) {
        /// Prints a section full of activity modules
        global $CFG, $USER;
        $tab = chr(9);
        $tab2 = $tab.$tab;
        $cr = chr(13);

        if(is_string($course->modinfo)){
            $modinfo = unserialize($course->modinfo);
        }
        
        if (!empty($section->sequence)) {
            $sectionmods = explode(",", $section->sequence);
            
            foreach ($sectionmods as $modnumber) {
                if (empty($mods[$modnumber])) {
                    continue;
                }
                $mod = $mods[$modnumber];
                if ($mod->visible or has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $course->id))) {
                    $instancename = urldecode($modinfo[$modnumber]->name);
             
                    if (!empty($modinfo[$modnumber]->extra)) {
                        $extra = urldecode($modinfo[$modnumber]->extra);
                    } 
                    else {
                        $extra = "";
                    }

                    // don't do anything for labels
                    if ($mod->modname != 'label') {
                        // Normal activity
                        if ($mod->visible) { 
                            $instancename = shorten_text($instancename);
                            if(empty($instancename)){
                                $instancename = $mod->modfullname;
                            }
                            $instancename = truncate_description($instancename, 15);
                            $instancename = addslashes($instancename);
                            $instancename = clean_text($instancename);

                            if ($mod->modname != 'resource') {
                                $this->content->text .= $tab2.'var myobj = { label: "'.$instancename.'", href: "'.$CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id.'" };'.$cr;
                                $this->content->text .= $tab2.'var mod'.$modnumber.' = new YAHOO.widget.TextNode(myobj, section'.$num.', false);'.$cr;
                                $this->content->text .= $tab2.'mod'.$modnumber.'.labelStyle = "icon-'.substr($mod->modname,0,4).'";'.$cr;
                            }
                            else{
                                require_once($CFG->dirroot.'/mod/resource/lib.php');
                                $info=resource_get_coursemodule_info($mod);
                                $this->content->text .= $tab2.'var myobj = {label: "'.$instancename.'", href: "'.$CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id.'" };'.$cr;
                                $this->content->text .= $tab2.'var mod'.$modnumber.' = new YAHOO.widget.TextNode(myobj, section'.$num.', false);'.$cr;
                                if (!empty($info->icon)) {
                                    $this->content->text .= $tab2.'mod'.$modnumber.'.labelStyle = "icon-'.substr($info->icon,2,3).'";'.$cr;
                                } 
                                else if(empty($info->icon)) {
                                    $this->content->text .= $tab2.'mod'.$modnumber.'.labelStyle = "icon-oth";'.$cr;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function print_course_sections($course,$sections,$sectionname, $mods, $modnamesused,$sectiongroup){
        global $CFG, $USER;
        $tab = chr(9);
        $tab2 = $tab.$tab;
        $cr = chr(13);   
        $section = 1;
        if ($course->format == 'topics') {
            $format = 'topic';
        }
        else {
            $format = 'week';
        }

        while ($section <= $course->numsections) {
            if (empty($sections[$section])) {
                $strsummary = '';
            } 
            else {
                $strsummary = shorten_text($sections[$section]->summary);
                if (!strlen($strsummary)){
                    $strsummary = ucwords($sectionname)." ".$section;
                }
                $strsummary = truncate_description($strsummary);
                $strsummary = trim($strsummary);
                $summary = $strsummary;
                $strsummary = addslashes($strsummary);
                
                if ($sections[$section]->visible==1) {
                    $this->content->text .= $tab2.'var myobj = { label: "'.$strsummary.'", href: "'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'&amp;'.$format.'='.$section.'" };'.$cr;
                    $this->content->text .= $tab2.'var section'.$section.' = new YAHOO.widget.TextNode(myobj, crsTitle, false);'.$cr;
                                                
                    $this->content->text .= $this->print_course_section($course, $sections[$section], $mods, $modnamesused, $section);
                    $this->content->text .= $tab2.'if (section'.$section.'.hasChildren(true)) {'.$cr;
                    $this->content->text .= $tab2.$tab.'section'.$section.'.labelStyle = "icon-fld";'.$cr;
                    $this->content->text .= $tab2.'} else {'.$cr;
                    $this->content->text .= $tab2.$tab.'section'.$section.'.labelStyle = "icon-fld-emp";'.$cr;
                    $this->content->text .= $tab2.'}'.$cr;

                }
            }
            $section++;
        }
    }
}

function truncate_description($string, $max_size=20, $trunc = '...') {
    $split_tags = array('<br>','<BR>','<Br>','<bR>','</dt>','</dT>','</Dt>','</DT>','</p>','</P>', '<BR />', '<br />', '<bR />', '<Br />');
    $temp = $string;
    foreach($split_tags as $tag) {
    	if(strpos($temp, $tag) == 1) {
    		$temp = substr($temp, (strlen($tag) + 1));
    	}
        list($temp) = explode($tag, $temp, 2);
    }
    
    $rstring = strip_tags($temp);

    $rstring = html_entity_decode($rstring);

    if (strlen($rstring) > $max_size) {
        $rstring = chunk_split($rstring, ($max_size-strlen($trunc)), "\n");
        $temp = explode("\n", $rstring);
        // catches new lines at the beginning
        if (trim($temp[0]) != '') {
            $rstring = trim($temp[0]).$trunc;
        }
        else {
           $rstring = trim($temp[1]).$trunc;
        }
    }

    if (strlen($rstring) > $max_size) {
        $rstring = substr($rstring, 0, ($max_size - strlen($trunc))).$trunc;
    }
    elseif($rstring == '') {
        // we chopped everything off... lets fall back to a failsafe but harsher truncation
        $rstring = substr(trim(strip_tags($string)),0,($max_size - strlen($trunc))).$trunc;
    }
    
    // single quotes need escaping
    
    return str_replace("'", "\\'", $rstring);
}
    
?>