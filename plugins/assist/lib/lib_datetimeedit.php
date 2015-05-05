<?php
//20110523 update DataBox UserBox FormBox assist 共通

if (strpos ($_SERVER['PHP_SELF'], 'lib_datetimeedit.php') !== false) {
    die ('This file can not be used on its own.');
}


function LIB_datetimeedit(
    $datetime_value
    ,$lang
    ,$datetime_name="datetime"
    ,$mode="datetime"
) {
    global $_CONF;

    $var = $lang;
    global $$var;
    $lang_ary=$$var;

    $datetime_year = date('Y', $datetime_value);
    $datetime_month = date('m', $datetime_value);
    $datetime_day = date('d', $datetime_value);
    //
    $datetime_hour = date ('H', $datetime_value);
    $datetime_minute = date ('i', $datetime_value) ;

    $rt="";
    //Year
    $year_options = COM_getYearFormOptions ($datetime_year);
    $rt.="<select name=\"".$datetime_name."_year\">".LB;
    $rt.=$year_options.LB;
    $rt.="</select>".$lang_ary['yy'].LB;

    //month
    $month_options = COM_getMonthFormOptions ($datetime_month);
    $rt.="<select name=\"".$datetime_name."_month\">".LB;
    $rt.=$month_options.LB;
    $rt.="</select>".$lang_ary['mm'].LB;

    //day
    $day_options = COM_getDayFormOptions ($datetime_day);
    $rt.="<select name=\"".$datetime_name."_day\">".LB;
    $rt.=$day_options.LB;
    $rt.="</select>".$lang_ary['dd'].LB;

    //hour
    if ($mode==="datetime"){
        if ($_CONF['hour_mode'] == 24) {
            $hour_options = COM_getHourFormOptions ($datetime_hour, 24);
        }else{
            $datetime_hour_wk=$datetime_hour;
            if ($datetime_hour_wk >= 12) {
                if ($datetime_hour_wk > 12) {
                    $datetime_hour_wk = $datetime_hour_wk - 12;
                }
                $ampm = 'pm';
            } else {
                $ampm = 'am';
            }
            $ampm_select = COM_getAmPmFormSelection ($datetime_name.'_ampm', $ampm);
            if (empty ($ampm_select)) {
                $ampm_select = '<input type="hidden" name="cmt_close_ampm" value=""' . XHTML . '>';
            }
            $rt.=$ampm_select;
            $hour_options = COM_getHourFormOptions ($datetime_hour_wk);
        }

        $rt.="<select name=\"".$datetime_name."_hour\">".LB;
        $rt.=$hour_options.LB;
        $rt.="</select>:".LB;

        //minute
        $minute_options = COM_getMinuteFormOptions ($datetime_minute);

        $rt.="<select name=\"".$datetime_name."_minute\">".LB;
        $rt.=$minute_options.LB;
        $rt.="</select>".LB;

    }

    return $rt;

}

?>
