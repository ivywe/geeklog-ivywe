<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | UserBox Plugin 0.0.0 for Geeklog 1.8 and later                            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// | Authors    : Tsuchi            - tsuchi AT geeklog DOT jp                 |
// | Authors    : Tetsuko Komma/Ivy - komma AT ivywe DOT co DOT jp             |
// +---------------------------------------------------------------------------+

###############################################################################
# plugins/userbox/language/english_utf-8.php

###############################################################################
## Admin menu
$LANG_USERBOX_admin_menu = array();
$LANG_USERBOX_admin_menu['1']= 'Information';
$LANG_USERBOX_admin_menu['2']= 'Data';
$LANG_USERBOX_admin_menu['3']= 'Attributes';
$LANG_USERBOX_admin_menu['31']= 'Type';
$LANG_USERBOX_admin_menu['4']= 'Categories';
$LANG_USERBOX_admin_menu['5']= 'Groups';
$LANG_USERBOX_admin_menu['51']= 'Master';
$LANG_USERBOX_admin_menu['6']= 'Backup / Restore';
//
$LANG_USERBOX_admin_menu['8']= 'Proversion';


## User
$LANG_USERBOX_user_menu = array();
$LANG_USERBOX_user_menu['2']= 'My Profile';
$LANG_USERBOX_user_menu['7']= 'My Group';

$LANG_USERBOX_user_menu['1']= 'View Profile';

###############################################################################
$LANG_USERBOX_MSG = array();
$LANG_USERBOX_MSG['alluser'] = "";
$LANG_USERBOX_MSG['draftuser'] = 
 "<a href=\"{$_CONF['site_url']}/userbox/myprofile/profile.php\">"
."プロフィールを編集してください。</a>";
$LANG_USERBOX_MSG['descriptionempty'] = 
 "<a href=\"{$_CONF['site_url']}/userbox/myprofile/profile.php\">"
."プロフィールを編集してください。</a>";

###############################################################################
$LANG_USERBOX = array();
$LANG_USERBOX['list']="List";
$LANG_USERBOX['countlist']="Count list";
$LANG_USERBOX['selectit']="Select";
$LANG_USERBOX['selectall']="Select all";
$LANG_USERBOX['byconfig']="By configuration settings";

$LANG_USERBOX['profile'] = 'Profile';
$LANG_USERBOX['myprofile'] = 'My Profile';

$LANG_USERBOX['Norecentnew'] = 'No new data.';
$LANG_USERBOX['nohit'] = 'No hits';
$LANG_USERBOX['nopermission'] = 'No Permissions';

$LANG_USERBOX['more'] = 'More';
$LANG_USERBOX['day'] = "{$_CONF['shortdate']}";

$LANG_USERBOX['home']="Home";
$LANG_USERBOX['view']="View";
$LANG_USERBOX['count']="Count";
$LANG_USERBOX['category_top']="Categories Top";
$LANG_USERBOX['attribute_top']="Attributes Top";
$LANG_USERBOX['search_link']="";

$LANG_USERBOX['download'] = 'download';
$LANG_USERBOX['downloadrequired'] = 'Click, please download';
$LANG_USERBOX['display'] = 'display';
$LANG_USERBOX['displayrequired'] = 'Click, please display';

$LANG_USERBOX['category_separater']=" / ";
$LANG_USERBOX['category_separater_code']=" ";

$LANG_USERBOX['category_separater_text']=", ";
$LANG_USERBOX['field_separater']="|";

$LANG_USERBOX['loginrequired'] = 'Login Required';

$LANG_USERBOX['lastmodified'] = '%B/%e/%Y Updated';
$LANG_USERBOX['lastcreated'] = '%B/%e/%Y Created';

$LANG_USERBOX['deny_msg'] =  'No data or no permissions.';

###############################################################################
# admin/plugins/

$LANG_USERBOX_ADMIN['piname'] = 'UserBox';

# Admin start block title
$LANG_USERBOX_ADMIN['admin_list'] = 'UserBox';

$LANG_USERBOX_ADMIN['edit'] = 'Edit';
$LANG_USERBOX_ADMIN['ref'] = 'Reference';
$LANG_USERBOX_ADMIN['view'] = 'View';

$LANG_USERBOX_ADMIN['new'] = 'New';
$LANG_USERBOX_ADMIN['drafton'] = 'Draft On All';
$LANG_USERBOX_ADMIN['draftoff'] = 'Draft Off All';
$LANG_USERBOX_ADMIN['export'] = 'Export';
$LANG_USERBOX_ADMIN['import'] = 'Import';
$LANG_USERBOX_ADMIN['sampleimport'] = 'Import sample';

$LANG_USERBOX_ADMIN['importfile'] = 'Path';
$LANG_USERBOX_ADMIN['importurl'] = 'URL';

$LANG_USERBOX_ADMIN['delete'] = 'Delete';
$LANG_USERBOX_ADMIN['deletemsg_user'] = "Delete all.<br ".xhtml.">";

$LANG_USERBOX_ADMIN['idfrom'] = "From ID";
$LANG_USERBOX_ADMIN['idto'] = "To ID";

