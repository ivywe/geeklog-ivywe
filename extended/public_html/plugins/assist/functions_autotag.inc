<?php

if (strpos ($_SERVER['PHP_SELF'], 'functions_autotag.inc') !== false) {
    die ('This file can not be used on its own.');
}

//=====自動タグ=================================================================
function plugin_autotags_assist (
    $op
    , $content = ''
    , $autotag = ''
)
// +---------------------------------------------------------------------------+
// | 自動タグ
// | 書式 plugin_autotags_assist ($op, $content, $autotag)
// +---------------------------------------------------------------------------+
// 2010/08/03
{
    global $_TABLES;
    global $_CONF;
    global $_USER;
	
//    global $_PLUGINS;

    global $LANG_ASSIST;
    global  $topic;
//    global  $page;

    global $LANG_ASSIST_autotag_desc;
	global $_GROUPS;
	
	$tagname = array();
    $tagname[]='newstories';
    $tagname[]='newstories2';
    $tagname[]='conf';
    $tagname[]='assist';
	
	//Option Display
    if ($op == 'tagname' ) {
		return $tagname;
	} elseif ($op == 'permission' || $op == 'nopermission') {
	    if ($op == 'permission') {
            $flag = true;
        } else {
            $flag = false;
        }

        if (isset($_GROUPS['Assist Admin'])) {
            $group_id = $_GROUPS['Assist Admin'];
        } else {
            $group_id = DB_getItem($_TABLES['groups'], 'grp_id',
                                   "grp_name = 'Assist Admin'");
        }
        $owner_id = SEC_getDefaultRootUser();

		if (COM_getPermTag(
			$owner_id
			, $group_id
			, $_ASSIST_CONF['autotag_permissions_assist'][0]
			, $_ASSIST_CONF['autotag_permissions_assist'][1]
			, $_ASSIST_CONF['autotag_permissions_assist'][2]
			, $_ASSIST_CONF['autotag_permissions_assist'][3]) == $flag) {
            return $tagname;
        }
    } elseif ($op == 'description') {
        return $LANG_ASSIST_autotag_desc;
		
	} else if ($op == 'parse') {
        //引数チェック
        $p1 = COM_applyFilter ($autotag['parm1']);
        $p2 = COM_applyFilter ($autotag['parm2']);
        $tag = COM_applyFilter ($autotag['tag']);
        $tagstr = COM_applyFilter ($autotag['tagstr']);

        //各処理
        switch( $tag ) {
            //----------------
            //新着記事（おしらせ）
            //[newstories:話題ID RSSファイル]
            case 'newstories' :
                $rt= phpblock_shownewstories($p1,$p2);
                $content = str_replace ($autotag['tagstr'], $rt, $content);
                return $content;
                break;
            case 'newstories2' :
				$parm2_ary[]="rss_file";
				$parm2_ary[]="title_trim_length";
				$parm2_ary[]="intervalday";
				$parm2_ary[]="limitcnt";
				$parm2_ary[]="newmarkday";
				$parm2_ary[]="thtml";
			$p=assist_parm2($p2,$parm2_ary);
               $rt= phpblock_shownewstories($p1
                        ,$p['rss_file']
                        ,$p['title_trim_length']
                        ,$p['intervalday']
                        ,$p['limitcnt']
                        ,$p['newmarkday']
                        ,$p['thtml']
                        );
                $content = str_replace ($autotag['tagstr'], $rt, $content);
                return $content;
                break;
            case 'conf' :
                $rt=$_CONF[$p1];
                $content = str_replace ($autotag['tagstr'], $rt, $content);
                return $content;
                break;
			case 'assist' :
					$parm2_ary[]="id";
					$parm2_ary[]="urlkey";
					$parm2_ary[]='mode';
					$parm2_ary[]='uid';
					$parm2_ary[]='width';
					$parm2_ary[]='height';
					$parm2_ary[]='topics';
					$parm2_ary[]='rss_file';
					$parm2_ary[]='title_trim_length';
					$parm2_ary[]='intervalday';
					$parm2_ary[]='limitcnt';
					$parm2_ary[]='newmarkday';
					$parm2_ary[]='templatedir';
					$parm2_ary[]='urlrss';
					$parm2_ary[]='description_trim_length';
					$parm2_ary[]='strip_tags';
					$parm2_ary[]='templatedir';
					$parm2_ary[]='logoutform';
					$parm2_ary[]='type';
					$parm2_ary[]='urlrss';
					$parm2_ary[]='text';
					$parm2_ary[]='maxlen';
					$parm2_ary[]='filler';
					$parm2_ary[]='endchars';
					$parm2_ary[]='title';
                    $parm2_ary[]='permission';
					$p=assist_parm2($p2,$parm2_ary);
                switch($p1) {
                    case 'usercount':
                        $rt=DB_getItem($_TABLES['userinfo']
                            ,"count(uid)","uid<>1");
                        $rt=COM_NumberFormat($rt);
                        break;
                    case 'usercount_location':
                        $rt=assist_usercount_location();
                        break;
                    case 'staticpage_content':
                        $rt=phpblock_showstaticpage(
                             $p['id']
                            ,$p['urlkey']
                            ,$p['mode']
                            );
                        break;
                    case 'user_homepage':
                        $rt=assist_returnUser(
                             "homepage"
                            ,$p['uid']
                            );
                        break;
                    case 'user_location':
                        $rt=assist_returnUser(
                             "location"
                            ,$p['uid']
                            );
                        break;
                    case 'user_sig':
                        $rt=assist_returnUser(
                             "sig"
                            ,$p['uid']
                            );
                        break;
                    case 'user_photo':
                        $width=$p['width'];
                        $height=$p['height'];
                        $rt=assist_returnUser(
                             "photo"
                            ,$p['uid']
                            ,$p['width']
                            ,$p['height']
                            );
                        break;
                    case 'user_about':
                        $rt=assist_returnUser(
                             "about"
                            ,$p['uid']
                            );
                        break;
                    case 'user_pgpkey':
                        $rt=assist_returnUser(
                             "pgpkey"
                            ,$p['uid']
                            );
                        break;

                    case 'uid':
                        $rt=$_USER['uid'];
                    break;

                    case 'username':
                    case 'fullname':
                        $rt=assist_returnUser(
                             $p1
                            ,$p['uid']
                            );
                        break;
                    case 'showgroup':
                        $rt=phpblock_showgroup();
                        break;
                    case 'newstories' :
                        $rt= phpblock_shownewstories(
                            $p['topics']
                            ,$p['rss_file']
                            ,$p['title_trim_length']
                            ,$p['intervalday']
                            ,$p['limitcnt']
                            ,$p['newmarkday']
                            ,$p['templatedir']
                            ,true
                            ,$p['permission']
                        );
                        break;
					case 'rss' :
                        $rt= assist_getrss(
                            $p['urlrss']
                            ,$p['description_trim_length']
                            ,$p['strip_tags']
                            ,$p['templatedir']
                        );
                        break;
                    //
                    case 'home_id' :
                        if( COM_onFrontpage() ){
                            $rt=$LANG_ASSIST['home_id']['home'];
                        } else {
                            $rt=$LANG_ASSIST['home_id']['sub'];
                        }
                        break;
                    case 'login_status' :
                        if  (COM_isAnonUser()){
                            $rt=$LANG_ASSIST[$p1][0];
                        } else {
                            $rt=$LANG_ASSIST[$p1][1];
                        }
                        break;
                    case 'login_withfacebook' :
						$rt=assist_loginform(
							'facebook'
							,$p['logoutform']);
                        break;
                    case 'topic_id' :
                    case 'sp_id' :
                        $rt=assist_arg($p1);
                        break;
                    case 'btn' :
                        $rt= assist_btn(
                            $p['type']
                            ,$p['templatedir']
                        );
                        break;
                    case 'truncate' :
                        $rt= assist_truncate(
                            $p['text']
                            ,$p['maxlen']
                            ,$p['filler']
                            ,$p['endchars']
                        );
                        break;
                    case 'cache' :
                        $rt= assist_cache(
                            $p['id']
                            ,$p['type']
                        );
                        break;
                    case 'staticpage':
                        $rt=assist_staticpagelink(
                             $p['id']
                            ,$p['lastparm2']
                            );
                        break;

                }

                $content = str_replace ($autotag['tagstr'], $rt, $content);
                return $content;
                break;

        }
    }
}

