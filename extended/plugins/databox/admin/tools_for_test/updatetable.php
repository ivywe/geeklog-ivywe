<?php
// $Id: updatetable.php 160 2008-06-19 00:46:16Z tacahi $
// +---------------------------------------------------------------------------+
// | テーブル構造調整　当面のテスト用
// +---------------------------------------------------------------------------+
// public_html/admin/plugins/databox/toolsfortest/updateconfvalues.php
// 20101004-1021 tsuchitani AT ivywe DOT co DOT jp

define ('THIS_SCRIPT', 'updatetable.php');

require_once ('../../../../lib-common.php');

// データベーステーブル名 - 原則変更禁止
$_TABLES['DATABOX_base']    = $_DB_table_prefix . 'databox_base';
$_TABLES['DATABOX_category']    = $_DB_table_prefix . 'databox_category';
$_TABLES['DATABOX_addition']    = $_DB_table_prefix . 'databox_addition';
//
$_TABLES['DATABOX_def_category']    = $_DB_table_prefix . 'databox_def_category';
$_TABLES['DATABOX_def_field']    = $_DB_table_prefix . 'databox_def_field';
$_TABLES['DATABOX_def_groupe']    = $_DB_table_prefix . 'databox_def_groupe';

$_TABLES['DATABOX_def_category_name']    = $_DB_table_prefix . 'databox_def_category_name';
$_TABLES['DATABOX_def_field_name']    = $_DB_table_prefix . 'databox_def_field_name';
$_TABLES['DATABOX_def_groupe_name']    = $_DB_table_prefix . 'databox_def_groupe_name';
//
$_TABLES['DATABOX_mst']    = $_DB_table_prefix . 'databox_mst';
//
$_TABLES['DATABOX_stats']    = $_DB_table_prefix . 'databox_stats';

//@@@@@groupe
//$_TABLES['DATABOX_def_groupe']    = $_DB_table_prefix . 'databox_def_groupe';
//$_TABLES['DATABOX_def_group']    = $_DB_table_prefix . 'databox_def_group';
//$_TABLES['DATABOX_def_groupe_name']    = $_DB_table_prefix . 'databox_def_groupe_name';
//$_TABLES['DATABOX_def_group_name']    = $_DB_table_prefix . 'databox_def_group_name';


function update_tables()
{

    global $_TABLES;
    global $_CONF;

    //マスタのデータ
    $_SQL =array();

    //  更新が必要なところの条件を変更して使用してください
   if (1===0){
        //カテゴリ定義に親カテゴリIDとグループID追加
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_category']}
        ADD `parent_id` INT( 11 ) NULL AFTER `orderno`
       ";
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_category']}
        ADD `group_id` INT(11) NOT NULL DEFAULT '0' AFTER `parent_id`
        ";
        //グループにコード追加
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_group']}
        ADD `code` VARCHAR(40) NULL AFTER `group_id`
       ";
    }
   if (1===0){
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_category']}
        CHANGE `category_id` `category_id` INT( 11 )
        NOT NULL AUTO_INCREMENT
        ";
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_group']}
        ADD `parent_flg` BINARY( 1 )
        NOT NULL DEFAULT '0' AFTER `orderno`
        ";

    }
//rename groupe groupe
    //20100921-20101004
   if (1===0){
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_category']}
        ADD `defaulttemplatesdirectory` VARCHAR( 40 )
        NULL AFTER `description`
        ";
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_base']}
        ADD `defaulttemplatesdirectory` VARCHAR( 40 )
        NULL AFTER `description`
        ";
    }
    if (1===0){
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_category']}
        CHANGE `group_id` `categorygroup_id` INT( 11 )
        NOT NULL DEFAULT '0'NULL AFTER `description`
        ";
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_field']}
        CHANGE `group_id` `fieldgroup_id` INT( 11 )
        NOT NULL DEFAULT '0'NULL AFTER `description`
        ";
    }

    //20101007-
    // databox.edit databox.submit databox.moderate (gl_feature) delete
    // databox.admin (gl_feature) add
    if (1===0){
        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_def_field']}
        ADD `allow_display` BINARY( 1 )
        NOT NULL DEFAULT '0' AFTER `orderno` ,
        ADD `allow_edit` BINARY( 1 )
        NOT NULL DEFAULT '0' AFTER `allow_display`
        ";

    }
    if (1===0){

        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_base']}
        ADD `orderno` INT( 2 )
        NULL DEFAULT '0' AFTER `expired`
        ";


    }

    //20110131
    // databox.submit (gl_feature) add
    if (1===0){

        $_SQL[] = "
        INSERT INTO {$_TABLES['features']} (
        `ft_name` ,
        `ft_descr` ,
        `ft_gl_core`
        )
        VALUES (
         'databox.submit', 'can submit data to databox plugin', '0'
        )
        ";


    }

    //20110208
    if (1===0){

        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_base']}
        CHANGE `orderno` `orderno` INT( 2 ) NOT NULL DEFAULT '0'
        ";

        $_SQL[] = "
        ALTER TABLE {$_TABLES['DATABOX_base']}
        CHANGE `expired` `expired` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
        ";

    }
    //20110622
    // databox.edit (gl_feature) add
    if (1===0){

        $_SQL[] = "
        INSERT INTO {$_TABLES['features']} (
        `ft_name` ,
        `ft_descr` ,
        `ft_gl_core`
        )
        VALUES (
         'databox.edit', 'can edit data to databox plugin', '0'
        )
        ";


    }
    //20110803
    // group_id=0 add
    if (1===1){

		$_SQL[] = "
		INSERT INTO {$_TABLES['DATABOX_def_group']} (
		`group_id` 
		)
		VALUES (
		'0'
		);
		";


    }
	
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
