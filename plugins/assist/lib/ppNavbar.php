<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | lib-portalparts.php                                                       |
// | lib-portalparts.php より、ppNavbar抜粋
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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
//
// @@@@@20081020 lib-portalparts.php より、ppNavbar抜粋
/* PortalPart Navbar Function */

function ppNavbarjp ($menuitems, $selected='', $parms='') {
    global $_CONF;

    $navbar = new Template($_CONF['path_layout'] . 'navbar');
    $navbar->set_file (array (
        'navbar'       => 'navbar.thtml',
        'menuitem'     => 'menuitem.thtml',
        ));
    for ($i=1; $i <= count($menuitems); $i++)  {
        $parms = explode( "=",current($menuitems) );
        $navbar->set_var( 'link',   current($menuitems));
        if (key($menuitems) == $selected) {
            $navbar->set_var( 'cssactive', ' id="active"');
            $navbar->set_var( 'csscurrent',' id="current"');
        } else {
            $navbar->set_var( 'cssactive', '');
            $navbar->set_var( 'csscurrent','');
        }
        $navbar->set_var( 'label',  key($menuitems));
        $navbar->parse( 'menuitems', 'menuitem', true );
        next($menuitems);
    }
    $navbar->parse ('output', 'navbar');
    $retval = $navbar->finish($navbar->get_var('output'));
    return $retval;
}



?>