//第二引数分割
function assist_parm2 (
	$p2
	,$parm2_ary
)
{
    $ary=array();
    $px = explode (' ', trim ($p2));
    if (is_array ($px)) {
		foreach ($px as $part) {
			$a = explode (':', $part);
			if (in_array($a[0],$parm2_ary)){
				$ary["{$a[0]}"]=$a[1];
				$skip++;			
			}
		}
		
        if (count ($px) > $skip) {
            for ($i = 0; $i < $skip; $i++) {
                array_shift ($px);
            }
            $ary["lastparm2"] = trim(implode (' ', $px));
        }

	}else{
		$lastparm2=trim($p2);
	}
    return $ary;

}

//=====ブロック関数============================================================
function phpblock_showgroup()
// +---------------------------------------------------------------------------+
// | 機能  ブロック用 ログインユーザの所属するグループ一覧出力
// | 書式 phpblock_showgroup()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:ログインユーザの所属するグループ一覧
// +---------------------------------------------------------------------------+
//2007/09/03 15:11 tsuchi AT geeklog DOT jp http://www.geeklog.jp/
{

    global $_USER,$_TABLES;

    $retval="";

    if (!empty ($_USER['uid']) && ($_USER['uid'] > 1)) {
        $sql="SELECT ";
        $sql.=" grp_name ,grp_descr ";
        $sql.=" FROM {$_TABLES['group_assignments']}";
        $sql.=" ,{$_TABLES['groups']} ";
        $sql.=" WHERE ug_main_grp_id = grp_id ";
        $sql.=" and UG_UID=".$_USER['uid'];
        $sql.=" and grp_name<>'Logged-in Users'";
        $sql.=" and grp_name<>'All Users'";
        $sql.=" ORDER BY ug_main_grp_id";

        $result = DB_query ($sql);

        while( $A = DB_fetchArray( $result ) )    {
            $retval .= "<li>";
            $retval .="<div title='{$A['grp_descr']}'>";
            $retval .= $A['grp_name'];
            $retval .="</div>";
            $retval .= "</li>";
            $retval .= LB;
        }
    }
    return $retval;
}

