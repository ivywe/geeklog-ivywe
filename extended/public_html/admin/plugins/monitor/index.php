<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.3                                                        |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Plugin administration page                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014-2016 by the following authors:                         |
// |                                                                           |
// | Authors: Ben - ben AT geeklog DOT fr                                      |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
// +---------------------------------------------------------------------------+
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
* @package Monitor
*/
session_start();
$_SESSION['imgtoresize'] = 0;

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path_system'] . 'lib-user.php';

$ready_plugins = array('test','captcha','monitor','forum','ban');
define('GITHUB_REPOSITORY', "https://api.github.com/repos/{$_MONITOR_CONF['repository']}/");

/**
* Upload new photo, delete old photo
*
* @param    string  $delete_photo   'on': delete old photo
* @return   string                  filename of new photo (empty = no new photo)
*
*/
function MONITOR_handlePhotoUpload ($delete_photo = '', $uid)
{
    global $_CONF, $_TABLES, $_USER, $LANG24;

    require_once ($_CONF['path_system'] . 'classes/upload.class.php');

    $upload = new upload();
    
    if (!empty ($_CONF['image_lib'])) {
        if ($_CONF['image_lib'] == 'imagemagick') {
            // Using imagemagick
            $upload->setMogrifyPath ($_CONF['path_to_mogrify']);
        } elseif ($_CONF['image_lib'] == 'netpbm') {
            // using netPBM
            $upload->setNetPBM ($_CONF['path_to_netpbm']);
        } elseif ($_CONF['image_lib'] == 'gdlib') {
            // using the GD library
            $upload->setGDLib ();
        }
        $upload->setAutomaticResize (true);
        if (isset ($_CONF['debug_image_upload']) &&
                $_CONF['debug_image_upload']) {
            $upload->setLogFile ($_CONF['path'] . 'logs/error.log');
            $upload->setDebug (true);
        }
        if (isset($_CONF['jpeg_quality'])) {
            $upload->setJpegQuality($_CONF['jpeg_quality']);
        }
    }
    
    $upload->setAllowedMimeTypes (array ('image/gif'   => '.gif',
                                         'image/jpeg'  => '.jpg,.jpeg',
                                         'image/pjpeg' => '.jpg,.jpeg',
                                         'image/x-png' => '.png',
                                         'image/png'   => '.png'
                                 )      );
    if (!$upload->setPath ($_CONF['path_images'] . 'userphotos')) {
        $display = COM_siteHeader ('menu', $LANG24[30]);
        $display .= COM_startBlock ($LANG24[30], '',
                COM_getBlockTemplate ('_msg_block', 'header'));
        $display .= $upload->printErrors (false);
        $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block',
                                                        'footer'));
        $display .= COM_siteFooter ();
        COM_output($display);
        exit; // don't return
    }

    $filename = '';
    if (!empty ($delete_photo) && ($delete_photo == 'on')) {
        $delete_photo = true;
    } else {
        $delete_photo = false;
    }

    $curphoto = DB_getItem ($_TABLES['users'], 'photo',
                            "uid = {$uid}");
    if (empty ($curphoto)) {
        $delete_photo = false;
    }

    // see if user wants to upload a (new) photo
    $newphoto = $_FILES['photo'];
    if (!empty ($newphoto['name'])) {
        $pos = strrpos ($newphoto['name'], '.') + 1;
        $fextension = substr ($newphoto['name'], $pos);
        
        $username = DB_getItem ($_TABLES['users'], 'username',
                            "uid = {$uid}");
        $filename = $username . '.' . $fextension;

        if (!empty ($curphoto) && ($filename != $curphoto)) {
            $delete_photo = true;
        } else {
            $delete_photo = false;
        }
    }

    // delete old photo first
    if ($delete_photo) {
        USER_deletePhoto ($curphoto);
    }

    // now do the upload
    if (!empty ($filename)) {
        $upload->setFileNames ($filename);
        $upload->setPerms ('0644');
        if (($_CONF['max_photo_width'] > 0) &&
            ($_CONF['max_photo_height'] > 0)) {
            $upload->setMaxDimensions ($_CONF['max_photo_width'],
                                       $_CONF['max_photo_height']);
        } else {
            $upload->setMaxDimensions ($_CONF['max_image_width'],
                                       $_CONF['max_image_height']);
        }
        if ($_CONF['max_photo_size'] > 0) {
            $upload->setMaxFileSize($_CONF['max_photo_size']);
        } else {
            $upload->setMaxFileSize($_CONF['max_image_size']);
        }
        $upload->uploadFiles ();

        if ($upload->areErrors ()) {
            $display = COM_siteHeader ('menu', $LANG24[30]);
            $display .= COM_startBlock ($LANG24[30], '',
                    COM_getBlockTemplate ('_msg_block', 'header'));
            $display .= $upload->printErrors (false);
            $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block',
                                                            'footer'));
            $display .= COM_siteFooter ();
            COM_output($display);
            exit; // don't return
        }
    } else if (!$delete_photo && !empty ($curphoto)) {

        $filename = $curphoto;
    }

    return $filename;
}

/**
* Displays comments list
*
* @return   string          HTML for the list of items
*
*/
function MONITOR_commentsList()
{
    global $_CONF, $_TABLES, $LANG29, $LANG_ADMIN, $LANG_MONITOR_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
    
    $H = array($LANG29[14], $LANG29[10], $LANG29[36]);
    $section_title = $LANG_MONITOR_1['comments_list'];

    
    $sql = "SELECT cid AS id,date,title,comment,uid,type,sid "
          . "FROM {$_TABLES['comments']} ";

    $query_arr = array(
        //'table'          => 'comments',
        'sql'            => $sql,
        //'query_fields'   => array('clid', 'created', 'title', 'owner_id'),
        'default_filter' => COM_getPermSQL ('AND', 0, 3)
    );

    $header_arr = array(      // display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 0),
        array('text' => $H[0], 'field' => 1, 'sort' => true),
        array('text' => $H[1], 'field' => 2),
        array('text' => $H[2], 'field' => 3),
        array('text' => $LANG29[42], 'field' => 'uid', 'sort' => true),
    );

    $text_arr = array('has_menu' => false,
                      'has_extras' => true,
                      'title'    => $section_title,
                      'no_data'  => $LANG29[39],
                      'form_url' => "{$_CONF['site_admin_url']}/plugins/monitor/index.php?action=comments_list"
    );
    $form_arr = array('bottom' => '', 'top' => '');

    $defsort_arr = array('field' => 'id', 'direction' => 'desc');
    
    $retval .= LB. '<style>.admin-list-field { vertical-align:top !important;} </style>' . LB;
     
    $retval .= ADMIN_list('MONITOR_commentsList', 'MONITOR_commentsList_fields', $header_arr,
                                $text_arr, $query_arr, $defsort_arr, $filter = '', $extra = '',
            $options = '', $form_arr='', $showsearch = true);

    return $retval;
}

