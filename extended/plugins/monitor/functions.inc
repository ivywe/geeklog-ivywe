<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.3                                                        |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API methods and 2) implements all the common code needed by this plugin.  |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014-2016 by the following authors:                         |
// |                                                                           |
// | Authors: Ben - ben AT geeklog DOT fr                                      |
// +---------------------------------------------------------------------------+
// | Created with the Geeklog Plugin Toolkit.                                  |
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
* @package Monitor
*/

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/monitor/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/**
 * Load our config
 */
require_once $_CONF['path_system'].'classes/config.class.php';
$c = config::get_instance();
$_MONITOR_CONF = $c->get_config('monitor');

$_TABLES['monitor_ban'] = $_DB_table_prefix . 'monitor_ban';

//Check for bad users
MONITOR_banCheck();

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+


/**
* Return plugin entry for "Command and Control" (moderation.php)
*
* @return  array  Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    $retval = array();
    if (SEC_hasRights('monitor.admin')) {
        $retval = array($LANG_MONITOR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/monitor/index.php',
            plugin_geticon_monitor());
    }

    return $retval;
}

/**
* Return plugin entry for the Admins Only block
*
* @return array Array containing (plugin name, plugin admin url, # of plugin items or '')
*/
function plugin_getadminoption_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    if (SEC_hasRights('monitor.admin')) {
        return array($LANG_MONITOR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/monitor/index.php', 0);
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_monitor()
{
    global $_CONF;

    // popular alternative location:
    // return $_CONF['site_url'] . '/monitor/images/monitor.png';

    return $_CONF['site_admin_url'] . '/plugins/monitor/images/monitor.png';
}

/**
 * Returns the current plugin code version
 *
 * @return string    plugin version
 */
function plugin_chkVersion_monitor()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/autoinstall.php';

    $inst_parms = plugin_autoinstall_monitor('monitor');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the plugin
*
* @return   int     Number of message to display or true for success
*
*/
function plugin_upgrade_monitor()
{
    global $_CONF, $_TABLES, $_MONITOR_CONF;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'monitor'");
    $code_version = plugin_chkVersion_monitor();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }
    
    $inst_parms = plugin_autoinstall_monitor('monitor');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    require_once $_CONF['path'] . 'plugins/monitor/autoinstall.php';
    require_once $_CONF['path'] . 'plugins/monitor/install_defaults.php';

    if (! plugin_compatible_with_this_version_monitor('monitor')) {
        return 3002;
    }

    // other update code goes here
    
    switch($installed_version) {
        
        case '1.0':
            
        case '1.1.0':
            $sql = "CREATE TABLE " . $_TABLES['monitor_ban'] . " ("
                    . " bantype varchar(40) NOT NULL default '',"
                    . " data varchar(255) NOT NULL default '',"
                    . " created datetime NOT NULL,"
                    . " access int(3) NOT NULL default '0'"
                    . ") ENGINE=MyISAM
                ";
            DB_query($sql);
            
        case '1.1.1':
        case '1.2.0':
            $c = config::get_instance();
            if ($_MONITOR_CONF['emails'] == '') {

                //This is main subgroup #0
                $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'monitor');
                
                //Main settings   
                $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'monitor');
                $c->add('emails', $_CONF['site_mail'],
                        'text', 0, 0, 0, 10, true, 'monitor');

            }
        case '1.2.1': 
            $c = config::get_instance();
            $c->add('repository', $_monitor_DEFAULT['repository'],
                        'text', 0, 0, 0, 20, true, 'monitor');
        case '1.3.0':
        case '1.3.1':
        

        default : 
            // update plugin version number
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'monitor'");
            break;
    }



    return true;
}

/**
* Automatic uninstall function for plugins
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
* @return   array   Plugin information
*
*/
function plugin_autouninstall_monitor()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('monitor_ban'),
        /* give the full name of the group, as in the db */
        'groups' => array('Monitor Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('monitor.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars' => array()
    );

    return $out;
}

