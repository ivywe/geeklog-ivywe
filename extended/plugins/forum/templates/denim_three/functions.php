<?php
// Allows plugin to add extra CSS and javascript libraries for individual themes.
// Theme settings will overwrite any plugin settings that are the same name/file


// this file can't be used on its own (keep this in as plugin template files could be located with theme) 
if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.php') !== false) {
    die('This file can not be used on its own!');
}


/**
 * Return an array of CSS files to be loaded
 */
function forum_css_denim_three()
{
    global $_CONF, $LANG_DIRECTION;

    // No extra CSS files needed by plugin. Assume theme loads all required
    // These forum templates requires UIkit 2.
    return array();

    // BELOW NOT NEEDED ANYMORE AS LOADED BY THEME NOW
    // The only extra component that the plugin needs from uikit is the tooltip
    $direction = ($LANG_DIRECTION == 'rtl') ? '_rtl' : '';
    return array(
        array(
            'name'       => 'uikit-tooltip',
            'file'       => '/vendor/uikit/css' . $direction . '/components/tooltip.gradient.min.css',
            'attributes' => array('media' => 'all'),
            'priority'   => 70
        )
    );
}

/**
 * Return an array of JS libraries to be loaded
 */
function forum_js_libs_denim_three()
{
    // No extra JS libraries needed by plugin. Assume theme loads all required.
    return array();
}

/**
 * Return an array of JS files to be loaded
 */
function forum_js_files_denim_three()
{
    global $_CONF;

    // No extra JS libraries needed by plugin. Assume theme loads all required.
    // These forum templates requires UIkit 2.
    return array();

    // BELOW NOT NEEDED ANYMORE AS LOADED BY THEME NOW
    // The only component that the plugin needs from uikit is the tooltip 
    return array(
       array(
            'file'      => '/vendor/uikit/js/components/tooltip.js',
            'footer'    => false, // Not required, default = true
            'priority'  => 110 // Not required, default = 100
        )
    );
}

/**
 * Return information for the request item location
 * CSS Classes, Styles, etc... can be passed
 */
function forum_getThemeItem_denim_three($item)
{
    $retval = '';
    
    switch ($item) {
        // ***************************
        // Used for lists by COM_makeList 
            
        case 'forum-css-list-menu': 
            $retval = ''; // no extra css classes needed for forum menu for this theme
            break;
            
        // ***************************    
        default:
           
           break;
    }
    
    return $retval;
}

?>
