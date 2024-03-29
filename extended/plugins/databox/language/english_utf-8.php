<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | DataBox Plugin 0.0.0 for Geeklog 1.8 and later                            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// | Authors    : Tsuchi            - tsuchi AT geeklog DOT jp                 |
// | Authors    : Tetsuko Komma/Ivy - komma AT ivywe DOT co DOT jp             |
// +---------------------------------------------------------------------------+

###############################################################################
# plugins/databox/language/english_utf-8.php

###############################################################################
## Admin menu
$LANG_DATABOX_admin_menu = array();
$LANG_DATABOX_admin_menu['1']= 'Information';
$LANG_DATABOX_admin_menu['2']= 'Data';
$LANG_DATABOX_admin_menu['3']= 'Attributes';
$LANG_DATABOX_admin_menu['31']= 'Type';
$LANG_DATABOX_admin_menu['4']= 'Categories';
$LANG_DATABOX_admin_menu['5']= 'Groups';
$LANG_DATABOX_admin_menu['51']= 'Master';
$LANG_DATABOX_admin_menu['6']= 'Backup / Restore';
//
$LANG_DATABOX_admin_menu['8']= 'XML';
$LANG_DATABOX_admin_menu['9']= 'CSV';
$LANG_DATABOX_admin_menu['10']= 'MAPS';
$LANG_DATABOX_admin_menu['11']= 'SEL';

## User
$LANG_DATABOX_user_menu = array();
$LANG_DATABOX_user_menu['2']= 'My Data';


###############################################################################
$LANG_DATABOX = array();
$LANG_DATABOX['list']="List";
$LANG_DATABOX['countlist']="Count list";
$LANG_DATABOX['selectit']="Select";
$LANG_DATABOX['selectall']="Select all";
$LANG_DATABOX['byconfig']="By configuration settings";

$LANG_DATABOX['data'] = 'Data';
$LANG_DATABOX['mydata'] = 'My Data';

$LANG_DATABOX['Norecentnew'] = 'No new data.';
$LANG_DATABOX['nohit'] = 'No hits';
$LANG_DATABOX['nopermission'] = 'No Permissions';
$LANG_DATABOX['notapplicable'] = 'Not Applicable';

$LANG_DATABOX['more'] = 'More';
$LANG_DATABOX['day'] = "{$_CONF['shortdate']}";

$LANG_DATABOX['home']="Home";
$LANG_DATABOX['view']="View";
$LANG_DATABOX['count']="Count";
$LANG_DATABOX['category_top']="Categories Top";
$LANG_DATABOX['attribute_top']="Attributes Top";
$LANG_DATABOX['search_link']="";
$LANG_DATABOX['return']="return";
$LANG_DATABOX['search'] = 'search';

$LANG_DATABOX['download'] = 'download';
$LANG_DATABOX['downloadrequired'] = 'Click, please download';
$LANG_DATABOX['display'] = 'display';
$LANG_DATABOX['displayrequired'] = 'Click, please display';

$LANG_DATABOX['category_separater']=" / ";
$LANG_DATABOX['category_separater_code']=" ";
$LANG_DATABOX['category_separater_text']=", ";
$LANG_DATABOX['field_separater']="|";

$LANG_DATABOX['loginrequired'] = 'Login Required';

$LANG_DATABOX['lastmodified'] = '%B/%e/%Y Updated';
$LANG_DATABOX['lastcreated'] = '%B/%e/%Y Created';
$LANG_DATABOX['deny_msg'] =  'No data or no permissions.';

###############################################################################
# admin/plugins/

$LANG_DATABOX_ADMIN['piname'] = 'DataBox';

# Admin start block title
$LANG_DATABOX_ADMIN['admin_list'] = 'DataBox';

$LANG_DATABOX_ADMIN['edit'] = 'Edit';
$LANG_DATABOX_ADMIN['ref'] = 'Reference';
$LANG_DATABOX_ADMIN['view'] = 'View';
$LANG_DATABOX_ADMIN['add'] = 'add';

$LANG_DATABOX_ADMIN['new'] = 'New';
$LANG_DATABOX_ADMIN['drafton'] = 'Draft On All';
$LANG_DATABOX_ADMIN['draftoff'] = 'Draft Off All';
$LANG_DATABOX_ADMIN['export'] = 'Export';
$LANG_DATABOX_ADMIN['import'] = 'Import';
$LANG_DATABOX_ADMIN['unlinkafterimport'] = 'Delete CSV file after import';
$LANG_DATABOX_ADMIN['sampleimport'] = 'Import sample';
$LANG_DATABOX_ADMIN['datadelete'] = 'Bulk Delete';

$LANG_DATABOX_ADMIN['importfile'] = 'Path';
$LANG_DATABOX_ADMIN['importurl'] = 'URL';

$LANG_DATABOX_ADMIN['delete'] = 'Delete';
$LANG_DATABOX_ADMIN['deletemsg_user'] = "Delete all.<br ".XHTML.">";

$LANG_DATABOX_ADMIN['idfrom'] = "From ID";
$LANG_DATABOX_ADMIN['idto'] = "To ID";

$LANG_DATABOX_ADMIN['mail1'] = 'Send';
$LANG_DATABOX_ADMIN['mail2'] = 'Settings';

$LANG_DATABOX_ADMIN['submit'] = 'Submit';
$LANG_DATABOX_ADMIN['confirm'] = 'Sure you want to execute?';

$LANG_DATABOX_ADMIN['delete1'] = 'delete the draft data of the selected type';
$LANG_DATABOX_ADMIN['delete2'] = 'delete the public end data of the selected type';
$LANG_DATABOX_ADMIN['delete3'] = 'delete all the data of the selected type';

//
$LANG_DATABOX_ADMIN['link_admin'] = 'Admin';
$LANG_DATABOX_ADMIN['link_admin_top'] = 'Admin TOP';
$LANG_DATABOX_ADMIN['link_public'] = '|Public';
$LANG_DATABOX_ADMIN['link_list'] = 'List';
$LANG_DATABOX_ADMIN['link_detail'] = 'Detail';



//
$LANG_DATABOX_ADMIN['id'] = 'ID';
$LANG_DATABOX_ADMIN['help_id'] ="
";

$LANG_DATABOX_ADMIN['seq'] = 'SEQ';

$LANG_DATABOX_ADMIN['tag'] = 'TAG';
$LANG_DATABOX_ADMIN['value'] = 'VALUE';
$LANG_DATABOX_ADMIN['value2'] = 'VALUE2';
$LANG_DATABOX_ADMIN['disp'] = 'disp';
$LANG_DATABOX_ADMIN['relno'] = 'relno';

$LANG_DATABOX_ADMIN['code']='Code';