function phpblock_shownewstories(
     $topics=""
    ,$rss_file=""
    ,$title_trim_length=""
    ,$intervalday=""
    ,$limitcnt=""
    ,$newmarkday=""
    ,$thtml=""
    ,$templatePath=false
    ,$permission=""
)
// +---------------------------------------------------------------------------+
// | 機能  ブロック用 新着記事一覧出力                                         |
// | 書式 phpblock_shownewstories                                              |
// | 書式 phpblock_shownewstories("news","news.rss")                           |
// | 書式 phpblock_shownewstories("news","news.rss",20,90,10,3)                |
// | 書式 phpblock_shownewstories("ALL")                                       |
// | 書式 phpblock_shownewstories("news,geeklog")                              |
// +---------------------------------------------------------------------------+
// | 引数 $topic         :選択トピック 省略時設定値or'ALL'                     |
// | 引数 $rss_file      :RSS_file     省略時""                                |
// | 引数 $title_trim_length :タイトル長の制限 省略時設定値or20                |
// | 引数 $intervalday   :新着記事の期間 単位日  省略時設定値or90              |
// |                      0の時　全件                                          |
// | 引数 $limitcnt      :表示件数   省略時設定値or10                          |
// | 引数 $newmarkday    :新着マーク 省略時設定値or3                           |
// | 引数 $newmarkday    :新着マーク 省略時設定値or3                           |
// | 引数 $thtml         :テンプレートファイルのフォルダ  layout/テーマ/XXXX/  |
// |                      省略時は、プラグイン内のテンプレート                 |
// | 引数 $templatePath=false
// | 引数 $permission    :ブランク　または　ignore
// +---------------------------------------------------------------------------+
// | 戻値 nomal:新着記事一覧                                                   |
// +---------------------------------------------------------------------------+
// 2009/04/02　引数 $thtml追加
// 2009/04/02　テンプレート変数 class 追加
// 2009/04/02　$intervalday=0の時全件
// 2012/05/02  テーマ変数　introtext description　追加
{

    global $_USER;
    global $_TABLES;
    global $_CONF;
    global $_ASSIST_CONF;
    global $LANG_ASSIST;
    //
    if ($title_trim_length==""){
        $title_trim_length=$_ASSIST_CONF['title_trim_length'];
        if ($title_trim_length==""){
            $title_trim_length=20;
        }
    }
    //
    if ($intervalday==""){
        $intervalday=$_ASSIST_CONF['intervalday'];
        if ($intervalday==""){
            $intervalday=90;
        }
    }
    //
    if ($limitcnt==""){
        $limitcnt=$_ASSIST_CONF['limitcnt'];
        if ($limitcnt==""){
            $limitcnt=10;
        }
    }
    if ($newmarkday==""){
        $newmarkday=$_ASSIST_CONF['newmarkday'];
        if ($newmarkday==""){
            $newmarkday=3;
        }
    }

    if ($topics==""){
        if  ($_ASSIST_CONF['topics']!==""){
            $topics="'".$_ASSIST_CONF['topics']."'";
        }
	}elseif (strtoupper($topics)==="ALL"){
        $topics="";
    }elseif (strpos($topics,"|")=="") {
		if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
			$topics = TOPIC_getChildList($topics);
		}else{
			$topics="'$topics'";
		}
    }else{
		$ary=explode("|",$topics);
		$topics="";
		foreach( $ary as $val ){
			//FOR GL2.0.0 
			if (COM_versionCompare(VERSION, "2.0.0",  '>=')){
				$tid_list = TOPIC_getChildList($val);
				if  ($tid_list<>""){
					if ($topics<>""){
						$topics.=",";
					}
					$topics.=$tid_list;
				}
			}else{
				if ($topics<>""){
					$topics.=",";
				}
				$topics.="'$val'";
			}
		}
    }

    //
    $new_img=$_ASSIST_CONF['new_img'];
    if ($new_img==""){
        $new_img="New!";
    }
    $rss_img=$_ASSIST_CONF['rss_img'];
    if ($rss_img=="") {
        $rss_img="[RSS]";
    }

