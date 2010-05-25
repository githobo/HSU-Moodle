<?php
/*
 * Created on May 29, 2007
 * Created by Sam Chaffee
 *
 * This script defines the ticket form for inclusion by ticket.php.
 */
require_once("$CFG->libdir/formslib.php");

class ticket_form extends moodleform {
    
    function definition() {
        global $CFG;
        
        $mform =& $this->_form;
        
        //the header
        $mform->addElement('header', 'ticket', get_string('troubletickettitle', 'block_course_menu'));
        
        //the name field
        $mform->addElement('static', 'name', get_string('name').': ');
        $mform->setType('name', PARAM_TEXT);
        
        //the email field
        $mform->addElement('static', 'email', get_string('email').': ');
        $mform->setType('name', PARAM_TEXT);
        
        //the to field
        $mform->addElement('static', 'to', get_string('to').': ');
        $mform->setType('to', PARAM_TEXT);
        
        //the subject field
        $mform->addElement('text', 'subject', get_string('subject', 'block_course_menu').': ');
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('required'), 'required');
        
        //the comments field
        $mform->addElement('htmleditor', 'comments', get_string('comments', 'block_course_menu').': ');
        $mform->setType('comments', PARAM_RAW);
        $mform->addRule('comments', get_string('required'), 'required');
        
        //hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'sesskey', sesskey());
        $buttonarray = array();
        $buttonarray[] =& $mform->createElement('submit', 'submit', get_string('submit'));
        $buttonarray[] =& $mform->createElement('reset', 'reset', get_string('reset'));
        $buttonarray[] =& $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonarr', '', array(' '), false);
        $mform->closeHeaderBefore('buttonarr');
    }
}

?>