function MONITOR_commentsList_fields($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_MONITOR_1;

    switch($fieldname) {
        case "edit":
            $edit_url = $_CONF['site_url'] . '/comment.php'
                    . '?mode=edit&type=article&amp;cid=' . $A[0] . '&amp;sid='
                            . $A['sid'];
            $retval = COM_createLink($icon_arr['edit'], $edit_url);
            break;
        case "title":
            $url = $_CONF['site_url'] .
                                 '/index.php?mode=v&amp;ad=' . $A['clid'];
            $retval = COM_createLink($A['title'], $url);
            break;
        case "uid":
            if ($A['uid'] < 2) {
                $retval = $LANG_MONITOR_1['anonymous'];
                break;
            }
            $uid_url = $_CONF['site_url'] .
                                 '/users.php?mode=profile&uid=' . $A['uid'];
            $retval = COM_createLink(COM_getDisplayName($fieldvalue), $uid_url);
            break;
        default:
            $retval = stripslashes($fieldvalue);
            break;
    }
    return $retval;
}

/**
* Displays items needing edition
*
* Displays the list of items from tables
*
* @param    string  $type   Type of object to build list for
* @return   string          HTML for the list of items
*
*/
function MONITOR_itemlist($type, $subtype = '')
{
    global $_CONF, $_TABLES, $LANG29, $LANG_ADMIN,$LANG_MONITOR_1;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    if (empty($type)) {
        // something is terribly wrong, bail
        COM_errorLog("Type not set for items list in monitor plugin");
        return $retval;
    }

    $isplugin = false;

    if ($type == 'image') {
        
        $sql = "SELECT i.ai_sid, i.ai_img_num, i.ai_filename, s.date, s.uid, s.title, u.username "
              . "FROM {$_TABLES['article_images']} AS i"
              . " LEFT JOIN {$_TABLES['stories']} AS s ON i.ai_sid=s.sid"
              . " LEFT JOIN {$_TABLES['users']} AS u ON s.uid=u.uid WHERE 1=1";
        
        $form_url = "{$_CONF['site_admin_url']}/plugins/monitor/index.php?action=images";
        
        $defsort_arr = array('field'     => 'ai_filename',
                         'direction' => 'ASC');
                         
        $query_arr = array('table' => 'article_images',
                       'sql' => $sql,
                       'query_fields' => array('ai_sid', 'ai_img_num', 'ai_filename', 'date', 's.uid', 'title', 'username'),
                       'default_filter' => "");
        //Build header list
        $header_arr = array(      # display 'text' and use table field 'field'
                        //array('text' => $LANG29[14], 'field' => 'date', 'sort' => true),
                        array('text' => $LANG29[10], 'field' => 'ai_filename', 'sort' => true), 
                        array('text' => $LANG29[42], 'field' => 'uid', 'sort' => true)
        );
        
        $text_arr = array('has_menu' => false,
                      'title'    => '',
                      'no_data'  => '',
                      'form_url' => $form_url,
                      'has_extras' => true,
                      'inline'   => true
                    );
    } else {
        $retval .= "Type $type not implemented in monitor plugin";
        COM_errorLog($retval);
        return $retval;
    }
    
    $retval .= LB. '<style>.admin-list-field { vertical-align:top !important;} </style>' . LB;
    $retval .= ADMIN_list('monitor', 'MONITOR_getListField_images', $header_arr,
                          $text_arr, $query_arr, $defsort_arr);

    return $retval;
}



function MONITOR_getListField_images ($fieldname, $fieldvalue, $A, $icon_arr) {

    global $_CONF;
    
    switch ($fieldname) {

        case 'date':
            
            $retval = '<small style="white-space: nowrap;">#'. $A['cid'] . ' - ';
            $creation = COM_getUserDateTimeFormat(strtotime($fieldvalue));
            $retval .= $creation[0]. '</small>';
            
            break;
        
        //Images
        case 'ai_filename':
            
            if (!is_file($_CONF['path_images'] . 'articles/' . $fieldvalue)) {
                $image = $_CONF['site_url'] . '/admin/plugins/monitor/images/unavailable.png';
            } else {
                $image = $_CONF['site_url'] . '/images/articles/' . $A['ai_filename'];
            }
            $retval = '<div style="float:left;margin:10px 20px 10px 5px; "><a href="' . $_CONF['site_url'] . '/images/articles/' . $fieldvalue . '" target="_blank"><img src="' . $_CONF['site_url'] . '/admin/plugins/monitor/images.php?src=' . $image . '&amp;w=100&amp;h=100&amp;a=t" align="top" alt="' . stripslashes($fieldvalue) . '" /></a></div><p><strong>' . stripslashes($fieldvalue) . '</strong><br' . XHTML . '>';
            $retval .= '#'. $A['ai_img_num'] . ' ';
            $creation = COM_getUserDateTimeFormat(strtotime($A['date']));
           $retval .= $creation[0]  . '<br' . XHTML . '><a href="' . $_CONF['site_url'] . '/article.php?story=' . $A['ai_sid']. '" target="_blank">' . stripslashes($A['title']) . '</a></p>';
               
            break;
            
        case 'title_image' :
            
            $retval = stripslashes($fieldvalue);
            
            break;
            
        case 'uid':
            
            if ($fieldvalue >= 2) {
                $retval = COM_createLink($A['username'], $_CONF['site_url']
                    . '/users.php?mode=profile&amp;uid=' .  $A['uid']);
                 $retval = '<p style="white-space: nowrap;">' .  $retval . '</span></p>';
            } else {
                 $retval = '<p style="white-space: nowrap;">' .  $A['username'] . '</p>';
            } 
            
            break;
            
        default:
            
            $retval = stripslashes($fieldvalue);
            
            break;
    }
    
    return $retval;
}