//-------------------------------------------
    $retval="";

    $pi_name="assist";


    if ($templatePath){
        //テンプレートフォルダの設定
        $tmplfld=assist_templatePath('newstories',$thtml,$pi_name);
        $list = new Template($tmplfld);

    }else{
        if ($thtml<>""){
            if (file_exists($_CONF['path_layout'] . "/".$thtml)) {
                $list = new Template ($_CONF['path_layout']. "/".$thtml);
            }else{
                $list = new Template ($_CONF['path'] . "plugins/assist/templates/");
            }
        } else {
            $list = new Template ($_CONF['path'] . "plugins/assist/templates/");
        }
    }

    //
    if ($title_trim_length==""){
        $title_trim_length=$_ASSIST_CONF['title_trim_length'];
        if ($title_trim_length==""){
            $title_trim_length=20;
        }
    }
    //
    if ($intervalday==""){
        $intervalday=$_ASSIST_CONF['intervalday'];
        if ($intervalday==""){
            $intervalday=90;
        }
    }


    $list->set_file (array (
        'list' => 'stories.thtml',
        'col'   => 'stories_col.thtml',
		'col_ft'   => 'featuredstories_col.thtml'
        ));


    $sql="SELECT DISTINCT".LB;
	$sql.=" s.title".LB;
	$sql.=" ,s.meta_description".LB;
	$sql.=" ,s.introtext".LB;
	$sql.=" ,s.introtext".LB;
	$sql.=" ,s.featured".LB;
	
    $sql.=" , UNIX_TIMESTAMP(s.date) AS day".LB;
    //$sql.=" , t.topic".LB;
    //$sql.=" , t.tid".LB;
    $sql.=" , s.sid".LB;
    $sql.=" ,s.owner_id".LB;
    $sql.=" ,s.group_id".LB;
    $sql.=" ,s.perm_owner".LB;
    $sql.=" ,s.perm_group".LB;
    $sql.=" ,s.perm_members".LB;
    $sql.=" ,s.perm_anon".LB;

	$sql.=" FROM ".LB;
    $sql.=" {$_TABLES['stories']} AS s".LB;
    $sql.=" ,{$_TABLES['topics']} AS t".LB;
	$sql.=" ,{$_TABLES['topic_assignments']} AS t2".LB;
	
	$sql.=" WHERE".LB;
	$sql.="  s.sid = t2.id".LB;
	$sql.="  AND t2.tid = t.tid".LB;
	
	$sql.=" AND t.archive_flag=0 ".LB;
    $sql.=" AND s.draft_flag=0 ".LB;
    $sql.=" AND date <= NOW() ".LB;

    if ($intervalday<>0){
        $sql.=" AND (date >= (date_sub(NOW(), INTERVAL $intervalday day)))".LB;
    }
    if (strlen($topics)>0){
		$sql.=" AND t.tid IN ($topics)".LB;
	}
	
	
    //アクセス権のないデータ はのぞく
    if  ($_ASSIST_CONF['disable_permission_ignore']=="0" AND strtoupper($permission)=="IGNORE"){
    }else{
        $sql .= COM_getTopicSql ('AND', 0, 't');
        $sql .= COM_getPermSql ('AND', 0, 2, 's' );
    }
    $sql .= COM_getLangSQL ('sid', 'AND');

    $sql.=" ORDER BY date DESC".LB;

	$sql.=" LIMIT $limitcnt".LB;
    $result = DB_query ($sql);
    $i=0;
    while( $A = DB_fetchArray( $result ) )    {
		$A = array_map('stripslashes', $A);

        $url=COM_buildUrl($_CONF['site_url'] . '/article.php?story=' . $A['sid']);
        $title= COM_truncate( $A['title'], $title_trim_length,'...' );
        $meta_description= $A['meta_description'];
        $introtext= $A['introtext'];

        $list->set_var ('site_url', $_CONF['site_url']);
        $list->set_var ('day', strftime($LANG_ASSIST[2],$A['day']));

        $chkday=strtotime("- $newmarkday days",time());
        if (date("Ymd",$A['day']) >= date("Ymd",$chkday)){
            $list->set_var ('new_img', $new_img);
        }else{
            $list->set_var ('new_img', '');
        }

        $list->set_var ('url', $url);
        $list->set_var ('title', $title);
		
		$list->set_var ('meta_description', $meta_description);
		$list->set_var ('introtext', $introtext);

		$n=($i%2)+1;
        $class='class="row'.$n.'"';
        $list->set_var ('class', 'class="row'.$n.'"');

		$permission=SEC_hasAccess2($A);
        $list->set_var ('permission',$permission);
        if  ($permission>=2){
            $list->set_var ('class_a', 'class="gl-tooltip"');
            $list->set_var ('class_c', 'class="classic"');
        }else{
            $list->set_var ('class_a', 'class="assist_nolink"');
            $list->set_var ('class_c', 'class="assist_displaynon"');
        }
        $retary=assist_getTopics('article',$A['sid']);
        $list->set_var ('topicslink',$retary['topicslink']);
        $list->set_var ('topicidslink',$retary['topicidslink']);
        $list->set_var ('topics',$retary['topics']);
        $list->set_var ('topicids',$retary['topicids']);
        $list->set_var ('topicidspipeline',$retary['topicidspipeline']);
        $list->set_var ('topics_li',$retary['topics_li']);
        $list->set_var ('topicids_li',$retary['topicids_li']);
        $list->set_var ('topicslink_li',$retary['topicslink_li']);
        $list->set_var ('topicidslink_li',$retary['topicidslink_li']);

		if ($A['featured']==1){
			$list->parse ('stories_col', 'col_ft', true);
		}else{
			$list->parse ('stories_col', 'col', true);
		}
        $i++;
    }

    $list->set_var ('site_url', $_CONF['site_url']);
    //
    if ($rss_file<>""){
        $list->set_var ('rss_url', $_CONF['site_url'] . '/backend/'.$rss_file);
        $list->set_var ('rss_title', $rss_img);
    }
    //
    $more_title=$LANG_ASSIST[1];
    if (ltrim(rtrim($topics,"'"),"'")=="" OR $topics=="ALL" OR strpos($topics,",")<>"") {
        $more_url=COM_buildUrl($_CONF['site_url'] . '/directory.php');
    }else{
        //$more_url=COM_buildUrl($_CONF['site_url'] . '/index.php?topic=' . $topics);
        $more_url=$_CONF['site_url'] . '/index.php?topic=' . ltrim(rtrim($topics,"'"),"'");
    }
    $list->set_var ('more_url', $more_url);
    $list->set_var ('more_title', $more_title);

    $list->parse ('output', 'list');
    $retval .= $list->finish ($list->get_var ('output'));

    return $retval;
}
//-----
function phpblock_shownewstories_news(
)
{
    return phpblock_shownewstories("news","news.rss");
}


function phpblock_showstaticpage(
     $id="blockpage"
	,$urlkey=""
    ,$mode=""
)
// +---------------------------------------------------------------------------+
// | 機能  ブロック用 静的ページ出力
// | 書式 phpblock_showstaticpage
// | 書式 phpblock_showstaticpage($id)
// | 書式 phpblock_showstaticpage($id,"yyyy")
// | 書式 phpblock_showstaticpage($id,"","home")
// | 書式 phpblock_showstaticpage($id,"","sub")
// | 書式 phpblock_showstaticpage($id,"yyyy","except")
// +---------------------------------------------------------------------------+
// | 引数 $id         :静的ページID
// | 引数 $urlkey     :URLにkeywordが含まれた場合に表示する
// |                   省略時は、無条件
// |                   mode=except の時は、キーワードを含まないサブページの場合
// | 引数 $mode       :home,sub,except それ以外はall
// +---------------------------------------------------------------------------+
// | 戻値 nomal:静的ページの内容                                               |
// +---------------------------------------------------------------------------+
// 20121116
{
	global $_CONF;
	global $_PLUGINS;
	
	if (!in_array('staticpages', $_PLUGINS)) {
		return "";
	}

    $retval ='';
	
	$pageurl ="";
	if  ($_SERVER["REQUEST_URI"]<>""){
		$pageurl = $_SERVER["REQUEST_URI"];
	}else{
		if  ($_SERVER['argc']>1){
			for ($i = 1; $i < count($_SERVER['argv']); $i++) {
				$pageurl .= $_SERVER['argv'][$i]." ";
			}
		}
	}
	
    if ($id==""){
        $id="blockpage";
	}else{
		$id=assist_switchlang($id);
	}
	$mode=strtoupper($mode);
	
	if ($mode=="HOME"){
		if  (COM_onFrontpage()){
			$retval .= assist_returnStaticpage( $id ,"autotag");
		}
	}elseif ($mode=="SUB"){
		if  (!COM_onFrontpage()){
			$retval .= assist_returnStaticpage( $id ,"autotag");
		}
	}elseif ($mode=="EXCEPT"){
		if  (COM_onFrontpage()){
		}else{
			if ($urlkey==""){
				$retval .= assist_returnStaticpage( $id ,"autotag");
			}else{
				if( strpos($pageurl, $urlkey) == FALSE ){
					$retval .= assist_returnStaticpage( $id ,"autotag");
				}
			}
		}
	}else{
		if ($urlkey==""){
			$retval .= assist_returnStaticpage( $id ,"autotag");
		}else{
			if( strpos($pageurl, $urlkey) !== FALSE ){
				$retval .= assist_returnStaticpage( $id ,"autotag");
			}
		}
	}
    return $retval;
}

