<?php
//20110713 COPY

if (strpos ($_SERVER['PHP_SELF'], 'lib_mail_is_mobile.php') !== false) {
    die ('This file can not be used on its own.');
}

// hiroron 20091003 <配信先選択>
/**
* メールアドレスが携帯かどうか判定する関数
*/
function LIB_mail_is_mobile (
$mail
) {
    if ( preg_match("/@(docomo|softbank|disney|ezweb|[dhtkrsnqc]\.vodafone|pdx|d[kij]\.pdx|wm\.pdx|"
         ."em\.nttpnet|pipopa|.*sky\.tu-ka|.*sky\.tk[ck]|jp-[dhtkrsnqc]|t[2-9]\.ezweb)\.ne\.jp$/i", $mail)
         OR preg_match("/@(bandai\.jp|i\.softbank\.jp|willcom\.com)$/i", $mail)
    ) {
        return TRUE;
    } else {
        return FALSE;
    }
}

?>