$LANG_USERBOX_ADMIN['mail1'] = 'Send';
$LANG_USERBOX_ADMIN['mail2'] = 'Settings';


$LANG_USERBOX_ADMIN['submit'] = 'Submit';
$LANG_USERBOX_ADMIN['confirm'] = 'Sure you want to execute?';

//
$LANG_USERBOX_ADMIN['link_admin'] = 'Admin';
$LANG_USERBOX_ADMIN['link_admin_top'] = 'Admin TOP';
$LANG_USERBOX_ADMIN['link_public'] = '|Public';
$LANG_USERBOX_ADMIN['link_list'] = 'List';
$LANG_USERBOX_ADMIN['link_detail'] = 'Detail';

//
$LANG_USERBOX_ADMIN['id'] = 'ID';
$LANG_USERBOX_ADMIN['help_id'] ="
";

$LANG_USERBOX_ADMIN['seq'] = 'SEQ';

$LANG_USERBOX_ADMIN['tag'] = 'TAG';
$LANG_USERBOX_ADMIN['value'] = 'VALUE';
$LANG_USERBOX_ADMIN['value2'] = 'VALUE2';
$LANG_USERBOX_ADMIN['disp'] = 'disp';
$LANG_USERBOX_ADMIN['relno'] = 'relno';

$LANG_USERBOX_ADMIN['code']='Code';

$LANG_USERBOX_ADMIN['title']='Title';
$LANG_USERBOX_ADMIN['page_title']='Page Title';

$LANG_USERBOX_ADMIN['description']='Description';
$LANG_USERBOX_ADMIN['defaulttemplatesdirectory']='Theme';
$LANG_USERBOX_ADMIN['layout']='Layout';

$LANG_USERBOX_ADMIN['category']='Category';

$LANG_USERBOX_ADMIN['meta_description']='META Description';

$LANG_USERBOX_ADMIN['meta_keywords']='META Keywords';

$LANG_USERBOX_ADMIN['hits']='Hits';
$LANG_USERBOX_ADMIN['hitsclear']='clear hits';

$LANG_USERBOX_ADMIN['comments']='Comment Number';

$LANG_USERBOX_ADMIN['commentcode']='Comment';

$LANG_USERBOX_ADMIN['comment_expire']='Comment Expiry Date';

$LANG_USERBOX_ADMIN['trackbackcode']='trackback';
$LANG_USERBOX_ADMIN['cache_time']='Cache Time';
$LANG_USERBOX_ADMIN['cache_time_desc']='
This DATA content will be cached for no longer than this many seconds. If 0 caching is disabled. If -1 cached until page is edited again.  (3600 = 1 hour,  86400 = 1 day)
';

$LANG_USERBOX_ADMIN['group']='Group';
$LANG_USERBOX_ADMIN['parent']='Parent';

$LANG_USERBOX_ADMIN['fieldset']='Attribute Type';
$LANG_USERBOX_ADMIN['fieldset_id']="Type ID";

$LANG_USERBOX_ADMIN['fieldsetfields']="Type List";
$LANG_USERBOX_ADMIN['fieldsetfieldsregistered']="Registered attribute";
$LANG_USERBOX_ADMIN['fieldlist']="Attribute List";
$LANG_USERBOX_ADMIN['fieldsetgroups']="Category Group List";
$LANG_USERBOX_ADMIN['fieldsetgroupsregistered']="Registered Category Group";
$LANG_USERBOX_ADMIN['grouplist']="Category Group List";
$LANG_USERBOX_ADMIN['fieldsetlist']='Type List';

$LANG_USERBOX_ADMIN['registset']='Register Type';
$LANG_USERBOX_ADMIN['changeset']='Change Type';
$LANG_USERBOX_ADMIN['inst_changeset0']='Set attribute to none attribute set data:<br'.XHTML.'>';
$LANG_USERBOX_ADMIN['inst_changesetx']='<br'.XHTML.'>';

$LANG_USERBOX_ADMIN['inst_changeset'] = 'Select attribute sets.<br'.XHTML.'>';

$LANG_USERBOX_ADMIN['inst_dataexport'] = 
"
Select export attribute set.<br".XHTML.">
";

$LANG_USERBOX_ADMIN['allow_display']='Allow Display (For users)';
$LANG_USERBOX_ADMIN['allow_edit']='Edit Permission (For user edit)';


$LANG_USERBOX_ADMIN['type']='Attribute Type';

$LANG_USERBOX_ADMIN['size']='Size( text ,Multiselect)';
$LANG_USERBOX_ADMIN['maxlength']='maxlength( text )';
$LANG_USERBOX_ADMIN['rows']='rows(Multi line text )';
$LANG_USERBOX_ADMIN['br']='BR(Radio button)';
$LANG_USERBOX_ADMIN['help_br']='0:no break,1-9:newline every 1-9';


//
$LANG_USERBOX_ADMIN['language_id']="Language ID";
$LANG_USERBOX_ADMIN['owner_id']="Owner ID";
$LANG_USERBOX_ADMIN['group_id']="Group ID";
$LANG_USERBOX_ADMIN['perm_owner']="Permission(Owner)";
$LANG_USERBOX_ADMIN['perm_group']="Permission(Group)";;
$LANG_USERBOX_ADMIN['perm_members']="Permission(Member)";
$LANG_USERBOX_ADMIN['perm_anon']="Permission(Anonimous)";
//

