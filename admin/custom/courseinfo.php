<?php // $Id: courseinfo.php, Author: Mark Nielsen

    require_once("../config.php");
    require_once($CFG->libdir."/tablelib.php");

    $filter = optional_param('filter', '', PARAM_ALPHA);
    $action = optional_param('action', 'courselist', PARAM_ALPHA);
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $search = optional_param('search', '0', PARAM_ALPHA);
    $searchfor = optional_param('searchfor', '', PARAM_CLEAN);

    if (optional_param('cancel', '', PARAM_ALPHA)) {
        $searchfor = '';
        $search = 0;
    }

    if (!empty($searchfor)) { // clean for sql injections
        $searchfors = explode(' ', $searchfor);
        foreach ($searchfors as $key => $searchfor) {
            $searchfors[$key] = clean_param($searchfor, PARAM_ALPHANUM);
        }
        $searchfor = implode(' ', $searchfors);
    }

    require_login();

    if (!isadmin()) {
        error("Only admins can access this page");
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }

    // ALL strings are defined here
    switch($action) {
        case 'courselist':
            $strcurrent = 'Course List';
            break;
        case 'userlist':
            $courseshortname = get_field('course', 'shortname', 'id', $courseid);
            $strcurrent = 'User List for '.$courseshortname;
            break;            
    }

    $stradministration = get_string("administration");
    $strcourseinfo     = 'Course Information';
    
    // duplicate idnumber table strings
    $strduplicatestudents = 'Duplicate User idnumbers';
    $strduplicatecourses = 'Duplicate Course idnumbers';
    $stridnumber = 'idnumber';
    $strusers = get_string("users");
    
    // main table
    $strshortname = 'Course Short Name';
    $strfullname = 'Course Full Name';
    $strnumuser = '# of users enrolled';
    
    //search
    $strsearchfor = 'Search for';
    $strcrn = 'CRN';
    $straxeid = 'Teacher axeid';
    $strlastname = 'Teacher\'s Surname';
    $strcancel = 'Cancel';
    $strsearch = 'Search';
    
    // search errors
    $struseraxeidnotfound = 'Teacher axeid not found';
    $strdoesnotteach = 'User does not teach any courses';
    $strlastnamenotfound = 'Teacher\'s surname not found';
    
    //END string definitions 

    print_header("$site->shortname: $stradministration: $strcourseinfo", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> <a href=\"courseinfo.php\">$strcourseinfo</a> -> $strcurrent");

    echo '<style type="text/css"> '.
                'body#admin-courseinfo #page table#courseinfo .header .commands { display: inline; } '. // makes the collapsible buttons inline
                'body#admin-courseinfo #page table#courseinfo .r1 { background-color: #eeeeee; } '.     // alternating row color for course list table
                'body#admin-courseinfo #page table.generaltable .r0 { background-color: #FFFFFF; } '.   // alternating row color for duplicate idnumber tables
                'body#admin-courseinfo #page table#userlist .r1 { background-color: #eeeeee; } '.       // alternating row color for user list table
        '</style>';

    // Print out courses that have duplicate idnumber fields
    if ($dupidnumbers = get_records_sql("SELECT id, idnumber FROM {$CFG->prefix}course WHERE idnumber != '' GROUP BY idnumber HAVING COUNT(idnumber) > 1")) {
        $table = new stdClass;
        $table->head = array($stridnumber, $strcourseinfo);
        $table->align = array("left", "left");
        $table->wrap = array();
        $table->width = "90%";
        $table->size = array("40%", "*");  
        $table->data = array();
        
        foreach ($dupidnumbers as $dupidnumber) {
            $dupcourses = get_records('course', 'idnumber', $dupidnumber->idnumber);
            $links = array();
            foreach ($dupcourses as $dupcourse) {
                $links[] = "<a href=\"$CFG->wwwroot/course/view.php?id=$dupcourse->id\" title=\"$dupcourse->fullname\">$dupcourse->shortname</a>";
            }
            // each table row has the idnumber and a CSL of links to the courses with that idnumber
            $table->data[] = array($dupidnumber->idnumber, implode(', ', $links));
        } 
        print_heading($strduplicatecourses); 
        print_table($table);
    }

    // Print out users that have duplicate idnumber fields
    if ($dupidnumbers = get_records_sql("SELECT id, idnumber FROM {$CFG->prefix}user WHERE idnumber != '' GROUP BY idnumber HAVING COUNT(idnumber) > 1")) {
        $table = new stdClass;
        $table->head = array($stridnumber, $strusers);
        $table->align = array("left", "left");
        $table->wrap = array();
        $table->width = "90%";
        $table->size = array("40%", "*");  
        $table->data = array();
        
        foreach ($dupidnumbers as $dupidnumber) {
            $dupusers = get_records('user', 'idnumber', $dupidnumber->idnumber);
            $links = array();
            foreach ($dupusers as $dupuser) {
                $links[] = "<a href=\"$CFG->wwwroot/user/view.php?id=$dupuser->id&course=".SITEID."\">$dupuser->firstname $dupuser->lastname</a>";
            }
            // each table row has the idnumber and a CSL of links to the users with that idnumber
            $table->data[] = array($dupidnumber->idnumber, implode(', ', $links));
        }
        print_heading($strduplicatestudents);           
        print_table($table);
    }    
    
    print_heading($strcurrent);

    // handles the main output (currently the course list and the user list)
    switch ($action) {
        case 'courselist':
            $tablecolumns = array('idnumber', 'shortname', 'fullcoursename', 'users');
            $tableheaders = array($stridnumber, $strshortname, $strfullname, $strnumuser);

            $table = new flexible_table('course-courseinfo-courselist-report');

            $table->define_columns($tablecolumns);
            $table->define_headers($tableheaders);
            $table->define_baseurl($CFG->wwwroot.'/admin/courseinfo.php?filter='.$filter.'&search='.$search.'&searchfor='.$searchfor);

            $table->sortable(true);
            $table->pageable(true);
            $table->collapsible(true);
    
            // attributes in the table tag
            $table->set_attribute('cellpadding', '3px');
            $table->set_attribute('id', 'courseinfo');
            $table->set_attribute('class', 'generaltable generalbox');
            $table->set_attribute('align', 'center');
            $table->set_attribute('width', '90%');

            $table->setup(); 
            
            // start of sql query build
            $select = 'SELECT c.id, c.idnumber, c.shortname, c.fullname as fullcoursename, count(c.id) as users ';
            $from = 'FROM '.$CFG->prefix.'course c LEFT JOIN '.$CFG->prefix.'user_students u ON(c.id = u.course) ';

            if(!empty($filter)) {
                $where = 'WHERE c.fullname LIKE \''.$filter.'%\' ';
            } else {
                $where = '';
            }
            
            if (!empty($search)) {
                $whereadd = '';
                switch($search) {
                    case 'crn':
                        $whereadd = "c.idnumber LIKE '%$searchfor' ";
                        break;
                    case 'shortname':
                        $whereadd = "c.shortname LIKE '$searchfor%' ";
                        break;
                    case 'axeid';
                        if ($userid = get_field('user', 'id', 'username', $searchfor)) {
                            if($teaches = get_records('user_teachers', 'userid', $userid, '', 'course, id')) {
                                $whereadd = 'c.id IN('.implode(', ', array_keys($teaches)).') ';
                            } else {
                                notify($strdoesnotteach);
                            }
                        } else {
                            notify($struseraxeidnotfound);
                        }
                        break;
                    case 'lastname':
                        if ($userids = get_records('user', 'lastname', $searchfor)) {
                            if($teaches = get_records_list('user_teachers', 'userid', implode(', ', array_keys($userids)), '', 'course, id')) {
                                $whereadd = 'c.id IN('.implode(', ', array_keys($teaches)).') ';
                            } else {
                                notify($strdoesnotteach);
                            }
                        } else {
                            notify($strlastnamenotfound);
                        }
                        break;                        
                }
                if (!empty($whereadd)) {
                    if (empty($where)) {
                        $where = 'WHERE ';
                    } else {
                        $where .= 'AND ';
                    }
                    $where .= "$whereadd";
                }
            }
            
            $groupby = 'GROUP BY c.id ';
    
            if ($sort = $table->get_sql_sort()) {
                $sort = 'ORDER BY '.$sort.' ';
            } else {
                $sort = 'ORDER BY c.shortname';  // default
            }

            // set up the pagesize
            $countsql = 'SELECT COUNT(DISTINCT(c.id)) FROM '.$CFG->prefix.'course c '.$where;
            $total  = count_records_sql($countsql);
            $table->pagesize(30, $total);  // first argument of pagesize determines how many per page to display
            
            // this is for getting the correct records for a given page
            if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
                $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size()).' ';
            } else {
                $limit = '';
            }
            
            // execute sql and build our table data
            if ($courses = get_records_sql($select.$from.$where.$groupby.$sort.$limit)) {
                foreach ($courses as $course) {
                    $table->add_data( array($course->idnumber, 
                                            '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a>', 
                                            $course->fullcoursename, 
                                            '<a href="courseinfo.php?action=userlist&courseid='.$course->id.'">'.number_format($course->users, 0).'</a>') );
                }
            }
    
            // NOTE: had to create my own filter because flexible_table only does firstname/surname filter bars
            // alphabet bar for filtering by course full name
            $strall = get_string('all');
            $alpha  = explode(',', get_string('alphabet'));

            echo '<div class="initialbar firstinitial">'.$strfullname.' : ';
            if(empty($filter)) {
                echo '<strong>'.$strall.'</strong>';        
            } else {
                echo '<a href="'.$CFG->wwwroot.'/admin/courseinfo.php">'.$strall.'</a>';
            }
            foreach ($alpha as $letter) {
                if ($letter == $filter) {
                    echo ' <strong>'.$letter.'</strong>';
                } else {
                    echo ' <a href="'.$CFG->wwwroot.'/admin/courseinfo.php?filter='.$letter.'&search='.$search.'&searchfor='.$searchfor.'">'.$letter.'</a>';
                }
            }
            echo '</div><br />';
    
            echo '<div id="tablecontainer">';
                $table->print_html();  // prints our table
            echo '</div>';
            $options = array();
            $options['crn'] = $strcrn;
            $options['shortname'] = $strshortname;
            $options['axeid'] = $straxeid;
            $options['lastname'] = $strlastname;
            echo '<div align="center"><form method="post" action="courseinfo.php">';
            echo $strsearchfor.' ';
            choose_from_menu($options, 'search', $search);
            echo '<input type="text" name="searchfor" value="'.$searchfor.'">';
            echo '<input type="submit" value="'.$strsearch.'" />';
            echo '<input type="submit" value="'.$strcancel.'" name="cancel"/>';

            echo '</form></div>';
            break;
        case 'userlist':
            $tablecolumns = array('fullname');
            $tableheaders = array('');

            $table = new flexible_table('course-courseinfo-userlist-report');

            $table->define_columns($tablecolumns);
            $table->define_headers($tableheaders);
            $table->define_baseurl($CFG->wwwroot.'/admin/courseinfo.php?action=userlist&courseid='.$courseid);

            $table->sortable(true);
            $table->initialbars(count_records('user_students', 'course', $courseid) > 50);  // will show initialbars if there are more than 50 users
            $table->pageable(true);
    
            // attributes in the table tag
            $table->set_attribute('cellpadding', '2px');
            $table->set_attribute('id', 'userlist');
            $table->set_attribute('class', 'generaltable generalbox');
            $table->set_attribute('align', 'center');
            $table->set_attribute('width', '20%');

            $table->setup(); 

            // build sql
            $select = 'SELECT u.* ';
            $from = 'FROM '.$CFG->prefix.'user u, '.$CFG->prefix.'user_students s ';
            $where = 'WHERE u.id = s.userid AND s.course = '.$courseid.' ';
            
            if ($table->get_sql_where()) {
                $where .= 'AND '.$table->get_sql_where().' ';
            }
    
            // sorting of the table
            if ($sort = $table->get_sql_sort()) {
                $sort = 'ORDER BY '.$sort.' ';
            } else {
                // my default sort rule
                $sort = 'ORDER BY u.firstname, u.lastname';
            }
    
            // set up the pagesize
            $total  = count_records_sql('SELECT COUNT(DISTINCT(u.id)) '.$from.$where);
            $table->pagesize(20, $total);

            // this is for getting the correct records for a given page
            if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
                $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size()).' ';
            } else {
                $limit = '';
            }
            
            // execute sql and build our table data
            if ($users = get_records_sql($select.$from.$where.$sort.$limit)) {
                foreach ($users as $user) {
                    $table->add_data( array("<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$courseid\">".$user->firstname.' '.$user->lastname.'</a>') );
                }
            }            
            
            echo '<div id="tablecontainer">';
                $table->print_html();  // output table
            echo '</div>';
            break;
    }

    print_footer($site);

?>