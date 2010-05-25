<?php
require_once('../../config.php');
global $CFG, $USER;

if (! $site = get_site()) {
    redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
}

print_header(strip_tags($site->fullname), $site->fullname,
                build_navigation(get_string('thankyou', 'block_course_menu')), '',
                '<meta name="description" content="'. s(strip_tags($site->summary)). '">',
                true, '', '');

$fromcourse = optional_param('id', 0, PARAM_INT);

if (!is_numeric($fromcourse) || $fromcourse == SITEID) {
    $continuepage = $CFG->wwwroot . '?&amp;sesskey='. $USER->sesskey;
} else {
//	print_object($course);
    $continuepage = $CFG->wwwroot . '/course/view.php?&amp;id='. $fromcourse . '&amp;sesskey='. $USER->sesskey;
}
?>
<div class="centerblock">
    <div id="blockcontent">
        <?php
            // The custom auto-reply message.
            p($CFG->block_course_menu_trouble_ticket_autoreply);

            if ((!empty($CFG->block_course_menu_trouble_ticket_autoreply_url)) && (!empty($CFG->block_course_menu_trouble_ticket_autoreply_linktext))) {
                echo '<p>';
				print_string('seealso', 'block_course_menu');

				// take of the "http://"
				$newURL = substr($CFG->block_course_menu_trouble_ticket_autoreply_url, 7);

				// I recall reading somewhere that echo statements were stylistically frowned upon in Moodle,
				// but I could not find a built-in print function that would print the HTML properly, so 
				// Moodle can just go ahead and frown.
                echo '<a href="'.$CFG->block_course_menu_trouble_ticket_autoreply_url.'">'.
                     $CFG->block_course_menu_trouble_ticket_autoreply_linktext.'</a></p>';
            }

            // Unable to quote the original message. See block_trouble_ticket.php for details.
/*            if ($CFG->block_trouble_ticket_autoreply_quote == 1) {
				// This doesn't work, and I don't know why. If you print_object($CFG), it shows
				// no block_trouble_ticket_quote, but I'm not sure why it's getting destroyed
                // when all the other block_trouble_ticket variables are present.
                echo $CFG->block_trouble_ticket_quote;
            }*/
        ?>


    </div>
    <p><a target="_top" href="<?php echo $continuepage;?>"><?php print_string('continue', 'block_course_menu') ?></a></p>
</div>
<?php print_footer(); ?>