//@@@@@
$LANG_USERBOX_ADMIN['selection']='Selection';
$LANG_USERBOX_ADMIN['selectlist']='Select List';
$LANG_USERBOX_ADMIN['checkrequried']='Check Requried';

$LANG_USERBOX_ADMIN['textcheck']='Input validation(text)';
$LANG_USERBOX_ADMIN['textconv']='Transform input value(text)';
$LANG_USERBOX_ADMIN['searchtarget']='Search target';

$LANG_USERBOX_ADMIN['initial_value']='initial value';
$LANG_USERBOX_ADMIN['range']='range';
$LANG_USERBOX_ADMIN['dfid']=$LANG04[42];

$LANG_USERBOX_ADMIN['draft'] = 'Draft';
$LANG_USERBOX_ADMIN['draft_msg'] = '
Your profile is draft mode. When you want to change the mode, please report it to  site manager.
';
$LANG_USERBOX_ADMIN['uid'] = 'UserID';
$LANG_USERBOX_ADMIN['modified'] = 'Modified';
$LANG_USERBOX_ADMIN['created'] = 'Created';
$LANG_USERBOX_ADMIN['released'] = 'Released';
$LANG_USERBOX_ADMIN['expired'] = 'Archive Options';
$LANG_USERBOX_ADMIN['remaingdays'] = 'remaining days';

$LANG_USERBOX_ADMIN['udatetime'] = 'Modified';
$LANG_USERBOX_ADMIN['uuid'] = 'Modified by user ID';

$LANG_USERBOX_ADMIN['kind'] = 'Kind';
$LANG_USERBOX_ADMIN['no'] = 'No.';//@@@@@-->

$LANG_USERBOX_ADMIN['draftonmsg'] = "
All drafts are turned on <br".XHTML.">
";
$LANG_USERBOX_ADMIN['draftoffmsg'] = "
All drafts are turned off <br".XHTML.">
";
$LANG_USERBOX_ADMIN['hitsclearmsg'] = "
Hits becomes 0<br".XHTML.">
";

$LANG_USERBOX_ADMIN['yy'] = '/';
$LANG_USERBOX_ADMIN['mm'] = '/';
$LANG_USERBOX_ADMIN['dd'] = ' ';

$LANG_USERBOX_ADMIN['must'] = '*';

$LANG_USERBOX_ADMIN['enabled'] = 'Enabled';
$LANG_USERBOX_ADMIN['modified_autoupdate'] = 'Auto Updated';

$LANG_USERBOX_ADMIN['additionfields'] = 'Attributes';
$LANG_USERBOX_ADMIN['basicfields'] = 'Default Attributes';


$LANG_USERBOX_ADMIN['category_id'] = 'Category ID';
$LANG_USERBOX_ADMIN['field_id'] = 'Attribute ID';
$LANG_USERBOX_ADMIN['name'] = 'Name';
$LANG_USERBOX_ADMIN['templatesetvar'] = 'Theme Variable';
$LANG_USERBOX_ADMIN['templatesetvars'] = 'Theme Variables';
$LANG_USERBOX_ADMIN['parent_id'] = 'Parent ID';
$LANG_USERBOX_ADMIN['parent_flg'] = 'Is Parent Group?';
$LANG_USERBOX_ADMIN['input_type'] = 'Render with Input Type';


$LANG_USERBOX_ADMIN['orderno'] = 'Order';

$LANG_USERBOX_ADMIN['field'] = 'Attribute';
$LANG_USERBOX_ADMIN['fields'] = 'Attribute';
$LANG_USERBOX_ADMIN['content'] = 'Contents';

$LANG_USERBOX_ADMIN['byusingid'] = 'Use ID';
$LANG_USERBOX_ADMIN['byusingcode'] = 'Use Code';
$LANG_USERBOX_ADMIN['byusingtemplatesetvar'] = 'Use Theme Variable';

$LANG_USERBOX_ADMIN['withlink'] = 'With Link';
$LANG_USERBOX_ADMIN['groupbygroup'] = 'Group by Group  ';

$LANG_USERBOX_ADMIN['number'] ="Number";
$LANG_USERBOX_ADMIN['endmessage'] = "Finished";
//help
$LANG_USERBOX_ADMIN['delete_help_field'] = '(NOTE: Data is removed, too!)';
$LANG_USERBOX_ADMIN['delete_help_group'] = '(There is data present. Will not delete group.)';
$LANG_USERBOX_ADMIN['delete_help_category'] = '(There is data present. Will not delete category and parent.)';
$LANG_USERBOX_ADMIN['delete_help_fieldset'] = '(There is data present. Will not delete attribute.)';
$LANG_USERBOX_ADMIN['delete_help_mst'] = '(There is registered data, can\'t remove.)';

//backup&restore
$LANG_USERBOX_ADMIN['config'] = 'Configuration';

