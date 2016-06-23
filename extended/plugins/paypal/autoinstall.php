<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Paypal Plugin 1.4.3                                                       |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
// |                                                                           |
// | This file provides helper functions for the automatic plugin install.     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - cordiste AT free DOT fr                                  |
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
* @package Paypal
*/

/**
* Plugin autoinstall function
*
* @param    string  $pi_name    Plugin name
* @return   array               Plugin information
*
*/
function plugin_autoinstall_paypal($pi_name)
{	
    $pi_name         = 'paypal';
    $pi_display_name = 'Paypal';
    $pi_admin        = $pi_display_name . ' Admin';
    $pi_user         = $pi_display_name . ' User';
    $pi_viewer       = $pi_display_name . ' Viewer';
	
    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '1.5.0',
        'pi_gl_version'   => '1.8.0',
        'pi_homepage'     => 'http://www.geeklog.fr'
    );

    $groups = array(
        $pi_admin => 'Users in this group can administer the '
                     . $pi_display_name . ' plugin',
		$pi_user => 'Users in this group can access to the '
                     . $pi_display_name . ' plugin',
		$pi_viewer => 'Users in this group can view the '
                     . $pi_display_name . ' plugin'
    );

    $features = array(
        $pi_name . '.admin'    => 'Full access to ' . $pi_display_name
                                  . ' plugin',
        $pi_name . '.user'    => 'People who can shop with the ' . $pi_display_name
                                  . ' plugin',
        $pi_name . '.viewer'    => 'People who can browse (but not buy) with the ' . $pi_display_name
                                  . ' plugin',
    );

    $mappings = array(
        $pi_name . '.admin'     => array($pi_admin),
        $pi_name . '.user'      => array($pi_admin, $pi_user),		
        $pi_name . '.viewer'    => array($pi_admin, $pi_user, $pi_viewer),

	);
	
    $tables = array(
        'paypal_ipnlog',
		'paypal_downloads',
		'paypal_products',
		'paypal_purchases',
		'paypal_images',
		'paypal_categories',
		'paypal_subscriptions',
		'paypal_users',
		'paypal_attributes',
		'paypal_attribute_type',
		'paypal_product_attribute',
		'paypal_stock',
		'paypal_delivery',
		'paypal_stock_movements',
		'paypal_providers',
		'paypal_shipper_service',
		'paypal_shipping_to',
		'paypal_shipping_cost'
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
        'tables'    => $tables
    );

    return $inst_parms;
}

/**
* Check if the plugin is compatible with this Geeklog version
*
* @param    string  $pi_name    Plugin name
* @return   boolean             true: plugin compatible; false: not compatible
*
*/
function plugin_compatible_with_this_version_paypal($pi_name)
{
    global $_CONF, $_DB_dbms;

    // check if we support the DBMS the site is running on
    $dbFile = $_CONF['path'] . 'plugins/' . $pi_name . '/sql/'
            . $_DB_dbms . '_install.php';
    if (! file_exists($dbFile)) {
        return false;
    }

    // add checks here

    return true;
}

function plugin_postinstall_paypal($pi_name)
{
    global $_TABLES, $_CONF, $_USER;
	
    /* New Groups */
    $groups['Paypal User']   = 'Users in this group can purchase products';
    $groups['Paypal Viewer'] = 'Users in this group can view products';	

    // Groups assignment mapping (note: group -> group, not user -> group) 
    $grp_assign['Paypal User']   = array(13); // 13 is the Logged-in Users group
    $grp_assign['Paypal Viewer'] = array(2);  // 2 is the All Users group
	
     // Assign created paypal groups to other (logical) groups
    foreach ($grp_assign as $group => $grparray) {
		
        foreach ($grparray as $togroup) {
            COM_errorLog("Assigning group $togroup to $group",1);
			$result = DB_query("SELECT grp_id, grp_name FROM {$_TABLES['groups']} WHERE grp_name ='$group'");
            $A = DB_fetchArray($result);
            DB_query("INSERT INTO {$_TABLES['group_assignments']} "
                   . "VALUES ({$A[0]}, NULL, $togroup)");
            if (DB_error()) {
                COM_errorLog("Failed assigning group $togroup to $group",1);
                PLG_uninstall('paypal');
                return false;
            }
            COM_errorLog('...success',1);
        }
    }
	
    /* This code is for statistics ONLY */
    $message =  'Completed paypal plugin install: ' .date('m d Y',time()) . "   AT " . date('H:i', time()) . "\n";
    $message .= 'Site: ' . $_CONF['site_url'] . ' and Sitename: ' . $_CONF['site_name'] . "\n";
    $pi_version = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = 'paypal'");
    COM_mail("ben@geeklog.fr","$pi_name Version:$pi_version Install successfull",$message);
	
	// Create paypal_downloads.log file
	$paypalDownload = fopen($_CONF['path_log'] . 'paypal_downloads.log', 'w') or die("can't create file paypal_downloads.log file");
    fclose($paypalDownload);

	return true;
}

function plugin_load_configuration_paypal($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_paypal();
}

?>
