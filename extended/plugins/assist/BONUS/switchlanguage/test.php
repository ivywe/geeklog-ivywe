<?php
require_once('../lib-common.php');
require_once('phpblock_switchlanguage.php');

$display=phpblock_switchlanguage();
$display=COM_createHTMLDocument($display);
COM_output($display);

?>
