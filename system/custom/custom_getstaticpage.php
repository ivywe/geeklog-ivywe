<?php

if (strpos(strtolower($_SERVER['PHP_SELF']), 'custom_getstaticpage.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* Returns the content of a given staticpage
*
* @author   mystral-kk - geeklog AT mystral-kk DOT net
* @license  GPL v2
* @param    $sp_id  string  an id of a staticpage
* @return           string  the content of the staticpage
*/
function CUSTOM_getStaticpage($sp_id) {
    global $_TABLES, $_PLUGINS, $_SP_CONF, $LANG_STATIC;
    
    $retval = '';
    
    if (!in_array('staticpages', $_PLUGINS)) {
        return $retval;
    }
    
    $sql = "SELECT sp_php, sp_content, cache_time, template_id FROM {$_TABLES['staticpage']} "
         . "WHERE (sp_id = '" . COM_applyBasicFilter($sp_id) . "') "
         . "AND " . SP_getPerms();
    $result = DB_query($sql);
    if (DB_error() OR (DB_numRows($result) == 0)) {
        return $retval;
    } else {
        $A = DB_fetchArray($result);
        $retval = SP_render_content($sp_id, $A['sp_content'], $A['sp_php'], $A['cache_time'], $A['template_id']);
    }
    
    return $retval;
}
