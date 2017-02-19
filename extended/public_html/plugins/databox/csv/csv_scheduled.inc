<?php
/* Reminder: always indent with 4 spaces (no tabs). */

if (strpos ($_SERVER['PHP_SELF'], 'csv_scheduled.inc') !== false) {
    die ('This file can not be used on its own.');
}
//log 出力モード設定　0:作成しない,1:ファイルに出力
$logmode =1;
$logfile = $_CONF['path_log'] . 'databox_csvimport.log';

$dummy=databox_scheduled_csv ("databox");
function databox_scheduled_csv (
    $pi_name
 )
{
    global $_DATABOX_CONF;
    global $_TABLES;
    global $_CONF;
    global $logfile;
    global $logmode;

    $name='databox_csv_cron';
    if ($_DATABOX_CONF['csv_cron_schedule_interval'] > 0) {
        $datetime = DB_getItem( $_TABLES['vars'], 'value', "name = '{$name}'" );
        if  ($datetime<>""){
            if (($datetime + $_DATABOX_CONF['csv_cron_schedule_interval']) <= time()) {
                DB_query("UPDATE {$_TABLES['vars']} SET value=UNIX_TIMESTAMP() WHERE name='{$name}'");
                require_once($_CONF['path'] . 'plugins/databox/csv/csv_functions.inc');
                require_once ($_CONF['path'] . 'plugins/databox/csv/csv_importexec.inc');
                $dummy=databox_importexec($pi_name,"scheduled");
                if ($_DATABOX_CONF['csv_cron_schedule_nextmaps'] ==1) {
                    $functions_sc = $_CONF['path'] . 'plugins/databox/maps/maps_markersupdate.inc';
                    if (file_exists ($functions_sc)) {
                        require_once ($functions_sc);
                        $dummy=fncmarkersclear();
                        $dummy=fncmarkersupdate("scheduled");
                    }
                }
                CTL_clearCache();
                $logentry="clearCache";
                $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
            }
    	}
    }

}

?>