$LANG_USERBOX_ADMIN['config_backup'] = 'Backup';
$LANG_USERBOX_ADMIN['config_backup_help'] = 'Save Backup File';

$LANG_USERBOX_ADMIN['config_init'] = 'Initialize';
$LANG_USERBOX_ADMIN['config_init_help'] = 'Initialize.';

$LANG_USERBOX_ADMIN['config_restore'] = 'Restore';
$LANG_USERBOX_ADMIN['config_restore_help'] = 'Restore Backup File';

$LANG_USERBOX_ADMIN['config_update'] = 'Update';
$LANG_USERBOX_ADMIN['config_update_help'] = 'Update.';

//
$LANG_USERBOX_ADMIN['document'] = 'Document';
$LANG_USERBOX_ADMIN['configuration'] = 'Configuration Settings';
$LANG_USERBOX_ADMIN['autotags'] = 'Autotags';
$LANG_USERBOX_ADMIN['files'] = 'file list';
$LANG_USERBOX_ADMIN['tables'] = 'table list';
$LANG_USERBOX_ADMIN['online'] = 'Online';


//Admin
$LANG_USERBOX_ADMIN['about_admin_information'] = '';
$LANG_USERBOX_ADMIN['about_admin_data'] = 'Data Admin';
$LANG_USERBOX_ADMIN['about_admin_category'] = 'Category Admin';
$LANG_USERBOX_ADMIN['about_admin_field'] = 'Attribute Admin';
$LANG_USERBOX_ADMIN['about_admin_group'] = 'Group Admin';
$LANG_USERBOX_ADMIN['about_admin_fieldset'] = 'Attribute Set Adimin';
$LANG_USERBOX_ADMIN['about_admin_backuprestore'] = 'Create Backup and Restore<br{KHTML}><br{KHTML}>';
$LANG_USERBOX_ADMIN['about_admin_mst'] = 'Master Admin';

$LANG_USERBOX_ADMIN['about_admin_view'] = 'Display for general login user page.';
$LANG_USERBOX_ADMIN['inst_fieldsetfields'] = 
"Attribute Edit: click Attributes name, click add or remove button.<br".XHTML."br>
to add Attributes, select only right side.<br".XHTML."br>
After edit, click Save button.<br".XHTML."br>";

$LANG_USERBOX_ADMIN['inst_newdata'] = 
'Select Type for Creation of Data<br'.XHTML.'>
';

//ERR
$LANG_USERBOX_ADMIN['err'] = 'Error';
$LANG_USERBOX_ADMIN['err_empty'] = 'File is nothing';

$LANG_USERBOX_ADMIN['err_profile'] = 'プロフィールを登録してください';

$LANG_USERBOX_ADMIN['err_id'] = 'ID is not valid';
$LANG_USERBOX_ADMIN['err_name'] = 'Name is not valid';
$LANG_USERBOX_ADMIN['err_templatesetvar'] = 'Theme variableis not invalid';
$LANG_USERBOX_ADMIN['err_templatesetvar_w'] = 'Theme variable is already used';
$LANG_USERBOX_ADMIN['err_code_w'] = 'Code is duplicated';
$LANG_USERBOX_ADMIN['err_code'] = 'Code input error';
$LANG_USERBOX_ADMIN['err_title'] = 'Title input error';
$LANG_USERBOX_ADMIN['err_numeric'] = 'Numerical value can only be input';

$LANG_USERBOX_ADMIN['err_text1'] = 'Numbers value can only be input';
$LANG_USERBOX_ADMIN['err_text2'] = 'Alphanumeric value can only be input';
$LANG_USERBOX_ADMIN['err_text3'] = 'Alphanumeric / -. value can only be input';
$LANG_USERBOX_ADMIN['err_text4'] = 'Alphanumeric symbols value can only be input';
$LANG_USERBOX_ADMIN['err_range'] = 'Range check error';

$LANG_USERBOX_ADMIN['err_description'] = 'enter a description';

$LANG_USERBOX_ADMIN['err_selection'] = 'Nothing selected';

$LANG_USERBOX_ADMIN['err_modified'] = 'Edit date is not valid.';
$LANG_USERBOX_ADMIN['err_created'] = 'Created date is not valid.';
$LANG_USERBOX_ADMIN['err_released'] = 'Published date is not valid.';
$LANG_USERBOX_ADMIN['err_expired'] = 'Publish date is not valid.';

$LANG_USERBOX_ADMIN['err_checkrequried'] = 'Check Required,';

$LANG_USERBOX_ADMIN['err_date'] = 'Date is invalid.';//@@@@@
$LANG_USERBOX_ADMIN['err_writable'] = ' is not writable';

$LANG_USERBOX_ADMIN['err_size'] = 'Size is invalid.';//@@@@@
$LANG_USERBOX_ADMIN['err_type'] = ' Type is invalid.';//@@@@@

$LANG_USERBOX_ADMIN['err_url'] = 'This URL is not a valid address';
$LANG_USERBOX_ADMIN['err_maxlength'] = '文字以内で入力してください';

