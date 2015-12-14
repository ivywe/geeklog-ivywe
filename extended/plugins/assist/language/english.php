<?php
###############################################################################
# plugins/assist/language/english.php
###############################################################################
## Admin
$LANG_ASSIST_admin_menu = array();
$LANG_ASSIST_admin_menu['1']= 'Information';
$LANG_ASSIST_admin_menu['2']= 'Import Users';

$LANG_ASSIST_admin_menu['3']= 'Variables';
$LANG_ASSIST_admin_menu['4']= 'Newsletter';

$LANG_ASSIST_admin_menu['5']= 'Backup and Restore';

$LANG_ASSIST_admin_menu['8']= 'Proversion';




###############################################################################
$LANG_ASSIST= array(
    1 => 'More',
    2 => "{$_CONF['shortdate']}",

    999 => ''
);


$LANG_ASSIST['home_id']['sub'] = 'sub';
$LANG_ASSIST['home_id']['home'] = 'home';

$LANG_ASSIST['login_status'][0] = 'guest';
$LANG_ASSIST['login_status'][1] = 'member';

$LANG_ASSIST['login_logout'][0] = "
<a href=\"{$_CONF['site_url']}/users.php?mode=login\">\"Log in\"></a>
";

$LANG_ASSIST['login_logout'][1] = "
<a href=\"{$_CONF['site_url']}/users.php?mode=loout\">\"Log out\"></a>
";
###############################################################################
$LANG_ASSIST_autotag_desc['newstories']="
[newstories:(TopicID) (RSSfile)] - New stories.<br{xhtml}>
See the /admin/plugins/assist/docs/japanese/autotags.html file for more informations.
";
$LANG_ASSIST_autotag_desc['newstories2']="
[newstories2:(TopicID) (RSSfile)] - New stories.<br{xhtml}>
See the /admin/plugins/assist/docs/japanese/autotags.html file for more informations.
";

$LANG_ASSIST_autotag_desc['conf']="
[conf:(var name)] - <br{xhtml}>
ex.1：[conf:site_url]<br{xhtml}>
ex.2：[conf:site_name]<br{xhtml}>
ex.3：[conf:site_mail]<br{xhtml}>
ex.4：[conf:site_slogan]<br{xhtml}>
See the /admin/plugins/assist/docs/japanese/autotags.html file for more informations.
";
$LANG_ASSIST_autotag_desc['assist']="
[assist:〜] - <br{xhtml}>	
[assist:usercount]others<br{xhtml}>
See the /admin/plugins/assist/docs/japanese/autotags.html file for more informations.
<a href=\"{$_CONF['site_admin_url']}/plugins/assist/docs/japanese/autotags.html\">*</a>
";


###########
$LANG_ASSIST['home']="HOME";
$LANG_ASSIST['view']="View";
$LANG_ASSIST['articles']="Articles";

###############################################################################
# admin/plugins/

$LANG_ASSIST_ADMIN['instructions'] =
'
';

$LANG_ASSIST_ADMIN['piname'] = 'Assist';
$LANG_ASSIST_ADMIN['edit'] = 'edit';
$LANG_ASSIST_ADMIN['new'] = 'new';

$LANG_ASSIST_ADMIN['export'] = 'Export';

$LANG_ASSIST_ADMIN['import'] = 'Import';
$LANG_ASSIST_ADMIN['importfile'] = 'path';
$LANG_ASSIST_ADMIN['importmsg_user'] =
"User list ca be regist to Geeklog system silently.<br{xhtml}>"
."(UserID)<TAB>(Full Name)<TAB>(User Name)<TAB>(Email)<br{xhtml}>"
."UserID, User Name, Email can't be duplicated.<br{xhtml}>"
."But if UserID is 0, automatically set.<br{xhtml}>"
."User's password is automatically set.<br{xhtml}>"
."Notification email are not sent to users.<br{xhtml}>"
;

$LANG_ASSIST_ADMIN['delete'] = 'Delete';
$LANG_ASSIST_ADMIN['deletemsg_user'] = "Batch delete users<br{xhtml}>";

$LANG_ASSIST_ADMIN['uidfrom'] = "Start:";
$LANG_ASSIST_ADMIN['uidto'] = "End:";