//=====専用関数============================================================
//
function assist_getrss(
    $urlrss=''
	,$description_trim_length=0
	,$strip_tags=-1
    ,$thtml=""
)
{

	global $_CONF;
	
	$pi_name="assist";


	if  ($urlrss==""){
		$urlrss=$_CONF["site_url"]."/backend/geeklog.rss";
	}else{
		$urlrss="http://".$urlrss;
	}
	
	
    //テンプレートフォルダの設定
    $tmplfld=assist_templatePath('rss',$thtml,$pi_name);
    $list = new Template($tmplfld);

    $list->set_file (array (
        'list' => 'rss.thtml',
        'col'   => 'rss_col.thtml'
        ));
	
//echo "urlrss=".$urlrss."<br>";
	$retval = '';
	$values=assist_getfeedvalues($urlrss);
	
	$retval.="<ul>";
	foreach($values as $value){
		$tag=$value["tag"];
		$tag=strtolower($tag);
		$type=$value["type"];
		$level=$value["level"];
		$value=$value["value"];
		if  ($type=="open"){
			$pubdate="";
			$title="";
			$description="";
			$link="";
		}elseif  ($type=="close"){
			if ($tag=="item"){
				$list->set_var ('link', $link);
				$list->set_var ('title', $title);
				$list->set_var ('pubdate', $pubdate);
				$list->set_var ('description', $description);
		        $list->parse ('rss_col', 'col', true);

			}elseif  ($tag=="image"){
				$list->set_var ('image_link', $link);
				$list->set_var ('image_title', $title);
				$list->set_var ('image_url', $url);
			}
		}elseif  ($type=="complete" AND $level==4){
			switch($tag){
				case "title":
					$title=$value;
					break;
				case "link":
					$link=$value;
					break;
				case "url":
					$url=$value;
					break;
				case "description":
					if ($strip_tags){
						$value=strip_tags($value);
						if	($description_trim_length<>0 
							AND mb_strlen($value)>$description_trim_length){
							$value=mb_substr(
							$value,0,$description_trim_length)."...";
						}
					}
					$description=$value;
					break;
				case "pubdate":
					$dateary=COM_getUserDateTimeFormat($value);
					$pubdate=$dateary['0'];
					break;
			}
		}elseif  ($type=="complete" AND $level==3){
			switch($tag){
				case "title":
					$list->set_var ('head_link', $link);
					break;
				case "title":
					$list->set_var ('head_title', $value);
					break;
				case "description":
					$list->set_var ('head_description', $value);
					break;
				case "webmaster":
					$list->set_var ('head_webmaster', $value);
					break;
				case "copyright":
					$list->set_var ('head_copyright', $value);
					break;
				case "pubdate":
					$dateary=COM_getUserDateTimeFormat($value);
					$list->set_var ('head_copyright', $dateary['0']);
					break;
			}
		}
	}
    $list->parse ('output', 'list');
    $retval .= $list->finish ($list->get_var ('output'));
	
	return $retval;

}


function assist_getfeedvalues(
	$url
)
{

	global $_CONF;
    require_once $_CONF['path_system']
                 . '/classes/syndication/parserfactory.class.php';
    require_once $_CONF['path_system']
                 . '/classes/syndication/feedparserbase.class.php';

    $factory = new FeedParserFactory($_CONF['path_system']
                                     . '/classes/syndication/');
	$content = $factory->_getFeed($url);
	
    // XMLデータを配列に格納
	$xml_parser=xml_parser_create();
	xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE,1);
    $rt=xml_parse_into_struct($xml_parser,$content,$values,$idx);
	if  ($rt){
	}else{
		COM_errorLog("error xml_parse ".$urlrss ,1);
	}
	xml_parser_free($xml_parser);

	return $values;
}

function assist_returnStaticpage(
    $page=''
    , $mode=''
    , $comment_order = 'ASC'
    , $comment_mode = 'nested'
    , $msg = 0
)
// +---------------------------------------------------------------------------+
// | 機能  静的ページを返す                                                    |
// | 書式 assist_returnStaticpage ($id ,"autotag")
// +---------------------------------------------------------------------------+
// | 引数 $page         :静的ページID                                          |
// | 引数 $mode         :""                                                    |
// |                    :print                                                 |
// |                    :sutotag                                               |
// | 引数 $comment_order:"ASC"                                                 |
// | 引数 $comment_mode :"nested"                                              |
// | 引数 $msg          :0                                                     |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:静的ページの内容                                               |
// +---------------------------------------------------------------------------+
// 2010/03/19 modify SP_returnStaticpage (gl1.6.1)
{
    global $_TABLES;
    global $_PLUGINS;

    $retval = '';

    if (!in_array('staticpages', $_PLUGINS)) {
        return $retval;
    }
    if ($page==="") {
        return $retval;
    }

    $sql = "SELECT sp_php, sp_content FROM {$_TABLES['staticpage']} "
         . "WHERE (sp_id = '" . addslashes($page) . "') "
         . "AND " . SP_getPerms();
    $result = DB_query($sql);

    if (DB_error() OR (DB_numRows($result) == 0)) {

        return $retval;
    } else {
        $A = DB_fetchArray($result);

        if ($mode == 'print') {
            $retval = SP_printPage($page, $A);
		} else if ($mode =='autotag') {
			if  (COM_versionCompare(VERSION, "2.0.1",  '>=')){
				$retval = SP_render_content($page, stripslashes($A['sp_content']), $A['sp_php'], 0 , '');
			}else{
				$retval = SP_render_content(stripslashes($A['sp_content']), $A['sp_php']);
			}
		} else {
            $retval = SP_displayPage($page, $retval, $comment_order, $comment_mode, $msg);
        }

        // increment hit counter for page
        DB_query("UPDATE {$_TABLES['staticpage']} SET sp_hits = sp_hits + 1 WHERE sp_id = '$page'");

    }

    return $retval;
}

