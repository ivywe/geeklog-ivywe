<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin                                           |
// +---------------------------------------------------------------------------+
// | spanish_utf-8.php                                                         |
// |                                                                           |
// | Spanish language file                                                     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011-2018 by the following authors:                         |
// |                                                                           |
// | Authors: Rouslan Placella - rouslan AT placella DOT com                   |
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
* @package LanguageSelectionBlock
*/

$LANG_LANGSEL_1 = array(
    'plugin_name'          => 'Bloque Selección de Idioma',
    'conf_link'            => 'Configuración',
    'title'                => 'Selecciona idioma',
    'submit'               => 'Ir',
    'autotag_desc_langsel' => 'Autotag Langsel proporciona un selector de idioma.',
);

// Localization of the Admin Configuration UI
$LANG_configsections['langsel'] = array(
    'label' => 'Bloque Selección de Idioma',
    'title' => 'Configuración del bloque de Selección de Idioma'
);

$LANG_confignames['langsel'] = array(
    'block_pos' => 'Lugar donde se mostrará el bloque',
    'block_order' => 'Orden del bloque',
);

$LANG_configsubgroups['langsel'] = array(
    'sg_main' => 'Ajustes principales'
);

$LANG_tab['langsel'] = array(
    'tab_main' => 'Ajustes principales del bloque de selección de idioma'
);

$LANG_fs['langsel'] = array(
    'fs_main' => 'Ajustes principales del bloque de selección de idioma'
);

$LANG_configselects['langsel'] = array(
    1 => array('No como un bloque' => 2, 'Izquierda' => 1, 'Derecha' => 0)
);