$LANG_ASSIST_ADMIN['mail1'] = 'Email send';
$LANG_ASSIST_ADMIN['mail2'] = 'Email settings';

$LANG_ASSIST_ADMIN['submit'] = 'Submit';

//newsletter
$LANG_ASSIST_ADMIN['mail_logfile'] ="Log file %s is ignored.<br{xhtml}>";

$LANG_ASSIST_ADMIN['mail_msg'] =
"Create story by topic '%s'. <br{xhtml}>"
."Newsletter is sent by text format.<br{xhtml}>"
."Please test.<br{xhtml}>"
."Log file is assist_newsletter.log<br{xhtml}>"
."Registrated time oftn delay, because this system use Geeklog programmed CRON.<br{xhtml}>"
;

$LANG_ASSIST_ADMIN['mail_msg1'] ="(1) Settings";
$LANG_ASSIST_ADMIN['mail_msg2'] ="(2) Test";
$LANG_ASSIST_ADMIN['mail_msg3'] ="(3) Users";
$LANG_ASSIST_ADMIN['mail_msg4'] ="(4) Submit";

$LANG_ASSIST_ADMIN['select_sid'] = 'Select Story';
$LANG_ASSIST_ADMIN['wkymlmguser_on'] = "You can send users by wkymlmguser plgin's users";
$LANG_ASSIST_ADMIN['wkymlmguser_off'] = 'NOTE: if you wish to send a message to all site members, select the Logged-in Users group from the drop down.';
$LANG_ASSIST_ADMIN['wkymlmguser_user'] = 'wkymlmguser plugin user';

$LANG_ASSIST_ADMIN['fromname']="From";
$LANG_ASSIST_ADMIN['replyto']="Reply to";
$LANG_ASSIST_ADMIN['sprefix']="Prefix";
$LANG_ASSIST_ADMIN['sid']="Story";

$LANG_ASSIST_ADMIN['toenv']="User type";
$LANG_ASSIST_ADMIN['selectgroup']="Select Group";

$LANG_ASSIST_ADMIN['testto']="Testing Email";
$LANG_ASSIST_ADMIN['sendto']="Send users ID";
$LANG_ASSIST_ADMIN['sendto_remarks']="NOTE: if you wish to send all users, set 0 - 0.";

$LANG_ASSIST_ADMIN['mail_test'] = 'Send now for testing';
$LANG_ASSIST_ADMIN['mail_send'] = 'Send now';

//backup&restore
$LANG_ASSIST_ADMIN['config'] = 'Configuration';

$LANG_ASSIST_ADMIN['config_backup'] = 'Backup';
$LANG_ASSIST_ADMIN['config_init'] = 'Initialize';

$LANG_ASSIST_ADMIN['config_restore'] = 'Restre';
$LANG_ASSIST_ADMIN['config_update'] = 'Update';

$LANG_ASSIST_ADMIN['config_backup_help'] = 'Create backup file.';
$LANG_ASSIST_ADMIN['config_init_help'] = 'Initialize.';

$LANG_ASSIST_ADMIN['config_restore_help'] = 'Restore from backup.';
$LANG_ASSIST_ADMIN['config_update_help'] = 'Update.';

//
$LANG_ASSIST_ADMIN['importform'] = 'Import';
$LANG_ASSIST_ADMIN['exportform'] = 'Export';

$LANG_ASSIST_ADMIN['seq'] = 'SEQ';

$LANG_ASSIST_ADMIN['tag'] = 'TAG';
$LANG_ASSIST_ADMIN['value'] = 'VALUE';

$LANG_ASSIST_ADMIN['must'] = '*';
$LANG_ASSIST_ADMIN['field'] = 'field';
$LANG_ASSIST_ADMIN['fields'] = 'field';

$LANG_ASSIST_ADMIN['udatetime'] = 'timestamp';
$LANG_ASSIST_ADMIN['uuid'] = 'Modified User';

$LANG_ASSIST_ADMIN['inpreparation'] = '(inpreparation)';
$LANG_ASSIST_ADMIN['markerclear'] = 'Clear maps marker';
$LANG_ASSIST_ADMIN['mapsmarker'] = 'maps marker';
$LANG_ASSIST_ADMIN['xml_def'] = 'XML definition';
$LANG_ASSIST_ADMIN['init'] = 'Initualize';
$LANG_ASSIST_ADMIN['list'] = 'List';

