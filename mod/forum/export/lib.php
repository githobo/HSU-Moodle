<?php
	require_once('../../../config.php');
	require_once($CFG->dirroot.'/mod/forum/lib.php');
	
	function forum_export_get_forum_posters($forumid) {
		global $CFG;
		return get_records_sql("SELECT DISTINCT u.id, u.lastname, u.firstname
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}forum_discussions d,
                                      {$CFG->prefix}forum_posts p
                                 WHERE d.forum = '$forumid' and
                                       p.discussion = d.id and
                                       u.id = p.userid");
	}
	
	function forum_export_get_posts($forumid, $cm, $userid, $forumsort) {
	//get the posts
	    $posts = array();
		if (!empty($userid)) {
			$userposts = forum_get_user_posts($forumid, $userid);
			$posts = $userposts;
		} else {
			$discussions = forum_get_discussions($cm, $forumsort);
			$forumsort = $forumsort == 'd.timemodified DESC' ? 'p.modified DESC' : $forumsort;
		    foreach ($discussions as $discussion) {
		    	$disposts[] = forum_get_all_discussion_posts($discussion->discussion, $forumsort);
		    }
		    foreach ($disposts as $dispost) {
		    	foreach ($dispost as $post) {
		    		$posts[$post->id] = $post;
		    	}
		    }
		}
		
		return $posts;
	}
	
	function forum_export_print_posts($posts, $forum, $course) {
		
		global $CFG;
	    
		print_container_start(false, 'forumprint');
	    foreach ($posts as $post) {
	        $post->forum = $forum->id;
	        $post->course = $course->id;
	    	echo '<a id="p'.$post->id.'"></a>';
		    echo '<table cellspacing="0" class="forumpost">';
		
		    // Picture
		    $postuser = new object();
		    $postuser->id        = $post->userid;
		    $postuser->firstname = $post->firstname;
		    $postuser->lastname  = $post->lastname;
		    $postuser->imagealt  = $post->imagealt;
		    $postuser->picture   = $post->picture;
		    
		                          // Anonymous Code
		    $anonymous = get_field('forum','anonymous','id', $forum->id);
		    $reveal = get_field('forum_posts', 'reveal','id',$post->id);
		    if ($reveal) { // preserve ability to use ternary operators
		        $anonymous = 0;
		    }
		
		    echo '<tr class="header"><td class="picture left">';
		    print_user_picture($anonymous ? 1 : $postuser, $course->id, 1);
		    echo '</td>';
		
		    if ($post->parent) {
		        echo '<td class="topic">';
		    } else {
		        echo '<td class="topic starter">';
		    }
		
		    if (!empty($post->subjectnoformat)) {
		        echo '<div class="subject">'.$post->subject.'</div>';
		    } else {
		        echo '<div class="subject">'.format_string($post->subject).'</div>';
		    }
		
		    echo '<div class="author">';
		    $fullname = fullname($postuser);
		    $by = new object();
		    
		                          
		    if($anonymous) {
		         $by->name = get_string('anonymous', 'forum');  
		    } else { // not anonymous
		         $by->name = $fullname;
		    }
		
		    $by->date = userdate($post->modified);
		    print_string('bynameondate', 'forum', $by);
		    echo '</div></td></tr>';
		
		    echo '<tr><td class="picture left"></td>';
		
		// Actual content
		
		    echo '<td class="content">'."\n";
		
		    if ($post->attachment) {
		        echo '<div class="attachments">';
		        $attachedimages = forum_print_attachments($post);
		        echo '</div>';
		    } else {
		        $attachedimages = '';
		    }
		
		
		    $options = new object();
		    $options->para      = false;
		    $options->trusttext = true;
		    
	        // Print whole message
	        echo format_text($post->message, $post->format, $options, $course->id);
	
	        echo $attachedimages;
	        
	        echo '</td></tr></table>'."\n\n";
	    }
	    print_container_end();
	}
	
	function forum_export_download($posts, $forum, $course, $format) {
		
		$function = 'forum_export_download_' . $format;
		$function($posts, $forum, $course);
	}
	
	function forum_export_download_txt($posts, $forum, $course) {
		
		$downloadfilename = clean_filename("$course->shortname $forum->name.txt");
		$separator = "\t";

		/// Print header to force download
        @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        @header('Pragma: no-cache');
        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=\"$downloadfilename\"");
        
        //print the headings
        echo get_string('subject', 'forum').$separator.
        	 get_string('postedby', 'forum').$separator.
        	 get_string('postedon', 'forum').$separator.
        	 get_string('message', 'forum').$separator.
        	 get_string('attachments', 'forum')."\n";
        	 
		$anonymous = false;
    	if ($forum->anonymous) {
    		$anonymous = true;
    	}
        	 
        //print the posts
        foreach($posts as $post) {
            $post->forum = $forum->id;
            $post->course = $course->id;
        	//fullname
    		if ($anonymous) {
    			$fullname = get_string('anonymous', 'forum');
    		} else {
    			$postuser = new object();
    			$postuser->id        = $post->userid;
		    	$postuser->firstname = $post->firstname;
		    	$postuser->lastname  = $post->lastname;
    			$fullname = fullname($postuser);
    		}
    		$message = str_replace($separator, ' ', strip_tags($post->message));
    		$title = strip_tags($post->subject);
    		$date = userdate($post->modified);
    		$attachments = '';
    		$attachmentarr = forum_get_attachments($post);
    		if (!empty($attachmentarr)) {
    		    $attachments = implode(', ', $attachmentarr);
    		}
    		
    		echo $title.$separator.$fullname.$separator.$date.$separator.$message.$separator.$attachments."\n";
        }
        exit;
	}
	
	function forum_export_download_xls($posts, $forum, $course) {
		global $CFG;
        require_once($CFG->dirroot.'/lib/excellib.class.php');
        
        $downloadfilename = clean_filename("$course->shortname $forum->name.xls");
        
    /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($forum->name);
        
    /// Add the headers
    	$myxls->write_string(0, 0, get_string('subject', 'forum'));
    	$myxls->write_string(0, 1, get_string('postedby', 'forum'));
    	$myxls->write_string(0, 2, get_string('postedon', 'forum'));
    	$myxls->write_string(0, 3, get_string('message', 'forum'));
    	$myxls->write_string(0, 4, get_string('attachments', 'forum'));
    	
    	$anonymous = false;
    	if ($forum->anonymous) {
    		$anonymous = true;
    	}
    	
    	$row = 1;
    	
    	foreach ($posts as $post) {
    	    $post->forum = $forum->id;
    	    $post->course = $course->id;
    		//fullname
    		if ($anonymous) {
    			$fullname = get_string('anonymous', 'forum');
    		} else {
    			$postuser = new object();
    			$postuser->id        = $post->userid;
		    	$postuser->firstname = $post->firstname;
		    	$postuser->lastname  = $post->lastname;
    			$fullname = fullname($postuser);
    		}
    		$message = strip_tags($post->message);
    		$title = strip_tags($post->subject);
    		$date = userdate($post->modified);
    	    $attachments = '';
            $attachmentarr = forum_get_attachments($post);
            if (!empty($attachmentarr)) {
                $attachments = implode(', ', $attachmentarr);
            }
    		
    		$myxls->write_string($row, 0, $title);
    		$myxls->write_string($row, 1, $fullname);
    		$myxls->write_string($row, 2, $date);
    		$myxls->write_string($row, 3, $message);
    		$myxls->write_string($row, 4, $attachments);
    		
    		$row++;
    	}
    	
    	$workbook->close();
    	
    	exit;
	}
	
	function forum_export_download_ods($posts, $forum, $course) {
		global $CFG;
        require_once($CFG->dirroot.'/lib/odslib.class.php');
        
        $downloadfilename = clean_filename("$course->shortname $forum->name.ods");
        
    /// Creating a workbook
        $workbook = new MoodleODSWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($forum->name);
        
    /// Add the headers
    	$myxls->write_string(0, 0, get_string('subject', 'forum'));
    	$myxls->write_string(0, 1, get_string('postedby', 'forum'));
    	$myxls->write_string(0, 2, get_string('postedon', 'forum'));
    	$myxls->write_string(0, 3, get_string('message', 'forum'));
    	$myxls->write_string(0, 4, get_string('attachments', 'forum'));
    	
    	$anonymous = false;
    	if ($forum->anonymous) {
    		$anonymous = true;
    	}
    	
    	$row = 1;
    	
    	foreach ($posts as $post) {
    	    $post->forum = $forum->id;
            $post->course = $course->id;
    		//fullname
    		if ($anonymous) {
    			$fullname = get_string('anonymous', 'forum');
    		} else {
    			$postuser = new object();
    			$postuser->id        = $post->userid;
		    	$postuser->firstname = $post->firstname;
		    	$postuser->lastname  = $post->lastname;
    			$fullname = fullname($postuser);
    		}
    		$message = strip_tags($post->message);
    		$title = strip_tags($post->subject);
    		$date = userdate($post->modified);
    	    $attachments = '';
    	    $attachmentarr = forum_get_attachments($post);
            if (!empty($attachmentarr)) {
                $attachments = implode(', ', $attachmentarr);
            }
    		
    		$myxls->write_string($row, 0, $title);
    		$myxls->write_string($row, 1, $fullname);
    		$myxls->write_string($row, 2, $date);
    		$myxls->write_string($row, 3, $message);
    		$myxls->write_string($row, 4, $attachments);
    		
    		
    		$row++;
    	}
    	
    	$workbook->close();
    	
    	exit;
	}
?>