$LANG_USERBOX_ADMIN['err_backup_file_not_exist'] = 'Configuration backup file is non existence.<br{KHTML}>';
$LANG_USERBOX_ADMIN['err_backup_file_non_rewritable'] = 'Configuration Backup File not (re)writable<br{KHTML}>';
$LANG_USERBOX_ADMIN['err_not_exist'] = 'Not found';


$LANG_USERBOX_ADMIN['err_kind'] = 'Kind error.';
$LANG_USERBOX_ADMIN['err_no'] = 'no error.';
$LANG_USERBOX_ADMIN['err_no_w'] = 'This number is already used.';

###############################################################################
//$LANG28 = array(
//    2 => 'ユーザID',
//    3 => 'ユーザ名', username
//    4 => '氏名', fullname

$LANG_USERBOX_ORDER['random']="Random Order";
$LANG_USERBOX_ORDER['date']="Date Order";
$LANG_USERBOX_ORDER['orderno']="Display Order";
$LANG_USERBOX_ORDER['code']="Code Order";
$LANG_USERBOX_ORDER['title']="Title Order";
$LANG_USERBOX_ORDER['description']="Description Order";
$LANG_USERBOX_ORDER['id']="ID order";
$LANG_USERBOX_ORDER['released']="Released";
$LANG_USERBOX_ORDER['order']="Order";


###############################################################################
$LANG_USERBOX_MAIL['subject_regist1'] =
"【{$_CONF['site_name']}】ユーザ登録 by {$_USER['username']}";

$LANG_USERBOX_MAIL['message_regist1']=
"{$_USER['username']}さん(user no.{$_USER['uid']})によって、登録されました。".LB.LB;

$LANG_USERBOX_MAIL['subject_regist2'] =
"【{$_CONF['site_name']}】ユーザ登録";

$LANG_USERBOX_MAIL['message_regist2']=
"ユーザ登録されました。".LB.LB;


$LANG_USERBOX_MAIL['subject_data'] =
"【{$_CONF['site_name']}】Data Update by %s";

$LANG_USERBOX_MAIL['message_data']=
"Updated by %s(user no.%s)".LB.LB;

$LANG_USERBOX_MAIL['subject_category'] =
"【{$_CONF['site_name']}】Category Update by {$_USER['username']}.";

$LANG_USERBOX_MAIL['message_category']=
"Category was edited by {$_USER['username']}(user no.{$_USER['uid']}).".LB.LB;

$LANG_USERBOX_MAIL['subject_group'] =
"[{$_CONF['site_name']}] Group was edited by {$_USER['username']}.";

$LANG_USERBOX_MAIL['message_group']=
"Group was edited by {$_USER['username']}(user no.{$_USER['uid']}).".LB.LB;

$LANG_USERBOX_MAIL['subject_fieldset'] =
"【{$_CONF['site_name']}】Type was edted by {$_USER['username']}";

$LANG_USERBOX_MAIL['message_fieldset']=
"Type was updated by {$_USER['username']}(user no.{$_USER['uid']}).".LB.LB;

#
$LANG_USERBOX_MAIL['sig'] = LB
."------------------------------------".LB
."{$_CONF['site_name']}".LB
."{$_CONF['site_url']}".LB
."This is automaticaly sended.".LB
."------------------------------------".LB
;

$LANG_USERBOX_MAIL['subject_data_delete'] =
"【{$_CONF['site_name']}】DataDelete by {$_USER['username']}";
$LANG_USERBOX_MAIL['message_data_delete']=
"Data was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;


$LANG_USERBOX_MAIL['subject_category_delete'] =
"【{$_CONF['site_name']}】CategoryDelete by {$_USER['username']}";
$LANG_USERBOX_MAIL['message_category_delete']=
"Category was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;

$LANG_USERBOX_MAIL['subject_group_delete'] =
"【{$_CONF['site_name']}】Group Delete by {$_USER['username']}";
$LANG_USERBOX_MAIL['message_group_delete']=
"Group was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;

$LANG_USERBOX_MAIL['subject_fieldset_delete'] =
"【{$_CONF['site_name']}】Type removed by {$_USER['username']}.";
$LANG_USERBOX_MAIL['message_fieldset_delete']=
"Attributes set was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;


###############################################################################
#
$LANG_USERBOX_NOYES = array(
    0 => 'No',
    1 => 'Yes'
);
$LANG_USERBOX_INPUTTYPE = array(
    0 => 'Checkbox',
    1 => 'Multi Select List'
    ,2 => 'Radio Button List'
    ,3 => 'Option List'
);
$LANG_USERBOX_ALLOW_DISPLAY = array();
$LANG_USERBOX_ALLOW_DISPLAY[0] ='Display(order)';
$LANG_USERBOX_ALLOW_DISPLAY[1] ='Login User';
$LANG_USERBOX_ALLOW_DISPLAY[2] ='Owner, Group and Admin';
$LANG_USERBOX_ALLOW_DISPLAY[3] ='Owner and Admin';
$LANG_USERBOX_ALLOW_DISPLAY[4] ='Admin only';
$LANG_USERBOX_ALLOW_DISPLAY[5] = 'No Display';

