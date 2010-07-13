<?php
/***
 * define capabilities for this function
 **/

$mod_turningtech_capabilities = array(
  'mod/turningtech:manage' => array(
    'captype' => 'write',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'teacher' => CAP_ALLOW,
      'editingteacher' => CAP_ALLOW
    )
  )
);
?>