/**
* List available plugins
*
* @param    string  $token  Security token
* @return   string          formatted list of plugins
*
*/
function MONITOR_listplugins($token)
{
    global $_CONF, $_MONITOR_CONF, $LANG_MONITOR_1, $_TABLES, $LANG32, $LANG_ADMIN, $_IMAGE_TYPE;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG32[16], 'field' => 'pi_name', 'sort' => true),
        array('text' => $LANG32[17], 'field' => 'pi_version', 'sort' => false),
        //array('text' => $LANG32[50], 'field' => 'pi_dependencies', 'sort' => false),
        array('text' => $LANG_MONITOR_1['updates'], 'field' => 'pi_update', 'sort' => false)
    );

    $defsort_arr = array('field' => 'pi_name', 'direction' => 'asc');

    $retval .= COM_startBlock($LANG_MONITOR_1['plugin_list'], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $text_arr = array(
        'has_extras'   => false,
        //'instructions' => $LANG32[11],
        'form_url'     => $_CONF['site_admin_url'] . '/plugins/monitor/index.php'
    );

    $query_arr = array(
        'table' => 'plugins',
        'sql' => "SELECT pi_name, pi_version, pi_gl_version, "
                ."pi_enabled, pi_homepage FROM {$_TABLES['plugins']} WHERE pi_enabled=1",
        'query_fields' => array('pi_name'),
        'default_filter' => ''
    );
    
    $form_arr = array(
        'top' => '<p>' . $LANG_MONITOR_1['available_updates'] . ' <a href="https://github.com/' . $_MONITOR_CONF['repository'] . '" target="_blank">' . $_MONITOR_CONF['repository'] . '</a></p>'
    );

    $retval .= ADMIN_list('plugins', 'MONITOR_getListField_plugins', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

/**
* See if we can figure out the plugin's real name
*
* @param    string  $plugin     internal name / directory name
* @return   string              real or beautified name
*
*/
function MONITOR_get_pluginname($plugin)
{
    global $_CONF;

    $retval = '';

    $plugins_dir = $_CONF['path'] . 'plugins/';
    $autoinstall = $plugins_dir . $plugin . '/autoinstall.php';

    // for new plugins, get the name from the autoinstall.php
    if (file_exists($autoinstall)) {

        require_once $autoinstall;

        $fn = 'plugin_autoinstall_' . $plugin;
        if (function_exists($fn)) {
            $info = $fn($plugin);
            if (is_array($info) && isset($info['info']) &&
                    isset($info['info']['pi_display_name'])) {
                $retval = $info['info']['pi_display_name'];
            }
        }

    }

    if (empty($retval)) {
        // give up and fake it
        $retval = ucwords(str_replace('_', ' ', $plugin));
    }

    return $retval;
}

function MONITOR_getListField_plugins($fieldname, $fieldvalue, $A, $icon_arr, $token)
{
    global $_CONF, $_MONITOR_CONF, $LANG_ADMIN, $LANG32, $_TABLES, $ready_plugins, $LANG_MONITOR_1;

    $retval = '';

    switch ($fieldname) {

    case 'pi_name':
        $retval = MONITOR_get_pluginname($A['pi_name']);
        break;

    case 'pi_version':
        $plugin_code_version = PLG_chkVersion($A['pi_name']);
        if (empty($plugin_code_version)) {
            $code_version = $LANG_ADMIN['na'];
        } else {
            $code_version = $plugin_code_version;
        }
        $pi_installed_version = $A['pi_version'];
        if (empty($plugin_code_version) ||
                ($pi_installed_version == $code_version)) {
            $retval = $pi_installed_version;
        } else {
            $retval = "{$LANG32[37]}: $pi_installed_version,&nbsp;{$LANG32[36]}: $plugin_code_version";
            if ($A['pi_enabled'] == 1) {
                $retval .= " <b>{$LANG32[38]}</b>";
                $csrftok = '&amp;' . CSRF_TOKEN . '=' . $token;
                $style = 'style="vertical-align: middle;"';
                $img = $_CONF['layout_url'] . '/images/update.png';
                $img = "<img $style alt=\"[" . $LANG32[38] . "]\" src=\"$img\"" . XHTML . ">";
                $url = $_CONF['site_admin_url'] . '/plugins.php?mode=updatethisplugin&amp;pi_name=' . $A['pi_name'] . $csrftok;
                $retval .= COM_CreateLink($img, $url, array('title' => $LANG32[42]));
            }
        }
        break;

    case 'pi_dependencies':
           
        //https://raw.githubusercontent.com/hostellerie/test/master/autoinstall.php
        if (PLG_checkDependencies($A['pi_name'])) {
            $retval = COM_getTooltip($LANG32[51], PLG_printDependencies($A['pi_name'], $A['pi_gl_version']));
        } else {
            $style = "display: inline; color: #a00; border-bottom: 1px dotted #a00;";
            $retval = COM_getTooltip("<b class='notbold' style='$style'>{$LANG32[52]}</b>", PLG_printDependencies($A['pi_name'], $A['pi_gl_version']));
        }
        break;

    case 'pi_update':
        
        if (!PLG_checkDependencies($A['pi_name'])) {
            $retval = str_replace('<img ', '<img title="' . $LANG32[64] . '" ', $icon_arr['warning']);
        } else {
            $available = false; // plugin in repository
            $dependencie = true;
            $not_present = false;
            if ($A['pi_enabled'] == 1) {
                $title  = '';
                $link = '';
                $plugin = $A['pi_name'];
                
                if (in_array($plugin, $ready_plugins)) {
                    //Check if plugin is in repo
                    $url = GITHUB_REPOSITORY . $plugin . '/releases';
                    //Get last release for this plugin
                    $releases = MONITOR_curlRequestOnGitApi($url);
                    $tag = $releases[0]['tag_name'];
                    if ($tag != '') $available = true; 
                    //Is release newer
                    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                            "pi_name = '$plugin'");
                    if ($tag != '' && 'v'.$installed_version != $tag ) {
                        
                        $update = true;
                        
                        //TODO Check dependencies
                        $autoinstall_url = 'https://raw.githubusercontent.com/' . $_MONITOR_CONF['repository'] . '/' . $plugin . '/' . $tag . '/autoinstall.php';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $autoinstall_url);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $data = curl_exec($ch);
                        curl_close($ch);
                        
                        //Get infos from plugin
                        $data = preg_replace('/\s+/', '', $data);
                        $gl_version = MONITOR_extract_unit($data, "pi_gl_version'=>'", "',");
                        
                        //Geeklog dependencie
                        if ( COM_versionCompare(VERSION, $gl_version, '>=') ) {
                            $title = $LANG_MONITOR_1['update_to'] . ' ' . $tag;
                            $link = $_CONF['site_admin_url'] . "/plugins/monitor/index.php?action=update_plugin&amp;plugin=$plugin";
                        } else {
                            $dependencie = false;
                            $title = $LANG_MONITOR_1['need_upgrade'] . $gl_version . '+ ' . $LANG_MONITOR_1['before_update'] . ' ' . $plugin . ' ' . $tag;
                            $link = "https://www.geeklog.net";
                        }
                    } else if ($tag == '') {
                        // The plugin is not available in this repo
                        $update = false;
                        $title = $LANG_MONITOR_1['not_available'];
                        $link = "https://github.com/{$_MONITOR_CONF['repository']}";
                    } else {
                        // The plugin is up to date
                        $update = false;
                    }
                } else {
                    $update = false;
                    //Ask plugin author to change this :)
                    $title = $LANG_MONITOR_1['ask_author'];
                    $link = DB_getItem($_TABLES['plugins'], 'pi_homepage',
                                            "pi_name = '$plugin'");
                }
            } else {
                $title  = 'Update this plugin';
                $link = '';
                $update = true;
                if (!file_exists($_CONF['path'] . 'plugins/' . $A['pi_name'] . '/functions.inc')) {
                    $not_present = true;
                }
            }
            if ($not_present) {
                $retval = str_replace('<img ', '<img title="' . $LANG32[64] . '" ', $icon_arr['unavailable']);
            } else {
                $sorting = '';
                $csrftoken = '&amp;' . CSRF_TOKEN . '=' . $token;
                if (!empty($_GET['order']) && !empty($_GET['direction'])) { // Remember how the list was sorted
                    $ord = trim($_GET['order']);
                    $dir = trim($_GET['direction']);
                    $old = trim($_GET['prevorder']);
                    $sorting = "&amp;order=$ord&amp;direction=$dir&amp;prevorder=$old";
                }
                //Icons in update coloumn
                if(!$update && in_array($plugin, $ready_plugins) & $available) {
                    $retval = str_replace('<img ', $LANG_MONITOR_1['up_to_date'] . ' <img title="' . $LANG_MONITOR_1['up_to_date'] . '" ', $icon_arr['enabled']);
                    $retval = $icon_arr['enabled'] . ' ' . $LANG_MONITOR_1['up_to_date'];
                } else if(!$update && in_array($plugin, $ready_plugins) & !$available) {
                    $retval = COM_createLink($icon_arr['info'] . ' ' . $title, $link.$sorting,
                            array('title' => ''));
                } else if(!$update && !in_array($plugin, $ready_plugins)) {
                    $retval = COM_createLink($icon_arr['disabled'] . ' ' . $title, $link.$sorting,
                            array('title' => ''));
                } else if(!$update) {
                    $retval = str_replace('<img ', '<img title="' . $LANG_MONITOR_1['no_update'] . '" ', $icon_arr['disabled']);
                } else if ($dependencie == false ){
                    $retval = COM_createLink($icon_arr['warning'] . ' ' . $title, $link.$sorting,
                            array('title' => ''));
                } else {
                    $retval = COM_createLink($icon_arr['info'] . ' ' . $title, $link.$sorting,
                            array('title' => ''));
                }
            }
        }
        break;

    default:
        $retval = $fieldvalue;
        break;
    }

    return $retval;
}

