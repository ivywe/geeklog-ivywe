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

    $retval = '';


    $sql = "SELECT ";
    $sql .= " field_id";
    $sql .= " ,type";
    $sql .= " ,selection";

    $sql .= " FROM ";
    $sql .= $table;
    $sql .= " order by field_id ";

    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);

            $field_id=$A['field_id'];
            $type=$A['type'];
            $selection=$A['selection'];



            $sql2="INSERT INTO ".$table2.LB;
            $sql2.=" (`id`,`field_id`,`value`)".LB;
            $sql2.=" SELECT id";
            $sql2.=" ,".$field_id;
            //7 = 'オプションリスト';
            //8 = 'ラジオボタンリスト';
            if (($type==7 OR $type==8) AND ($selection<>"")){
                $sql2.=",'0' ";
            }else{
                $sql2.=",'' ";
            }
            $sql2.=" FROM " .$table1.LB;
            $sql2.=" where id NOT IN (select id from ".$table2.LB;
            $sql2.=" where field_id=".$field_id.")".LB;
            DB_query($sql2);
        }

    }

    $rt=" finish! ";
    return $rt;
}


function fncDisply(
    $pi_name
)
{

    $table=$_TABLES[strtoupper($pi_name).'_def_field'];
    $table1=$_TABLES[strtoupper($pi_name).'_base'];
    $table2=$_TABLES[strtoupper($pi_name).'_addition'];

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