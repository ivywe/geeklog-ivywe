<?php
//20101222 update DataBox UserBox FormBox assist 共通

if (strpos ($_SERVER['PHP_SELF'], 'additiondata_functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

function fncexec (
    $pi_name
)
{
    global $_TABLES;

    $table=$_TABLES[strtoupper($pi_name).'_def_field'];
    $table1=$_TABLES[strtoupper($pi_name).'_base'];
    $table2=$_TABLES[strtoupper($pi_name).'_addition'];
	
	
    $table=$_TABLES[strtoupper($pi_name).'_addition'];
	
    $table1=$_TABLES[strtoupper($pi_name).'_base'];
    $table2=$_TABLES[strtoupper($pi_name).'_def_fieldset_assignments'];
    $table3=$_TABLES[strtoupper($pi_name).'_def_field'];
    $table4=$_TABLES[strtoupper($pi_name).'_addition'];
	
	
    $retval = '';
	
    //7 = 'オプションリスト';
    //8 = 'ラジオボタンリスト';
	
	$sql = "INSERT INTO ".$table.LB;
	$sql .= " ( `id` ,`field_id` ,`value` )";
	$sql .= " SELECT t1.id, t2.field_id,";
	$sql .= " CASE WHEN TYPE =7";
	$sql .= " OR TYPE =8";
	$sql .= " THEN \"0\"";
	$sql .= " END";
	$sql .= " FROM ".$table1." AS t1";
	$sql .= " , ".$table2." AS t2";
	$sql .= " , ".$table3." AS t3";
	$sql .= " WHERE t1.fieldset_id = t2.fieldset_id";
	$sql .= " AND t2.field_id = t3.field_id";
	$sql .= " AND t1.id NOT";
	$sql .= " IN (";
	$sql .= " SELECT t4.id";
	$sql .= " FROM ".$table4." AS t4";
	$sql .= " WHERE t4.field_id = t2.field_id";
	$sql .= " )";

    DB_query($sql);

    $rt=" finish! ";
    return $rt;
}


function fncDisply(
    $pi_name
)
{

    $retval = "";
    $retval .= "<!DOCTYPE    HTML PUBLIC '-//W3C//DTD HTML   4.01 Transitional//EN'>".LB;
    $retval .= "<html>".LB;
    $retval .= "<head>".LB;
    $retval .= "    <title>".strtoupper($pi_name) ." insert addtion data </title>".LB;
    $retval .= "</head>".LB;
    $retval .= "<body   bgcolor='#ffffff'>".LB;
    $retval .= "<h1>".strtoupper($pi_name)." insert addtion data </h1>".LB;
    $retval .= "<form action="."'".THIS_SCRIPT."'"."method='post'>".LB;
    $retval .= "    <input type='submit' name='action' value='submit'>".LB;
    $retval .= "    <input type='submit' name='action' value='cancel'>".LB;
    $retval .= "</form>".LB;
    $retval .= "</body>".LB;
    $retval .= "</html>".LB;

    return $retval ;

}

v?>