/**
* Mails the contents of log files
*
* @param    string  $log        The name of the log file
*/
function MONITOR_logEmail($logs)
{
    global $_CONF, $_MONITOR_CONF;
    
    foreach ($logs as $log) {
        if (file_exists($_CONF['path_log'] . $log)) {			
            //Read log file
            $message .= "<p><b>Log File: " . $log . "</b></p><pre>";
            $message .= implode('', file($_CONF['path_log'] . $log));
            $message .= "</pre><hr>";
            
            //delelete log file
            unlink($_CONF['path_log'] . $log);
            $timestamp = strftime( "%c" );
            $fd = fopen( $_CONF['path_log'] . $log, a );
            fputs( $fd, "$timestamp - Log File Cleared \n" );
            fclose($fd);

            
        }
    }
    
    //Find error_log files created by server
    $message .= MONITOR_findErrorLog ();
    
    //List of emails to send logs to
    $to = explode(',', $_MONITOR_CONF['emails']);
    $count = count ($to);
    for ($i=0; $i < $count; $i++) { 
        $contact = trim($to[$i]);
        $subject = $_CONF['site_name'] . ' | Log files';
        COM_errorLog("MONITOR - Log files >> " . $contact );
        COM_mail($contact, $subject, $message, '',true);
        COM_errorLog("MONITOR - Log files sent" );
    }
}

function MONITOR_findErrorLog ($folder='') {

    global $_CONF;
  
    set_time_limit(0);
    
    if ($folder == '') $folder = $_CONF['path_html'];
  
    foreach( scandir( $folder ) as $file ) {
        $filepath = $folder . '/' . $file;
        if( preg_match( '/^\./', $file ) ) continue; // not . and ..
        if( is_dir( $filepath ) ) {
            $retval .= MONITOR_findErrorLog ( $filepath );
            
        } else {
            if ($file == 'error_log') {
            //Read log file
            $message .= "<p><b>Log File: " . $filepath . "</b></p><pre>";
            $message .= implode('', file($filepath));
            $message .= "</pre><hr>";
            ob_flush();
            flush();
            //delelete log file
            unlink($filepath);
            }
        }
    }
    return $message;
}

function MONITOR_recursiveFiles( $folder= '' )
{
  global $_CONF, $LANG_MONITOR_1;
  
  set_time_limit(0);
  
  if ($folder == '') $folder = $_CONF['path_images'];
  $maxwidth = 1600;
  
  $retval .= '<ul>'; 
  
  foreach( scandir( $folder ) as $file )
  {
    $filepath = $folder . '/' . $file;
    if( preg_match( '/^\./', $file ) ) continue; // not . and ..
    if( is_dir( $filepath ) ) {
        $retval .= '<li>' . $file . '</li>';
        $retval .= MONITOR_recursiveFiles( $filepath );
        
    } else {
        list($width, $height, $type, $attr) = getimagesize($filepath);
        if ($width > $maxwidth || $hreight > $maxwidth) {
            $_SESSION['imgtoresize'] ++;
            $size = intval(filesize($filepath)/1024);
            if ($_REQUEST['resize'] == $LANG_MONITOR_1['resize_images']) {
                MONITOR_resizeImage($filepath);
            } else { 
                $retval .= '<li><span style="color:red;font-weight:bold;">' . $file . ' | ' . $width . 'px/' . $height .'px | ' . $size . ' Ko</span></li>';
            }
            
        }
    }
  }
  
  $retval .= '</ul>'; 
  
  return $retval;
}

function MONITOR_resizeImage($img) {
    
    global $_CONF;
    
    require_once $_CONF['path'] . 'plugins/monitor/class/simpleresize.class.php';
    $maxwidth = 1600;
    $maxheight = 1600;
    
    $image = new SimpleImage(); 
    $image->load($img);
    
    if ($image->getWidth() > $maxwidth) {
        $image->resizeToWidth($maxwidth);
        $image->save($img);
        COM_errorLog ("Monitor - Resizing image $img");
    } else	if ($image->getHeight() > $maxheight) {
        $image->resizeToHeight($maxheight);
        $image->save($img);
        COM_errorLog ("Monitor - Resizing image $img");
    }
}  	
    
