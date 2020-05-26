<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Box Plugin lib : ppNavibar.php
// +---------------------------------------------------------------------------+
// | lib-portalparts.php
// | lib-portalparts.php より、ppNavbar抜粋
// | 古すぎるのでMediaGalleryプラグインを参考にnavbar.class.phpを利用するよう変更
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2020 by the following authors:
// | Blaine Lang            - Blaine.Lang@nextide.ca
// | Randy Kolenko          - Randy.Kolenko@nextide.ca
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca
// | hiroron                - hiroron AT hiroron DOT com
// +---------------------------------------------------------------------------+
// |
// | This program is free software; you can redistribute it and/or
// | modify it under the terms of the GNU General Public License
// | as published by the Free Software Foundation; either version 2
// | of the License, or (at your option) any later version.
// |
// | This program is distributed in the hope that it will be useful,
// | but WITHOUT ANY WARRANTY; without even the implied warranty of
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// | GNU General Public License for more details.
// |
// | You should have received a copy of the GNU General Public License
// | along with this program; if not, write to the Free Software Foundation,
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// |
// +---------------------------------------------------------------------------+
//
// 20200526 hiroron - MediaGalleryプラグインを参考にnavbar.class.phpを利用
// @@@@@20081020 lib-portalparts.php より、ppNavbar抜粋
/* PortalPart Navbar Function */

function ppNavbarjp ($menuitems, $selected='', $parms='') {
    global $_CONF;

    include_once $_CONF['path'] . 'system/classes/navbar.class.php';
    $navbar = new navbar;
    foreach ($menuitems as $k => $v) {
        $navbar->add_menuitem($k, $v);
        $navbar->set_onclick($k, 'location.href="' . $v . '";');
    }
    $navbar->set_selected($selected);
    $retval = $navbar->generate();
    return $retval;
}



?>