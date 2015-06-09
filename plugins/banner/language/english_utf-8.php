<?php

###############################################################################
# english_utf-8.php
#
# This is the english language file for the Geeklog Banner Plugin
#
# Copyright (C) 2009-2010 Hiroron - hiroron AT hiroron DOT com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

global $LANG32, $LANG_ADMIN, $LANG_ACCESS;

/**
* the banner plugin's lang array
*
* @global array $LANG_BANNER
*/
$LANG_BANNER = array(
    10 => 'Submissions',
    14 => 'Banner',
    84 => 'Banner',
    88 => 'No recent new banner',
    114 => 'Banner',
    116 => 'Add A Banner',
    117 => 'Report Broken Banner',
    118 => 'Broken Banner Report',
    119 => 'The following banner has been reported to be broken: ',
    120 => 'To edit the banner, click here: ',
    121 => 'The broken Banner was reported by: ',
    122 => 'Thank you for reporting this broken banner. The administrator will correct the problem as soon as possible',
    123 => 'Thank you',
    124 => 'Go',
    125 => 'Categories',
    126 => 'You are here:',
    'root' => 'Root' // title used for top level category
);

###############################################################################
# for stats
/**
* the banner plugin's lang stats array
*
* @global array $LANG_BANNER_STATS
*/
$LANG_BANNER_STATS = array(
    'banner' => 'Banner (Clicks) in the System',
    'stats_headline' => 'Top Ten Banner',
    'stats_page_title' => 'Banner',
    'stats_hits' => 'Hits',
    'stats_no_hits' => 'It appears that there are no banner on this site or no one has ever clicked on one.',
);

###############################################################################
# for the search
/**
* the banner plugin's lang search array
*
* @global array $LANG_BANNER_SEARCH
*/
$LANG_BANNER_SEARCH = array(
 'results' => 'Banner Results',
 'title' => 'Title',
 'date' => 'Date Added',
 'author' => 'Submitted by',
 'hits' => 'Clicks'
);

###############################################################################
# for the submission form
/**
* the banner plugin's lang submit form array
*
* @global array $LANG_BANNER_SUBMIT
*/
$LANG_BANNER_SUBMIT = array(
    1 => 'Submit a Banner',
    2 => 'Banner',
    3 => 'Category',
    4 => 'Other',
    5 => 'If other, please specify',
    6 => 'Error: Missing Category',
    7 => 'When selecting "Other" please also provide a category name',
    8 => 'Title',
    9 => 'URL',
    10 => 'Category',
    11 => 'Banner Submissions'
);

###############################################################################
# Messages for COM_showMessage the submission form

$PLG_banner_MESSAGE1 = "Thank-you for submitting a banner to {$_CONF['site_name']}.  It has been submitted to our staff for approval.  If approved, your banner will be seen in the <a href={$_CONF['site_url']}/banner/index.php>banner</a> section.";
$PLG_banner_MESSAGE2 = 'Your banner has been successfully saved.';
$PLG_banner_MESSAGE3 = 'The banner has been successfully deleted.';
$PLG_banner_MESSAGE4 = "Thank-you for submitting a banner to {$_CONF['site_name']}.  You can see it now in the <a href={$_CONF['site_url']}/banner/index.php>banner</a> section.";
$PLG_banner_MESSAGE5 = "You do not have sufficient access rights to view this category.";
$PLG_banner_MESSAGE6 = 'You do not have sufficient rights to edit this category.';
$PLG_banner_MESSAGE7 = 'Please enter a Category Name and Description.';

$PLG_banner_MESSAGE10 = 'Your category has been successfully saved.';
$PLG_banner_MESSAGE11 = 'You are not allowed to set the id of a category to "site" or "user" - these are reserved for internal use.';
$PLG_banner_MESSAGE12 = 'You are trying to make a parent category the child of it\'s own subcategory. This would create an orphan category, so please first move the child category or categories up to a higher level.';
$PLG_banner_MESSAGE13 = 'The category has been successfully deleted.';
$PLG_banner_MESSAGE14 = 'Category contains banner and/or categories. Please remove these first.';
$PLG_banner_MESSAGE15 = 'You do not have sufficient rights to delete this category.';
$PLG_banner_MESSAGE16 = 'No such category exists.';
$PLG_banner_MESSAGE17 = 'This category id is already in use.';

// Messages for the plugin upgrade
$PLG_banner_MESSAGE3001 = 'Plugin upgrade not supported.';
$PLG_banner_MESSAGE3002 = $LANG32[9];