$LANG_ASSIST_ADMIN['path'] = 'Path';
$LANG_ASSIST_ADMIN['url'] = 'URL';

$LANG_ASSIST_ADMIN['default'] = 'Default';
$LANG_ASSIST_ADMIN['importmsg'] = '
Set path(directory and file name) or URL.<{XHTML}br>
xml file is imported.<{XHTML}br>
Log file is assist_xmlimport.log.<{XHTML}br>
';
$LANG_ASSIST_ADMIN['exportmsg'] = '
Set path.<{XHTML}br>
Log file is assist_xmlimport.log.<{XHTML}br>
';

//
$LANG_ASSIST_ADMIN['document'] = 'Document';
$LANG_ASSIST_ADMIN['configuration'] = 'Configuration';
$LANG_ASSIST_ADMIN['autotags'] = 'Auto tags';
$LANG_ASSIST_ADMIN['online'] = 'Online';
$LANG_ASSIST_ADMIN['templatesetvar'] = 'templatesetvar';

//Admin
$LANG_ASSIST_ADMIN['about_admin_information'] = ' ';
$LANG_ASSIST_ADMIN['about_admin_backuprestore'] = 'Backup and Restore';

//ERR
$LANG_ASSIST_ADMIN['msg'] = 'Message';
$LANG_ASSIST_ADMIN['err'] = 'Error';
$LANG_ASSIST_ADMIN['err_not_writable'] = 'The directory is not writable.';
$LANG_ASSIST_ADMIN['err_not_exist'] = 'No file was uploaded.';
$LANG_ASSIST_ADMIN['err_empty'] = 'No file was uploaded.';

$LANG_ASSIST_ADMIN['err_invalid'] = 'No data';
$LANG_ASSIST_ADMIN['err_permission denied'] = 'Access Denied';

$LANG_ASSIST_ADMIN['err_id'] = 'Ignore ID';
$LANG_ASSIST_ADMIN['err_name'] = 'Ignore name';
$LANG_ASSIST_ADMIN['err_templatesetvar'] = 'Ignore template variable';
$LANG_ASSIST_ADMIN['err_templatesetvar_w'] = 'The template variable is already used';
$LANG_ASSIST_ADMIN['err_code_w'] = 'The code is already used';
$LANG_ASSIST_ADMIN['err_code'] = 'Code cannot be null';
$LANG_ASSIST_ADMIN['err_title'] = 'Title cannot be null';

$LANG_ASSIST_ADMIN['err_selection'] = 'List is not selected';

$LANG_ASSIST_ADMIN['err_modified'] = 'Ignore modified date';
$LANG_ASSIST_ADMIN['err_created'] = 'Ignore created date';
$LANG_ASSIST_ADMIN['err_released'] = 'Ignore released date';
$LANG_ASSIST_ADMIN['err_expired'] = 'Ignore expired date';

$LANG_ASSIST_ADMIN['err_checkrequried'] = ' The data is requried';

$LANG_ASSIST_ADMIN['err_date'] = 'Ignore date';//@@@@@

$LANG_ASSIST_ADMIN['err_size'] = 'Ignore size';//@@@@@
$LANG_ASSIST_ADMIN['err_type'] = 'Ignore type';//@@@@@

$LANG_ASSIST_ADMIN['err_field_w'] = 'The field is already exist.';


$LANG_ASSIST_ADMIN['err_backup_file_not_exist'] = 'No backup files';
$LANG_ASSIST_ADMIN['err_backup_file_non_rewritable'] = 'Not rewritable';

$LANG_ASSIST_ADMIN['err_fromname'] = 'Please fill in all the fields on the form and select a group of users from the drop down.';
$LANG_ASSIST_ADMIN['err_replyto'] = 'Please fill in all the fields on the form and select a group of users from the drop down.';
$LANG_ASSIST_ADMIN['err_sid'] = 'Please select Story.';
$LANG_ASSIST_ADMIN['err_testto'] = 'Please fill testing Email.';