function assist_returnUser(
    $item
    , $uid=0
    ,$width=0
    ,$height=0
)

// +---------------------------------------------------------------------------+
// | 機能 ユーザ情報を返す                                                     |
// | 書式 assist_returnUser("homepage")                                        |
// +---------------------------------------------------------------------------+
// | 引数 $item         :項目名                                                |
// | 引数 $uid          :0 省略時　ログインユーザ                              |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:指定ユーザ、指定項目(個人情報)の内容                           |
// +---------------------------------------------------------------------------+
// 20110117
{
    global $_CONF;
    global $_TABLES;
    global $_USER;

    global $LANG_ASSIST;

    if ($uid==0) {
        $uid=$_USER['uid'];
    }
    $login_uid=$_USER['uid'];


    $rt="";
    if ($uid<>0){

        switch($item) {
            //-----
            case 'fullname':
            case 'username':
                $rt = DB_getItem(
                    $_TABLES['users']." AS t1 "
                    ,$item
                    ,"t1.uid = ".$uid
                );

                break;

            case 'sig':
            case 'homepage':
                $w = DB_getItem(
                    $_TABLES['users']." AS t1 "
                    ,$item
                    ,"t1.uid = ".$uid
                );
                $rt=assist_autolink($w);
                break;
            case 'about':
            case 'pgpkey':
            case 'location':
                $rt = DB_getItem(
                    $_TABLES['userinfo']." AS t1 "
                    ,$item
                    ,"t1.uid = ".$uid
                );
                break;
            case 'photo':
                $photo = DB_getItem(
                    $_TABLES['users']." AS t1 "
                    ,$item
                    ,"t1.uid = ".$uid
                );
                $w="";
                if ($photo<>""){
                    $w="<img src=\"".$_CONF['site_url']."/images/userphotos/".$photo."\"";
                    if ($width<>0){
                        $w.=" width=\"".$width."\" ";
                    }
                    if ($height<>0){
                        $w.=" height=\"".$height."\" ";
                    }
                    $w.=" class=\"uk-border-circle\">";
                } else if ($_CONF['default_photo']) {
                    $w="<img src=\"".$_CONF['default_photo']."\"";
                    if ($width<>0){
                        $w.=" width=\"".$width."\" ";
                    }
                    if ($height<>0){
                        $w.=" height=\"".$height."\" ";
                    }
                    $w.=" class=\"uk-border-circle\">";
		}
                $rt=$w;
                break;

        }
    }
    return $rt;
}

//==============================================================================

function assist_nl2br (
    $text
)
// +---------------------------------------------------------------------------+
// | 機能 改行をBRに変換                                                       |
// | 書式 assist_nl2br ($text)                                                 |
// +---------------------------------------------------------------------------+
// | 引数 $text         :文字列                                                |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:改行をBRに変換後の文字列                                       |
// +---------------------------------------------------------------------------+
// 2010/08/03 copy from  "hiroron - 改行をBRに変換"
{
    return PLG_replacetags ( str_replace(array("\r\n","\r","\n"), '<br'.XHTML.'>', $text) );

}
// +---------------------------------------------------------------------------+
// | 機能 urlをリンク変換                                                      |
// | 書式 assist_autolink($text)                                               |
// +---------------------------------------------------------------------------+
// | 引数 $text         :文字列                                                |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:改行をBRに変換後の文字列                                       |
// +---------------------------------------------------------------------------+
// 2010/08/03 copy from  "hiroron - 自動リンクを
function assist_autolink (
    $text
) {
    $patterns = array("/(https?|ftp)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/i");
    $replacements = array("<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>");
    return preg_replace($patterns, $replacements, $text);
}

// +---------------------------------------------------------------------------+
// | 機能  テンプレートフォルダパスを取得
// | 書式 databox_templatePath('data','default','databox')
// +---------------------------------------------------------------------------+
// | 引数 $kind:'data' 'category' ''newlist' 'admin'
// | 引数 $template:  'default'
// | 引数 $pi_name: 'databox' 'userbox' 'formbox'
// +---------------------------------------------------------------------------+
// | 戻値 :テンプレートフォルダパス
// +---------------------------------------------------------------------------+
// 20101227 copy from functions_databox.php
function assist_templatePath (
    $kind = 'data'
    ,$template ='default'
    ,$pi_name= 'assit'
    )
{

    global $_CONF;

    if (is_null($template) OR ($template==="")){
        $template="default";
    }

    if (is_null($pi_name)){
        $pi_name="databox";
    }

    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;


    if ($kind==="admin"){
        $conf_templates=$box_conf["templates_admin"];
    }else{
        $conf_templates=$box_conf["templates"];
    }



    //"standard";//標準テンプレートを使用する
    //"custom";//カスタムテンプレートを使用する
    //"theme";//テーマテンプレートを使用する
    if  ($conf_templates==="theme"){

        $tmplfld=$_CONF['path_layout'] .$box_conf['themespath'].$kind;
        if ($kind<>"admin"){

            $tmplfld.="/".$template;
        }
        if (is_dir($tmplfld)) {

        }else{
            COM_handle404();
            exit;
        }

    }else if  ($conf_templates==="custom"){

        $tmplfld=$_CONF['path'] .'plugins/'.$pi_name.'/custom/templates/'.$kind;

        if ($kind<>"admin"){

            $tmplfld.="/".$template;

        }

        if (is_dir($tmplfld)){

        }else{
			
            COM_handle404();
            exit;
        }
    }else{

        $tmplfld=$_CONF['path'] .'plugins/'.$pi_name.'/templates/'.$kind;
        if ($kind<>"admin"){

            $tmplfld.="/default";
        }

    }


    return $tmplfld;
}