$LANG_DATABOX_ADMIN['title']='Title';
$LANG_DATABOX_ADMIN['page_title']='Page Title';

$LANG_DATABOX_ADMIN['description']='Description';
$LANG_DATABOX_ADMIN['description2']='Description2';
$LANG_DATABOX_ADMIN['fieldgroupno']='Field group';
$LANG_DATABOX_ADMIN['defaulttemplatesdirectory']='Template directory';
$LANG_DATABOX_ADMIN['layout']='Layout';

$LANG_DATABOX_ADMIN['category']='Category';

$LANG_DATABOX_ADMIN['meta_description']='META Description';

$LANG_DATABOX_ADMIN['meta_keywords']='META Keywords';

$LANG_DATABOX_ADMIN['hits']='Hits';
$LANG_DATABOX_ADMIN['hitsclear']='clear hits';

$LANG_DATABOX_ADMIN['comments']='Comment Number';

$LANG_DATABOX_ADMIN['commentcode']='Comment';

$LANG_DATABOX_ADMIN['comment_expire']='Comment Expiry Date';

$LANG_DATABOX_ADMIN['trackbackcode']='trackback';
$LANG_DATABOX_ADMIN['cache_time']='キャッシュタイム';
$LANG_DATABOX_ADMIN['cache_time_desc']='
This DATA content will be cached for no longer than this many seconds. If 0 caching is disabled. If -1 cached until page is edited again.  (3600 = 1 hour,  86400 = 1 day)
';

$LANG_DATABOX_ADMIN['group']='Group';
$LANG_DATABOX_ADMIN['parent']='Parent';

$LANG_DATABOX_ADMIN['fieldset']='Attribute Type';
$LANG_DATABOX_ADMIN['fieldset_id']="Type ID";
$LANG_DATABOX_ADMIN['fieldsetfields']="Type List";
$LANG_DATABOX_ADMIN['fieldsetfieldsregistered']="Registered attribute";
$LANG_DATABOX_ADMIN['fieldlist']="Attribute List";
$LANG_DATABOX_ADMIN['fieldsetgroups']="Category Group List";
$LANG_DATABOX_ADMIN['fieldsetgroupsregistered']="Registered Category Group";
$LANG_DATABOX_ADMIN['grouplist']="Category Group List";
$LANG_DATABOX_ADMIN['fieldsetlist']='Type List';

$LANG_DATABOX_ADMIN['registset']='Register Type';
$LANG_DATABOX_ADMIN['changeset']='Change Type';
$LANG_DATABOX_ADMIN['inst_changeset0']='Set attribute to none attribute set data:<br'.XHTML.'>';
$LANG_DATABOX_ADMIN['inst_changesetx']='<br'.XHTML.'>';

$LANG_DATABOX_ADMIN['inst_changeset'] = 'Select attribute sets.<br'.XHTML.'>';

$LANG_DATABOX_ADMIN['inst_dataexport'] =
"
Select export attribute set.<br".XHTML.">
";


$LANG_DATABOX_ADMIN['allow_display']='Allow Display (For users)';
$LANG_DATABOX_ADMIN['allow_edit']='Edit Permission (For user edit)';

$LANG_DATABOX_ADMIN['type']='Attribute Type';

$LANG_DATABOX_ADMIN['size']='Size( text ,Multiselect)';
$LANG_DATABOX_ADMIN['maxlength']='maxlength( text )';
$LANG_DATABOX_ADMIN['rows']='rows(Multi line text )';
$LANG_DATABOX_ADMIN['br']='BR(Radio button)';
$LANG_DATABOX_ADMIN['help_br']='0:no break,1-9:newline every 1-9';

//
$LANG_DATABOX_ADMIN['language_id']="Language ID";
$LANG_DATABOX_ADMIN['owner_id']="Owner ID";
$LANG_DATABOX_ADMIN['group_id']="Group ID";
$LANG_DATABOX_ADMIN['perm_owner']="Permission(Owner)";
$LANG_DATABOX_ADMIN['perm_group']="Permission(Group)";;
$LANG_DATABOX_ADMIN['perm_members']="Permission(Member)";
$LANG_DATABOX_ADMIN['perm_anon']="Permission(Anonimous)";
//

$LANG_DATABOX_ADMIN['selection']='Selection';
$LANG_DATABOX_ADMIN['selectlist']='Select List';
$LANG_DATABOX_ADMIN['checkrequried']='Check Requried';

$LANG_DATABOX_ADMIN['textcheck']='Input validation(text)';
$LANG_DATABOX_ADMIN['textconv']='Transform input value(text)';
$LANG_DATABOX_ADMIN['searchtarget']='Search target';

$LANG_DATABOX_ADMIN['initial_value']='initial value';
$LANG_DATABOX_ADMIN['range']='range';
$LANG_DATABOX_ADMIN['dfid']=$LANG04[42];

$LANG_DATABOX_ADMIN['draft'] = 'Draft';
$LANG_DATABOX_ADMIN['draft_msg'] = 'This data is in draft mode. When you want to change the mode, please report it to  site manager.';
$LANG_DATABOX_ADMIN['uid'] = 'UserID';
$LANG_DATABOX_ADMIN['modified'] = 'Modified';
$LANG_DATABOX_ADMIN['created'] = 'Created';
$LANG_DATABOX_ADMIN['released'] = 'Released';
$LANG_DATABOX_ADMIN['expired'] = 'Archive Options';
$LANG_DATABOX_ADMIN['remaingdays'] = 'remaining days';

$LANG_DATABOX_ADMIN['udatetime'] = 'Modified';
$LANG_DATABOX_ADMIN['uuid'] = 'Modified by user ID';

$LANG_DATABOX_ADMIN['kind'] = 'Kind';
$LANG_DATABOX_ADMIN['no'] = 'No.';//@@@@@-->

//@@@@@-->
$LANG_DATABOX_ADMIN['inpreparation'] = '(n.a.)';
$LANG_DATABOX_ADMIN['xml_def'] = 'XML definition';
$LANG_DATABOX_ADMIN['init'] = 'Initialize';
$LANG_DATABOX_ADMIN['list'] = 'List';
$LANG_DATABOX_ADMIN['dataclear'] = 'Clear data';
$LANG_DATABOX_ADMIN['allclear'] = 'Clear all';

$LANG_DATABOX_ADMIN['configbackup'] = 'Configuration Backup';
$LANG_DATABOX_ADMIN['configinit'] = 'Configuration Initialize';
$LANG_DATABOX_ADMIN['configrestore'] = 'Configuration restore';
$LANG_DATABOX_ADMIN['configupdate'] = 'Configuration update';
$LANG_DATABOX_ADMIN['configbackupmsg'] = 'Configuration Backup';
$LANG_DATABOX_ADMIN['configinitmsg'] = 'Configuration Initialize';
$LANG_DATABOX_ADMIN['configrestoremsg'] = 'Configuration restore';
$LANG_DATABOX_ADMIN['configupdatemsg'] = 'Configuration update';

