<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// $Id: plugins/databox/custom/functions_custom.inc
// call from funstions.inc
if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions_custom.inc') !== false) {
    die('This file can not be used on its own!');
}

function databox_custom_templateSetVars(
	$templatename
	, &$template
) 
// +---------------------------------------------------------------------------+
// | サイト独自のテーマ変数を編集します　add custom template variables
// | call from databox_data (functions_autotag.inc)
// +---------------------------------------------------------------------------+
// | 引数 $templatename :"data"
// | 引数 $template
// +---------------------------------------------------------------------------+
{
    switch ($templatename) {
		case 'data':
			$aaa = COM_applyFilter($_POST['aaa']);
			$template->set_var ('aaa', $aaa);
			break;
	}
}

?>
