<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// |自動タグ[currenturl:]を使い指定言語の場合のURLを返すカスタム関数
// +---------------------------------------------------------------------------+
// | phpautotags_currenturl.php
// |
// | Copyright (C) 2018 by the following authors:
// |
// | Authors: hiroron    - hiroron AT hiroron DOT com
// |
// | Version: 1.0.1 (2018-03-12)
// |
// +---------------------------------------------------------------------------+
if (strpos(strtolower($_SERVER['PHP_SELF']), 'phpautotags_currenturl.php') !== false) {
    die('This file can not be used on its own!');
}

/**
* 自動タグ [currenturl:<LanguageId>] LanguageIdのカレントURLを返す
* 自動タグ [currenturl:<LanguageId> topicid:<TopicId>] LanguageIdつきのTopicIDのトピックURLを返す
**/
function phpautotags_currenturl($p1, $p2, $p0) {
    global $_CONF;

    if (empty($p1)) { return COM_getCurrentURL(); }

    if (COM_onFrontpage()) {
        $loc = MBYTE_strrpos($p2, 'topicid:');
        if ($loc !== false) {
            $topicid = MBYTE_substr($p2, ($loc + 8));
            return TOPIC_getUrl($topicid . '_' . $p1);
        }
    }

    $curl = COM_getCurrentURL();
    $loc = MBYTE_strrpos($curl, '_');
    if (($loc > 0) && (($loc + 1) < MBYTE_strlen($curl))) {
        $current_lang_id = MBYTE_substr($curl, ($loc + 1), 2);
    }

    $new_url = $curl;
    if (($loc > 0) && ($p1 != $current_lang_id)) {
        $new_url = substr_replace($curl, $p1, $loc + 1, strlen($current_lang_id));
    }

    return $new_url;
}