$LANG_DATABOX_ADMIN['path'] = 'Path';
$LANG_DATABOX_ADMIN['url'] = 'URL';

$LANG_DATABOX_ADMIN['default'] = 'Default';
$LANG_DATABOX_ADMIN['importxmlmsg'] = '
Absolute path (directory , file) or URL select.<br'.XHTML.'>
If directory selected, xml file is imported under the directory.<br'.XHTML.'>
Log file is logs/databox_xmlimport.log<br'.XHTML.'>
';
$LANG_DATABOX_ADMIN['exportxmlmsg'] = '
Select absolute path (directory).<br'.XHTML.'>
Log file is logs/databox_xmlimport.log<br'.XHTML.'>
';
$LANG_DATABOX_ADMIN['initmsg'] = '
Proversion Initialize. List delete.
';
$LANG_DATABOX_ADMIN['dataclearmsg'] = '
Did you Backup?<br'.XHTML.'>
clear data now.<br'.XHTML.'>
Uploaded file delete, too.<br'.XHTML.'>
Attributes, Category, Group not deleted.<br'.XHTML.'>
';
$LANG_DATABOX_ADMIN['allclearmsg'] = '
Did you Backup?<br'.XHTML.'>
master and clear data.<br'.XHTML.'>
Uploaded file is deleted.<br'.XHTML.'>
';
$LANG_DATABOX_ADMIN['backupmsg'] =
"{$_CONF['backup_path']}"."databox/<br".XHTML.">"
.'DataBox database is backup-ed.<br'.XHTML.'br>
Backup upload file.<br'.XHTML.'>
';
$LANG_DATABOX_ADMIN['restoremsg'] =
"{$_CONF['backup_path']}"."databox/にある"
.'Filename  select.(default: databox.xml)<br'.XHTML.'>
DataBox  Database Data Restore.<br'.XHTML.'>
Restore UploadFile.<br'.XHTML.'>
';
$LANG_DATABOX_ADMIN['restoremsgPHP'] = "{$_CONF['backup_path']}"."databox/にある".'Select file name.（default:databox.xml）<br'.XHTML.'>phpMyAdmin でexport したDataBox のdatabase データをRestoreします.<br'.XHTML.'>phpMyAdmin XML Dump version 3.3.8用<br'.XHTML.'>接頭子が異なる場合は, あらかじめchange しておいてください.<br'.XHTML.'>upload file は別途もどしてください.<br'.XHTML.'>';
//<---
$LANG_DATABOX_ADMIN['datadeletemsg'] = "
Types of data that are checked will be deleted<br".XHTML.">
Uploaded file will be deleted<br".XHTML.">
";

//maps
$LANG_DATABOX_ADMIN['mapsmarkersclear'] = 'MAPS markers clear';
$LANG_DATABOX_ADMIN['mapsmarkersupdate'] = 'MAPS markers update';
$LANG_DATABOX_ADMIN['mapsmarkers'] = 'MAPS marker';

$LANG_DATABOX_ADMIN['mapsmarkersclearmsg'] = "
Clears the marker of a particular map that are registered in the attribute.
";
$LANG_DATABOX_ADMIN['mapsmarkersupdatemsg'] = "
Clears the marker of a particular map that are registered in the attribute.
It is created with the contents of the Data.
";
$LANG_DATABOX_ADMIN['mapsmarkersupdateend'] = "Success %d Error %d";

$LANG_DATABOX_ADMIN['mapsmarkersupdateend2'] = "(Latitude and longitude calculation %d Updateing %d)";
//csv
$LANG_DATABOX_ADMIN['csv_def'] = 'CSV definition';
$LANG_DATABOX_ADMIN['csv_select'] = 'SELECT';
$LANG_DATABOX_ADMIN['csv_select_dtl'] = 'SELECT:detail';
$LANG_DATABOX_ADMIN['csvheader'] = 'CSV First line';
$LANG_DATABOX_ADMIN['help_field_csv'] = "
There are two attributes that use the master
If you want to edit the input file No. Example of master (prefecture)
If you want to edit the input file the value of the master (prefectures _value)
";
$LANG_DATABOX_ADMIN['help_value_csv'] = "
In the case of category, please register the name of the category of
";
$LANG_DATABOX_ADMIN['help_value_csv_sel'] = "
In the case of multiple  Separated, Example Parking,apartments
";

$LANG_DATABOX_ADMIN['importmsgcsv'] = "
Absolute path (directory , file) or URL select.<br".XHTML.">
If directory selected, xml file is imported under the directory.<br".XHTML.">
Log file is logs/databox_csvimport.log<br".XHTML.">
";
$LANG_DATABOX_ADMIN['exportmsgcsv'] = "
Select absolute path (directory).<br".XHTML.">
Log file is logs/databox_csvimport.log<br".XHTML.">
";
$LANG_DATABOX_ADMIN['initmsgcsv'] = '
CSV Initialize. List delete.
';

$LANG_DATABOX_ADMIN['draftonmsg'] = "
All drafts are turned on <br".XHTML.">
";
$LANG_DATABOX_ADMIN['draftoffmsg'] = "
All drafts are turned off <br".XHTML.">
";
$LANG_DATABOX_ADMIN['hitsclearmsg'] = "
Hits becomes 0<br".XHTML.">
";

$LANG_DATABOX_ADMIN['yy'] = '/';
$LANG_DATABOX_ADMIN['mm'] = '/';
$LANG_DATABOX_ADMIN['dd'] = ' ';

$LANG_DATABOX_ADMIN['must'] = '*';

$LANG_DATABOX_ADMIN['enabled'] = 'Enabled';
$LANG_DATABOX_ADMIN['modified_autoupdate'] = 'Auto Updated';

$LANG_DATABOX_ADMIN['additionfields'] = 'Attributes';
$LANG_DATABOX_ADMIN['basicfields'] = 'Default Attributes';

$LANG_DATABOX_ADMIN['category_id'] = 'Category ID';
$LANG_DATABOX_ADMIN['field_id'] = 'Attribute ID';
$LANG_DATABOX_ADMIN['name'] = 'Name';
$LANG_DATABOX_ADMIN['templatesetvar'] = 'Theme Variable';
$LANG_DATABOX_ADMIN['templatesetvars'] = 'Theme Variables';
$LANG_DATABOX_ADMIN['parent_id'] = 'Parent ID';
$LANG_DATABOX_ADMIN['parent_flg'] = 'Is Parent Group?';
$LANG_DATABOX_ADMIN['input_type'] = 'Render with Input Type';