/*
Credits: Bit Repository
URL: http://www.bitrepository.com/extract-content-between-two-delimiters-with-php.html
*/
function MONITOR_extract_unit($string, $start, $end)
{
    $pos = stripos($string, $start); 
    $str = substr($string, $pos);
    $str_two = substr($str, strlen($start));
    $second_pos = stripos($str_two, $end);
    $str_three = substr($str_two, 0, $second_pos);
    $unit = trim($str_three); // remove whitespaces
     
    return $unit;
}

function MONITOR_curlRequestOnGitApi($url)
{
    global $_MONITOR_CONF;
    
    $ch = curl_init($url);

    //Set the User Agent as username
    //TODO config user
    curl_setopt($ch, CURLOPT_USERAGENT, $_MONITOR_CONF['repository']);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, $header); // returns header in output
//    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    //Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute
    $result = curl_exec($ch);

    // Closing
    curl_close($ch);

    //Decode the json in array
    $return = json_decode($result,true);

    //Return array
    return $return;
}

/**
* Handle uploaded plugin
*
* @return   string      HTML or error message
*
*/
function MONITOR_plugin_upload($plugin='')
{
    global $_CONF, $_MONITOR_CONF, $_TABLES;

    $retval = '';
    
    if ($plugin == '' || $_MONITOR_CONF['repository'] == '') return;
    
    $url = "https://api.github.com/repos/{$_MONITOR_CONF['repository']}/" . $plugin . '/releases';
    //Get last release for this plugin
    $releases = MONITOR_curlRequestOnGitApi($url);
    $version = $releases[0]['tag_name'];

    $path_admin = $_CONF['path_html'] . substr($_CONF['site_admin_url'],
            strlen($_CONF['site_url']) + 1) . '/';

    $upload_success = false;
    
    //Download the zip file from repository
    $source = "https://codeload.github.com/{$_MONITOR_CONF['repository']}/$plugin/zip/$version";
    $destination = fopen($_CONF['path_data']. $plugin . '.zip','w+');

    set_time_limit(0); // unlimited max execution time
    
    $options = array(
      CURLOPT_FILE    => $destination,
      CURLOPT_TIMEOUT =>  28800, // set this to 8 hours so we dont timeout on big files
      CURLOPT_URL     => $source,
      CURLOPT_USERAGENT => $_MONITOR_CONF['repository'],
      CURLOPT_SSL_VERIFYPEER => false
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    

    $plugin_file = $_CONF['path_data'] . $plugin . '.zip'; // Name the plugin file
    
    if (!file_exists($plugin_file)) {
        COM_errorLog('MONITOR - Download failed for Plugin: ' .  $plugin);
        return 'Download failed for Plugin: ' .  $plugin;
    } else {
        chmod($plugin_file,0755);
    }

    require_once $_CONF['path_system'] . 'classes/unpacker.class.php';
    $archive = new unpacker($plugin_file, 'application/x-zip');
        
    if ($archive == false) return 72;
    
    COM_errorLog('MONITOR - Download ' . $plugin . ' plugin: ok');

    $pi_did_exist   = false; // plugin directory already existed
    $pi_had_entry   = false; // plugin had an entry in the database
    $pi_was_enabled = false; // plugin was enabled
    $alternate = false;

    if (file_exists($_CONF['path'] . 'plugins/' . $plugin)) {
        $pi_did_exist = true;

        // plugin directory already exists
        $pstatus = DB_query("SELECT pi_name, pi_enabled FROM {$_TABLES['plugins']} WHERE pi_name = '$plugin'");
        $A = DB_fetchArray($pstatus);
        if (isset($A['pi_name'])) {
            $pi_had_entry = true;
            $pi_was_enabled = ($A['pi_enabled'] == 1);
        }

        if ($pi_was_enabled) {
            // disable temporarily while we move the files around
            DB_change($_TABLES['plugins'], 'pi_enabled', 0,
                                           'pi_name', $plugin);
            COM_errorLog('MONITOR - Disable Plugin: ' .  $plugin);
        }

        require_once 'System.php';

        $plugin_dir = $_CONF['path'] . 'plugins/' . $plugin;
        if (file_exists($plugin_dir . '.previous')) {
            @System::rm('-rf ' . $plugin_dir . '.previous');
        }
        if (file_exists($plugin_dir)) {
            rename($plugin_dir, $plugin_dir . '.previous');
            COM_errorLog('MONITOR - Rename: ' .  $plugin_dir . ' to ' . $plugin_dir . '.previous');
        }

        $public_dir = $_CONF['path_html'] . $plugin;
        if (file_exists($public_dir . '.previous')) {
            @System::rm('-rf ' . $public_dir . '.previous');
        }
        if (file_exists($public_dir)) {
            rename($public_dir, $public_dir . '.previous');
            COM_errorLog('MONITOR - Rename: ' .  $public_dir . ' to ' . $public_dir . '.previous');
        }

        $admin_dir = $path_admin . 'plugins/' . $plugin;
        if (file_exists($admin_dir . '.previous')) {
            @System::rm('-rf ' . $admin_dir . '.previous');
        }
        if (file_exists($admin_dir)) {
            rename($admin_dir, $admin_dir . '.previous');
            COM_errorLog('MONITOR - Rename: ' .  $admin_dir . ' to ' . $admin_dir . '.previous');
        }
    }
    
    $upload_success = false;
    
    // Extract the uploaded archive to the data directory
    $upload_success = $archive->unpack($_CONF['path_data']);
    
    if ( !$upload_success ) {
        //Try alternative unzip
        unset($archive);
        require_once 'Archive/Zip.php';
        $archive = new Archive_Zip($plugin_file); 
        if ($archive == false) return 72;
        $params = array('add_path' => $_CONF['path_data']);
        $extract = $archive->extract($params);
        if ( is_array( $extract ) ) $upload_success = true;
        $alternate = true;
    }

    if ( !$upload_success ) {
                
        COM_errorLog("MONITOR - Can't unzip the archive. Update for $plugin_dir plugin failed! Please check the archive in your data folder. Could be an OS issue during unzip.");
        
        if (file_exists($plugin_dir . '.previous')) {
            rename($plugin_dir . '.previous', $plugin_dir);
            COM_errorLog('MONITOR - Rename: ' .  $plugin_dir . '.previous' . ' to ' . $plugin_dir);
        }

        if (file_exists($public_dir . '.previous')) {
            rename($public_dir . '.previous', $public_dir);
            COM_errorLog('MONITOR - Rename: ' .  $public_dir . '.previous' . ' to ' . $public_dir);
        }

        if (file_exists($admin_dir . '.previous')) {
            rename($admin_dir . '.previous', $admin_dir);
            COM_errorLog('MONITOR - Rename: ' .  $admin_dir . '.previous' . ' to ' . $admin_dir);
        }
        
        if ($pi_was_enabled) {
            DB_change($_TABLES['plugins'], 'pi_enabled', 1,
                                           'pi_name', $plugin);
            COM_errorLog('MONITOR - Enable Plugin: ' .  $plugin);
        }
        return 72;
        exit;
    } else {
        //Move files to plugins directory
        COM_errorLog('MONITOR - Plugin update: ' .  $plugin);    
        if (!$alternate) { 
            $folder_name = $archive->getdir();
        } else {
            $listcontent = $archive->listContent();
            $folder_name = $listcontent[0]['filename'];
            if(substr($folder_name, -1) == '/') {
                $folder_name = substr($folder_name, 0, -1);
            }
        }
        if ($folder_name == '') exit;
        
        $srcDir = $_CONF['path_data'] . $folder_name;
        $destDir = $_CONF['path'] . 'plugins/' . $plugin;
        
        //Move from data folder to plugins folder
        rename($srcDir, $destDir);

        $plg_path = $_CONF['path'] . 'plugins/' . $plugin . '/';
        
        if (file_exists($plg_path . 'public_html')) {
            rename($plg_path . 'public_html',
                   $_CONF['path_html'] . $plugin);
            COM_errorLog('MONITOR - Move ' .  $plg_path . 'public_html to ' . $_CONF['path_html'] . $plugin);
        } else {
            COM_errorLog('MONITOR - ' .  $plg_path . 'public_html does not exist');
        }
        if (file_exists($plg_path . 'admin')) {
            rename($plg_path . 'admin',
                   $path_admin . 'plugins/' . $plugin);
            COM_errorLog('MONITOR - Move ' .  $plg_path . 'admin to ' . $path_admin . 'plugins/' . $plugin);
        } else {
            COM_errorLog('MONITOR - ' .  $plg_path . 'admin does not exist');
        }

        unset($archive); // Collect some garbage

        // cleanup when uploading a new version
        if ($pi_did_exist) {
            $plugin_dir = $_CONF['path'] . 'plugins/' . $plugin;
            if (file_exists($plugin_dir . '.previous')) {
                @System::rm('-rf ' . $plugin_dir . '.previous');
            }

            $public_dir = $_CONF['path_html'] . $plugin;
            if (file_exists($public_dir . '.previous')) {
                @System::rm('-rf ' . $public_dir . '.previous');
            }

            $admin_dir = $path_admin . 'plugins/' . $plugin;
            if (file_exists($admin_dir . '.previous')) {
                @System::rm('-rf ' . $admin_dir . '.previous');
            }

            if ($pi_was_enabled) {
                DB_change($_TABLES['plugins'], 'pi_enabled', 1,
                                               'pi_name', $plugin);
                COM_errorLog('MONITOR - Enable Plugin: ' .  $plugin);
            }
        }

        $msg_with_plugin_name = false;
        if ($pi_did_exist) {
            if ($pi_was_enabled) {
                // check if we have to perform an update
                $pi_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                         "pi_name = '$plugin'");
                $code_version = PLG_chkVersion($plugin);
                if (! empty($code_version) &&
                        ($code_version != $pi_version)) {
                    /**
                    * At this point, we would have to call PLG_upgrade().
                    * However, we've loaded the plugin's old functions.inc
                    * (in lib-common.php). We can't load the new one here
                    * now since that would result in duplicate function
                    * definitions. Solution: Trigger a reload (with the new
                    * functions.inc) and continue there.
                    */
                    $url = $_CONF['site_admin_url'] . '/plugins/monitor/index.php'
                         . '?action=continue_upgrade'
                         . '&amp;codeversion=' . urlencode($code_version)
                         . '&amp;piversion=' . urlencode($pi_version)
                         . '&amp;plugin_update=' . urlencode($plugin);
                    COM_errorLog('MONITOR - Update Plugin ' . $plugin  . ' from version: ' . $pi_version . ' to code version: ' . $code_version);
                    echo COM_refresh($url);
                    exit;
                } else {
                    $msg = 98; // successfully uploaded
                }
            } else {
                $msg = 98; // successfully uploaded
            }
        } elseif (file_exists($plg_path . 'autoinstall.php')) {
            // if the plugin has an autoinstall.php, install it now
            if (plugin_autoinstall($plugin)) {
                PLG_pluginStateChange($plugin, 'installed');
                $msg = 44; // successfully installed
            } else {
                $msg = 72; // an error occured while installing the plugin
            }
        } else {
            $msg = 98; // successfully uploaded
        }
    }

    return $msg;
}