function plugin_runScheduledTask_monitor ()
{	
    global $_TABLES, $_PLUGINS;
    
    //Check tables
    foreach ($_TABLES as $table) {
        $resp = DB_query("CHECK TABLE {$table}  FAST QUICK;");
        if(is_array($resp) && $resp['Msg_text'] != 'OK' && $resp['Msg_text'] != 'Table is already up to date') {
            COM_errorLog("MONITOR - {$table}: {$resp['Msg_text']}");
        }
    }
    
    //TODO add custom name
    MONITOR_logEmail(array('error.log','captcha.log','access.log','spamx.log', 'purepro_downloads.log', 'paypal_downloads.log', '404.log'));
    
    //Add number of IP banned last 24H
    $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE 1=1",1);
    $nrows = DB_numRows( $result );
    COM_errorLog("MONITOR - Banned $nrows IP Adress during the last 24 hours");
    
    //Ban type profile, newuser, dokuwiki, captcha
        if (in_array('captcha', $_PLUGINS)) {
        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='captcha'",1);
        $nrows = DB_numRows( $result );
        COM_errorLog("MONITOR - Captcha $nrows IP Adress");
    }
    if (in_array('dokuwiki', $_PLUGINS)) {
        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='dokuwiki'",1);
        $nrows = DB_numRows( $result );
        COM_errorLog("MONITOR - Dokuwiki $nrows IP Adress");
    }
    
    $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='newuser'",1);
    $nrows = DB_numRows( $result );
    COM_errorLog("MONITOR - New user $nrows IP Adress");
    
    $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='profile'",1);
    $nrows = DB_numRows( $result );
    COM_errorLog("MONITOR - Profile $nrows IP Adress");
}


/**
 * Filters submitted variables
 *
 * Uses geeklog functions to filter submitted variables for illegal content.  Any editing is
 * done in place and no value is returned
 *
 * @param array $vars associative array of variable name => type (type in (number,alpha,text,html))
 * @param array &$arr reference to associative array of variable name => value
 */
function MONITOR_filterVars($vars, &$arr) 
{
    // cycle through each variable applying the appropriate filter
    foreach ($vars as $var => $type) {
        switch ($type) {
            case 'number':
                $arr[$var] = COM_applyFilter($arr[$var], true);
                break;

            case 'html':
                $arr[$var] = COM_checkHTML($arr[$var], 'classifieds.admin');
                break;

            case 'text':
                $arr[$var] = htmlspecialchars(strip_tags(COM_stripslashes($arr[$var])));
                break;

            case 'alpha':
            default:
                $arr[$var] = COM_applyFilter($arr[$var], false);
                break;
        }
    }
}

