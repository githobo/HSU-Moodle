<?php
    require_once(dirname(__FILE__) . '/../../../config.php');

    echo stripslashes(purify_html($_POST['text']));

?>