$LANG_DATABOX_ADMIN['orderno'] = 'Order';

$LANG_DATABOX_ADMIN['field'] = 'Attribute';
$LANG_DATABOX_ADMIN['fields'] = 'Attribute';
$LANG_DATABOX_ADMIN['content'] = 'Contents';

$LANG_DATABOX_ADMIN['byusingid'] = 'Use ID';
$LANG_DATABOX_ADMIN['byusingcode'] = 'Use Code';
$LANG_DATABOX_ADMIN['byusingtemplatesetvar'] = 'Use Theme Variable';

$LANG_DATABOX_ADMIN['withlink'] = 'With Link';
$LANG_DATABOX_ADMIN['groupbygroup'] = 'groupby group  ';

$LANG_DATABOX_ADMIN['number'] ="Number";
$LANG_DATABOX_ADMIN['endmessage'] = "Finished";
//help
$LANG_DATABOX_ADMIN['delete_help_field'] = '(NOTE: Data is removed, too!)';
$LANG_DATABOX_ADMIN['delete_help_group'] = '(There is data present. Will not delete group.)';
$LANG_DATABOX_ADMIN['delete_help_category'] = '(There is data present. Will not delete category and parent.)';
$LANG_DATABOX_ADMIN['delete_help_fieldset'] = '(There is data present. Will not delete attribute.)';
$LANG_DATABOX_ADMIN['delete_help_mst'] = '(There is registered data, can\'t remove.)';
//xmlimport_help
$LANG_DATABOX_xmlimport['help']=
"<br{KHTML}>"
."(Note)<br{KHTML}>"
."assist DataBox Plugin XML Batch Import Path registered same path.<br{KHTML}>"
."<br{KHTML}>"
."assist Plugin xmlImport exit.<br{KHTML}>"
."maps:item_10 is Code registered.<br{KHTML}>"
."same code is already registered, delete added.<br{KHTML}>"
."<br{KHTML}>"
."DataBox Plugin xmlImport exit.<br{KHTML}>"
."same code is already registered, delete added.<br{KHTML}>"
."process, XMLFile Delete.<br{KHTML}>"
."(permission delete) <br{KHTML}>"
."<br{KHTML}>"
."exit　databox_xmlimport.log register.<br{KHTML}>"

;
$LANG_DATABOX_ADMIN['jobend'] = "Finished<br".XHTML.">";
$LANG_DATABOX_ADMIN['cnt_ok'] = "Done: %d<br".XHTML.">";
$LANG_DATABOX_ADMIN['cnt_ng'] = "Error: %d<br".XHTML.">";
$LANG_DATABOX_ADMIN['cnt_ex'] = "Excluded: %d <br".XHTML.">";

//backup&restore
$LANG_DATABOX_ADMIN['config'] = 'Configuration';

$LANG_DATABOX_ADMIN['config_backup'] = 'Backup';
$LANG_DATABOX_ADMIN['config_backup_help'] = 'Save Backup File';

$LANG_DATABOX_ADMIN['config_init'] = 'Initialize';
$LANG_DATABOX_ADMIN['config_init_help'] = 'Initialize.';

$LANG_DATABOX_ADMIN['config_restore'] = 'Restore';
$LANG_DATABOX_ADMIN['config_restore_help'] = 'Restore Backup File';

$LANG_DATABOX_ADMIN['config_update'] = 'Update';
$LANG_DATABOX_ADMIN['config_update_help'] = 'Update.';

//(2)
$LANG_DATABOX_ADMIN['datamaster'] = 'data & master';
$LANG_DATABOX_ADMIN['data_clear'] = 'data clear';
$LANG_DATABOX_ADMIN['data_allclear'] = 'data & master clear';
$LANG_DATABOX_ADMIN['data_backup'] = 'data & master backup';
$LANG_DATABOX_ADMIN['data_restore'] = 'data & master restore';

$LANG_DATABOX_ADMIN['document'] = 'Document';
$LANG_DATABOX_ADMIN['configuration'] = 'Configuration Settings';
$LANG_DATABOX_ADMIN['install'] = 'install';
$LANG_DATABOX_ADMIN['autotags'] = 'Autotags';
$LANG_DATABOX_ADMIN['files'] = 'file list';
$LANG_DATABOX_ADMIN['tables'] = 'table list';
$LANG_DATABOX_ADMIN['input'] = 'input list';
$LANG_DATABOX_ADMIN['online'] = 'Online';

//Admin
$LANG_DATABOX_ADMIN['about_admin_information'] = '';
$LANG_DATABOX_ADMIN['about_admin_data'] = 'Data Admin';
$LANG_DATABOX_ADMIN['about_admin_category'] = 'Category Admin';
$LANG_DATABOX_ADMIN['about_admin_field'] = 'Attribute Admin';
$LANG_DATABOX_ADMIN['about_admin_group'] = 'Group Admin';
$LANG_DATABOX_ADMIN['about_admin_fieldset'] = 'Attribute Set Adimin';
$LANG_DATABOX_ADMIN['about_admin_backuprestore'] = 'Create Backup and Restore<br{KHTML}><br{KHTML}>';
$LANG_DATABOX_ADMIN['about_admin_mst'] = 'Master Admin';

$LANG_DATABOX_ADMIN['about_admin_xml'] = 'XML definition ';
$LANG_DATABOX_ADMIN['about_admin_csv'] = 'CSV definition ';


$LANG_DATABOX_ADMIN['about_admin_view'] = 'Display for general login user page.';

$LANG_DATABOX_ADMIN['inst_fieldsetfields'] =
'Attribute Edit: click Attributes name, click add or remove button.<br'.XHTML.'>
to add Attributes, select only right side.<br'.XHTML.'>
After edit, click Save button.<br'.XHTML.'>';

$LANG_DATABOX_ADMIN['inst_newdata'] =
'Select Type for Creation of Data<br'.XHTML.'>
';

//ERR
$LANG_DATABOX_ADMIN['err'] = 'Error';
$LANG_DATABOX_ADMIN['err_empty'] = 'File is nothing';

$LANG_DATABOX_ADMIN['err_invalid'] = 'Invalid data';
$LANG_DATABOX_ADMIN['err_permission_denied'] = 'Not permitted.';

