<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Calendarjp Plugin for Geeklog                                             |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Geeklog Calendarjp Plugin administration page.                            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2008-2014 by dengen - taharaxp AT gmail DOT com             |
// |                                                                           |
// | Calendarjp plugin is based on prior work by:                              |
// | Authors: Tony Bibbs        - tony AT tonybibbs DOT com                    |
// |          Mark Limburg      - mlimburg AT users DOT sourceforge DOT net    |
// |          Jason Whittenburg - jwhitten AT securitygeeks DOT com            |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
* Geeklog common function library
*/
require_once '../../../lib-common.php';

/**
* Security check to ensure user even belongs on this page
*/
require_once '../../auth.inc.php';

// Uncomment the line below if you need to debug the HTTP variables being passed
// to the script.  This will sometimes cause errors but it will allow you to see
// the data being passed in a POST operation
// COM_debug($_POST);

$display = '';

// Ensure user even has the rights to access this page
if (!SEC_hasRights('calendarjp.edit')) {
    $display .= COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30]));

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the event administration screen.");

    COM_output($display);

    exit;
}


/**
* Shows event editor
*
* @param    string  $mode   Indicates if this is a submission or a regular entry
* @param    array   $A      array holding the event's details
* @param    string  $msg    an optional error message to display
* @return   string          HTML for event editor or error message
*
*/
function CALENDARJP_editEvent ($mode, $A, $msg = '')
{
    global $_CONF, $_GROUPS, $_TABLES, $_USER, $_CAJP_CONF, $LANG_CALJP_1,
           $LANG_CALJP_ADMIN, $LANG10, $LANG12, $LANG24, $LANG_ACCESS, $LANG_ADMIN,
           $MESSAGE, $_SCRIPTS;

    // Loads jQuery UI datepicker and timepicker-addon
    $_SCRIPTS->setJavaScriptLibrary('jquery.ui.slider');
    $_SCRIPTS->setJavaScriptLibrary('jquery.ui.datepicker');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-i18n');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-timepicker-addon');
    $_SCRIPTS->setJavaScriptLibrary('jquery-ui-timepicker-addon-i18n');
    $_SCRIPTS->setJavaScriptFile('datetimepicker', '/javascript/datetimepicker.js');

    // Add JavaScript
    $_SCRIPTS->setJavaScriptFile('postmode_control', '/javascript/postmode_control.js');

    $langCode = COM_getLangIso639Code();
    $toolTip  = $MESSAGE[118];
    $imgUrl   = $_CONF['site_url'] . '/images/calendar.png';

    $_SCRIPTS->setJavaScript(
        "jQuery(function () {"
        . "  geeklog.hour_mode = {$_CONF['hour_mode']};"
        . "  geeklog.datetimepicker.options.stepMinute = 15;"
        . "  geeklog.datetimepicker.set('start', '{$langCode}', '{$toolTip}', '{$imgUrl}');"
        . "  geeklog.datetimepicker.set('end', '{$langCode}', '{$toolTip}', '{$imgUrl}');"
        . "});", TRUE, TRUE
    );

    $retval = '';

    if (!empty ($msg)) {
        $retval .= COM_showMessageText($msg, $LANG_CALJP_ADMIN[2]);
    }

    $ja = ($_CONF['language'] == 'japanese_utf-8');

    $event_templates = COM_newTemplate($_CONF['path'] . 'plugins/calendarjp/templates/admin');
    $advanced_editor = ($_CONF['advanced_editor'] && $_USER['advanced_editor'] && $_CAJP_CONF['advanced_editor']);
    if ($advanced_editor) {
        $event_templates->set_file('editor','eventeditor_advanced' . ($ja ? '_ja' : '') . '.thtml');
    } else {
        $event_templates->set_file('editor','eventeditor' . ($ja ? '_ja' : '') . '.thtml');
    }

    $allowed = '';
    foreach (array('plaintext', 'html') as $pm) {
        $allowed .= COM_allowedHTML('calendarjp.edit', false, 1, $pm);
    }
    $allowed .= COM_allowedAutotags();

    $event_templates->set_var('lang_allowed_html', $allowed);
    $event_templates->set_var('lang_postmode', $LANG_CALJP_ADMIN[3]);
    $event_templates->set_var('lang_expandhelp', $LANG24[67]);
    $event_templates->set_var('lang_reducehelp', $LANG24[68]);
    $event_templates->set_var ('lang_toolbar', $LANG24[70]);
    $event_templates->set_var ('toolbar1', $LANG24[71]);
    $event_templates->set_var ('toolbar2', $LANG24[72]);
    $event_templates->set_var ('toolbar3', $LANG24[73]);
    $event_templates->set_var ('toolbar4', $LANG24[74]);
    $event_templates->set_var ('toolbar5', $LANG24[75]);
    $event_templates->set_var ('change_editormode', 'onchange="change_editmode(this);"');

    if ($advanced_editor) {
        if ($A['postmode'] == 'adveditor') {
            $event_templates->set_var ('show_texteditor', 'none');
            $event_templates->set_var ('show_htmleditor', '');
        } else {
            $event_templates->set_var ('show_texteditor', '');
            $event_templates->set_var ('show_htmleditor', 'none');
        }
    }

    if ($advanced_editor) {
        // Setup Advanced Editor
        COM_setupAdvancedEditor('/calendarjp/adveditor.js', 'calendarjp.edit');
    }

    if ($mode <> 'editsubmission' AND !empty($A['eid'])) {
        // Get what level of access user has to this object
        $access = SEC_hasAccess($A['owner_id'],$A['group_id'],$A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']);
        if ($access == 0 OR $access == 2) {
            // Uh, oh!  User doesn't have access to this object
            $retval .= COM_showMessageText($LANG_CALJP_ADMIN[17], $LANG_ACCESS['accessdenied']);
            COM_accessLog("User {$_USER['username']} tried to illegally submit or edit event $eid.");
            return $retval;
        }
    } else {
        if (empty($A['owner_id'])) {
            $A['owner_id'] = $_USER['uid'];
        }
        if (isset ($_GROUPS['Calendarjp Admin'])) {
            $A['group_id'] = $_GROUPS['Calendarjp Admin'];
        } else {
            $A['group_id'] = SEC_getFeatureGroup ('calendarjp.edit');
        }
        SEC_setDefaultPermissions ($A, $_CAJP_CONF['default_permissions']);
        $access = 3;
    }

// ------------------------------>>
/*
    if ($mode == 'editsubmission') {
        $event_templates->set_var('post_options', COM_optionList($_TABLES['postmodes'],'code,name','plaintext'));
    } else {
        if (!isset ($A['postmode'])) {
            $A['postmode'] = $_CONF['postmode'];
        }
        $event_templates->set_var('post_options', COM_optionList($_TABLES['postmodes'],'code,name',$A['postmode']));
    }
*/
// ------------------------------||
    if ($mode == 'editsubmission') {
        $post_options = COM_optionList($_TABLES['postmodes'],'code,name','plaintext');
    } else {
        if (!isset($A['postmode'])) {
            $A['postmode'] = $_CAJP_CONF['postmode'];
        }
        $post_options = COM_optionList($_TABLES['postmodes'],'code,name',$A['postmode']);

        if ($advanced_editor) {
            if ($A['postmode'] == 'adveditor') {
                $post_options .= '<option value="adveditor" selected="selected">'.$LANG24[86].'</option>';
            } else {
                $post_options .= '<option value="adveditor">'.$LANG24[86].'</option>';
            }
        }
        if ($_CAJP_CONF['wikitext_editor']) {
            if ($A['postmode'] == 'wikitext') {
                $post_options .= '<option value="wikitext" selected="selected">'.$LANG24[88].'</option>';
            } else {
                $post_options .= '<option value="wikitext">'.$LANG24[88].'</option>';
            }
        }
    }
    $event_templates->set_var('post_options', $post_options);
// ------------------------------<<



    $token = SEC_createToken();

    $retval .= COM_startBlock($LANG_CALJP_ADMIN[1], '',
                              COM_getBlockTemplate('_admin_block', 'header'));
    $retval .= SEC_getTokenExpiryNotice($token);

    if (!empty($A['eid'])) {
        $delbutton = '<input type="submit" value="' . $LANG_ADMIN['delete']
                   . '" name="mode"%s' . XHTML . '>';
        $jsconfirm = ' onclick="return confirm(\'' . $MESSAGE[76] . '\');"';
        $event_templates->set_var ('delete_option',
                                   sprintf ($delbutton, $jsconfirm));
        $event_templates->set_var ('delete_option_no_confirmation',
                                   sprintf ($delbutton, ''));
        if ($mode == 'editsubmission') {
            $event_templates->set_var('submission_option',
                '<input type="hidden" name="type" value="submission"'
                . XHTML . '>');
        }
    } else { // new event
        $A['eid'] = COM_makesid ();
        $A['title'] = '';
        $A['description'] = '';
        $A['url'] = '';
        $A['hits'] = 0;

        // in case a start date/time has been passed from the calendar,
        // pick it up for the end date/time
        if (empty ($A['dateend'])) {
            $A['dateend'] = $A['datestart'];
        }
        if (empty ($A['timeend'])) {
            $A['timeend'] = $A['timestart'];
        }
        $A['event_type'] = '';
        $A['location'] = '';
        $A['address1'] = '';
        $A['address2'] = '';
        $A['city'] = '';
        $A['state'] = '';
        $A['zipcode'] = '';
        $A['allday'] = 0;
    }

    $event_templates->set_var('lang_eventid', $LANG_CALJP_ADMIN[34]);
    $event_templates->set_var('event_id', $A['eid']);
    $event_templates->set_var('lang_eventtitle', $LANG_ADMIN['title']);
    $A['title'] = str_replace('{','&#123;',$A['title']);
    $A['title'] = str_replace('}','&#125;',$A['title']);
    $A['title'] = str_replace('"','&quot;',$A['title']);
    $event_templates->set_var('event_title', stripslashes ($A['title']));

    $event_templates->set_var('lang_eventtype', $LANG_CALJP_1[37]);
    $event_templates->set_var('lang_editeventtypes', $LANG12[50]);
    $event_templates->set_var('type_options',
                              CALENDARJP_eventTypeList ($A['event_type']));

    $event_templates->set_var('lang_eventurl', $LANG_CALJP_ADMIN[4]);
    $event_templates->set_var('max_url_length', 255);
    $event_templates->set_var('event_url', $A['url']);
    $event_templates->set_var('lang_includehttp', $LANG_CALJP_ADMIN[9]);
    $event_templates->set_var('lang_eventstartdate', $LANG_CALJP_ADMIN[5]);
    //$event_templates->set_var('event_startdate', $A['datestart']);
    $event_templates->set_var('lang_starttime', $LANG_CALJP_1[30]);

    // Combine date/time for easier manipulation
    $A['datestart'] = trim ($A['datestart'] . ' ' . $A['timestart']);
    if (empty ($A['datestart'])) {
// ------------------------------>>
//        $start_stamp = time ();
// ------------------------------||
        if (!empty($A['month']) && !empty($A['day']) && !empty($A['year']) && !empty($A['hour'])) {
            $start_stamp = mktime($A['hour'], 0, 0, $A['month'], $A['day'], $A['year']);
        } else {
            $start_stamp = time ();
        }
// ------------------------------<<
    } else {
        $start_stamp = strtotime ($A['datestart']);
    }
    $A['dateend'] = trim ($A['dateend'] . ' ' . $A['timeend']);
    if (empty ($A['dateend'])) {
//        $end_stamp = time ();
        $end_stamp = $start_stamp;
    } else {
        $end_stamp = strtotime ($A['dateend']);
    }
    $start_month = date('m', $start_stamp);
    $start_day = date('d', $start_stamp);
    $start_year = date('Y', $start_stamp);
    $end_month = date('m', $end_stamp);
    $end_day = date('d', $end_stamp);
    $end_year = date('Y', $end_stamp);

    $start_hour = date ('H', $start_stamp);
    $start_minute = intval (date ('i', $start_stamp) / 15) * 15;
    if ($start_hour >= 12) {
        $startampm = 'pm';
    } else {
        $startampm = 'am';
    }

    $start_hour_24 = $start_hour % 24;
    if ($start_hour > 12) {
        $start_hour = $start_hour - 12;
    } else if ($start_hour == 0) {
        $start_hour = 12;
    }

    $end_hour = date('H', $end_stamp);
    $end_minute = intval (date('i', $end_stamp) / 15) * 15;
    if ($end_hour >= 12) {
        $endampm = 'pm';
    } else {
        $endampm = 'am';
    }

    $end_hour_24 = $end_hour % 24;
    if ($end_hour > 12) {
        $end_hour = $end_hour - 12;
    } else if ($end_hour == 0) {
        $end_hour = 12;
    }

    $month_options = CALENDARJP_getMonthFormOptions ($start_month);
    $event_templates->set_var ('startmonth_options', $month_options);

    $month_options = CALENDARJP_getMonthFormOptions ($end_month);
    $event_templates->set_var ('endmonth_options', $month_options);

    $day_options = COM_getDayFormOptions ($start_day);
    $event_templates->set_var ('startday_options', $day_options);

    $day_options = COM_getDayFormOptions ($end_day);
    $event_templates->set_var ('endday_options', $day_options);

    $year_options = COM_getYearFormOptions ($start_year);
    $event_templates->set_var ('startyear_options', $year_options);

    $year_options = COM_getYearFormOptions ($end_year);
    $event_templates->set_var ('endyear_options', $year_options);

    if (isset ($_CAJP_CONF['hour_mode']) && ($_CAJP_CONF['hour_mode'] == 24)) {
        $hour_options = COM_getHourFormOptions ($start_hour_24, 24);
        $event_templates->set_var ('starthour_options', $hour_options);

        $hour_options = COM_getHourFormOptions ($end_hour_24, 24);
        $event_templates->set_var ('endhour_options', $hour_options);

        $event_templates->set_var ('hour_mode', 24);
    } else {
        $hour_options = COM_getHourFormOptions ($start_hour);
        $event_templates->set_var ('starthour_options', $hour_options);

        $hour_options = COM_getHourFormOptions ($end_hour);
        $event_templates->set_var ('endhour_options', $hour_options);

        $event_templates->set_var ('hour_mode', 12);
    }

    $event_templates->set_var ('startampm_selection',
                        CALENDARJP_getAmPmFormSelection ('start_ampm', $startampm));
    $event_templates->set_var ('endampm_selection',
                        CALENDARJP_getAmPmFormSelection ('end_ampm', $endampm));

    $event_templates->set_var ('startminute_options',
                               COM_getMinuteFormOptions ($start_minute, 15));
    $event_templates->set_var ('endminute_options',
                               COM_getMinuteFormOptions ($end_minute, 15));

    $event_templates->set_var('lang_enddate', $LANG12[13]);
    $event_templates->set_var('lang_eventenddate', $LANG_CALJP_ADMIN[6]);
    $event_templates->set_var('event_enddate', $A['dateend']);
    $event_templates->set_var('lang_enddate', $LANG12[13]);
    $event_templates->set_var('lang_endtime', $LANG_CALJP_1[29]);
    $event_templates->set_var('lang_alldayevent', $LANG_CALJP_1[31]);
    if ($A['allday'] == 1) {
        $event_templates->set_var('allday_checked', 'checked="checked"');
    }
    $event_templates->set_var('lang_location',$LANG12[51]);
    $event_templates->set_var('event_location', stripslashes ($A['location']));
    $event_templates->set_var('lang_addressline1',$LANG12[44]);
    $event_templates->set_var('event_address1', stripslashes ($A['address1']));
    $event_templates->set_var('lang_addressline2',$LANG12[45]);
    $event_templates->set_var('event_address2', stripslashes ($A['address2']));
    $event_templates->set_var('lang_city',$LANG12[46]);
    $event_templates->set_var('event_city', stripslashes ($A['city']));
    $event_templates->set_var('lang_state',$LANG12[47]);
    $event_templates->set_var('state_options', '');
    $event_templates->set_var('event_state', stripslashes ($A['state']));
    $event_templates->set_var('lang_zipcode',$LANG12[48]);
    $event_templates->set_var('event_zipcode', $A['zipcode']);
    $event_templates->set_var('lang_eventlocation', $LANG_CALJP_ADMIN[7]);
    $event_templates->set_var('event_location', stripslashes ($A['location']));
    $event_templates->set_var('lang_eventdescription', $LANG_CALJP_ADMIN[8]);
    $event_templates->set_var('event_description', stripslashes ($A['description']));
    $event_templates->set_var('lang_hits', $LANG10[30]);
    $event_templates->set_var('hits', COM_numberFormat ($A['hits']));
    $event_templates->set_var('lang_save', $LANG_ADMIN['save']);
    $event_templates->set_var('lang_cancel', $LANG_ADMIN['cancel']);
    $event_templates->set_var('lang_tail_year', $LANG_CALJP_ADMIN['tail_year']);
    $event_templates->set_var('lang_tail_month', $LANG_CALJP_ADMIN['tail_month']);
    $event_templates->set_var('lang_tail_day', $LANG_CALJP_ADMIN['tail_day']);

    // user access info
    $event_templates->set_var('lang_accessrights',$LANG_ACCESS['accessrights']);
    $event_templates->set_var('lang_owner', $LANG_ACCESS['owner']);
    $ownername = COM_getDisplayName ($A['owner_id']);
    $event_templates->set_var('owner_username', DB_getItem($_TABLES['users'],
                              'username', "uid = {$A['owner_id']}"));
    $event_templates->set_var('owner_name', $ownername);
    $event_templates->set_var('owner', $ownername);
    $event_templates->set_var('owner_id', $A['owner_id']);
    $event_templates->set_var('lang_group', $LANG_ACCESS['group']);
    $event_templates->set_var('group_dropdown',
                              SEC_getGroupDropdown ($A['group_id'], $access));
    $event_templates->set_var('lang_permissions', $LANG_ACCESS['permissions']);
    $event_templates->set_var('lang_permissionskey', $LANG_ACCESS['permissionskey']);
    $event_templates->set_var('lang_perm_key', $LANG_ACCESS['permissionskey']);
    $event_templates->set_var('permissions_editor', SEC_getPermissionsHTML($A['perm_owner'],$A['perm_group'],$A['perm_members'],$A['perm_anon']));
    $event_templates->set_var('lang_permissions_msg', $LANG_ACCESS['permmsg']);
    $event_templates->set_var('gltoken_name', CSRF_TOKEN);
    $event_templates->set_var('gltoken', $token);
    $event_templates->parse('output', 'editor');
    $retval .= $event_templates->finish($event_templates->get_var('output'));
    $retval .= COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer'));

    return $retval;
}

