<?php

//  Subscribes to or unsubscribes from a discussion in a forum
    global $CFG, $USER;

    // Tried with $CFG->dirroot and it apparently is empty?
    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('d', PARAM_INT);

    $user = $USER;

    // Check to make sure the discussion, forum and course all exist and get info
    if (! $discussion = get_record("forum_discussions", "id", $id)) {
        error("Discussion ID was incorrect");
    }

    if (! $forum = get_record("forum", "id", $discussion->forum)) { 
        error("Discussion doesn't belong to a forum.");
    }

    if (! $course = get_record("course", "id", $forum->course)) { 
        error("Forum doesn't belong to a course.");
    }

    if ($cm = get_coursemodule_from_instance("forum", $discussion->forum, $discussion->course)) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    } else {
        $cm->id = 0;
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    }

    // Check credentials. Part of the group (if required), logged on, and not a guest.
    if (groupmode($course, $cm)
                and !forum_is_subscribed($user->id, $forum->id)
                and !has_capability('moodle/site:accessallgroups', $context)) {
        if (!mygroupid($course->id)) {
            error('Sorry, but you must be a group member to subscribe.');
        }
    }

    require_login($course->id, false, $cm);

    // Guests can't subscribe
    if (isguest()) {   
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http','https', $wwwroot);
        }

        $strforums = get_string('modulenameplural', 'forum');
        if ($course->id != SITEID) {
            print_header($course->shortname, $course->fullname,
                 "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> 
                  <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>", '', '', true, "", navmenu($course, $cm));
        } else {
            print_header($course->shortname, $course->fullname,
                 "<a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> 
                  <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>", '', '', true, "", navmenu($course, $cm));
        }
        notice_yesno(get_string('noguestsubscribe', 'forum').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $_SERVER['HTTP_REFERER']);
        print_footer($course);
        exit;
    }

    $returnto = forum_go_back_to('discuss.php?d='.$id);

    // Can't subscribe or unsubscribe if forum is force subscribed.
    if (forum_is_forcesubscribed($forum->id)) {
        redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
    }

    // User is verified, subscribe or unsubscribe them.
    if (forum_discussion_subscriber($user->id,$discussion->id)) { 
        if (forum_discussion_unsubscribe($user->id,$discussion->id)) { 
            add_to_log($course->id, 'forum', 'unsubscribe', 'view.php?f='.$forum->id, $forum->id, $cm->id);
            redirect($returnto, get_string("discussionunsubscribed", "forum"),1);
        } else { 
            error("Could not unsubscribe you from that discussion", $_SERVER["HTTP_REFERER"]);
        }
    } else { 
        if (forum_discussion_subscribe($user->id,$discussion->id)) { 
            add_to_log($course->id, 'forum', 'subscribe', 'view.php?f='.$forum->id, $forum->id, $cm->id);
            redirect($returnto, get_string("discussionsubscribed", "forum"),1);
        } else { 
            error("Could not subscribe you to that discussion", $_SERVER["HTTP_REFERER"]);
        }
    }
?>