$LANG_DATABOX_ADMIN['err_id'] = 'ID is not valid';
$LANG_DATABOX_ADMIN['err_name'] = 'Name is not valid';
$LANG_DATABOX_ADMIN['err_templatesetvar'] = 'Theme variableis not invalid';
$LANG_DATABOX_ADMIN['err_templatesetvar_w'] = 'Theme variable is already used';
$LANG_DATABOX_ADMIN['err_code_w'] = 'Code is duplicated';
$LANG_DATABOX_ADMIN['err_code'] = 'Code input error';
$LANG_DATABOX_ADMIN['err_title'] = 'Title input error';
$LANG_DATABOX_ADMIN['err_numeric'] = 'Numerical value can only be input';

$LANG_DATABOX_ADMIN['err_text1'] = 'Numbers value can only be input';
$LANG_DATABOX_ADMIN['err_text2'] = 'Alphanumeric value can only be input';
$LANG_DATABOX_ADMIN['err_text3'] = 'Alphanumeric / -. value can only be input';
$LANG_DATABOX_ADMIN['err_text4'] = 'Alphanumeric symbols value can only be input';
$LANG_DATABOX_ADMIN['err_range'] = 'Range check error';

$LANG_DATABOX_ADMIN['err_selection'] = 'Nothing selected';

$LANG_DATABOX_ADMIN['err_modified'] = 'Edit date is not valid.';

$LANG_DATABOX_ADMIN['err_created'] = 'Created date is not valid.';
$LANG_DATABOX_ADMIN['err_released'] = 'Published date is not valid.';
$LANG_DATABOX_ADMIN['err_expired'] = 'Publish date is not valid.';

$LANG_DATABOX_ADMIN['err_checkrequried'] = 'Check Required,';

$LANG_DATABOX_ADMIN['err_date'] = 'Date is invalid.';
$LANG_DATABOX_ADMIN['err_time'] = 'Time is invalid.';
$LANG_DATABOX_ADMIN['err_writable'] = ' is not writable';

$LANG_DATABOX_ADMIN['err_size'] = 'Size is invalid.';//@@@@@
$LANG_DATABOX_ADMIN['err_type'] = ' Type is invalid.';//@@@@@

$LANG_DATABOX_ADMIN['err_field_w'] = 'This attribute is already registered';
$LANG_DATABOX_ADMIN['err_tag_w'] = 'This tag is already registered.';

$LANG_DATABOX_ADMIN['err_csvheader_w'] = 'This CSV header title is already registered';
$LANG_DATABOX_ADMIN['err_csvheader'] = 'CSV header title is invalid.';

$LANG_DATABOX_ADMIN['err_url'] = 'This URL is not a valid address';

$LANG_DATABOX_ADMIN['err_backup_file_not_exist'] = 'Configuration backup file is non existence.<br{KHTML}>';
$LANG_DATABOX_ADMIN['err_backup_file_non_rewritable'] = 'Configuration Backup File not (re)writable<br{KHTML}>';

$LANG_DATABOX_ADMIN['err_not_exist'] = 'Not found';

$LANG_DATABOX_ADMIN['err_kind'] = 'Kind error.';
$LANG_DATABOX_ADMIN['err_no'] = 'no error.';
$LANG_DATABOX_ADMIN['err_no_w'] = 'This number is already used.';

###############################################################################
$LANG_DATABOX_ORDER['random']="Random Order";
$LANG_DATABOX_ORDER['date']="Date Order";
$LANG_DATABOX_ORDER['orderno']="Display Order";
$LANG_DATABOX_ORDER['code']="Code Order";
$LANG_DATABOX_ORDER['title']="Title Order";
$LANG_DATABOX_ORDER['description']="Description Order";
$LANG_DATABOX_ORDER['id']="ID order";
$LANG_DATABOX_ORDER['released']="Released";
$LANG_DATABOX_ORDER['order']="Order";

###############################################################################
##
$LANG_DATABOX_XML['base:code']=$LANG_DATABOX_ADMIN['code'];
$LANG_DATABOX_XML['base:title']=$LANG_DATABOX_ADMIN['title'];



###############################################################################
$LANG_DATABOX_MAIL['subject_data'] =
"【{$_CONF['site_name']}】Data Update by %s";

$LANG_DATABOX_MAIL['message_data']=
"Updated by %s(user no.%s)".LB.LB;






if (isset($_USER['username']) && isset($_CONF['site_name']) && isset( $_USER['uid'])) {

$LANG_DATABOX_MAIL['subject_category'] =
"【{$_CONF['site_name']}】Category Update by {$_USER['username']}.";

$LANG_DATABOX_MAIL['message_category']=
"Category was edited by {$_USER['username']}(user no.{$_USER['uid']}).".LB.LB;

$LANG_DATABOX_MAIL['subject_group'] =
"[{$_CONF['site_name']}] Group was edited by {$_USER['username']}.";

$LANG_DATABOX_MAIL['message_group']=
"Group was edited by {$_USER['username']}(user no.{$_USER['uid']}).".LB.LB;

$LANG_DATABOX_MAIL['subject_fieldset'] =
"【{$_CONF['site_name']}】Type was edted by {$_USER['username']}";

$LANG_DATABOX_MAIL['message_fieldset']=
"Type was updated by {$_USER['username']}(user no.{$_USER['uid']}).".LB.LB;
}

#
$LANG_DATABOX_MAIL['sig'] = LB
."------------------------------------".LB
."{$_CONF['site_name']}".LB
."{$_CONF['site_url']}".LB
."This is automaticaly sended.".LB
."------------------------------------".LB
;

if (isset($_USER['username']) && isset($_CONF['site_name']) && isset( $_USER['uid'])) {

$LANG_DATABOX_MAIL['subject_data_delete'] =
"【{$_CONF['site_name']}】DataDelete by {$_USER['username']}";
$LANG_DATABOX_MAIL['message_data_delete']=
"Data was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;


$LANG_DATABOX_MAIL['subject_category_delete'] =
"【{$_CONF['site_name']}】CategoryDelete by {$_USER['username']}";
$LANG_DATABOX_MAIL['message_category_delete']=
"Category was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;

$LANG_DATABOX_MAIL['subject_group_delete'] =
"【{$_CONF['site_name']}】Group Delete by {$_USER['username']}";
$LANG_DATABOX_MAIL['message_group_delete']=
"Group was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;

$LANG_DATABOX_MAIL['subject_fieldset_delete'] =
"【{$_CONF['site_name']}】Type removed by {$_USER['username']}.";
$LANG_DATABOX_MAIL['message_fieldset_delete']=
"Attributes set was removed by {$_USER['username']}(user no.{$_USER['uid']}).".LB;
}

###############################################################################
#
$LANG_DATABOX_NOYES = array(
    0 => 'No',
    1 => 'Yes'
);
$LANG_DATABOX_INPUTTYPE = array(
    0 => 'Checkbox',
    1 => 'Multi Select List'
    ,2 => 'Radio Button List'
    ,3 => 'Option List'
);


