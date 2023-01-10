<?php

$_tempArray = array(
    'afrikaans'           => array('ltr', 'Afrikaans'),
    'bosnian'             => array('ltr', 'босански'),
    'bulgarian'           => array('ltr', 'български'),
    'catalan'             => array('ltr', 'Català'),
    'chinese_simplified'  => array('ltr', '简体中文'),
    'chinese_traditional' => array('ltr', '繁體中文'),
    'croatian'            => array('ltr', 'hrvatski'),
    'czech'               => array('ltr', 'čeština'),
    'danish'              => array('ltr', 'Dansk'),
    'dutch'               => array('ltr', 'Nederlands'),
    'english'             => array('ltr', 'English'),
    'estonian'            => array('ltr', 'eesti keel'),
    'finnish'             => array('ltr', 'suomen kieli'),
    'french_canada'       => array('ltr', 'Français canadien'),
    'french_france'       => array('ltr', 'Français'),
    'german_formal'       => array('ltr', 'Deutsch formell'),
    'german'              => array('ltr', 'Deutsch'),
    'hebrew'              => array('rtl', 'עברית'),
    'hellenic'            => array('ltr', 'ελληνικά'),
    'indonesian'          => array('ltr', 'bahasa Indonesia'),
    'italian'             => array('ltr', 'Italiano'),
    'japanese'            => array('ltr', '日本語'),
    'korean'              => array('ltr', '한국어'),
    'norwegian'           => array('ltr', 'Norsk'),
    'polish'              => array('ltr', 'Polski'),
    'portuguese_brazil'   => array('ltr', 'Portugues do Brasil'),
    'portuguese'          => array('ltr', 'português'),
    'romanian'            => array('ltr', 'limba română'),
    'russian'             => array('ltr', 'русский язык'),
    'serbian'             => array('ltr', 'српски'),
    'slovak'              => array('ltr', 'Slovenčina'),
    'slovenian'           => array('ltr', 'Slovenski jezik'),
    'spanish_argentina'   => array('ltr', 'Español Argentino'),
    'spanish'             => array('ltr', 'Español'),
    'swedish'             => array('ltr', 'Svenska'),
    'turkish'             => array('ltr', 'Türkçe'),
    'ukrainian'           => array('ltr', 'українська мова'),
);

if (COM_versionCompare(VERSION, '2.2.2', '>=')) {
    $_tempArray['persian'] = array('rtl', 'فارسی');
} else {
    $_tempArray['farsi'] = array('rtl', 'فارسی');
}

return $_tempArray;
