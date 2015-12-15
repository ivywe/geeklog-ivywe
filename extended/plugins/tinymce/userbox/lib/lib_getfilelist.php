<?php
//20110506

if (strpos ($_SERVER['PHP_SELF'], 'lib_getfilelist.php') !== false) {
    die ('This file can not be used on its own.');
}

// 指定フォルダのファイルの一覧を取得
function LIB_getfilelist(
    $fd
    ,$exte =""
)
{
    $fary= array();
    $dir=opendir($fd);
    while(($ent = readdir()) !==FALSE){
        if  ($exte=="") {
           $fary[$i] = $ent;
           $i++;
        }else{
            $w=explode('.',$ent);
            if (strtolower($w[1])===$exte){
               $fary[$i] = $ent;
               $i++;
            }
        }
    }
    return $fary;

}

?>
