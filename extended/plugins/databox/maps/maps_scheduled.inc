<?php
/* Reminder: always indent with 4 spaces (no tabs). */

if (strpos ($_SERVER['PHP_SELF'], 'maps_scheduled.inc') !== false) {
    die ('This file can not be used on its own.');
}
//log 出力モード設定　0:作成しない,1:ファイルに出力
$logmode =1;
$logfile = $_CONF['path_log'] . 'databox_mapsupdate.log';

$dummy=databox_scheduled_maps ();
function databox_scheduled_maps ()
{
    global $_DATABOX_CONF;
    global $_TABLES;
    global $_CONF;
    $name='databox_maps_cron';
    if ($_DATABOX_CONF['maps_cron_schedule_interval'] > 0) {
        $datetime = DB_getItem( $_TABLES['vars'], 'value', "name = '{$name}'" );
        if  ($datetime<>""){
            if (($datetime + $_DATABOX_CONF['maps_cron_schedule_interval']) <= time()) {
                require_once ($_CONF['path'] . 'plugins/databox/maps/maps_markersupdate.inc');
                $dummy=fncmarkersclear();
                $dummy=fncmarkersupdate("scheduled");
                DB_query("UPDATE {$_TABLES['vars']} SET value=UNIX_TIMESTAMP() WHERE name='{$name}'");
            }
        }
    }

}

?>