/**
* Saves an event to the database
*
* (parameters should be obvious - old list was incomplete anyway)
* @return   string                  HTML redirect or error message
*
*/
function CALENDARJP_saveEvent ($eid, $title, $event_type, $url, $allday,
                             $start_month, $start_day, $start_year, $start_hour,
                             $start_minute, $start_ampm, $end_month, $end_day,
                             $end_year, $end_hour, $end_minute, $end_ampm,
                             $location, $address1, $address2, $city, $state,
                             $zipcode, $description, $postmode, $owner_id,
                             $group_id, $perm_owner, $perm_group, $perm_members,
                             $perm_anon, $hour_mode)
{
    global $_CONF, $_TABLES, $_USER, $LANG_CALJP_ADMIN, $MESSAGE, $_CAJP_CONF;

    $retval = '';

    // Convert array values to numeric permission values
    list($perm_owner,
        $perm_group,
        $perm_members,
        $perm_anon) = SEC_getPermissionValues($perm_owner,
                                              $perm_group,
                                              $perm_members,
                                              $perm_anon);

    $access = 0;
    if (DB_count ($_TABLES['eventsjp'], 'eid', $eid) > 0) {
        $result = DB_query ("SELECT owner_id,group_id,perm_owner,perm_group,"
                           ."perm_members,perm_anon FROM {$_TABLES['eventsjp']} "
                           ."WHERE eid = '{$eid}'");
        $A = DB_fetchArray ($result);
        $access = SEC_hasAccess ($A['owner_id'], $A['group_id'],
                $A['perm_owner'], $A['perm_group'], $A['perm_members'],
                $A['perm_anon']);
    } else {
        $access = SEC_hasAccess ($owner_id, $group_id, $perm_owner, $perm_group,
                $perm_members, $perm_anon);
    }
    if (($access < 3) || !SEC_inGroup ($group_id)) {
        $retval .= COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
        $retval = COM_createHTMLDocument($retval, array('pagetitle' => $MESSAGE[30]));
        COM_accessLog ("User {$_USER['username']} tried to illegally submit or edit event $eid.");
        return $retval;
    }

    if ($hour_mode == 24) {
        // to avoid having to mess with the tried and tested code below, map
        // the 24-hour values onto their 12-hour counterparts and use those
        if ($start_hour >= 12) {
            $start_ampm = 'pm';
            $start_hour = $start_hour - 12;
        } else {
            $start_ampm = 'am';
            $start_hour = $start_hour;
        }
        if ($start_hour == 0) {
            $start_hour = 12;
        }
        if ($end_hour >= 12) {
            $end_ampm = 'pm';
            $end_hour = $end_hour - 12;
        } else {
            $end_ampm = 'am';
            $end_hour = $end_hour;
        }
        if ($end_hour == 0) {
            $end_hour = 12;
        }
/*
        if ($start_hour >= 12) {
            $start_ampm = '午後';
            $start_hour = $start_hour - 12;
        } else {
            $start_ampm = '午前';
            $start_hour = $start_hour;
        }
        if ($start_hour == 0) {
            $start_hour = 12;
        }
        if ($end_hour >= 12) {
            $end_ampm = '午後';
            $end_hour = $end_hour - 12;
        } else {
            $end_ampm = '午前';
            $end_hour = $end_hour;
        }
        if ($end_hour == 0) {
            $end_hour = 12;
        }
*/
    }

    if ($allday == 'on') {
        $allday = 1;
    } else {
        $allday = 0;
    }

    // Make sure start date is before end date
    if (checkdate ($start_month, $start_day, $start_year)) {
        $datestart = $start_year . '-' . $start_month . '-' . $start_day;
        $timestart = $start_hour . ':' . $start_minute . ':00';
    } else {
        $retval .= COM_showMessageText($LANG_CALJP_ADMIN[23], $LANG_CALJP_ADMIN[2]);
        $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG_CALJP_ADMIN[2]));

        return $retval;
    }
    if (checkdate ($end_month, $end_day, $end_year)) {
        $dateend = $end_year . '-' . $end_month . '-' . $end_day;
        $timeend = $end_hour . ':' . $end_minute . ':00';
    } else {
        $retval .= COM_showMessageText($LANG_CALJP_ADMIN[24], $LANG_CALJP_ADMIN[2]);
        $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG_CALJP_ADMIN[2]));

        return $retval;
    }
    if ($allday == 0) {
        if ($dateend < $datestart) {
            $retval .= COM_showMessageText($LANG_CALJP_ADMIN[25], $LANG_CALJP_ADMIN[2]);
            $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG_CALJP_ADMIN[2]));

            return $retval;
        }
    } else {
        if ($dateend < $datestart) {
            // Force end date to be same as start date
            $dateend = $datestart;
        }
    }
    
    // Remove any autotags the user doesn't have permission to use
    $description = PLG_replaceTags($description, '', true);    

    // clean 'em up
    if ($postmode == 'html' || $postmode == 'adveditor') {
        $description = COM_checkHTML(COM_checkWords($description),
                                     'calendarjp.edit');
    } else {
        if ($postmode != 'wikitext') {
            $postmode = 'plaintext';
        }
        $description = htmlspecialchars(COM_checkWords($description));
    }
    $description = DB_escapeString($description);
    $title = DB_escapeString(strip_tags(COM_checkWords($title)));
    $location = DB_escapeString(COM_checkHTML(COM_checkWords($location),
                                         'calendarjp.edit'));
    $address1 = DB_escapeString(strip_tags(COM_checkWords($address1)));
    $address2 = DB_escapeString(strip_tags(COM_checkWords($address2)));
    $city = DB_escapeString(strip_tags(COM_checkWords($city)));
    $zipcode =  DB_escapeString(strip_tags(COM_checkWords($zipcode)));
    $event_type = DB_escapeString(strip_tags(COM_checkWords($event_type)));
    $url = DB_escapeString(strip_tags($url));

    if ($allday == 0) {
        // Add 12 to make time on 24 hour clock if needed
        if ($start_ampm == 'pm' AND $start_hour <> 12) {
            $start_hour = $start_hour + 12;
        }
        // If 12AM set hour to 00
        if ($start_ampm == 'am' AND $start_hour == 12) {
            $start_hour = '00';
        }
        // Add 12 to make time on 24 hour clock if needed
        if ($end_ampm == 'pm' AND $end_hour <> 12) {
           $end_hour = $end_hour + 12;
        }
        // If 12AM set hour to 00
        if ($end_ampm == 'am' AND $end_hour == 12) {
            $end_hour = '00';
        }
/*
        // Add 12 to make time on 24 hour clock if needed
        if ($start_ampm == '午後' AND $start_hour <> 12) {
            $start_hour = $start_hour + 12;
        }
        // If 12AM set hour to 00
        if ($start_ampm == '午前' AND $start_hour == 12) {
            $start_hour = '00';
        }
        // Add 12 to make time on 24 hour clock if needed
        if ($end_ampm == '午後' AND $end_hour <> 12) {
           $end_hour = $end_hour + 12;
        }
        // If 12AM set hour to 00
        if ($end_ampm == '午前' AND $end_hour == 12) {
            $end_hour = '00';
        }
*/

        $timestart = $start_hour . ':' . $start_minute . ':00';
        $timeend = $end_hour . ':' . $end_minute . ':00';
    }

    if (!empty ($eid) AND !empty ($description) AND !empty ($title)) {
        if (!SEC_checkToken()) {
            COM_accessLog("User {$_USER['username']} tried to save event $eid and failed CSRF checks.");
            return COM_refresh($_CONF['site_admin_url']
                               . '/plugins/calendarjp/index.php');
        }

        $hits = DB_getItem($_TABLES['eventsjp'], 'hits', "eid = '$eid'");
        if (empty($hits)) {
            $hits = 0;
        }

        DB_delete ($_TABLES['eventsubmissionjp'], 'eid', $eid);

        DB_save($_TABLES['eventsjp'],
               'eid,title,event_type,url,allday,datestart,dateend,timestart,'
               .'timeend,location,address1,address2,city,state,zipcode,description,'
               .'postmode,owner_id,group_id,perm_owner,perm_group,perm_members,'
               .'perm_anon,hits',
               "'$eid','$title','$event_type','$url',$allday,'$datestart',"
               ."'$dateend','$timestart','$timeend','$location','$address1',"
               ."'$address2','$city','$state','$zipcode','$description','$postmode',"
               ."$owner_id,$group_id,$perm_owner,$perm_group,$perm_members,$perm_anon,$hits");
        if (DB_count ($_TABLES['personal_eventsjp'], 'eid', $eid) > 0) {
            $result = DB_query ("SELECT uid FROM {$_TABLES['personal_eventsjp']} "
                               ."WHERE eid = '{$eid}'");
            $numrows = DB_numRows ($result);
            for ($i = 1; $i <= $numrows; $i++) {
                $P = DB_fetchArray ($result);
                DB_save ($_TABLES['personal_eventsjp'],
                        'eid,title,event_type,datestart,dateend,address1,address2,'
                       .'city,state,zipcode,allday,url,description,postmode,'
                       .'group_id,owner_id,perm_owner,perm_group,perm_members,'
                       .'perm_anon,uid,location,timestart,timeend',
                        "'$eid','$title','$event_type','$datestart','$dateend',"
                       ."'$address1','$address2','$city','$state','$zipcode',"
                       ."$allday,'$url','$description','$postmode',$group_id,"
                       ."$owner_id,$perm_owner,$perm_group,$perm_members,"
                       ."$perm_anon,{$P['uid']},'$location','$timestart','$timeend'");
            }
        }

        PLG_itemSaved($eid, 'calendarjp');
        COM_rdfUpToDateCheck('calendarjp', $event_type, $eid);

        return PLG_afterSaveSwitch (
            $_CAJP_CONF['aftersave'],
            $_CONF['site_url'] . '/calendarjp/event.php?eid=' . $eid,
            'calendarjp',
            17
        );
    } else {
        $retval .= COM_showMessageText($LANG_CALJP_ADMIN[10], $LANG_CALJP_ADMIN[2]);
        $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG_CALJP_ADMIN[2]));

        return $retval;
    }
}