$LANG_DATABOX_ALLOW_DISPLAY = array();
$LANG_DATABOX_ALLOW_DISPLAY[0] ='Display';
$LANG_DATABOX_ALLOW_DISPLAY[1] ='Login User';
$LANG_DATABOX_ALLOW_DISPLAY[2] ='Owner, Group and Admin';
$LANG_DATABOX_ALLOW_DISPLAY[3] ='Owner and Admin';
$LANG_DATABOX_ALLOW_DISPLAY[4] ='Admin only';
$LANG_DATABOX_ALLOW_DISPLAY[5] = 'No Display';

$LANG_DATABOX_ALLOW_EDIT = array();
$LANG_DATABOX_ALLOW_EDIT[0] = 'Edit';
$LANG_DATABOX_ALLOW_EDIT[2] = 'Only Owner,Group,and Admin';
$LANG_DATABOX_ALLOW_EDIT[3] = 'Owner and Admin';
$LANG_DATABOX_ALLOW_EDIT[4] = 'Display Disabled';
$LANG_DATABOX_ALLOW_EDIT[5] = 'Display only';

$LANG_DATABOX_TEXTCHECK = array();
$LANG_DATABOX_TEXTCHECK[0] = 'no check';
$LANG_DATABOX_TEXTCHECK[11] = 'only half-width(change half-width automatically)';
$LANG_DATABOX_TEXTCHECK[12] = 'only half-width alphanumeric(change half-width automatically)';
$LANG_DATABOX_TEXTCHECK[13] = 'ID format(change half-width automatically)';
$LANG_DATABOX_TEXTCHECK[14] = 'only half-width alphanumeric or symbol(change half-width automatically)';
$LANG_DATABOX_TEXTCONV = array();
$LANG_DATABOX_TEXTCONV[0] = 'no';
$LANG_DATABOX_TEXTCONV[10] = 'change half-width';
$LANG_DATABOX_TEXTCONV[20] = 'change half-width';

//TYPE (Chenge Disabled)
$LANG_DATABOX_TYPE = array();
$LANG_DATABOX_TYPE[0] = '1 Line Text Attribute';
$LANG_DATABOX_TYPE[1] = 'MultiLine Text Attribute (HTML)';
$LANG_DATABOX_TYPE[20] = 'Multi Line Text Attribute(HTML/TinyMCE)';
$LANG_DATABOX_TYPE[10] = 'MultiLine Text Attribute (TEXT)';
$LANG_DATABOX_TYPE[19] = 'MultiLine Text Attribute (CKEditor)';
$LANG_DATABOX_TYPE[15] = 'Numeric';
$LANG_DATABOX_TYPE[21] = 'Currency';

$LANG_DATABOX_TYPE[2] = 'Yes/No';
$LANG_DATABOX_TYPE[3] = 'Date (Date Picker)';
$LANG_DATABOX_TYPE[22] = 'date (jquery ui datepicker)';
$LANG_DATABOX_TYPE[23] = 'date (Uikit datepicker)';
$LANG_DATABOX_TYPE[4] = 'Date & time (In preparation)';
$LANG_DATABOX_TYPE[26] = 'time (Uikit timepicker)';
$LANG_DATABOX_TYPE[5] = 'Mail Address';
$LANG_DATABOX_TYPE[6] = 'url';
$LANG_DATABOX_TYPE[7] = 'Option List';
$LANG_DATABOX_TYPE[8] = 'Radio Button List';
$LANG_DATABOX_TYPE[14] = 'Multiselect';
$LANG_DATABOX_TYPE[24] = 'Check Box';
$LANG_DATABOX_TYPE[17] = 'Flag';
$LANG_DATABOX_TYPE[9] = 'Definition List';
$LANG_DATABOX_TYPE[16] = 'Radio Button List（from master）';//@@@@@
$LANG_DATABOX_TYPE[18] = 'Multiselect（from master）';//@@@@@
$LANG_DATABOX_TYPE[25] = 'Check Box （from master）';


$LANG_DATABOX_TYPE[11] = 'Image(DB Save)';
$LANG_DATABOX_TYPE[12] = 'Image(File Save)';
$LANG_DATABOX_TYPE[13] = 'File(In Preparation )';


###############################################################################
#
$LANG_DATABOX_SEARCH['type'] = 'DataBox';

$LANG_DATABOX_SEARCH['results_databox'] = 'DataBox Search Results';

$LANG_DATABOX_SEARCH['title'] =  'Title';
$LANG_DATABOX_SEARCH['udate'] =  'Update';

###############################################################################
#
$LANG_DATABOX_STATS['data'] = 'DataBox';
$LANG_DATABOX_STATS['stats_page_title']='Title';
$LANG_DATABOX_STATS['stats_hits']='Hits';
$LANG_DATABOX_STATS['stats_headline']='Top Ten DataBox Pages';
$LANG_DATABOX_STATS['stats_no_hits']='It appears that there are no DataBox pages on this site';

###############################################################################
# COM_showMessage()
$PLG_databox_MESSAGE1  = 'Saved';
$PLG_databox_MESSAGE2  = 'Deleted';
$PLG_databox_MESSAGE3  = 'Check a Problem.';

// Messages for the plugin upgrade
$PLG_databox_MESSAGE3002 = $LANG32[9];

###############################################################################
#
$LANG_DATABOX_autotag_desc['databox']="
[databox:count] <br".XHTML.">
More, see Databox Plugin documents.
<a href=\"{$_CONF['site_admin_url']}/plugins/databox/docs/japanese/autotags.html\">*</a>
";

###############################################################################
# configuration
// Localization of the Admin Configuration UI
$LANG_configsections['databox']['label'] = 'DataBox';
$LANG_configsections['databox']['title'] = 'DataBox Setting';

//----------
$LANG_configsubgroups['databox']['sg_main'] = 'Main';
//--(0)

// $LANG_tab['databox'][tab_main] = 'MainSetting';
// $LANG_fs['databox'][fs_main] = 'DataBox MainSetting';
$LANG_confignames['databox']['perpage'] = 'Date Number by Page';
$LANG_confignames['databox']['loginrequired'] = 'Login Required';
$LANG_confignames['databox']['hidemenu'] = 'Hide Menu';

$LANG_confignames['databox']['categorycode'] = 'Use Category Code ';
$LANG_confignames['databox']['datacode'] = 'Use Data Code';
$LANG_confignames['databox']['groupcode'] = 'Use Group Code';
$LANG_confignames['databox']['top'] = 'Program on Frontpage';
$LANG_confignames['databox']['templates'] = 'Templates Public';
$LANG_confignames['databox']['templates_admin'] = 'Templates Admin';

