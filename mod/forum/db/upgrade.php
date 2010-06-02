<?php  //$Id$

// This file keeps track of upgrades to 
// the forum module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_forum_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }

    if ($result && $oldversion < 2007101000) {

    /// Define field timemodified to be added to forum_queue
        $table = new XMLDBTable('forum_queue');
        $field = new XMLDBField('timemodified');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'postid');

    /// Launch add field timemodified
        $result = $result && add_field($table, $field);
    }

//===== 1.9.0 upgrade line ======//

    if ($result and $oldversion < 2007101511) {
        notify('Processing forum grades, this may take a while if there are many forums...', 'notifysuccess');
        //MDL-13866 - send forum ratins to gradebook again
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        // too much debug output
        $db->debug = false;
        forum_update_grades();
        $db->debug = true;
    }

    if ($result && $oldversion < 2007101512) {

    /// Cleanup the forum subscriptions
        notify('Removing stale forum subscriptions', 'notifysuccess');

        $roles = get_roles_with_capability('moodle/course:view', CAP_ALLOW);
        $roles = array_keys($roles);
        $roles = implode(',', $roles);

        $sql = "SELECT fs.userid, f.id AS forumid
                  FROM {$CFG->prefix}forum f
                       JOIN {$CFG->prefix}course c                 ON c.id = f.course
                       JOIN {$CFG->prefix}context ctx              ON (ctx.instanceid = c.id AND ctx.contextlevel = ".CONTEXT_COURSE.")
                       JOIN {$CFG->prefix}forum_subscriptions fs   ON fs.forum = f.id
                       LEFT JOIN {$CFG->prefix}role_assignments ra ON (ra.contextid = ctx.id AND ra.userid = fs.userid AND ra.roleid IN ($roles))
                 WHERE ra.id IS NULL";

        if ($rs = get_recordset_sql($sql)) {
            $db->debug = false;
            while ($remove = rs_fetch_next_record($rs)) {
                delete_records('forum_subscriptions', 'userid', $remove->userid, 'forum', $remove->forumid);
                echo '.';
            }
            $db->debug = true;
            rs_close($rs);
        }
    }

// Changes for Anonymous Forum posts
      if ($result && $oldversion < 2008073000) {
        $table = new XMLDBTable('forum');
        $AnonyField = new XMLDBField('anonymous');
        $AnonyField->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'type');
        $result = $result && add_field($table, $AnonyField);

    }

    if ($result && $oldversion < 2008073000) {

    /// Define field reveal to be added to forum_posts
        $table = new XMLDBTable('forum_posts');
        $field = new XMLDBField('reveal');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'mailnow');

    /// Launch add field reveal
        $result = $result && add_field($table, $field);
    }

    /// Create discussion_subscriptions table
    if ($result && $oldversion < 2008073000) {

        //Create table
        $table = new XMLDBTable('forum_discussion_subscripts');
        $table->comment = 'Keeps tracks of the subscriptions to specific topics';

        //Fields
        $f = $table->addFieldInfo('id',                 XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $f->comment = 'Unique Host ID';
        $f = $table->addFieldInfo('userid',             XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
        $f = $table->addFieldInfo('discussion',         XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, null);

        //Primary Key
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        //Foreign Key on forum_discussions.id
        $table->addKeyInfo('discussion', XMLDB_KEY_FOREIGN, array('discussion'), 'forum_discussions', array('id'));
        //Indexes
        $table->addIndexInfo('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        //Create the table
        $result = $result && create_table($table);
    }


    // Multiattach stuff
    if ($result && $oldversion < 2008080501) {

    /// Define field multiattach to be added to forum
        $table = new XMLDBTable('forum');
        $field = new XMLDBField('multiattach');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1', 'maxbytes');

    /// Launch add field multiattach
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2008080501) {

    /// Define field maxattach to be added to forum
        $table = new XMLDBTable('forum');
        $field = new XMLDBField('maxattach');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '5', 'multiattach');

    /// Launch add field maxattach
        $result = $result && add_field($table, $field);
    }

    // anonymous posts in public forum
    if ($result && $oldversion < 2008080505) {

    /// Define field allowanon to be added to forum
        $table = new XMLDBTable('forum');
        $field = new XMLDBField('allowanon');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'blockperiod');

    /// Launch add field allowanon
        $result = $result && add_field($table, $field);
    }


    // flag to show if post is anonymous or not
    if ($result && $oldversion < 2008080505) {

    /// Define field anonpost to be added to forum_posts
        $table = new XMLDBTable('forum_posts');
        $field = new XMLDBField('anonpost');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'reveal');

    /// Launch add field anonpost
        $result = $result && add_field($table, $field);
    }



    return $result;
}

?>