$LANG_USERBOX_ALLOW_EDIT = array();
$LANG_USERBOX_ALLOW_EDIT[0] = 'Edit';
$LANG_USERBOX_ALLOW_EDIT[2] = 'Only Owner,Group,and Admin';
$LANG_USERBOX_ALLOW_EDIT[3] = 'Owner and Admin';
$LANG_USERBOX_ALLOW_EDIT[4] = 'Display Disabled';
$LANG_USERBOX_ALLOW_EDIT[5] = 'Display only';

$LANG_USERBOX_TEXTCHECK = array();
$LANG_USERBOX_TEXTCHECK[0] = 'no check';
$LANG_USERBOX_TEXTCHECK[11] = 'only half-width(change half-width automatically)';
$LANG_USERBOX_TEXTCHECK[12] = 'only half-width alphanumeric(change half-width automatically)';
$LANG_USERBOX_TEXTCHECK[13] = 'ID format(change half-width automatically)';
$LANG_USERBOX_TEXTCHECK[14] = 'only half-width alphanumeric or symbol(change half-width automatically)';
$LANG_USERBOX_TEXTCONV = array();
$LANG_USERBOX_TEXTCONV[0] = 'no';
$LANG_USERBOX_TEXTCONV[10] = 'change half-width';
$LANG_USERBOX_TEXTCONV[20] = 'change half-width';


//TYPE (Chenge Disabled)
$LANG_USERBOX_TYPE = array();
$LANG_USERBOX_TYPE[0] = '1 Line Text Attribute';
$LANG_USERBOX_TYPE[1] = 'Multi Line Text Attribute(HTML)';
$LANG_USERBOX_TYPE[20] = 'Multi Line Text Attribute(HTML/TinyMCE)';
$LANG_USERBOX_TYPE[10] = 'Multi Line Text Attribute(TEXT)';
$LANG_USERBOX_TYPE[15] = 'Numeric';
$LANG_USERBOX_TYPE[21] = 'Currency';

$LANG_USERBOX_TYPE[2] = 'Yes/No';
$LANG_USERBOX_TYPE[3] = 'Date (Date Picker)';
$LANG_USERBOX_TYPE[4] = 'Time (In preparation)';
$LANG_USERBOX_TYPE[5] = 'Mail Address';
$LANG_USERBOX_TYPE[6] = 'url';
$LANG_USERBOX_TYPE[7] = 'Option List';
$LANG_USERBOX_TYPE[8] = 'Radio Button List';
$LANG_USERBOX_TYPE[14] = 'Multiselect';
$LANG_USERBOX_TYPE[17] = 'Flag';
$LANG_USERBOX_TYPE[9] = 'Definition List';
$LANG_USERBOX_TYPE[16] = 'Radio Button List（from master）';//@@@@@
$LANG_USERBOX_TYPE[18] = 'Multiselect（from master）';//@@@@@
//$LANG_USERBOX_TYPE[19] = '';//@@@@@

$LANG_USERBOX_TYPE[11] = 'Image(DB Save)';
$LANG_USERBOX_TYPE[12] = 'Image(File Save)';
$LANG_USERBOX_TYPE[13] = 'File(In Preparation )';


###############################################################################
#
$LANG_USERBOX_SEARCH['type'] = 'UserBox';

$LANG_USERBOX_SEARCH['results_userbox'] = 'UserBox Search Results';

$LANG_USERBOX_SEARCH['title'] =  'Title';
$LANG_USERBOX_SEARCH['udate'] =  'Update';

###############################################################################
#
$LANG_USERBOX_STATS['data'] = 'DataBox';
$LANG_USERBOX_STATS['stats_page_title']='User Name';
$LANG_USERBOX_STATS['stats_hits']='Hits';
$LANG_USERBOX_STATS['stats_headline']='Top Ten UserBox Pages';
$LANG_USERBOX_STATS['stats_no_hits']='It appears that there are no UserBox pages on this site';

###############################################################################
# COM_showMessage()
$PLG_userbox_MESSAGE1  = 'Saved';
$PLG_userbox_MESSAGE2  = 'Deleted';
$PLG_userbox_MESSAGE3  = 'Check a Problem.';

// Messages for the plugin upgrade
$PLG_userbox_MESSAGE3002 = $LANG32[9];











###############################################################################
#
$LANG_USERBOX_autotag_desc['userbox']="
[userbox:count] <br".xhtml.">	
More, see UserBox Plugin documents.
<a href=\"{$_CONF['site_admin_url']}/plugins/userbox/docs/japanese/autotags.html\">*</a>
";


###############################################################################
# configuration
// Localization of the Admin Configuration UI
$LANG_configsections['userbox']['label'] = 'UserBox';
$LANG_configsections['userbox']['title'] = 'UserBox Setting';

//----------
$LANG_configsubgroups['userbox']['sg_main'] = 'Main';
//--(0)
$LANG_tab['userbox'][tab_main] = 'MainSetting';
$LANG_fs['userbox'][fs_main] = 'UserBox MainSetting';
$LANG_confignames['userbox']['perpage'] = 'Date Number by Page';
$LANG_confignames['userbox']['loginrequired'] = 'Login Required';
$LANG_confignames['userbox']['hidemenu'] = 'Hide Menu';

