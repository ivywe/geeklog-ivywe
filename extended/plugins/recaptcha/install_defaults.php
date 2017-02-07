<?php

// +---------------------------------------------------------------------------+
// | reCAPTCHA Plugin for Geeklog - The Ultimate Weblog                        |
// +---------------------------------------------------------------------------+
// | geeklog/plugins/recaptcha/install_defaults.php                            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2014-2017 mystral-kk - geeklog AT mystral-kk DOT net        |
// |                                                                           |
// | Based on the CAPTCHA Plugin by Ben                                        |
// |                                                - ben AT geeklog DOT fr    |
// | Based on the original CAPTCHA Plugin by Mark R. Evans                     |
// |                                                - mark AT glfusion DOT org |
// | Constructed with the Universal Plugin                                     |
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

if (stripos($_SERVER['PHP_SELF'], basename(__FILE__)) !== false) {
	die('This file cannot be used on its own!');
}

/**
* reCAPTCHA default settings
*
* Initial Installation Defaults used when loading the online configuration
* records.  These settings are only used during the initial installation
* and not referenced any more once the plugin is installed
*/
global $_RC_DEFAULT;

$_RC_DEFAULT = array(
	'public_key'          => '',
	'private_key'         => '',
	'logging'             => 0,
	'anonymous_only'      => 0,
	'remoteusers'         => 0,
	'enable_comment'      => 1,
	'enable_contact'      => 1,
	'enable_emailstory'   => 1,
	'enable_forum'        => 1,
	'enable_registration' => 1,
	'enable_mediagallery' => 1,
	'enable_rating'       => 1,
	'enable_story'        => 1,
	'enable_calendar'     => 1,
	'enable_links'        => 1,
);

/**
* Initializes reCAPTCHA plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist.  Initial values will be taken from $_RC_DEFAULT
* if available (e.g. from an old config.php), uses $_RC_DEFAULT
* otherwise.
*
* @return   boolean     true: success; false: an error occurred
*/
function plugin_initconfig_recaptcha() {
	global $_RECAPTCHA_CONF, $_RC_DEFAULT;

	global $_RC_DEFAULT;

	$c = config::get_instance();
	$me = 'recaptcha';
	$so = 10;

	if (!$c->group_exists($me)) {
		// Subgroup = 0
		$sg = 0;
		$c->add('sg_main', null, 'subgroup', $sg, 0, null, 0, true, $me, 0);
		$c->add('tab_main', null, 'tab', $sg, 0, null, 0, true, $me, 0);

		// Fieldset = 0
		$fs = 0;
		$c->add('fs_system', null, 'fieldset', $sg, $fs, null, 0, true, $me, 0);
		$c->add('public_key', $_RC_DEFAULT['public_key'], 'text', $sg, $fs, null, $so, true, $me, 0);
		$so += 10;
		$c->add('private_key', $_RC_DEFAULT['private_key'], 'text', $sg, $fs, null, $so, true, $me, 0);
		$so += 10;
		$c->add('logging', $_RC_DEFAULT['logging'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;

		// Subgroup = 0, Fieldset = 1
		$fs++;
		$c->add('fs_integration', null, 'fieldset', $sg, $fs, null, 0, true, $me, 0);
		$c->add('anonymous_only', $_RC_DEFAULT['anonymous_only'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		 $c->add('remoteusers', $_RC_DEFAULT['remoteusers'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_comment', $_RC_DEFAULT['enable_comment'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_contact', $_RC_DEFAULT['enable_contact'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_emailstory', $_RC_DEFAULT['enable_emailstory'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_forum', $_RC_DEFAULT['enable_forum'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_registration', $_RC_DEFAULT['enable_registration'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_mediagallery', $_RC_DEFAULT['enable_mediagallery'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_rating', $_RC_DEFAULT['enable_rating'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_story', $_RC_DEFAULT['enable_story'], 'select', $sg, $fs, 0, $so, true, $me, 0); 
		$so += 10;
		$c->add('enable_calendar', $_RC_DEFAULT['enable_calendar'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
		$c->add('enable_links', $_RC_DEFAULT['enable_links'], 'select', $sg, $fs, 0, $so, true, $me, 0);
		$so += 10;
	}

	return true;
}