// +---------------------------------------------------------------------------+
// | 機能  ラジヲボタン作成                                                    |
// | 書式 assit_getyesno
// |                ($lang_noyes,"afield[".$fid."]",$fvalue,$brx ,$disabled);
// +---------------------------------------------------------------------------+
// | 引数 $selected:選択値　省略時                                             |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:ラジヲボタン                                                   |
// +---------------------------------------------------------------------------+
function assist_getradiolist(
    $ary
    ,$fld
    ,$selected = 0
    ,$br=""
    ,$disabled=""
)
{
    $retval = '';

    foreach( $ary as $id => $value ){

        //echo "ary".$i."=".$ary[$i]."<br>";
        $retval .= "<input type='radio' NAME='$fld' $disabled VALUE='$id'";
        if( $id == $selected ){
                $retval .= " CHECKED=CHECKED ";
        }

        $retval .= ">$ary[$id]&nbsp".$br;
    }

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  ソーシャルボタン・ボックスのHTMlを返す
// | 書式 assist_btn($type,$thtml)
// | 		assist_btn('fblikebtn');
// | 		assist_btn('evclip');
// | 		assist_btn('fbcomment');
// | 		assist_btn('tweet');
// +---------------------------------------------------------------------------+
// | 引数 $type         :ボタンタイプ　省略値fblikebtn　type.thtml
// | 引数 $thtml        :テンプレートフォルダ名　省略時config値に準拠
// | 					 このフォルダは外部からアクセスできないようにすること
// +---------------------------------------------------------------------------+
// | 戻値 nomal:ソーシャルボタン・ボックスのHTMl
// +---------------------------------------------------------------------------+
// 2011/12/26
function assist_btn(
	$type =""
	,$thtml=""
)
{
	global $_CONF;
	
	$pi_name="assist";
	
	if ($type==""){
		$type="fblikebtn";
	}
	
	$retval = '';

    //テンプレートフォルダの設定
    $tmplfld=assist_templatePath('btn',$thtml,$pi_name);
	
	if (file_exists ($tmplfld."/".$type.".thtml")) {
	
		$btn = new Template($tmplfld);

		$btn->set_file (array (
			'btn' => $type.'.thtml',
			));
	
		$currenturl = COM_getCurrentURL();
		$appid=$_CONF['facebook_consumer_key'];
	
		$btn->set_var ('currenturl', htmlspecialchars($currenturl, ENT_QUOTES, 'UTF-8'));
		$btn->set_var ('appid', $appid);

		$btn->parse ('output', 'btn');
		$retval .= $btn->finish ($btn->get_var ('output'));
	}
	
	return $retval;

}

//テスト中----->
function assist_loginform(
	$mode
	,$logoutform=""
)
{
	global $LANG_ASSIST;
	global $LANG01;
	
	$return ="";
	
	if (strtoupper($logoutform)==="YES"){
		$logoutform=true;
	}
	
	if( COM_isAnonUser() ) {
		if ($mode==="facebook"){
			$return = assist_loginform_oauth($mode);
		}
	}else{
		if ($logoutform){
			$url = $_CONF['site_url'] . '/users.php?mode=logout';
            $url = COM_buildUrl( $url );
            $link= COM_createLink($LANG01[19], $url);
			$return = $link;
		}
	}
	return $return;
}

//
function assist_loginform_oauth(
	$service
)
{
    global $_CONF;
	
	$return="";
	
	if ($_CONF[$service.'_login']){
        $login = COM_newTemplate($_CONF['path_layout']);
		$login->set_file('oauth_login', 'loginform_oauth.thtml');
		$login->set_var('oauth_service', $service);
		// for sign in image
		$login->set_var('oauth_sign_in_image', $_CONF['site_url'] 
			. '/images/login-with-' . $service . '.png');
		$login->set_var('oauth_sign_in_image_style', '');
		$login->parse('output', 'oauth_login');
	
		$return= $login->finish($login->get_var('output'));
	}
	
	return $return;

}


//テスト中<-----

function assist_usercount_location ()
//location 別ユーザ数
{
    global $_TABLES;

    $rt="";

    $sql="SELECT t1.location ,count(t1.uid) AS num  ";
    $sql.=" FROM {$_TABLES['userinfo']} AS t1";
    $sql.=" WHERE t1.location<>''";
    $sql.=" GROUP BY location ";
    $sql.=" ORDER BY num DESC ";
    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);

    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
             $rt.=$A['location'].":".$A['num']."<br />".LB;
        }
    }
    return $rt;
}

//etc
function assist_arg (
    $item
)
{
    global $_CONF;
    global $_PLUGINS;

    global $LANG_ASSIST;
    global  $topic;
    global  $page;

    $rt="";
    switch( $item ) {

        case 'topic_id' :
            $rt=$topic;
            break;
        case 'sp_id' :
            if (in_array('staticpages', $_PLUGINS)) {
                $pageurl = COM_getCurrentURL();
                $staticpage=$_CONF['site_url']."/staticpages/index.php";
                if( strpos($pageurl, staticpage) !== FALSE ){
                    $rt=$page;
                }
            }

            break;
    }


    return $rt;
}


function assist_truncate(
	$text =''
	, $maxlen =''
	, $filler = ''
	, $endchars = '' )
//文字を指定数で区切る
{
	if  ($text==""){
		$rt="";
	}else{
		if  (!is_numeric($maxlen)){
			$maxlen=100;
		}
		if  (!is_numeric($endchars)){
			$endchars=0;
		}
		$rt=COM_truncate( $text, $maxlen, $filler, $endchars );
	}
    return $rt;
}
function assist_cache(
	$id =''
	, $type ='staticpages'
)
{
	global $_ASSIST_CONF;
	
	$ret="";
	if  ($type==""){
		$type="staticpages";
	}
	$path=$_ASSIST_CONF["path_cache"].$type."/".$id.".html";
	if  (file_exists($path)){
		$ret = @file_get_contents($path);
	}
	return $ret;
}