// MAIN
$mode = '';
if (isset($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}
if (isset($_POST['delbutton_x'])) {
    $mode = 'batchdeleteexec';
}

if (($mode == $LANG_ADMIN['delete']) && !empty ($LANG_ADMIN['delete'])) {
    $eid = COM_applyFilter ($_REQUEST['eid']);
    if (!isset ($eid) || empty ($eid) || ($eid == 0)) {
        COM_errorLog ('Attempted to delete event eid=\'' . $eid . "'");
        $display .= COM_refresh($_CONF['site_admin_url']
                                . '/plugins/calendarjp/index.php');
    } elseif (SEC_checkToken()) {
        $type = '';
        if (isset($_POST['type'])) {
            $type = COM_applyFilter($_POST['type']);
        }
        $display .= CALENDARJP_deleteEvent($eid, $type);
    } else {
        COM_accessLog("User {$_USER['username']} tried to illegally delete event $eid and failed CSRF checks.");
        echo COM_refresh($_CONF['site_admin_url'] . '/index.php');
    }
} elseif (($mode == $LANG_ADMIN['save']) && !empty($LANG_ADMIN['save'])) {
    if (!isset ($_POST['allday'])) {
        $_POST['allday'] = '';
    }
    $hour_mode = 12;
    if (isset($_POST['hour_mode']) && ($_POST['hour_mode'] == 24)) {
        $hour_mode = 24;
    }
    if ($hour_mode == 24) {
        // these aren't set in 24 hour mode
        $_POST['start_ampm'] = '';
        $_POST['end_ampm'] = '';
    }
    $display .= CALENDARJP_saveEvent (COM_applyFilter ($_POST['eid']),
            $_POST['title'], $_POST['event_type'],
            $_POST['url'], COM_applyFilter ($_POST['allday']),
            COM_applyFilter ($_POST['start_month'], true),
            COM_applyFilter ($_POST['start_day'], true),
            COM_applyFilter ($_POST['start_year'], true),
            COM_applyFilter ($_POST['start_hour'], true),
            COM_applyFilter ($_POST['start_minute'], true), $_POST['start_ampm'],
            COM_applyFilter ($_POST['end_month'], true),
            COM_applyFilter ($_POST['end_day'], true),
            COM_applyFilter ($_POST['end_year'], true),
            COM_applyFilter ($_POST['end_hour'], true),
            COM_applyFilter ($_POST['end_minute'], true), $_POST['end_ampm'],
            $_POST['location'], $_POST['address1'], $_POST['address2'],
            $_POST['city'], $_POST['state'], $_POST['zipcode'],
            $_POST['description'], $_POST['postmode'] ,
            COM_applyFilter ($_POST['owner_id'], true),
            COM_applyFilter ($_POST['group_id'], true),
            $_POST['perm_owner'], $_POST['perm_group'],
            $_POST['perm_members'], $_POST['perm_anon'], $hour_mode);
} else if ($mode == 'editsubmission') {
    $id = COM_applyFilter ($_REQUEST['id']);
    $result = DB_query ("SELECT * FROM {$_TABLES['eventsubmissionjp']} WHERE eid ='$id'");
    $A = DB_fetchArray ($result);
    $A['hits'] = 0;
    $display .= CALENDARJP_editEvent ($mode, $A);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_CALJP_ADMIN[1]));
} else if ($mode == 'clone') {
    $eid = COM_applyFilter ($_REQUEST['eid']);
    $result = DB_query ("SELECT * FROM {$_TABLES['eventsjp']} WHERE eid ='$eid'");
    $A = DB_fetchArray ($result);
    $A['hits'] = 0;
    $A['eid'] = COM_makesid ();
    $A['owner_id'] = $_USER['uid'];
    $display .= CALENDARJP_editEvent ($mode, $A);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_CALJP_ADMIN[1]));
} else if ($mode == 'edit') {
    $eid = '';
    if (isset ($_REQUEST['eid'])) {
        $eid = COM_applyFilter ($_REQUEST['eid']);
    }
    if (empty ($eid)) {
        $A = array ();
        $A['datestart'] = '';
        $A['timestart'] = '';
        if (isset ($_REQUEST['datestart'])) {
            $A['datestart'] = COM_applyFilter ($_REQUEST['datestart']);
        }
        if (isset ($_REQUEST['timestart'])) {
            $A['timestart'] = COM_applyFilter ($_REQUEST['timestart']);
        }

// Added ------------------------>>
        $A['hour'] = '';
        $A['month'] = '';
        $A['day'] = '';
        $A['year'] = '';
        if (isset ($_REQUEST['hour'])) {
            $A['hour'] = COM_applyFilter ($_REQUEST['hour'],true);
        }
        if (isset ($_REQUEST['month'])) {
            $A['month'] = COM_applyFilter ($_REQUEST['month'],true);
        }
        if (isset ($_REQUEST['day'])) {
            $A['day'] = COM_applyFilter ($_REQUEST['day'],true);
        }
        if (isset ($_REQUEST['year'])) {
            $A['year'] = COM_applyFilter ($_REQUEST['year'],true);
        }
// Added ------------------------<<

    } else {
        $result = DB_query ("SELECT * FROM {$_TABLES['eventsjp']} WHERE eid ='$eid'");
        $A = DB_fetchArray ($result);
    }
    $display .= CALENDARJP_editEvent ($mode, $A);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_CALJP_ADMIN[1]));
} else if ($mode == 'batchdelete') {
    // list_old
    if (isset ($_REQUEST['msg'])) {
        $display .= COM_showMessage (
            COM_applyFilter ($_REQUEST['msg'], true),
            'calendarjp'
        );
    }
    $display .= CALENDARJP_listOld();
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_CALJP_ADMIN[11]));
} elseif (($mode == 'batchdeleteexec') && SEC_checkToken()) {
    $msg = CALENDARJP_deleteOld();
    $display .= COM_showMessage($msg) . CALENDARJP_listOld();
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_CALJP_ADMIN[11]));
} else { // 'cancel' or no mode at all
    if (isset ($_REQUEST['msg'])) {
        $display .= COM_showMessage (COM_applyFilter ($_REQUEST['msg'],
                                                      true), 'calendarjp');
    }
    $display .= CALENDARJP_listevents();
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_CALJP_ADMIN[11]));
}

COM_output($display);

?>