/**
 * Check if an error occured while uploading a file
 *
 * @param   array   $mFile  $_FILE['uploaded_file']
 * @return  mixed           Returns the error string if an error occured,
 *                          returns false if no error occured
 *
 */
function MONITOR_plugin_getUploadError($mFile)
{
    global $LANG32;

    $retval = '';

    if (isset($mFile['error']) && ($mFile['error'] !== UPLOAD_ERR_OK)) { // If an error occured while uploading the file.

        if ($mFile['error'] > UPLOAD_ERR_EXTENSION) { // If the error code isn't known

            $retval = $LANG32[99]; // Unknown error

        } else {

            $retval = $LANG32[$mFile['error'] + 100]; // Print the error

        }

    } else { // If no upload error occurred

        $retval = false;

    }

    return $retval;
}

/**
* Continue a plugin upgrade that started in MONITOR_plugin_upload()
*
* @param    string  $plugin         plugin name
* @param    string  $pi_version     current plugin version
* @param    string  $code_version   plugin version to be upgraded to
* @return   string                  HTML refresh
* @see      function plugin_upload
*
*/
function MONITOR_continue_upgrade($plugin, $pi_version, $code_version)
{
    global $_CONF, $_TABLES;

    $retval = '';
    $msg_with_plugin_name = false;

    // simple sanity checks
    if (empty($plugin) || empty($pi_version) || empty($code_version) ||
            ($pi_version == $code_version)) {
        $msg = 72;
    } else {
        // more sanity checks
        $result = DB_query("SELECT pi_version, pi_enabled FROM {$_TABLES['plugins']} WHERE pi_name = '" . addslashes($plugin) . "'");
        $A = DB_fetchArray($result);
        if (!empty($A['pi_version']) && ($A['pi_enabled'] == 1) &&
                ($A['pi_version'] == $pi_version) &&
                ($A['pi_version'] != $code_version)) {
            // continue upgrade process that started in MONITOR_plugin_upload()
            $result = PLG_upgrade($plugin);
            if ($result === true) {
                PLG_pluginStateChange($plugin, 'upgraded');
                $msg = 60; // successfully updated
            } else {
                $msg_with_plugin_name = true;
                $msg = $result; // message provided by the plugin
            }
        } else {
            $msg = 72;
        }
    }

    $url = $_CONF['site_admin_url'] . '/plugins/monitor/index.php?msg=' . $msg;
    //if ($msg_with_plugin_name) {
    //    $url .= '&amp;plugin=' . $plugin;
    //}
    $retval = COM_refresh($url);

    return $retval;
}