$LANG_confignames['databox']['themespath'] = 'Theme Template Path';
$LANG_confignames['databox']['delete_data'] = 'Delete data when Owner Deleted';
$LANG_confignames['databox']['datefield'] = 'Date field';

$LANG_confignames['databox']['meta_tags'] = 'Use Meta';

$LANG_confignames['databox']['layout'] = 'Layout Public';
$LANG_confignames['databox']['layout_admin'] = 'Layout Admin';
//----------------------
$LANG_confignames['databox']['mail_to'] = 'Notify Address';
$LANG_confignames['databox']['mail_to_owner'] = 'Mail to owner';
$LANG_confignames['databox']['mail_to_draft'] = 'Notify draft data';
$LANG_confignames['databox']['allow_data_update'] = 'Permit User Update Data';
$LANG_confignames['databox']['allow_data_delete'] = 'Permit User Delete Data';
$LANG_confignames['databox']['allow_data_insert'] = 'Permit User Insert Data';
$LANG_confignames['databox']['admin_draft_default'] = 'Admin Create New: default Draft';
$LANG_confignames['databox']['user_draft_default'] = 'User Create New: default Draft';

$LANG_confignames['databox']['dateformat'] = 'Date Format with Date Picker';

$LANG_confignames['databox']['aftersave'] = 'Action after Save for Public';
$LANG_confignames['databox']['aftersave_admin'] = 'Action after Save for Admin';

$LANG_confignames['databox']['grp_id_default'] = 'Group Default';

$LANG_confignames['databox']['default_img_url'] = 'Default Image URL';

$LANG_confignames['databox']['maxlength_description'] = 'Maxlength description';
$LANG_confignames['databox']['maxlength_meta_description'] = 'Max length of meta description';
$LANG_confignames['databox']['maxlength_meta_keywords'] = 'Max length of meta keyword';

$LANG_confignames['databox']['hideuseroption'] = 'hide useroption';

$LANG_confignames['databox']['commentcode'] = 'Comment Default';

$LANG_confignames['databox']['sort_list_by'] = 'Sort Admin List by';
$LANG_confignames['databox']['sort_list_by_my'] = 'Sort My Data List by';
$LANG_confignames['databox']['default_cache_time'] = 'default cache time';
$LANG_confignames['databox']['disable_permission_ignore'] = 'disable permission ignore';

$LANG_confignames['databox']['sitemap_excepts'] = 'XMLSitemap excepted codes';

//--(1)
// $LANG_tab['databox'][tab_whatsnew] = 'Whats new Block';
// $LANG_fs['databox'][fs_whatsnew] = 'Whats new Block';
$LANG_confignames['databox']['whatsnew_interval'] = 'New Period';
$LANG_confignames['databox']['hide_whatsnew'] = 'Hide Whats New';
$LANG_confignames['databox']['title_trim_length'] = 'Title of Max Length';




//---(2)
// $LANG_tab['databox'][tab_search] = 'Search';
// $LANG_fs['databox'][fs_search] = 'Search Results';
$LANG_confignames['databox']['include_search'] = 'Data Search';
$LANG_confignames['databox']['additionsearch'] = 'Attributes number for Search';

//---(3)
// $LANG_tab['databox'][tab_permissions] = 'Permission';
// $LANG_fs['databox'][fs_permissions] = 'Data Permission Default([0]Owner [1]Group [2]Member [3]Anonimous)';
$LANG_confignames['databox']['default_permissions'] = 'Permissions';

//---(4)
// $LANG_tab['databox'][tab_autotag] = 'Autotags';
// $LANG_fs['databox'][fs_autotag] = 'Autotags';
$LANG_confignames['databox']['intervalday']="Display Period(Day)";
$LANG_confignames['databox']['limitcnt']="Display Number";//@@@@@
$LANG_confignames['databox']['newmarkday']="New Mark Display Period(Day)";//@@@@@
$LANG_confignames['databox']['categories']="Default Category";//@@@@@!!!!
$LANG_confignames['databox']['new_img']="New Mark";//@@@@@
$LANG_confignames['databox']['rss_img']="RSS Mark";//@@@@@

//---(５)
$LANG_tab['databox']['tab_file'] = 'Upload File';
$LANG_fs['databox']['fs_file'] = 'Upload File';
$LANG_confignames['databox']['imgfile_size'] = 'Image File(DB) MaxSize';
$LANG_confignames['databox']['imgfile_type'] = 'Image File(DB)  Type';

$LANG_confignames['databox']['imgfile_size2'] = 'Image File Max Size';
$LANG_confignames['databox']['imgfile_type2'] = 'Image File Type';
$LANG_confignames['databox']['imgfile_frd'] = 'Image Save URL';
$LANG_confignames['databox']['imgfile_thumb_frd'] = 'Thumbnail Image Save URL';

$LANG_confignames['databox']['imgfile_thumb_ok'] = 'Use Thumbnail';
$LANG_confignames['databox']['imgfile_thumb_w'] = 'Thumbnail Size(w)';
$LANG_confignames['databox']['imgfile_thumb_h'] = 'Thumbnail Size(h)';
$LANG_confignames['databox']['imgfile_thumb_w2'] = 'Original Image Size(w2)';
$LANG_confignames['databox']['imgfile_thumb_h2'] = 'Original Image Size(h2)';
$LANG_confignames['databox']['imgfile_smallw'] = 'Display Image Max Width';
$LANG_confignames['databox']['imgfile_subdir'] = 'use subdirectory for Image Save URL';



$LANG_confignames['databox']['file_path'] = 'File Save Absolute Path';
$LANG_confignames['databox']['file_size'] = 'File Size';
$LANG_confignames['databox']['file_type'] = 'File Type';
$LANG_confignames['databox']['file_subdir'] = 'use subdirectory for File Save Absolute Path';


//---(６)
$LANG_tab['databox']['tab_autotag_permissions'] = 'Autotags Permission';
$LANG_fs['databox']['fs_autotag_permissions'] = 'Autotags Permission ([0]Owner [1]Group [2]Member [3]Anonimous)';
$LANG_confignames['databox']['autotag_permissions_databox'] = '[databox: ] Permission';

//---(９)
$LANG_tab['databox']['tab_xml'] = 'XML';
$LANG_fs['databox']['fs_xml'] = '(OPTION:XML)';
$LANG_confignames['databox']['path_xml'] = 'XML Batch Import Path';
$LANG_confignames['databox']['path_xml_out'] = 'XML Export Path';
$LANG_confignames['databox']['xml_default_fieldset_id'] = 'XML Import Default Type';

