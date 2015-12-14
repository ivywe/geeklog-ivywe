<?php

if (strpos ($_SERVER['PHP_SELF'], 'lib_outlog.php') !== false) {
    die ('This file can not be used on its own.');
}

//log 出力モード設定　0:作成しない,1:ファイルに出力 2:画面にも出力
//$logmode =2;
//$logfile = $_CONF['path_log'] . "profile_point.log";

//echo OutLog( "----- photomailcheck.php start",$logmode);


// ログ出力処理
function LIB_OutLog( $logentry ,$logfile,$logmode=1)
{
    global $_CONF,$LANG01;

    $retval = '';

    if (!empty($logmode)) {
        if( !empty( $logentry )) {
            $logentry = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),
                                     $logentry );

            $timestamp = strftime( '%c' );

            if (!file_exists($logfile)) {
                echo $logfile ."  doesn't exist. ";
                exit();
            }
            if (!is_writable($logfile)) {
                echo $logfile ." cannot be written. ";
                exit();
            }

            $file = fopen( $logfile, 'a' );

            if( empty($file)) {
                $retval .= $LANG01[33] . ' ' . $logfile . ' (' . $timestamp . ')<br/><br/>' . LB;
            } else {
                fputs( $file, "$timestamp - $logentry\n" );
            }

            if ($logmode ==2){
                $retval .="$timestamp - $logentry<br/>" . LB;
            }
        }
    }

    return $retval;
}

?>