$display = '';

// Ensure user even has the rights to access this page
if (! SEC_hasRights('monitor.admin')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the Monitor plugin administration screen.");

    echo $display;
    exit;
}


$log = isset($_POST['log']) ? COM_applyFilter($_POST['log']) : '';
/*
* Main Function
*/

$content = '';

$display = COM_siteHeader('none');

//Menu
$display .= '<p><a href="' . $_CONF['site_admin_url'] . '/plugins/monitor/index.php">' . $LANG_MONITOR_1['home'] . '</a> 
            | <a href="' . $_CONF['site_admin_url'] . '/plugins/monitor/index.php?action=logs">' . $LANG_MONITOR_1['logs'] . '</a> 
            | <a href="' . $_CONF['site_admin_url'] . '/plugins/monitor/index.php?action=images">' . $LANG_MONITOR_1['images'] . '</a>
            | <a href="' . $_CONF['site_admin_url'] . '/plugins/monitor/index.php?action=resize_images">' . $LANG_MONITOR_1['resize'] . '</a>
            | <a href="' . $_CONF['site_admin_url'] . '/plugins/monitor/index.php?action=change_user_photo">' . $LANG_MONITOR_1['change_user_photo'] . '</a>
            | <a href="' . $_CONF['site_admin_url'] . '/plugins/monitor/index.php?action=comments_list">' . $LANG_MONITOR_1['comments'] . '</a>
            | <a href="#" onclick="document.monitor_conf_link.submit()">' . $LANG_MONITOR_1['configuration'] . "</a></p>
    <form class=\"uk-form\" name='monitor_conf_link' action='" . $_CONF['site_admin_url'] . "/configuration.php' method='POST'>
    <input type='hidden' name='conf_group' value='monitor'></form>";
 
