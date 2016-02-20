<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Switch language PHP block function for multi language site                |
// +---------------------------------------------------------------------------+
// | phpblock_switchlanguage.php                                               |
// | version: 1.1.0                                                            |
// +---------------------------------------------------------------------------+
// | Geeklog 1.7                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2010 by the following authors:                              |
// |                                                                           |
// | Authors:                                                                  |
// +---------------------------------------------------------------------------+
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// +---------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== FALSE) {
    die('This file can not be used on its own.');
}

//言語切替　phpblock 用関数　2012/11/02
//require_once( 'custom/phpblock_switchlanguage.php' );

function phpblock_switchlanguage()
{
	//languagename sample
	$languagenameary['ja']['ja']="日本語";
	$languagenameary['ja']['en']="英語";
	$languagenameary['ja']['es']="スペイン語";
	$languagenameary['ja']['de']="ドイツ語";
	$languagenameary['en']['ja']="Japanese";
	$languagenameary['en']['en']="English";
	$languagenameary['en']['es']="Spanish";
	$languagenameary['en']['de']="German";
	$languagenameary['es']['ja']="Japonés";
	$languagenameary['es']['en']="Ingres";
	$languagenameary['es']['de']="Alemán";
	$languagenameary['es']['es']="Español";
	$languagenameary['de']['ja']="Japonés";
	$languagenameary['de']['en']="Englisch";
	$languagenameary['de']['es']="Spanisch";
	$languagenameary['de']['de']="Deutsch";
	//
	global $_CONF;
	
	$languageid = COM_getLanguageId();

	$url=$_CONF['site_url']."/switchlang.php?lang=";

	$html='<ul class="uk-subnav uk-flex-center">';
	if  (is_array($_CONF['languages'])){
		$ary=$_CONF['languages'];
		foreach( $ary as $fid => $fvalue ){
			$html.="<li>";
			if  ($_CONF['language_files'][$fid]<>""){
				if ($languagenameary[$languageid][$fid]){
					$languagename=$languagenameary[$languageid][$fid];
				}else{
					$languagename=$fvalue;
				}	
				if  ($fid==$languageid) {
					$html.='<button class="uk-button uk-button-mini uk-margin-small-left" disabled>'.$languagename.'</button>'.LB;
				}else{	
					$html.='<a href="'.$url.$fid.'" class="uk-button uk-button-mini uk-button-primary uk-margin-small-left">'.$languagename.'</a>'.LB;
				}
			}
			$html.="</li>";
		}
	}		
	$html.="</ul>";
	return $html;
}
?>
