<?php
// +--------------------------------------------------------------------------+
// | Maps Plugin 1.2                                                          |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (!defined ('VERSION')) {
    die ('This file can not be used on its own.');
}

/* Maps Path Configuration. If you want to move the directory where
 * the Maps programs are stored and accessed, change the
 * to the new directory name in the config area.
 */
$_MAPS_CONF['path_html']  = $_CONF['path_html'] . $_MAPS_CONF['maps_folder'] . '/';
$_MAPS_CONF['site_url']   = $_CONF['site_url'] . '/' . $_MAPS_CONF['maps_folder'];
$_MAPS_CONF['path_overlay_images']  = $_CONF['path_images'] . 'maps/overlays/';
$_MAPS_CONF['images_overlay_url']  = $_CONF['site_url'] . '/'. substr($_CONF['path_images'], strlen($_CONF['path_html']), -1) . '/maps/overlays/';
$_MAPS_CONF['path_icons_images']  = $_CONF['path_images'] . 'maps/icons/';
$_MAPS_CONF['images_icons_url']  = $_CONF['site_url'] . '/'. substr($_CONF['path_images'], strlen($_CONF['path_html']), -1) . '/maps/icons/';
$_MAPS_CONF['max_image_width'] = 2000;
$_MAPS_CONF['max_image_height'] = 2000;
$_MAPS_CONF['max_image_size'] = 4194304; // size in bytes, 1048576 = 1MB



/**
 * Maps plugin table(s)
 */
$_TABLES['maps_geo'] = $_DB_table_prefix . 'maps_geo';
$_TABLES['maps_maps'] = $_DB_table_prefix . 'maps_maps';
$_TABLES['maps_markers'] = $_DB_table_prefix . 'maps_markers';
$_TABLES['maps_submission'] = $_DB_table_prefix . 'maps_submission';
$_TABLES['maps_markers_cat'] = $_DB_table_prefix . 'maps_markers_cat';
$_TABLES['maps_markers_fields'] = $_DB_table_prefix . 'maps_markers_fields';
$_TABLES['maps_markers_values'] = $_DB_table_prefix . 'maps_markers_values';
$_TABLES['maps_overlays'] = $_DB_table_prefix . 'maps_overlays';
$_TABLES['maps_map_overlay'] = $_DB_table_prefix . 'maps_map_overlay';
$_TABLES['maps_map_icons'] = $_DB_table_prefix . 'maps_map_icons';
$_TABLES['maps_overlays_groups'] = $_DB_table_prefix . 'maps_overlays_groups';
?>