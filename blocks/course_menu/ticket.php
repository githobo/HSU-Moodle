<?php
/*
 * Created on May 30, 2007
 * Created by Sam Chaffee with code from previous course_menu file form.php
 *
 */

require_once('../../config.php');
global $CFG, $USER;
require_once($CFG->dirroot.'/lib/blocklib.php');
require_once('form.php');

$id = required_param('id', PARAM_INT);
$comments = optional_param('comments', '', PARAM_RAW);
$subject = optional_param('subject', '', PARAM_ALPHA);

if (! $course = get_record('course', 'id', $id) ) {
        error(get_string('invalidcourse', 'block_course_menu'). $id);
}
require_login($course);
require_capability('block/course_menu:submitticket', get_context_instance(CONTEXT_COURSE, $id));
$ticket = new ticket_form();

if ($ticket->is_cancelled()) {
    redirect("$CFG->wwwroot/course/view.php?id=$id");
} else if ($fromform = $ticket->get_data()) {
    //process the form
    $from->id = $USER->id;
    $from->email = $USER->email;
    $from->firstname = $USER->firstname;
    $from->lastname = $USER->lastname;
    $from->maildisplay = true;

    // recipient must be an object; create it here.
    $admin = get_admin();
    $recipient->email = $CFG->block_course_menu_trouble_ticket_address;
    $recipient->firstname = '';
    $recipient->lastname = '';
    $recipient->maildisplay = true;

    
    
    $courseinfo = "\n".get_string('commentreceived', 'block_course_menu')."\n\n".
                  get_string('courseurl', 'block_course_menu').' '.$CFG->wwwroot .
                  '/course/view.php?&id='.$id."\n".get_string('course', 'block_course_menu').
                  ' '.$course->shortname."\n\n";
    $messagePreface = $courseinfo . "From:  name \nEmail:  $USER->email \nMessage:  \n";

    $comments = filter_text($comments);

    // Ideally this information if it has to be regurgitated, but it's not working for
    // reasons we cannot determine. See block_trouble_ticket.php or thanks.php for details.
/*    if ($CFG->block_trouble_ticket_autoreply_quote == 1) {
        // put in break tags for proper browser display
        $CFG->block_trouble_ticket_quote = str_ireplace("The following trouble ticket has been submitted:", 'You said:', str_ireplace("\n", "<br>\n", $messagePreface)).$comments;
    }*/

    // New variable needed here to send the autoreply without loads of unnecessary data.
    $toadmin = $messagePreface . $comments;
    $toadmin = wordwrap( $toadmin, 1024 );
    // Readying the subject
    if (!empty($id) && $id != SITEID) {
        //set the subject to start with [shortname]
        $subject = '[' . $course->shortname . '] '. $subject;
    } 
    else {
        if (!isset($CFG->block_course_menu_trouble_ticket_subject_prefix)) {
            if ($site = get_site()) {
                set_config('block_course_menu_trouble_ticket_subject_prefix','['. strip_tags($site->shortname) .']');
            } 
            else {
                set_config('block_course_menu_trouble_ticket_subject_prefix', '[moodle contact]');
            }
        } 
        $subject = $CFG->block_course_menu_trouble_ticket_subject_prefix . $subject;
    }
    $subject = clean_param($subject, PARAM_NOTAGS);
    // Sending the actual e-mail
    /// Check for error condition the hard way. Workaround for a bug in moodle discovered by Dan Marsden. If email is not configured properly and email_to_user() is called then "ERROR:" with no message prints out.
    ob_start();
    email_to_user($recipient, $from, stripslashes_safe($subject), stripslashes_safe($toadmin));
    $error = ob_get_contents();
    ob_end_clean();
    if ($CFG->debug && preg_match("/^ERROR:/", $error) ) {
        error('An error was encountered trying to send email in comments.php. It is likely that your email settings are not configured properly. The error reported was "'. $error);
    }
    add_to_log($course->id, 'Trouble Ticket', 'send mail', '', "To:$recipient->email; From:$from->email; Subject:$subject");
    
    //Once the data is entered, redirect the user to give them visual confirmation
    redirect('thanks.php?id='. $id);
} else {
    //form didn't validate or this is the first display
    $site = get_site();
    print_header(strip_tags($site->fullname), $site->fullname, build_navigation(get_string('troubletickettitle', 'block_course_menu')), '', '<meta name="description" content="'. s(strip_tags($site->summary)) .'">',
             true, '', '');
    if (!empty($CFG->block_course_menu_trouble_ticket_specialinstructions)) {
        notify($CFG->block_course_menu_trouble_ticket_specialinstructions);
    }
    if (!empty($CFG->block_course_menu_trouble_ticket_faq_url) && !empty($CFG->block_course_menu_trouble_ticket_faq_linktext)) {
        echo '<a href="'. $CFG->block_course_menu_trouble_ticket_faq_url . '">';
        notify($CFG->block_course_menu_trouble_ticket_faq_linktext);
        echo '</a>';
    }
    $toform = array();
    $toform['id'] = $id;
    $toform['name'] = fullname($USER);
    $toform['email'] = (!empty($email) ? $email : $USER->email);
    $toform['to'] = $CFG->block_course_menu_trouble_ticket_address;
    $toform['subject'] = $subject;
    $toform['comments'] = $comments;
    $ticket->set_data($toform);
    $ticket->display();
    print_footer();
}
 
?>
