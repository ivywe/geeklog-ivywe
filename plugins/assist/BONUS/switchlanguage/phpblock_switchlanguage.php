<?php
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
	$languagenameary['ja']['de']="独語";
	//
	global $_CONF;
	
	$languageid = COM_getLanguageId();

	$url=$_CONF['site_url']."/switchlang.php?lang=";

	$html="";
	if  (is_array($_CONF['languages'])){
		$ary=$_CONF['languages'];
		foreach( $ary as $fid => $fvalue ){
			if  ($html<>"") {
				$html.="|";
			}
			if  ($_CONF['language_files'][$fid]<>""){
				if ($languagenameary[$languageid][$fid]){
					$languagename=$languagenameary[$languageid][$fid];
				}else{
					$languagename=$fvalue;
				}	
				if  ($fid==$languageid) {
					$html.=$languagename.LB;
				}else{	
					$html.='<a href='.$url.$fid.'">'.$languagename.'</a>'.LB;
				}
			}
		}
	}		
	return $html;
}
?>
