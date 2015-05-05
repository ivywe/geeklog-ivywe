<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | DataBox プラグイン
// | cache 作成プログラムから require
// +---------------------------------------------------------------------------+
// $Id: plugins/databox/fnc_databoxcache.inc
//20130730 tsuchitani AT ivywe DOT co DOT jp http://www.ivywe.co.jp/

if (strpos ($_SERVER['PHP_SELF'], 'fnc_databoxcache.inc') !== false) {
    die ('This file can not be used on its own.');
}


function fnc_databoxcache(
	$function
	,$code
	,$template =""
)
// +---------------------------------------------------------------------------+
// | 機能  データ個別ページ、カテゴリ別一覧表示　のキャッシュファイル作成
// | 書式  fnc_putcache("data"	,"datacode1" ,"templatedir")
// | 書式  fnc_putcache("data"	,"datacode1" )
// +---------------------------------------------------------------------------+
// | 引数 $function:data category datapage categorypage
// | 引数 $code:カテゴリのコードまたはdataのコード
// | 引数 $template:使用するテンプレートディレクトリ名 省略時 default
// +---------------------------------------------------------------------------+
{
	global $_CACHE_VERBOSE;
	global $_ASSIST_CONF;
	global $_PLUGINS;
	
	if (!in_array('assist', $_PLUGINS)) {
		$logmsg="[databox_putcache] Assist Plugin is not effective!";
		COM_errorLog($logmsg);
		exit;
	}
	
	//出力ファイル名
	if  ($template<>""){
		$filename=$code."_THME_".$template.".html";
	}else{
		$filename=$code.$template.".html";
	}
	$filename=$_ASSIST_CONF["path_cache"].'databox/'.$filename;

	//
	$pi_name="databox";
	$m="";
	$id=0;
	switch ($function) {
		case 'data'://データ（ヘッダフッタなし）
			$display= databox_data($id,$template,"yes","",$code);
			break;
		case 'category'://カテゴリ（ヘッダフッタなし）
			$display= databox_category("autotag",null,$template,"",0,null,null,$code);
			break;
		case 'datapage'://データページ（ヘッダフッタは、設定による）
			//個別データのページ　public_html/databox/data.php?code=[2]&m=code&template=[3]<BR>
			$retval= databox_data($id,$template,"yes","page",$code);
			$layout=$retval['layout'];
			$information['headercode']=$retval['headercode'];
			$information['pagetitle']=$retval['title'];
			$display=$retval['display'];
			$display=DATABOX_displaypage($pi_name,$layout,$display,$information);
			break;
		case 'categorypage'://カテゴリページ（ヘッダフッタは、設定による）
			$display= databox_category("notautotag",null,$template,"yes",0,null,null,$code);
			break;
		default:
			$display="function error!";
	}
	
	@unlink($filename);
	file_put_contents($filename,  $display);
    @chmod($filename, 0666);

	if  ($_CACHE_VERBOSE){
		$logmsg="[databox_putcache] ".$filename;
		echo $logmsg."<br>";
		COM_errorLog($logmsg);
	}
}

?>