function MONITOR_getUserIP(){
    //check ip from share internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    //to check ip is pass from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function MONITOR_banCheck()
{
    global $_TABLES, $_USER, $_PLUGINS, $_CONF;
    
    //Do not track logged-in users
    if ($_USER['uid'] >= 2 || !DB_checkTableExists('monitor_ban')) return;
    
    $remoteAdress = MONITOR_getUserIP();
    $scriptName = $_SERVER['SCRIPT_NAME'];
    
    //Clean old datas
    DB_query("DELETE FROM {$_TABLES['monitor_ban']} WHERE ADDTIME(created, \"23:59:59\") <= NOW()",1);
    
    //check if IP is banned
    $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE data='{$remoteAdress}'AND (bantype='banned' OR bantype='captcha' OR bantype='dokuwiki')",1);
    $A = DB_fetchArray($result);
    if ( $A['data'] !='') {
        //COM_errorLog('MONITOR - Access Banned for IP ' . $remoteAdress . ' (Ban type "' . $A['bantype'] . '")');
        MONITOR_checkHtaccess ($remoteAdress);
        die();
    }
    
    //check if IP is in captcha.log file
    if (in_array('captcha', $_PLUGINS)) {
        $logfile = $_CONF['path_log'] . 'captcha.log';
        if (file_exists($logfile)) {
            $handle = fopen($logfile, 'r');
            $valid = false; // init as false
            while (($buffer = fgets($handle)) !== false) {
                if (strpos($buffer, $remoteAdress) !== false) {
                    $valid = TRUE;
                    break; // Once you find the string, you should break out the loop.
                }      
            }
            fclose($handle);
            if( $valid) {
                $sql = "bantype='captcha', "
                    . "data='{$remoteAdress}', "
                    . "created = NOW(), "
                    . "access = access + 1
                    ";
                $sql = "INSERT INTO {$_TABLES['monitor_ban']} SET $sql ";
                DB_query($sql,1);
                //COM_errorLog('MONITOR - Access Banned for IP ' . $remoteAdress . ' because IP is in captcha.log file');
                sleep(60);
                
                die();
            }
        }
    }
    
    //check if client try to abuse dokuwiki
    if (in_array('dokuwiki', $_PLUGINS)) {
        if ( $_GET['do'] == 'register' ) {
            $bantype = 'dokuwiki';
            $sql = "bantype='{$bantype}', "
                . "data='{$remoteAdress}', "
                . "created = NOW(), "
                . "access = access + 1
                ";
            $sql = "INSERT INTO {$_TABLES['monitor_ban']} SET $sql ";
            DB_query($sql,1);
            //COM_errorLog('MONITOR - Access Banned for IP ' . $remoteAdress . ' trying to register with dokuwiki plugin');
            MONITOR_checkHtaccess ($remoteAdress);
            die();
        }
    }
    
    //check if user try to use email or create an account
    $bantype = '';
    if ( preg_match("/profiles.php/", $scriptName) && $_POST['what'] == 'contact' ) $bantype = 'profile';
    if ( preg_match("/users.php/", $scriptName)  && $_POST['mode'] == 'create' ) $bantype = 'newuser';

    if ($bantype != '') {
        //check if datas for this user are in db
        $result = DB_Query("SELECT * FROM {$_TABLES['monitor_ban']} WHERE bantype='{$bantype}' AND data='{$remoteAdress}'",1);
        $A = DB_fetchArray($result);
        
        //if not create datas for this user
        if ( $A['data'] != $remoteAdress ) { 
            $sql = "bantype='{$bantype}', "
                . "data='{$remoteAdress}', "
                . "created = NOW(), "
                . "access = access + 1
                ";
        $sql = "INSERT INTO {$_TABLES['monitor_ban']} SET $sql ";
        
        } else {
            //update datas for this user
            $sql = "access = access + 1
                WHERE bantype='{$bantype}' AND data='{$remoteAdress}'
                ";
            $sql = "UPDATE {$_TABLES['monitor_ban']} SET $sql ";
        }
        DB_query($sql,1);
        
        //if 3rd attempt ban this user
        if ( $A['access'] > 1 ) {
            $access = $A['access'] + 1;
            //COM_errorLog('MONITOR - Access Banned for IP ' . $remoteAdress . ' because total access for ' . $bantype . " is {$access}");
            $sql = "bantype='banned', "
                . "data='{$remoteAdress}', "
                . "created = NOW(), "
                . "access = access + 1
                ";
            $sql = "INSERT INTO {$_TABLES['monitor_ban']} SET $sql ";
            MONITOR_checkHtaccess ($remoteAdress);
            die();			
        }
    } else {
        return; 
    }
    return;
}

function MONITOR_checkHtaccess ($remoteAdress) {
    
    global $_CONF;
    
    if ($remoteAdress != '') {	
        
        if( !filter_var($remoteAdress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            //Invalid IP
        } else {
            //Uncomment to deny adress in htaccess file
            /*
            $htaccess = $_CONF['path_html'] . '.htaccess';
            $contents = file_get_contents($htaccess, TRUE) 
                OR COM_errorLog('MONITOR - Unable to open .htaccess');
                
            $exists = !stripos($contents,  'Deny from ' . $remoteAdress) 
                OR exit();
                  
            $ban = "\n Deny from {$remoteAdress}";
            
            file_put_contents($htaccess, $ban, FILE_APPEND) 
                OR exit();
            */
        }
    }
    
    sleep (60);
    
    header('HTTP/1.1 403 Forbidden', TRUE);
    die ('<html><head><title>Error 403 - Banned</title></head><body>
    <center><h1>Error 403 - Forbidden</h1>Hello user, you have been 
    banned from accessing our site. If you feel this ban was a mistake, 
    please contact the website administrator to have it removed.<br />
    <em>IP Address: '.$remoteAdress.'</em></center></body></html>');
}
?>