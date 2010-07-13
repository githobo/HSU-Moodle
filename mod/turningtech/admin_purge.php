<?php
require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
require_once("lib.php");
require_once("lib/forms/turningtech_admin_purge_form.php");

admin_externalpage_setup('managemodules');

$form = new turningtech_admin_purge_form();
$redirect_url = "{$CFG->wwwroot}/admin/settings.php?section=modsettingturningtech";

if($form->is_cancelled()) {
  redirect($redirect_url);
}
else if($data = $form->get_data()) {
  $purged = DeviceMap::purgeGlobal();
  if($purged === FALSE) {
    turningtech_set_message(get_string('admincouldnotpurge','turningtech'));
    redirect($redirect_url);
  }
  else {
    turningtech_set_message(get_string('adminalldevicespurged','turningtech'));
    turningtech_set_message(get_string('numberdevicespurged','turningtech',$purged));
    redirect($redirect_url);
  }
}
// --------- page output -----------
admin_externalpage_print_header();

echo turningtech_show_messages();

$form->display();

print_footer();
?>