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
$_TABLES['USERBOX_base']    = $_DB_table_prefix . 'userbox_base';
$_TABLES['USERBOX_category']    = $_DB_table_prefix . 'userbox_category';
$_TABLES['USERBOX_addition']    = $_DB_table_prefix . 'userbox_addition';
//
$_TABLES['USERBOX_def_category']    = $_DB_table_prefix . 'userbox_def_category';
$_TABLES['USERBOX_def_field']    = $_DB_table_prefix . 'userbox_def_field';
$_TABLES['USERBOX_def_group']    = $_DB_table_prefix . 'userbox_def_group';

//
$_TABLES['USERBOX_mst']    = $_DB_table_prefix . 'userbox_mst';
//
$_TABLES['USERBOX_stats']    = $_DB_table_prefix . 'userbox_stats';


function update_tables()
{

    global $_TABLES;
    global $_CONF;

    //マスタのデータ
    $_SQL =array();

    //  更新が必要なところの条件を変更して使用してください

    //20110208
    if (1===0){

        $_SQL[] = "
        ALTER TABLE {$_TABLES['USERBOX_base']}
        CHANGE `orderno` `orderno` INT( 2 ) NOT NULL DEFAULT '0'
        ";

        $_SQL[] = "
        ALTER TABLE {$_TABLES['USERBOX_base']}
        CHANGE `expired` `expired` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
        ";

    }
    //20110622
    // userbox.edit (gl_feature) add
    if (1===0){

        $_SQL[] = "
        INSERT INTO {$_TABLES['features']} (
        `ft_name` ,
        `ft_descr` ,
        `ft_gl_core`
        )
        VALUES (
		'userbox.edit', 'can edit profile to userbox plugin', '0'
        )
		";
        $_SQL[] = "
        INSERT INTO {$_TABLES['features']} (
        `ft_name` ,
        `ft_descr` ,
        `ft_gl_core`
        )
        VALUES (
		'userbox.joingroup', 'can edit join group to userbox plugin', '0'
        )
		";
	}
    //20110803
    // group_id=0 add
    if (1===0){

		$_SQL[] = "
		INSERT INTO {$_TABLES['USERBOX_def_group']} (
		`group_id` 
		)
		VALUES (
		'0'
		);
		";


    }
    //20110826
    // group_id=0 add
    if (1===0){

		$_SQL[] = "
		ALTER TABLE {$_TABLES['USERBOX_base']}
		ADD `eyechatchingimage` MEDIUMTEXT NULL AFTER `defaulttemplatesdirectory` 
		";


    }
	
    //20110915
    // group_id=0 add
    if (1===1){
        $_SQL[] = "
        INSERT INTO {$_TABLES['features']} (
        `ft_name` ,
        `ft_descr` ,
        `ft_gl_core`
        )
        VALUES (
		'userbox.user', 'Can register to UserBox', '0'
        )
		";
	}
	
    //------------------------------------------------------------------
    for ($i = 1; $i <= count($_SQL); $i++) {
        $w=current($_SQL);
        DB_query(current($_SQL));
        next($_SQL);
    }
    if (DB_error()) {
        COM_errorLog("error UserBox table update ",1);
        return false;
    }

    COM_errorLog("Success - UserBox table update",1);
    return "end";
}


function fncDisply()
{
    $retval = "";
    $retval .= "<!DOCTYPE    HTML PUBLIC '-//W3C//DTD HTML   4.01 Transitional//EN'>".LB;
    $retval .= "<html>".LB;
    $retval .= "<head>".LB;
    $retval .= "    <title>UserBox update tables</title>".LB;
    $retval .= "</head>".LB;
    $retval .= "<body   bgcolor='#ffffff'>".LB;
    $retval .= "<h1>UserBox update tables. </h1>".LB;
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