$LANG_ASSIST_ADMIN['err_backup_file_not_exist'] = 'Could not open the backup file.';
$LANG_ASSIST_ADMIN['err_backup_file_non_rewritable'] = 'Could not rewrite the file.';

$LANG_ASSIST_ADMIN['err_marker_name'] = 'Please fill marker name.'.LB;
$LANG_ASSIST_ADMIN['err_marker_address'] = 'Please fill marker address'.LB;
$LANG_ASSIST_ADMIN['err_marker_coords'] = 'Could not calicurate coord.'.LB;
$LANG_ASSIST_ADMIN['err_map'] = 'Could not find map.'.LB;

$LANG_ASSIST_ADMIN['err_fbid'] = 'Facebook OAuth Application ID is not found.<br{xhtml}>'.LB
.'(Configuration setting Geeklog user)<br{xhtml}>'.LB
.'Facebook social button autotags needs.<br{xhtml}>'.LB;
//

$LANG_ASSIST_ADMIN['mail_save_ok'] = 'Saved';
$LANG_ASSIST_ADMIN['mail_test_message'] = 'This is test. Please check this newsletter.';
$LANG_ASSIST_ADMIN['mail_test_ok'] = 'Test is done.';
$LANG_ASSIST_ADMIN['mail_test_ng'] = 'Test could not be done.';
$LANG_ASSIST_ADMIN['mail_send_success'] = 'Suceedid';
$LANG_ASSIST_ADMIN['mail_send_failure'] = 'Falure';
// hiroron start 2010/07/13
$LANG_ASSIST_ADMIN['reserv_datetime']="Reserve";
$LANG_ASSIST_ADMIN['reserv_datetime_remarks']="NOTE: Please set 0 min., if you wish send one time.";

$LANG_ASSIST_ADMIN['mail_reserv'] = 'Reservation';
$LANG_ASSIST_ADMIN['err_reserv'] = 'Please set reservation time.';
// hiroron end 2010/07/13
// hiroron start 2010/07/15
$LANG_ASSIST_ADMIN['reservlist_title']="Resaervation table";
$LANG_ASSIST_ADMIN['reservlist_no']="no.";
$LANG_ASSIST_ADMIN['reservlist_datetime']="date";
$LANG_ASSIST_ADMIN['reservlist_range']="range(last id)";
$LANG_ASSIST_ADMIN['reservlist_sid']="Story";
$LANG_ASSIST_ADMIN['reservlist_cancel']="Delete";
$LANG_ASSIST_ADMIN['err_reservcancel_no_id']="Ignore ids";
$LANG_ASSIST_ADMIN['err_reservcancel_no_file']="Could not find reservation.";
$LANG_ASSIST_ADMIN['done_reservcancel']="Reservation is sucessfully deleted.";
// hiroron end 2010/07/15

$LANG_ASSIST_ADMIN['introbody']="Newsletter text";
$LANG_ASSIST_ADMIN['mail_bulkgooking'] = 'Bulk booking';

$LANG_ASSIST_ADMIN['minute'] = 'min.';
$LANG_ASSIST_ADMIN['every'] = 'every';
$LANG_ASSIST_ADMIN['increments'] = 'emails';

$LANG_ASSIST_ADMIN['yy'] = '/';
$LANG_ASSIST_ADMIN['mm'] = '/';
$LANG_ASSIST_ADMIN['dd'] = '';

$LANG_ASSIST_ADMIN['jobend'] = 'Done processing.<br{KHTML}>';
$LANG_ASSIST_ADMIN['cnt_ok'] = 'Successfully sent %d messages and ';
$LANG_ASSIST_ADMIN['cnt_ng'] = 'unsuccessfully sent %d messages.<br{KHTML}>';

###############################################################################
#
$LANG_ASSIST_INTROBODY = array();
$LANG_ASSIST_INTROBODY[0] ='Intro Text';
$LANG_ASSIST_INTROBODY[1] ='Body Text';
#
$LANG_ASSIST_TOENV = array();
$LANG_ASSIST_TOENV[0] ='All';
$LANG_ASSIST_TOENV[1] ='PC';
$LANG_ASSIST_TOENV[2] ='Cellular';