//---(１０)
$LANG_tab['databox']['tab_csv'] = 'CSV';
$LANG_fs['databox']['fs_csv'] = '（OPTION:CSV）';
$LANG_confignames['databox']['path_csv'] = 'CSV Batch Import Path';
$LANG_confignames['databox']['path_csv_out'] = 'CSV Export Path';
$LANG_confignames['databox']['csv_default_fieldset_id'] = 'CSV Import Default Type';
$LANG_confignames['databox']['csv_default_owner_id'] = 'CSV Import Default OwnerID';
$LANG_confignames['databox']['csv_cron_schedule_interval'] = 'Cron Schedule Interval';
$LANG_confignames['databox']['csv_cron_schedule_unlink'] = 'schedule.After the end of the input file Delete ';
$LANG_confignames['databox']['csv_cron_schedule_nextmaps'] = 'schedule.Maps continues execution ';
$LANG_confignames['databox']['csv_cron_schedule_sel_id'] = 'schedule.SELECT ';

//---(１１)
$LANG_tab['databox']['tab_maps'] = 'MAPS';
$LANG_fs['databox']['fs_maps'] = '（OPTION:MAPS）';
$LANG_confignames['databox']['maps_mid'] = 'Kind of master to register the map ID';
$LANG_confignames['databox']['maps_lat'] = 'Theme Variable for latitude';
$LANG_confignames['databox']['maps_lng'] = 'Theme Variable for Longitude';
$LANG_confignames['databox']['maps_pref'] = 'Theme Variable for Prefectural';
$LANG_confignames['databox']['maps_address1'] = 'Theme Variable for Address1';
$LANG_confignames['databox']['maps_address2'] = 'Theme Variable for Address2';
$LANG_confignames['databox']['maps_address3'] = 'Theme Variable for Address3';
$LANG_confignames['databox']['maps_cron_schedule_interval'] = 'Cron Schedule Interval';

// Note: entries 0, 1, 9, 12, 17 are the same as in $LANG_configselects['Core']
$LANG_configselects['databox'][0] =array('Yes' => 1, 'No' => 0);
$LANG_configselects['databox'][1] =array('Yes' => TRUE, 'No' => FALSE);
$LANG_configselects['databox'][12] =array('AccessDisabled' => 0, 'Display' => 2, 'Display・Edit' => 3);
$LANG_configselects['databox'][13] =array('AccessDisabled' => 0, 'Use' => 2);

$LANG_configselects['databox'][5] =array(
    'Hide' => 'hide'
    , 'Display by Modified Date' => 'modified'
    , 'Display by Created Date' => 'created'
    , 'Display by Released Date' => 'released');

//$LANG_configselects['databox'][17] =array('Access Denyed' => 0, 'Display' => 2, 'Display・Edit' => 3);

$LANG_configselects['databox'][20] =array(
    'Standard' => 'standard'
    , 'Custom' => 'custom'
    , 'Theme' => 'theme'
    );

//@@@@@
$LANG_configselects['databox'][21] =array(
     'By Modified Date' => 'modified'
    , 'By Created Date' => 'created'
    , 'Display by Released Date' => 'released'
    );

$LANG_configselects['databox'][22] =array(
    'Standard' => 'standard'
    , 'Left and Right Blocks' => 'leftrightblocks'
    , 'Blank Page' => 'blankpage'
    , 'No Block' => 'noblocks'
    , 'Left Block' => 'leftblocks'
    , 'Right Block' => 'rightblocks'
    );

$LANG_configselects['databox'][23] =array(
    'Yes' => 3
    , 'List and Detail' => 2
    , 'Only Detaile' => 1
    , 'No' => 0
    );


$LANG_configselects['databox'][9] =array(
    'Same Page Display' => 'no'
    , 'Page Display' => 'item'
    , 'List Display' => 'list'
    , 'Home Display' => 'home'
    , 'AdminTop Display' => 'admin'
    , 'PluginTop Display' => 'plugin'
        );
$LANG_configselects['databox'][25] =array(
    'Same Page Display' => 'no'
    , 'Page Display' => 'item'
    , 'List Display' => 'list'
    , 'Home Display' => 'home'
    , 'PluginTop Display' => 'plugin'
        );
//
$LANG_configselects['databox'][24] =array();
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
        $LANG_configselects['databox'][24][$grp_name]=$grp_id;
	}

$LANG_configselects['databox'][26] =array( 'Comments Enabled' => 0, 'Comments Disabled' => -1);

$LANG_configselects['databox'][27] =array(
    'Order' => 'orderno'
    ,'ID' => 'id'
    , 'Code' => 'code'
    , 'Title' => 'title'
    , 'Data Type' => 'fieldset_name'
    , 'remaining days' => 'remaingdays'
    , 'Hits' => 'hits'
    , 'Time Stamp (descending)' => 'udatetime'
    , 'Draft' => 'draft_flag'
        );

//
$LANG_configselects['databox'][28] =array();
    $sql = LB;
    $sql .= "SELECT ".LB;
    $sql .= " fieldset_id".LB;
    $sql .= ",name".LB;
    $sql .= " FROM {$_TABLES['DATABOX_def_fieldset']}".LB;
    $sql .= " ORDER BY fieldset_id".LB;
    $result = DB_query( $sql );
    $nrows = DB_numRows( $result );

    for( $i = 0; $i < $nrows; $i++ )    {
        $A = DB_fetchArray( $result, true );
        $name=$A['name'];
        $fieldset_id=$A['fieldset_id'];
        $LANG_configselects['databox'][28][$name]=$fieldset_id;
}
    if  ($rt<>0){
        $sql = LB;
        $sql .= "SELECT ".LB;
        $sql .= " csv_sel_id".LB;
        $sql .= ",name".LB;
        $sql .= " FROM {$_TABLES['DATABOX_def_csv_sel']}".LB;
        $sql .= " ORDER BY csv_sel_id".LB;
        $result = DB_query( $sql );
        $nrows = DB_numRows( $result );
        $LANG_configselects['databox'][29]['ALL']="";
        for( $i = 0; $i < $nrows; $i++ )    {
            $A = DB_fetchArray( $result, true );
            $name=$A['name'];
            $csv_sel_id=$A['csv_sel_id'];
            $LANG_configselects['databox'][29][$name]=$csv_sel_id;
        }
    }
$LANG_configselects['databox'][30] =array(
    'Order' => 'orderno'
    ,'ID' => 'id'
    , 'Code' => 'code'
    , 'Title' => 'title'
    , 'Data Type' => 'fieldset_name'
    , 'remaining days' => 'remaingdays'
    , 'Time Stamp (descending)' => 'udatetime'
    , 'Draft' => 'draft_flag'
        );

?>
