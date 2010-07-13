<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/version.php');
print_header_simple(get_string('supportinfo','turningtech'));


echo get_string('moduleversion','turningtech') . ": " . $module->version;
//phpinfo();

print_footer();

?>