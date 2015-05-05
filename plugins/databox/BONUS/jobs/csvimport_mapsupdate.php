<?php
/* Reminder: always indent with 4 spaces (no tabs). */
//コンフィギュレーション設定のスケジュール.条件(csv_cron_schedule_sel_id)を参照します
//コンフィギュレーション設定にかかわらず終了後入力ファイル削除します
//コンフィギュレーション設定にかかわらず続けてMAPS実行します

    set_time_limit(120);

    //      ↓フォルダ位置が変わる場合は修正してください
    include('/var/www/virtual/xxxxxx.net/htdocs/lib-common.php');

    //log 出力モード設定　0:作成しない,1:ファイルに出力
    $logmode =1;
    $logfile = $_CONF['path_log'] . 'databox_csvimport.log';

    $pi_name    = 'databox';
    require_once($_CONF['path'] . 'plugins/databox/csv/csv_functions.inc');
    require_once ($_CONF['path'] . 'plugins/databox/csv/csv_importexec.inc');
    $dummy=databox_importexec($pi_name,"CRON");
    require_once ($_CONF['path'] . 'plugins/databox/maps/maps_markersupdate.inc');
    $dummy=fncmarkersclear();
    $dummy=fncmarkersupdate("CRON");
    CTL_clearCache();
    $logentry="clearCache";
    $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
?>