$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file (array ('admin' => 'administration.thtml'));

$action = isset($_REQUEST['action']) ? COM_applyFilter($_REQUEST['action']) : '';
$log_file = isset($_REQUEST['log_file']) ? COM_applyFilter($_REQUEST['log_file']) : '';

$uid = intval($_POST['uid']);

switch ($action) {
    
    case 'images' :
        
        $T->set_var('title', $LANG_MONITOR_1['images_list'] );
        $content .= MONITOR_itemlist('image');
        
        break;
        
    case 'upload_user_photo' :
        
        //Get user info
        $result = DB_query("SELECT fullname,cookietimeout,email,homepage,sig,emailstories,about,location,pgpkey,photo,remoteservice FROM {$_TABLES['users']},{$_TABLES['userprefs']},{$_TABLES['userinfo']} WHERE {$_TABLES['users']}.uid = {$uid} AND {$_TABLES['userprefs']}.uid = {$uid} AND {$_TABLES['userinfo']}.uid = {$uid}");
        $A = DB_fetchArray ($result);
        
        $T->set_var('title', 'Upload new photo for user ' . $A['fullname'] . ' (' . $uid . ')' );
        
        $filename = MONITOR_handlePhotoUpload ('', $uid );
        
        if (!empty ($filename)) {
            if (!file_exists ($_CONF['path_images'] . 'userphotos/' . $filename)) {
                $filename = '';
            }
        }
        
        DB_query("UPDATE {$_TABLES['users']} SET photo='$filename' WHERE uid={$uid}");
        
        $content .= USER_getPhoto ($uid, $filename, $A['email'], -1);
        $content .= '<p>' . $filename . '</p>';
        
        break;
    
    case 'resize_images' :	
    
        $content .= "<form class=\"uk-form\" method=\"post\" action=\"{$_CONF['site_admin_url']}/plugins/monitor/index.php?action=resize_images\">";
        
        //List images from images folder
        $images_files = MONITOR_recursiveFiles();
        $content .= '<h2>' . $LANG_MONITOR_1['images_folder'] . '</h2>';
        if ($_SESSION['imgtoresize'] > 0) {
            $content .= '<p>' . $LANG_MONITOR_1['resize_images_help'] . '</p>';
            $content .="<p>$images_files</p>";
            $content .= "<input type=\"submit\" name=\"resize\" value=\"{$LANG_MONITOR_1['resize_images']}\">";
        } else {
            $content .= '<p>' . $LANG_MONITOR_1['no_images_to_resize'] . '</p>';
        }

        $content .= "</form>";
        
        break;
        
    case 'change_user_photo' :
        
        //User id is set
        if ( $_CONF['allow_user_photo'] == 1 && $uid > 0 ) {
        
            $username = DB_getItem ($_TABLES['users'], 'username',
                            "uid = {$uid}");
                            
            if ($username == '') {
                $T->set_var('title', 'Change photo of user ' . $A['fullname'] . ' (' . $uid . ')' );
                $content = 'This user does not exist';
            } else {
                //Get user info
                $result = DB_query("SELECT fullname,cookietimeout,email,homepage,sig,emailstories,about,location,pgpkey,photo,remoteservice FROM {$_TABLES['users']},{$_TABLES['userprefs']},{$_TABLES['userinfo']} WHERE {$_TABLES['users']}.uid = {$uid} AND {$_TABLES['userprefs']}.uid = {$uid} AND {$_TABLES['userinfo']}.uid = {$uid}");
                $A = DB_fetchArray ($result);
                
                $T->set_var('title', 'Change photo of user ' . $A['fullname'] . ' (' . $uid . ')' );
                
                $photo = USER_getPhoto ($uid, $A['photo'], $A['email'], -1);
                
                if (empty ($photo)) {
                    $display_photo = '';
                } else {
                    $display_photo = '<br' . XHTML . '>' . $photo;
                }
                
                if (empty($_CONF['image_lib'])) {
                    $scaling = $LANG04[162];
                } else {
                    $scaling = $LANG04[161];
                }
                
                $photo_max_dimensions =	sprintf($LANG04[160],
                            $_CONF['max_photo_width'], $_CONF['max_photo_height'],
                            $_CONF['max_photo_size'], $scaling);
                
                //Form for new photo
                $content .= "<form class=\"uk-form\" method=\"post\" action=\"{$_CONF['site_admin_url']}/plugins/monitor/index.php\" enctype=\"multipart/form-data\">";
                $content .= '<p>' . $display_photo . '</p><p>' . $photo_max_dimensions . '</p>';
                $content .= '<p><input type="file" dir="ltr" id="photo" name="photo" size="30"' . XHTML .'></p>';
                $content .= "<input type=\"hidden\" name=\"action\" value=\"upload_user_photo\">";
                $content .= "<input type=\"hidden\" name=\"uid\" value=\"{$uid}\">";
                $content .= "<p><input type=\"submit\" value=\"Upload\"></p>";
                $content .= "</form>";
            }
        } else {
            
            //Form for user id
            $T->set_var(array(
                'title'        => 'Select user id',
            ));
        
            $content .= "<form class=\"uk-form\" method=\"post\" action=\"{$_CONF['site_admin_url']}/plugins/monitor/index.php\">";
            $content .= "User uid: <input type=\"text\" name=\"uid\" value=\"\">";
            $content .= "<input type=\"hidden\" name=\"action\" value=\"change_user_photo\">";
            $content .= "<input type=\"submit\" value=\"Go\">";
            $content .= "</form>";
            }
        break;
    
    case 'comments_list' :
        
        $content .= MONITOR_commentsList();
        break;
        
    case 'logs' :
        
        $T->set_var(array(
            'title'        => $LANG_MONITOR_1['view_clear_logs'],
        ));
        
        $content .= "<form class=\"uk-form\" method=\"post\" action=\"{$_CONF['site_admin_url']}/plugins/monitor/index.php?action=logs\">";
        $content .= "<p>{$LANG_MONITOR_1['file']}&nbsp;&nbsp;&nbsp;";
        $files = array();
        if ($dir = @opendir($_CONF['path_log'])) {
            while(($file = readdir($dir)) !== false) {
                if (is_file($_CONF['path_log'] . $file)) { array_push($files,$file); }
            }
            closedir($dir);
        }
        $content .= '<SELECT name="log">';
        if (empty($log)) { $log = $files[0]; } // default file to show
        for ($i = 0; $i < count($files); $i++) {
            $content .= '<option value="' . $files[$i] . '"';
            if ($log == $files[$i]) { $content .= ' SELECTED'; }
            $content .= '>' . $files[$i] . '</option>';
            next($files);
        }
        $content .= "</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;";
        $content .= "<input type=\"submit\" name=\"log_file\" value=\"{$LANG_MONITOR_1['view_logs']}\">";
        $content .= "&nbsp;&nbsp;&nbsp;&nbsp;";
        $content .= "<input type=\"submit\" name=\"log_file\" value=\"{$LANG_MONITOR_1['clear_logs']}\"></p>";
        $content .= "</form>";
        
        if ($log_file == $LANG_MONITOR_1['clear_logs']) {
            unlink($_CONF['path_log'] . $_POST['log']);
            $timestamp = strftime( "%c" );
            $fd = fopen( $_CONF['path_log'] . $_POST['log'], a );
            fputs( $fd, "$timestamp - Log File Cleared \n" );
            fclose($fd);
            $log_file = $LANG_MONITOR_1['view_logs'];
        }

        if ($log_file == $LANG_MONITOR_1['view_logs']) {
            $content .= "<hr><p><b>{$LANG_MONITOR_1['log_file']} " . $_POST['log'] . "</b></p><pre>";
            $content .= implode('', file($_CONF['path_log'] . $_POST['log']));
            $content .= "</pre>";
        }
        
        break;
    
    default:

        $T->set_var(array(
            'title'        => $LANG_MONITOR_1['main'],
        ));
        
        //number of IP banned last 24H
        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE 1=1",1);
        $nrows = DB_numRows( $result );
        $content .= "<h3>Banned IP during the last 24 hours $nrows</h3>";

        //Ban type profile, newuser, dokuwiki, captcha
        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='captcha'",1);
        $nrows = DB_numRows( $result );
        $content .= "<ul><li>Captcha $nrows IP Adress</li>";

        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='dokuwiki'",1);
        $nrows = DB_numRows( $result );
        $content .= "<li>Dokuwiki $nrows IP Adress</li>";

        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='newuser'",1);
        $nrows = DB_numRows( $result );
        $content .= "<li>New user $nrows IP Adress</li>";

        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='profile'",1);
        $nrows = DB_numRows( $result );
        $content .= "<li>Profile $nrows IP Adress</li></ul>";

        //Plugins updates
        $token = SEC_createToken();
        $pluginToUpdate ='';

        if ($action == 'continue_upgrade') { 
            $content .= MONITOR_continue_upgrade(COM_sanitizeFilename($_GET['plugin_update']),
                                         $_GET['piversion'], $_GET['codeversion']);
        }
                                         
        if ($action == 'update_plugin' && in_array($_GET['plugin'], $ready_plugins)) {
            $pluginToUpdate = $_GET['plugin'];
        }
                
        $msg = MONITOR_plugin_upload($pluginToUpdate);
        if ($msg != '') $content .= COM_showMessageText($MESSAGE[$msg]);
        if ($_MONITOR_CONF['repository'] != '') $content .= MONITOR_listplugins($token);
        
        break;
}

$T->set_var(array(
    'admin_body'    => $content
));

$T->parse('output', 'admin');
$display .= $T->finish($T->get_var('output'));


$display .= COM_siteFooter();

COM_output ($display);

?>
