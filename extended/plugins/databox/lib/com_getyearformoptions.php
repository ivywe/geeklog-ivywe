<?php

// 2009/04/23
// $Id: lib-common.php,v 1.728 2008/09/21 08:37:09 dhaun Exp $
// より　COM_getYearFormOptions　抜粋　一部コメントアウト
/**
* Gets the <option> values for calendar years
*
* Returns Option list Containing 5 years starting with current
* unless @selected is < current year then starts with @selected
*
* @param        string      $selected     Selected year
* @param        int         $startoffset  Optional (can be +/-) Used to determine start year for range of years
* @param        int         $endoffset    Optional (can be +/-) Used to determine end year for range of years
* @see function COM_getMonthFormOptions
* @see function COM_getDayFormOptions
* @see function COM_getHourFormOptions
* @see function COM_getMinuteFormOptions
* @return string  HTML years as option values
*/

function LIBCOM_getYearFormOptions($selected = '', $startoffset = -1, $endoffset = 5)
{
    $year_options = '';
    $start_year  = date('Y') + $startoffset;
    $cur_year    = date('Y', time());
    $finish_year = $cur_year + $endoffset;

    //@@@@@if (!empty($selected) ) {
    //@@@@@    if ($selected < $cur_year) {
    //@@@@@        $start_year = $selected;
    //@@@@@    }
    //@@@@@}


    for ($i = $start_year; $i <= $finish_year; $i++) {
        $year_options .= '<option value="' . $i . '"';

        if ($i == $selected) {
            $year_options .= ' selected="selected"';
        }

        $year_options .= '>' . $i . '</option>'.LB;
    }

    return $year_options;
}

?>
