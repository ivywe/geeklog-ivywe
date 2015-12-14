<?php
// +---------------------------------------------------------------------------+
// | テーブル構造調整　当面のテスト用
// +---------------------------------------------------------------------------+
// public_html/admin/plugins/databox/toolsfortest/updatetable0509.php
// 20120509-0509 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_SCRIPT', 'updatetable0509.php');

require_once ('../../../../lib-common.php');

// データベーステーブル名 - 原則変更禁止
$_TABLES['DATABOX_base']    		= $_DB_table_prefix . 'databox_base';
$_TABLES['DATABOX_category']    	= $_DB_table_prefix . 'databox_category';
$_TABLES['DATABOX_addition']    	= $_DB_table_prefix . 'databox_addition';
//
$_TABLES['DATABOX_def_category']    = $_DB_table_prefix . 'databox_def_category';
$_TABLES['DATABOX_def_field']    	= $_DB_table_prefix . 'databox_def_field';
$_TABLES['DATABOX_def_group']    	= $_DB_table_prefix . 'databox_def_group';
$_TABLES['DATABOX_stats']    		= $_DB_table_prefix . 'databox_stats';
//
$_TABLES['DATABOX_mst']    = $_DB_table_prefix . 'databox_mst';

//
$_TABLES['DATABOX_def_xml']    = $_DB_table_prefix . 'databox_def_xml';

//
$_TABLES['DATABOX_def_category_name']    = $_DB_table_prefix . 'databox_def_category_name';
$_TABLES['DATABOX_def_field_name']    = $_DB_table_prefix . 'databox_def_field_name';
$_TABLES['DATABOX_def_group_name']    = $_DB_table_prefix . 'databox_def_group_name';

//20120509add----->
$_TABLES['DATABOX_def_fieldset']    = $_DB_table_prefix . 'databox_def_fieldset';
$_TABLES['DATABOX_def_fieldset_assignments']    = $_DB_table_prefix . 'databox_def_fieldset_assignments';



function update_tables()
{

    global $_TABLES;
    global $_CONF;

    //マスタのデータ
    $_SQL =array();

	//=====SQL　定義　ココから
    //  更新が必要なところの条件を変更して使用してください
   if (1===0){
		//カテゴリ定義に親カテゴリIDとグループID追加
		$_SQL[] = "
		CREATE TABLE {$_TABLES['DATABOX_def_fieldset']} (
		`fieldset_id` int(11) NOT NULL,
		`name` varchar(64) NOT NULL,
		`description` mediumtext,
		`udatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`uuid` mediumint(8) NOT NULL,
		PRIMARY KEY (`fieldset_id`)
		) ENGINE=MyISAM
		";

		//属性セット関連
		$_SQL[] = "
		CREATE TABLE {$_TABLES['DATABOX_def_fieldset_assignments']} (
		`seq` int(11) NOT NULL AUTO_INCREMENT,
		`fieldset_id` int(11) NOT NULL,
		`field_id` int(11) NOT NULL,
		PRIMARY KEY (`seq`),
		KEY `fieldset_id` (`fieldset_id`)
		) ENGINE=MyISAM
		";

        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_base']}
		ADD `fieldset_id` int(11) NOT NULL default 0 AFTER `orderno`,
       ";
    }
	//=====SQL　定義　ココまで
	
	//------------------------------------------------------------------
    for ($i = 1; $i <= count($_SQL); $i++) {
        $w=current($_SQL);
        DB_query(current($_SQL));
        next($_SQL);
    }
    if (DB_error()) {
        COM_errorLog("error DataBox table update ",1);
        return false;
    }

    COM_errorLog("Success - DataBox table update",1);
    return "end";
}


function fncDisply()
{
    $retval = "";
    $retval .= "<!DOCTYPE    HTML PUBLIC '-//W3C//DTD HTML   4.01 Transitional//EN'>".LB;
    $retval .= "<html>".LB;
    $retval .= "<head>".LB;
    $retval .= "    <title>DataBox update tables</title>".LB;
    $retval .= "</head>".LB;
    $retval .= "<body   bgcolor='#ffffff'>".LB;
    $retval .= "<h1>DataBox update tables. </h1>".LB;
    $retval .= "<form action="."'".THIS_SCRIPT."'"."method='post'>".LB;
    $retval .= "    <input type='submit' name='action' value='submit'>".LB;
    $retval .= "    <input type='submit' name='action' value='cancel'>".LB;
    $retval .= "</form>".LB;
    $retval .= "</body>".LB;
    $retval .= "</html>".LB;

    return $retval ;

}


$action ="";
if (isset ($_REQUEST['action'])) {
    $action = COM_applyFilter($_REQUEST['action'],false);
}

$display="";
if  ($action ==""){
    $display=fncDisply();
}elseif ($action =="submit"){
    $display=update_tables();
}else{
    $display ="cancel!";
}
echo $display;
?>