function assist_switchlang(
	$id
)
// +---------------------------------------------------------------------------+
// | 機能  ID多言語対応変換
// | 書式 assist_swichlang($id)
// |      xxxx_(言語id) 多言語モードの時に必要があれば言語idを現在の言語idに変換
// +---------------------------------------------------------------------------+
// | 引数 $id:ID
// +---------------------------------------------------------------------------+
// | 戻値 nomal:変換後のID
// +---------------------------------------------------------------------------+
{
	global $_CONF;
	$newlanguageid=assist_getnewlanguageid();
	if  ($newlanguageid<>""){
		if  ($id<>""){
			$lang_len = strlen($newlanguageid);
			$oldlanguageid=substr($id,-($lang_len));
			if  (array_key_exists($oldlanguageid, $_CONF['language_files'])){
				$id = substr_replace($id, $newlanguageid, -$lang_len);
			}
		}
	}
	return $id;
}

function assist_getnewlanguageid(
)
{
	global $_CONF;
	$rt="";
	
	$newlanguageid = COM_getLanguageId();
	if  ($newlanguageid<>""){
		$rt=$newlanguageid;
		$lang_len = strlen($newlanguageid);
        for ($i = 1; $i <= 10; $i++) {
                $names[]= "arg".$i;
        }
		COM_setArgNames($names);
		$w = strtolower(COM_applyFilter(COM_getArgument('arg1')));
		$w = substr($w,-($lang_len+1));
		if  (substr($w,0,1)=="_"){
			$lang = substr($w,-($lang_len));
			if (!empty($lang) && array_key_exists($lang, $_CONF['language_files'])) {
				$rt=$lang;
			}
		}
	}
	return $rt; 
}

function assist_staticpagelink(
	$sp_id
	,$title
)
{
	global $_CONF;
	global $_PLUGINS;
	global $_TABLES;
	
	if (!in_array('staticpages', $_PLUGINS)) {
		return "";
	}
	
	$rt="";
	if (! empty($sp_id)) {
		$sp_id=assist_switchlang($sp_id);
        $url = COM_buildUrl($_CONF['site_url']
                 . '/staticpages/index.php?page=' . $sp_id);
        if (empty($title)) {
             $title = stripslashes(DB_getItem($_TABLES['staticpage'],
                                             'sp_title', "sp_id = '$sp_id'"));
		}
        $rt = COM_createLink($title, $url);
	}
	return $rt; 
}
// assist_getYopics('article',$id);
function assist_getTopics(
    $type
    , $id
)
{
    global $_CONF;
    global $_TABLES;
    global $LANG_ASSIST;

    $retval = '';
    $retary = array();

    // Retrieve topic assignments
    $sql = "SELECT ".LB;
    $sql .= " t1.tid ".LB;
    $sql .= " ,t2.topic ".LB;
    $sql .= " ,t2.owner_id".LB;
    $sql .= " ,t2.group_id".LB;
    $sql .= " ,t2.perm_owner".LB;
    $sql .= " ,t2.perm_group".LB;
    $sql .= " ,t2.perm_members".LB;
	$sql .= " ,t2.perm_anon ".LB;
    $sql .= " FROM ".LB;
    $sql .= " {$_TABLES['topic_assignments']} AS t1".LB;
    $sql .= " ,{$_TABLES['topics']} AS t2".LB;
    $sql .= " WHERE t1.type = '$type' AND t1.id = '$id'".LB;
    $sql .= " AND t1.tid =t2.tid ".LB;
    
    $permission=1;
    $topics="";
    $topicslink="";
    $topicids="";
    $topicidslink="";
    $topicidspipeline="";
    $topics_li="";
    $topicids_li="";
    $topicslink_li="";
    $topicidslink_li="";
    
    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    
    for ($i = 0; $i < $numrows; $i++) {
        $A = DB_fetchArray ($result);
        $A = array_map('stripslashes', $A);
        $perm=SEC_hasAccess2($A);
        if  ($perm >= 2){
            $wurl = COM_buildUrl($_CONF['site_url'].'/index.php?topic=' . $A['tid']);
            $w=COM_createLink( $A['topic'],$wurl );
            $topicslink.=$w." ".$LANG_ASSIST['topic_separater'];
            $topicslink_li.="<li>".$w."</li> ";
            $w=COM_createLink( $A['tid'],$wurl );
            $topicidslink.=$w." ".$LANG_ASSIST['topic_separater_id'];
            $topicidslink_li.="<li>".$w."</li> ";
        }else{
            $topicslink.=$A['topic']." ".$LANG_ASSIST['topic_separater'];
            $topicidslink.=$A['tid']." ".$LANG_ASSIST['topic_separater_id'];
            $topicslink_li.="<li>".$A['topic']."</li> ";
            $topicidslink_li.="<li>".$A['tid']."</li> ";
        }
        $topics.=$A['topic']." ".$LANG_ASSIST['topic_separater'];
        $topicids.=$A['tid']." ".$LANG_ASSIST['topic_separater_id'];
        $topicidspipeline.=$A['tid']."|";
        $topics_li.="<li>".$A['topic']."</li> ";
        $topicids_li.="<li>".$A['tid']."</li> ";
    }
    $retary['topicslink']=rtrim($topicslink,$LANG_ASSIST['topic_separater']);//話題名列挙 リンク付き
    $retary['topicidslink']=rtrim($topicidslink,$LANG_ASSIST['topic_separater_id']);//話題ID列挙 リンク付き
    $retary['topics']=rtrim($topics,$LANG_ASSIST['topic_separater']);//話題名列挙
    $retary['topicids']=rtrim($topicids,$LANG_ASSIST['topic_separater_id']);//話題ID列挙
    $retary['topicidspipeline']=rtrim($topicidspipeline,"|");//話題ID列挙パイプライン区切
    
    $retary['topics_li']=$topics_li;//話題名列挙 liタグ区切り
    $retary['topicids_li']=$topicids_li;//話題ID列挙の区切り　liタグ区切り
    $retary['topicslink_li']=$topicslink_li;//話題ID列挙の区切り　liタグ区切り
    $retary['topicidslink_li']=$topicidslink_li;//話題ID列挙の区切り リンク付き　liタグ区切り 

    return $retary;
}

?>