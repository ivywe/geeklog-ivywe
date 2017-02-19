<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Language Selection Block Plugin 1.0.0                                     |
// +---------------------------------------------------------------------------+
// | italian_utf-8.php                                                         |
// |                                                                           |
// | Italian language file                                                     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2011 by the following authors:                              |
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
    'plugin_name' => 'Blocco di Selezione Lingue',
    'conf_link'   => 'Configurazione',
	'title'       => 'Selezione Lingue',
	'submit'      => 'Vai'
);

// Localization of the Admin Configuration UI
$LANG_configsections['langsel'] = array(
    'label' => 'Blocco di Selezione Lingue',
    'title' => 'Configurazione del Blocco di Selezione Lingue'
);

$LANG_confignames['langsel'] = array(
    'block_pos' => 'Dove mostrare il blocco',
    'block_order' => 'Ordine del blocco',
);

$LANG_configsubgroups['langsel'] = array(
    'sg_main' => 'Impostazioni principali'
);

$LANG_tab['langsel'] = array(
    'tab_main' => 'Impostazioni principali del Blocco di Selezione Lingue'
);

$LANG_fs['langsel'] = array(
    'fs_main' => 'Impostazioni principali del Blocco di Selezione Lingue'
);

$LANG_configselects['langsel'] = array(
    1 => array('Con i blocchi sinistri' => 1, 'Con i blocchi destri' => 0)
);
?>