###############################################################################
# COM_showMessage()
$PLG_assist_MESSAGE1  = 'Saved';
$PLG_assist_MESSAGE2  = 'Deleted';
$PLG_assist_MESSAGE3  = 'Check problem';

// Messages for the plugin upgrade
$PLG_assist_MESSAGE3002 = $LANG32[9];


###############################################################################
# configuration
// Localization of the Admin Configuration UI
$LANG_configsections['assist']['label'] = 'Assist';
$LANG_configsections['assist']['title'] = 'Assist configuration';

//----------
$LANG_configsubgroups['assist']['sg_main'] = 'Main';

//---()
$LANG_tab['assist']['tab_main'] = 'Main';
$LANG_fs['assist']['fs_main'] = 'Main';

$LANG_confignames['assist']['title_trim_length']="Length of title";
$LANG_confignames['assist']['intervalday']="Display period (date)";
$LANG_confignames['assist']['limitcnt']="Limit count";
$LANG_confignames['assist']['newmarkday']="New stickers period(date)";
$LANG_confignames['assist']['topics']="Default topic";
$LANG_confignames['assist']['new_img']="New stickers";
$LANG_confignames['assist']['rss_img']="RSS Mark";

$LANG_confignames['assist']['newsletter_tid']="Topic for Newsletter";

$LANG_confignames['assist']['templates'] = 'Templates for general';
$LANG_confignames['assist']['templates_admin'] = 'Templates for admin';

$LANG_confignames['assist']['themespath'] = 'Theme path';

$LANG_confignames['assist']['cron_schedule_interval'] = 'assist Cron scheduke interval';

$LANG_confignames['assist']['onoff_emailfromadmin'] = 'display_status_of_emailfromadmin(notyet)';

$LANG_confignames['assist']['aftersave'] = 'Screen changes after saved: General';
$LANG_confignames['assist']['aftersave_admin'] = 'Screen changes after saved: Admin';
$LANG_confignames['assist']['xmlins'] = 'xmlins';
$LANG_confignames['assist']['default_img_url'] = 'default image url';
$LANG_confignames['assist']['path_cache'] = 'Cache file path';
//---(１)
$LANG_tab['assist']['tab_autotag_permissions'] = 'Autotag permission';
$LANG_fs['assist']['fs_autotag_permissions'] = 'Autotag Usage Permissions ([0]Owners [1]Group [2]Member [3]Anonymous）';
$LANG_confignames['assist']['autotag_permissions_newstories'] = '[newstories: ] Permissions';
$LANG_confignames['assist']['autotag_permissions_newstories2'] = '[newstories2: ] Permissions';
$LANG_confignames['assist']['autotag_permissions_conf'] = '[conf: ] Permissions';
$LANG_confignames['assist']['autotag_permissions_assist'] = '[assist: ] Permissions';

//---(９)
$LANG_tab['assist']['tab_pro'] = 'profesional version';
$LANG_fs['assist']['fs_pro'] = '(profesional version)';
$LANG_confignames['assist']['path_xml'] = 'XML import directory';
$LANG_confignames['assist']['path_xml_out'] = 'XML export directory';

// Note: entries 0, 1, 9, 12, 17 are the same as in $LANG_configselects['Core']
$LANG_configselects['assist'][0] =array('Yes' => 1, 'No' => 0);
$LANG_configselects['assist'][1] =array('Yes' => TRUE, 'No' => FALSE);
$LANG_configselects['assist'][12] =array('Access Denied' => 0, 'Read' => 2, 'Read-Write' => 3);
$LANG_configselects['assist'][13] =array('Access Denied' => 0, 'Use' => 2);

$LANG_configselects['assist'][5] =array(
    'Access Denied' => 'hide'
    , 'Display by modified date' => 'modified'
    , 'Display by created date' => 'created');

$LANG_configselects['assist'][20] =array(
    'Standard' => 'standard'
    , 'Custom' => 'custom'
    , 'Theme' => 'theme');


$LANG_configselects['assist'][9] =array(
    'No schreen change' => 'no'
    ,'Display page' => 'item'
    , 'Display list' => 'list'
    , 'Display home' => 'home'
    , 'Display admin' => 'admin'
    , 'Display plugin admin' => 'plugin'
        );

?>