$LANG_confignames['userbox']['categorycode'] = 'Use Category Code ';
$LANG_confignames['userbox']['datacode'] = 'Use Data Code';
$LANG_confignames['userbox']['groupcode'] = 'Use Group Code';
$LANG_confignames['userbox']['top'] = 'Program on Frontpage';
$LANG_confignames['userbox']['templates'] = 'Templates Public';
$LANG_confignames['userbox']['templates_admin'] = 'Templates Admin';

$LANG_confignames['userbox']['themespath'] = 'Theme Template Path';
$LANG_confignames['userbox']['delete_data'] = 'Delete data when Owner Deleted';
$LANG_confignames['userbox']['datefield'] = 'Date field';

$LANG_confignames['userbox']['meta_tags'] = 'Use Meta';

$LANG_confignames['userbox']['layout'] = 'Layout Public';
$LANG_confignames['userbox']['layout_admin'] = 'Layout Admin';
$LANG_confignames['userbox']['mail_to'] = 'Notify Address';
$LANG_confignames['userbox']['mail_to_owner'] = 'Mail to owner';

$LANG_confignames['userbox']['mail_to_draft'] = 'Notify draft data';
$LANG_confignames['userbox']['dateformat'] = 'Date Format with Date Picker';

$LANG_confignames['userbox']['aftersave'] = 'Action after Save for Public';
$LANG_confignames['userbox']['aftersave_admin'] = 'Action after Save for Admin';

$LANG_confignames['userbox']['grp_id_default'] = 'Group Default';

$LANG_confignames['userbox']['allow_profile_update'] = 'ユーザにプロフィールの更新を許可する';
$LANG_confignames['userbox']['allow_group_update'] = 'ユーザにマイグループの更新を許可する';
$LANG_confignames['userbox']['allow_loggedinusers'] = 'ログインユーザを登録する';
$LANG_confignames['userbox']['default_img_url'] = 'Default Image URL';
$LANG_confignames['userbox']['descriptionemptycheck'] = '説明未登録チェック';

$LANG_confignames['userbox']['maxlength_description'] = 'Maxlength description';
$LANG_confignames['userbox']['maxlength_meta_description'] = 'Max length of meta description';
$LANG_confignames['userbox']['maxlength_meta_keywords'] = 'Max length of meta keyword';

$LANG_confignames['userbox']['hideuseroption'] = 'ユーザー情報に表示しない';

$LANG_confignames['userbox']['commentcode'] = 'Comment Default';
$LANG_confignames['userbox']['sort_list_by'] = 'Sort Admin List by';
$LANG_confignames['userbox']['default_cache_time'] = 'default cache time';

//--(1)
$LANG_tab['userbox'][tab_whatsnew] = 'Whats new Block';
$LANG_fs['userbox'][fs_whatsnew] = 'Whats new Block';
$LANG_confignames['userbox']['whatsnew_interval'] = 'New Period';
$LANG_confignames['userbox']['hide_whatsnew'] = 'Hide Whats New';
$LANG_confignames['userbox']['title_trim_length'] = 'Title of Max Length';

//---(2)
$LANG_tab['userbox'][tab_search] = 'Search';

$LANG_fs['userbox'][fs_search] = 'Search Results';
$LANG_confignames['userbox']['include_search'] = 'Data Search';
$LANG_confignames['userbox']['additionsearch'] = 'Attributes number for Search';

//---(3)
$LANG_tab['userbox'][tab_permissions] = 'Permission';
$LANG_fs['userbox'][fs_permissions] = 'Data Permission Default([0]Owner [1]Group [2]Member [3]Anonimous)';
$LANG_confignames['userbox']['default_permissions'] = 'Permissions';

//---(4)
$LANG_tab['userbox'][tab_autotag] = 'Autotags';
$LANG_fs['userbox'][fs_autotag] = 'Autotags';
$LANG_confignames['userbox']['intervalday']="Display Period(Day)";
$LANG_confignames['userbox']['limitcnt']="Display Number";//@@@@@
$LANG_confignames['userbox']['newmarkday']="New Mark Display Period(Day)";//@@@@@
$LANG_confignames['userbox']['categories']="Default Category";//@@@@@!!!!
$LANG_confignames['userbox']['new_img']="New Mark";//@@@@@
$LANG_confignames['userbox']['rss_img']="RSS Mark";//@@@@@

//---(５)
$LANG_tab['userbox']['tab_file'] = 'Upload File';

$LANG_fs['userbox']['fs_file'] = 'Upload File';
$LANG_confignames['userbox']['imgfile_size'] = 'Image File(DB) MaxSize';
$LANG_confignames['userbox']['imgfile_type'] = 'Image File(DB)  Type';
$LANG_confignames['userbox']['imgfile_size2'] = 'Image File Max Size';
$LANG_confignames['userbox']['imgfile_type2'] = 'Image File Type';
$LANG_confignames['userbox']['imgfile_frd'] = 'Image Save URL';
$LANG_confignames['userbox']['imgfile_thumb_frd'] = 'Thumbnail Image Save URL';