###############################################################################
# admin/banner.php
/**
* the banner plugin's lang admin array
*
* @global array $LANG_BANNER_ADMIN
*/
$LANG_BANNER_ADMIN = array(
    1 => 'Banner Editor',
    2 => 'Banner ID',
    3 => 'Banner Title',
    4 => 'Banner URL',
    5 => 'Category',
    6 => '(include http://)',
    7 => 'Other',
    8 => 'Banner Hits',
    9 => 'Banner Description',
    10 => 'You need to provide a banner Title and Description.',
    11 => 'Banner Manager',
    12 => 'To modify or delete a banner, click on that banner\'s edit icon below.  To create a new banner or a new category, click on "New banner" or "New category" above. To edit multiple categories, click on "Edit categories" above.',
    14 => 'Banner Category',
    16 => 'Access Denied',
    17 => "You are trying to access a banner that you don't have rights to.  This attempt has been logged. Please <a href=\"{$_CONF['site_admin_url']}/plugins/banner/index.php\">go back to the banner administration screen</a>.",
    20 => 'If other, specify',
    21 => 'save',
    22 => 'cancel',
    23 => 'delete',
    24 => 'Banner not found',
    25 => 'The banner you selected for editing could not be found.',
    26 => 'Validate Banner',
    27 => 'HTML Status',
    28 => 'Edit category',
    29 => 'Enter or edit the details below.',
    30 => 'Category',
    31 => 'Description',
    32 => 'Category ID',
    33 => 'Topic',
    34 => 'Parent',
    35 => 'All',
    40 => 'Edit this category',
    41 => 'Create child category',
    42 => 'Delete this category',
    43 => 'Site categories',
    44 => 'Add&nbsp;child',
    46 => 'User %s tried to delete a category to which they do not have access rights',
    50 => 'Banner categories',
    51 => 'New banner',
    52 => 'New category',
    53 => 'Banner banner',
    54 => 'Category Manager',
    55 => 'Edit categories below. Note that you cannot delete a category that contains other categories or banner - you should delete these first, or move them to another category.',
    56 => 'Category Editor',
    57 => 'Not validated yet',
    58 => 'Validate now',
    59 => '<p>To validate all banner displayed, please click on the "Validate now" banner below. Please note that this might take some time depending on the amount of banner displayed.</p>',
    60 => 'User %s tried illegally to edit category %s.'
);

$LANG_BANNER_STATUS = array(
    100 => "Continue",
    101 => "Switching Protocols",
    200 => "OK",
    201 => "Created",
    202 => "Accepted",
    203 => "Non-Authoritative Information",
    204 => "No Content",
    205 => "Reset Content",
    206 => "Partial Content",
    300 => "Multiple Choices",
    301 => "Moved Permanently",
    302 => "Found",
    303 => "See Other",
    304 => "Not Modified",
    305 => "Use Proxy",
    307 => "Temporary Redirect",
    400 => "Bad Request",
    401 => "Unauthorized",
    402 => "Payment Required",
    403 => "Forbidden",
    404 => "Not Found",
    405 => "Method Not Allowed",
    406 => "Not Acceptable",
    407 => "Proxy Authentication Required",
    408 => "Request Timeout",
    409 => "Conflict",
    410 => "Gone",
    411 => "Length Required",
    412 => "Precondition Failed",
    413 => "Request Entity Too Large",
    414 => "Request-URI Too Long",
    415 => "Unsupported Media Type",
    416 => "Requested Range Not Satisfiable",
    417 => "Expectation Failed",
    500 => "Internal Server Error",
    501 => "Not Implemented",
    502 => "Bad Gateway",
    503 => "Service Unavailable",
    504 => "Gateway Timeout",
    505 => "HTTP Version Not Supported",
    999 => "Connection Timed out"
);


// Localization of the Admin Configuration UI
$LANG_configsections['banner'] = array(
    'label' => 'Banner',
    'title' => 'Banner Configuration'
);

$LANG_confignames['banner'] = array(
    'bannerloginrequired' => 'Banner Login Required?',
    'bannersubmission' => 'Enable Submission Queue?',
    'newbannerinterval' => 'New Banner Interval',
    'bannertemplatevariables' => 'The banner is displayed with the template',
    'hidenewbanner' => 'Hide New Banner?',
    'hidebannermenu' => 'Hide Banner Menu Entry?',
    'bannercols' => 'Categories per Column',
    'bannerperpage' => 'Banner per Page',
    'show_top10' => 'Show Top 10 Banner?',
    'notification' => 'Notification Email?',
    'delete_banner' => 'Delete Banner with Owner?',
    'aftersave' => 'After Saving Banner',
    'show_category_descriptions' => 'Show Category Description?',
    'root' => 'ID of Root Category',
    'admin_editlink' => 'Banner Admin to display banner to the edit icon',
    'admin_disptitle' => $LANG_ADMIN['title'] . ' Item Disp List',
    'admin_dispdescription' => $LANG_BANNER_ADMIN[9] . ' Item Disp List',
    'admin_dispaccess' => $LANG_ACCESS['access'] . ' Item Disp List',
    'admin_dispcategory' => $LANG_BANNER_ADMIN[14] . ' Item Disp List',
    'admin_disppublishstart' => $LANG_BANNER_ADMIN[61] . ' Item Disp List',
    'admin_disppublishend' => $LANG_BANNER_ADMIN[62] . ' Item Disp List',
    'admin_disphits' => $LANG_BANNER_ADMIN[8] . ' Item Disp List',
    'default_permissions' => 'Banner Default Permissions'
);

$LANG_configsubgroups['banner'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['banner'] = array(
    'fs_public' => 'Public Banner Banner Settings',
    'fs_admin' => 'Banner Admin Settings',
    'fs_adminlist' => 'Banner Admin List',
    'fs_permissions' => 'Default Permissions'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['banner'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    9 => array('Forward to Bannered Site' => 'item', 'Display Admin Banner' => 'list', 'Display Public Banner' => 'plugin', 'Display Home' => 'home', 'Display Admin' => 'admin'),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3)
);

?>
