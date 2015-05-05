<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | 機能  テーブルのデータをCSV形式でダウンロードする                         |
// | 書式 COMJ_dltbldt($filenm,$fld,$tbl,$where,$order)                        |
// +---------------------------------------------------------------------------+
// | 引数 $filenm :                                                            |
// | 引数 $fld :                                                               |
// | 引数 $tbl :                                                               |
// | 引数 $where :                                                             |
// | 引数 $order :                                                             |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
// $Id: comj_dltbldt.php
//2007/09/07 18:06 tsuchi AT geeklog DOT jp http://www.geeklog.jp/

function COMJ_dltbldt($filenm,$fld,$tbl,$where="",$order="")
{

    global $_CONF;

    $retval="";

    //file output open
    $outfile = tempnam($_CONF['path_data'] ."tmp", $filenm);
    $file = @fopen( $outfile, 'w' );
    if ( $file === false ) {
        $retval .= "ERR! ".$outfile ." is not writable!<br />" . LB;
        return $retval;
    }

    //-----
    $sql = "SELECT DISTINCT ";
    foreach($fld as $k => $v) {
        $sql .=$k.",";
    }
    $sql=rtrim($sql,",");
    $sql.= " FROM ".$tbl;
    if (!empty($where)) {
        $sql.=" WHERE ".$where;
    }
    if (!empty($order)) {
        $sql.=" ORDER BY ".$order;
    }
    //-----
    $result = DB_query ($sql);
    //-----1行目ヘッダ
    $w="";
    foreach($fld as $k => $v) {
        $w .=$v.",";
    }
    $w=rtrim($w,",");

    $w = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),$w );
    $encode=mb_detect_encoding($w,"EUC-JP,UTF-8,JIS,SJIS");
    $w2=mb_convert_encoding($w, "SJIS",$encode);
    $w2 = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),$w2 );
    fputs( $file, $w2.LB);
    //-----2行目以降
    while( $A = DB_fetchArray( $result ) )    {

        $w="";
        foreach($fld as $k => $v) {
            $w .=$A[$k].",";
        }
        $w=rtrim($w,",");

        $w = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),$w );
        $encode=mb_detect_encoding($w,"EUC-JP,UTF-8,JIS,SJIS");
        $w2=mb_convert_encoding($w, "SJIS","UTF-8");
        $w2 = str_replace( array( '<?', '?>' ), array( '(@', '@)' ),$w2 );
        fputs( $file, $w2.LB);

    }

    $filename=basename($outfile).".csv";
    $dir=dirname($outfile);

    header ("Content-Disposition: attachment; filename=$filename");
    header ("Content-type: application/x-csv");
    readfile ($outfile);

    $rt=unlink($outfile);

    return $retval;
}

?>
