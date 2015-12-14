<?php

if (strpos(strtolower($_SERVER['PHP_SELF']), 'custom_phpmenuitem.php') !== false) {
    die('This file can not be used on its own!');
}

function phpmenuitem_home()
{
    global $_CONF, $LANG01;

    $url = $_CONF['site_url'] . '/';

    switch( COM_getLanguageId() ){
      case en: $label = "HOME"; break;
      case sc: $label = "INICIO"; break;
      case ja: $label = "HOME"; break;
    }
    
    $menuitems = array(
        'url'        => $url,
        'label'      => $label,
        'icon_url'   => '',
        'id_name'    => '',
        'class_name' => '',
        'submenu_entries' => array(),
    );
    return $menuitems;
}

function phpmenuitem_news()
{
    global $_CONF, $LANG01;

    $url = $_CONF['site_url'] . '/index.php?topic=General_' . COM_getLanguageId();

    switch( COM_getLanguageId() ){
      case en: $label = "NEWS"; break;
      case sc: $label = "NOTICIAS"; break;
      case ja: $label = "お知らせ"; break;
    }
    
    $menuitems = array(
        'url'        => $url,
        'label'      => $label,
        'icon_url'   => '',
        'id_name'    => '',
        'class_name' => '',
        'submenu_entries' => array(),
    );
    return $menuitems;
}



