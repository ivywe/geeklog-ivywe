<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |自動タグ[l:]を使い多言語サイト用コンテンツ表示を実装するカスタム関数
// +---------------------------------------------------------------------------+
// | phpautotags_l.php
// |
// | Copyright (C) 2017 by the following authors:
// |
// | Authors: hiroron         - hiroron AT hiroron DOT com
// |
// | Version: 1.0.0 (2017-07-25)
// | Update: 1.1.0 (2020-05-07) p3引数がプラグイン名なのでp0の全部を加工して返す
// |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'phpautotags_l.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* 自動タグ [l:<LanguageId>]LanguageIdの言語用コンテンツ[/l]
**/
function phpautotags_l($p1, $p2, $p0, $p3) {
    global $_CONF;
    if (empty($p1)) { return ''; }
    if (strcmp($p1, COM_getLanguageId($_CONF['language'])) === 0) {
# 2020/05/07 第4引数のp3にプラグイン名がはいってきているのでp0の全文から加工して返すよう修正
#        return $p3;
        return str_replace('[l:'.$p1.']','',str_replace('[/l]','',$p0));
    }
    return '';
}