$LANG_confignames['userbox']['imgfile_thumb_ok'] = 'Use Thumbnail';
$LANG_confignames['userbox']['imgfile_thumb_w'] = 'Thumbnail Size(w)';
$LANG_confignames['userbox']['imgfile_thumb_h'] = 'Thumbnail Size(h)';
$LANG_confignames['userbox']['imgfile_thumb_w2'] = 'Original Image Size(w2)';
$LANG_confignames['userbox']['imgfile_thumb_h2'] = 'Original Image Size(h2)';
$LANG_confignames['userbox']['imgfile_smallw'] = 'Display Image Max Width';
$LANG_confignames['userbox']['imgfile_subdir'] = 'use subdirectory for Image Save URL';

$LANG_confignames['userbox']['file_path'] = 'File Save Absolute Path';
$LANG_confignames['userbox']['file_size'] = 'File Size';
$LANG_confignames['userbox']['file_type'] = 'File Type';
$LANG_confignames['userbox']['file_subdir'] = 'use subdirectory for File Save Absolute Path';

//---(６)
$LANG_tab['userbox']['tab_autotag_permissions'] = 'Autotags Permission';
$LANG_fs['userbox']['fs_autotag_permissions'] = 'Autotags Permission ([0]Owner [1]Group [2]Member [3]Anonimous)';
$LANG_confignames['userbox']['autotag_permissions_userbox'] = '[userbox: ] Permission';

//---(９)
$LANG_tab['userbox']['tab_xml'] = 'ProfesionalVersion';
$LANG_fs['userbox']['fs_xml'] = '(Profesional Version)';
$LANG_confignames['userbox']['path_xml'] = 'XML Batch Import Path';
$LANG_confignames['userbox']['path_xml_out'] = 'XML Export Path';

// Note: entries 0, 1, 9, 12, 17 are the same as in $LANG_configselects['Core']
$LANG_configselects['userbox'][0] =array('Yes' => 1, 'No' => 0);
$LANG_configselects['userbox'][1] =array('Yes' => TRUE, 'No' => FALSE);
$LANG_configselects['userbox'][12] =array('AccessDisabled' => 0, 'Display' => 2, 'Display・Edit' => 3);
$LANG_configselects['userbox'][13] =array('AccessDisabled' => 0, 'Use' => 2);
$LANG_configselects['userbox'][5] =array(
    'Hide' => 'hide'
    , 'Display by Modified Date' => 'modified'
    , 'Display by Created Date' => 'created'
    , 'Display by Released Date' => 'released');

//$LANG_configselects['userbox'][17] =array('Access Denyed' => 0, 'Display' => 2, 'Display・Edit' => 3);

$LANG_configselects['userbox'][20] =array(
    'Standard' => 'standard'
    , 'Custom' => 'custom'
    , 'Theme' => 'theme'
    );

//@@@@@
$LANG_configselects['userbox'][21] =array(
     'By Modified Date' => 'modified'
    , 'By Created Date' => 'created'
    , 'Display by Released Date' => 'released'
    );

$LANG_configselects['userbox'][22] =array(
    'Standard' => 'standard'
    , 'Left and Right Blocks' => 'leftrightblocks'
    , 'Blank Page' => 'blankpage'
    , 'No Block' => 'noblocks'
    , 'Left Block' => 'leftblocks'
    , 'Right Block' => 'rightblocks'
    );

$LANG_configselects['userbox'][23] =array(
    'Yes' => 3
    , 'List and Detail' => 2
    , 'Only Detaile' => 1
    , 'No' => 0
    );


$LANG_configselects['userbox'][9] =array(
    'Same Page Display' => 'no'
    , 'Page Display' => 'item'
    , 'List Display' => 'list'
    , 'Home Display' => 'home'
    , 'AdminTop Display' => 'admin'
    , 'PluginTop Display' => 'plugin'
        );
$LANG_configselects['userbox'][25] =array(
    'Same Page Display' => 'no'
    , 'Page Display' => 'item'
    , 'List Display' => 'list'
    , 'Home Display' => 'home'
    , 'PluginTop Display' => 'plugin'
        );

//
$LANG_configselects['userbox'][24] =array();
    $sql = LB;
    $sql .= "SELECT ".LB;
    $sql .= " grp_id".LB;
    $sql .= ",grp_name".LB;
    $sql .= " FROM {$_TABLES['groups']}".LB;
    $sql .= " ORDER BY grp_name".LB;
    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
        $grp_name=$A['grp_name'];
        $grp_id=$A['grp_id'];
        $LANG_configselects['userbox'][24][$grp_name]=$grp_id;
	}

$LANG_configselects['userbox'][26] =array( 'Comments Enabled' => 0, 'Comments Disabled' => -1);

$LANG_configselects['userbox'][27] =array(
    'Order' => 'orderno'
    ,'User ID' => 'id'
    , 'Username' => 'username'
    , 'Full Name' => 'fullname'
    , 'Profile Type' => 'fieldset_name'
    , 'Hits' => 'hits'
    , 'Time Stamp (descending)' => 'udatetime'
    , 'Draft' => 'draft_flag'
        );
?>
