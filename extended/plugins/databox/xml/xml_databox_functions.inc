<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | 機能  XMLを読み込みデータ出力
// | 書式 fncimportexec2 ($pi_name,$importfile,$defxml)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function databox_xmlimport(
    $pi_name
    ,$importfile
    ,$defxml
)
{
    $retval['ok']=0;
    $retval['ng']=0;
    $retval['msg']="";

    $pi_name="databox";
    global $_TABLES;
    
    $addition_def=DATABOX_getadditiondef($pi_name);

    // XMLファイルの読込み
    $content=@file_get_contents($importfile);
    if ($content==FALSE){
        $retval['ng']=1;
        $retval['msg'] = "Error ".$importfile." file error!";
        return  $retval;
    }

    // XMLデータを配列に格納
    $xml_parser=xml_parser_create();
    xml_parse_into_struct($xml_parser,$content,$vals);
    xml_parser_free($xml_parser);

    $vals2=array();
    for ($i = 1; $i <= count($vals); $i++) {
        $w=current($vals);
        if ($w[tag]<>"ROOT"){
            $vals2[$w[tag]]=$w[value];
        }
        next($vals);
    }

    //-----
    $tbl2=$_TABLES[strtoupper($pi_name).'_base'];

    $addition=array();
    $base=array();
    $category=array();
    
    for ($i = 0; $i < count($defxml); $i++) {
        
        $type=    $defxml[$i]['type'];
        $field=    $defxml[$i]['field'];
        $tag=    $defxml[$i]['tag'];
        //基本項目
        if ($type==="base"){
            $base[$field]=$vals2[$tag];
            //追加項目
        }else if ($type==="addition"  OR $type==="addition_v"){
            $value=$vals2[$tag];
            $additionfields[$field]=fnc_getfieldvalue2(
                $value
               ,$addition_def[$field]['type']
               ,$addition_def[$field]['selectionary']
               ,$lang_box_noyes
               ,$addition_def[$field]['selectlist']
               ,$type
                );
        //カテゴリ
        }else{
            $value=    $defxml[$i]['value'];
            if  ($value<>"" AND $value===$vals2[$tag]){
                $category[]=$field;
            }
        }
    }

    $id=$base['id'];
    $code=$base['code'];
    
    if ($id==""){
        if ($code<>""){
            $id=DATABOX_codetoid(
                $code,strtoupper($pi_name).'_base',"id");
        }
    }
    
    //存在すれば、先に削除しておく
    if ($id>0){
        $dummy=databox_deletedata ($id);
    }
    
    $rt=databox_Insert($base,$additionfields,$category,$addition_def);
    $retval['ok']=$rt['ok'];
    $retval['ng']=$rt['ng'];
    $retval['msg'].=$rt['msg'];
    return $retval;
}
// +---------------------------------------------------------------------------+
// | 機能  XMLを読み込みデータ出力
// | 書式 fncInsert()                                                     |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
//追加
function databox_Insert(
    $base
    ,$additionfields
    ,$category
    ,$addition_def
    )
{
    $retval["ng"]=0;
    $retval["ok"]=0;
    $retval["msg"]="";
    
    global $_CONF;
    global $_TABLES;

    global $_DATABOX_CONF;
    global $_USER;

    global $LANG_DATABOX_ADMIN;

    $pi_name="databox";
    
    //-----
    $title = $base['title'];
    $code= $base['code'];
    $fieldset_id= $base['fieldset_id'];
    if  ($fieldset_id==""){
        $fieldset_id=$_DATABOX_CONF['xml_default_fieldset_id'];
    }
    $page_title = $base['page_title'];
    $description=$base['description'];
    $defaulttemplatesdirectory=$base['defaulttemplatesdirectory'];
    if ($base['draft_flag']<>""){
        $draft_flag = $base['draft_flag'];
    }else{
        $draft_flag=$_DATABOX_CONF['admin_draft_default'];
    }

//            $hits =0;
//            $comments=0;


    //コメント終了日
    $comment_expire_flag = 0;
    $comment_expire_month = 0;
    $comment_expire_day = 0;
    $comment_expire_year = 0;
    $comment_expire_hour = 0;
    $comment_expire_minute = 0;

    //公開終了日
    $expired_flag=0;
    $expired_month = 0;
    $expired_day = 0;
    $expired_year = 0;
    $expired_hour = 0;
    $expired_minute = 0;

    $commentcode = 0;
    $trackbackcode =0;
    $meta_description = "";
    $meta_keywords = "";


    //@@@@@
    //$additionfields=DATABOX_cleanaddtiondatas($additionfields,$addition_def);
    $additionfields_fnm=array();
    $additionfields_del=array();
    $additionfields_date=array();
    $additionfields_alt=array();
    $additionfields=DATABOX_cleanaddtiondatas(
        $additionfields
        ,$addition_def
        ,$additionfields_fnm
        ,$additionfields_del
        ,$additionfields_date
        ,$additionfields_alt
        ,false
        );

     //
    $owner_id =$_USER['uid'];//@@@@@
    $group_id =$_DATABOX_CONF['grp_id_default'];

    //
    $array = array();
    SEC_setDefaultPermissions($array, $_DATABOX_CONF['default_perm']);
    $perm_owner = $array['perm_owner'];
    $perm_group = $array['perm_group'];
    $perm_anon = $array['perm_anon'];
    $perm_members = $array['perm_members'];

    //編集日付
    $modified_month = date('m');
    $modified_day = date('d');
    $modified_year = date('Y');
    $modified_hour = date('H');
    $modified_minute = date('i');

    //公開日
    $released_month=$modified_month;
    $released_day = $modified_day;
    $released_year = $modified_year;
    $released_hour = $modified_hour;
    $released_minute = $modified_minute;

    //公開終了日
    $expired_flag=0;
    $expired_month = 0;
    $expired_day = 0;
    $expired_year = 0;
    $expired_hour = 0;
    $expired_minute = 0;

    $orderno="0";
    $orderno = mb_convert_kana($orderno,"a");//全角英数字を半角英数字に変換する

    //$name = mb_convert_kana($name,"AKV");
    //A:半角英数字を全角英数字に変換する
    //K:半角カタカナを全角カタカナに変換する
    //V:濁点つきの文字を１文字に変換する (K、H と共に利用する）
    //$name = str_replace ("'", "’",$name);
    //$code = mb_convert_kana($code,"a");//全角英数字を半角英数字に変換する


    //-----
    $type=1;
    $uuid=$_USER['uid'];

    // CHECK　はじめ
    $err="";

    //タイトル必須
    if (empty($title)){
        $err.=$LANG_DATABOX_ADMIN['err_title']." ";
    }

    //コード必須
    if ($_DATABOX_CONF['datacode']){
        if (empty($code)){
            $err.=$LANG_DATABOX_ADMIN['err_code']." ";
        }
    }
    if ($code<>""){
         $cntsql="SELECT code FROM {$_TABLES['DATABOX_base']} ";
         $cntsql.=" WHERE ";
         $cntsql.=" code='{$code}' ";
         $result = DB_query ($cntsql);
         $numrows = DB_numRows ($result);
         if ($numrows<>0 ) {
             $err.=$code.":".$LANG_DATABOX_ADMIN['err_code_w']." ";
         }
    }



    //----追加項目チェック
    $err.=DATABOX_checkaddtiondatas($additionfields,$addition_def,$pi_name);

    //編集日付
    $modified=$modified_year."-".$modified_month."-".$modified_day;
    if (checkdate($modified_month, $modified_day, $modified_year)==false) {
       $err.=$LANG_DATABOX_ADMIN['err_modified']." ";
    }
    $w=COM_convertDate2Timestamp(
        $modified_year."-".$modified_month."-".$modified_day
        , $modified_hour.":".$modified_minute."::00"
        );
    $modified=date("Y-m-d H:i:s",$w);

    //公開日
    $released=$released_year."-".$released_month."-".$released_day;
    if (checkdate($released_month, $released_day, $released_year)==false) {
       $err.=$LANG_DATABOX_ADMIN['err_released']." ";
    }
    $w=COM_convertDate2Timestamp(
        $released_year."-".$released_month."-".$released_day
        , $released_hour.":".$released_minute."::00"
        );
    $released=date("Y-m-d H:i:s",$w);


    //コメント受付終了日時
    IF ($comment_expire_flag){
        if (checkdate($comment_expire_month, $comment_expire_day, $comment_expire_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_comment_expire']."<br/>"." ";
        }
        $w=COM_convertDate2Timestamp(
            $comment_expire_year."-".$comment_expire_month."-".$comment_expire_day
            , $comment_expire_hour.":".$comment_expire_minute."::00"
            );
        $comment_expire=date("Y-m-d H:i:s",$w);

    }else{
        $comment_expire='0000-00-00 00:00:00';

    }

    //公開終了日
    IF ($expired_flag){
        if (checkdate($expired_month, $expired_day, $expired_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_expired']."<br/>"." ";
        }
        $w=COM_convertDate2Timestamp(
            $expired_year."-".$expired_month."-".$expired_day
            , $expired_hour.":".$expired_minute."::00"
            );
        $expired=date("Y-m-d H:i:s",$w);
        if ($expired<$released) {
           $err.=$LANG_DATABOX_ADMIN['err_expired']."<br/>"." ";
        }


    }else{
        $expired='0000-00-00 00:00:00';
        //$expired="";
    }

    //errorのあるとき
    if ($err<>"") {
        //$err log file ni put no koto
        $retval["ng"]=1;
        $retval["msg"]=$err;
        return $retval;

    }
    // CHECK　おわり
    $w=DB_getItem($_TABLES['DATABOX_base'],"max(id)","1=1");
    if ($w=="") {
        $w=0;
    }
    $id=$w+1;
    $created=COM_convertDate2Timestamp(date("Y-m-d"),date("H:i::00"));

    //
    $hits=0;
    $comments=0;
    $fields="id";
    $values="$id";

    $fields.=",code";
    $values.=",'$code'";

    $fields.=",title";//
    $values.=",'$title'";

    $fields.=",page_title";//
    $values.=",'$page_title'";


    $fields.=",description";//
    $values.=",'$description'";

    $fields.=",defaulttemplatesdirectory";//
    $values.=",'$defaulttemplatesdirectory'";

    $fields.=",comments";//
    $values.=",$comments";

    $fields.=",meta_description";//
    $values.=",'$meta_description'";

    $fields.=",meta_keywords";//
    $values.=",'$meta_keywords'";

    $fields.=",commentcode";//
    $values.=",$commentcode";
    
    $fields.=",trackbackcode";//
    $values.=",$trackbackcode";

    $fields.=",comment_expire";//
    if ($comment_expire=='0000-00-00 00:00:00'){
        $values.=",'$comment_expire'";
    }else{
        $values.=",FROM_UNIXTIME('$comment_expire')";
    }
    
    $fields.=",language_id";//
    $values.=",'$language_id'";

    $fields.=",owner_id";
    $values.=",$owner_id";

    $fields.=",group_id";
    $values.=",$group_id";

    $fields.=",perm_owner";
    $values.=",$perm_owner";

    $fields.=",perm_group";
    $values.=",$perm_group";

    $fields.=",perm_members";
    $values.=",$perm_members";

    $fields.=",perm_anon";
    $values.=",$perm_anon";

    $fields.=",modified";
    $values.=",FROM_UNIXTIME('$modified')";
    if  ($created<>""){
        $fields.=",created";
        $values.=",FROM_UNIXTIME('$created')";
    }

    $fields.=",expired";
    if ($expired=='0000-00-00 00:00:00'){
        $values.=",'$expired'";
    }else{
        $values.=",FROM_UNIXTIME('$expired')";
    }
    $fields.=",released";
    $values.=",FROM_UNIXTIME('$released')";

    $fields.=",orderno";//
    $values.=",$orderno";
    
    if  ($fieldset_id<>""){
        $fields.=",fieldset_id";//
        $values.=",$fieldset_id";
    }

    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",draft_flag";
    $values.=",$draft_flag";

    $fields.=",udatetime";
    $values.=",NOW( )";

    DB_save($_TABLES['DATABOX_base'],$fields,$values);

    //カテゴリ
    $dummy=DATABOX_savecategorydatas($id,$category);

    //追加項目
    //DATABOX_uploadaddtiondatas_xml    
    //    ($additionfields,$addition_def,$pi_name,$additionfields_fnm);
    DATABOX_uploadaddtiondatas_xml    
        ($additionfields,$addition_def,$pi_name,$id);

    $dummy=DATABOX_saveaddtiondatas($id,$additionfields,$addition_def);

    $dummy=databox_sendmail ('data',$id);
    
    $retval["ok"]=1;
    $retval["msg"]="Insert id: ".$id." title:".$title;

    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  メール送信                                                          |
// | 書式 fncsendmail ()                                                       |
// +---------------------------------------------------------------------------+
// | 戻値 nomal:                                                               |
// +---------------------------------------------------------------------------+
function databox_sendmail (
    $m=""
    ,$id=0
    ,$title=""
    )
{
    global $_CONF;
    global $_TABLES;
    global $LANG_DATABOX_MAIL;
    global $LANG_DATABOX_ADMIN;
    global $_USER ;
    global $_DATABOX_CONF ;

    $pi_name="databox";

    $site_name=$_CONF['site_name'];
    $subject= $LANG_DATABOX_MAIL['subject_'.$m];
    $message=$LANG_DATABOX_MAIL['message_'.$m];

    if ($m==="data_delete"){
        $msg= $LANG_DATABOX_ADMIN['id'].":".$id.LB;
        $msg.= $LANG_DATABOX_ADMIN['title'].":".$title.LB;
        //URL
        $url=$_CONF['site_url'] . "/databox/data.php";
        $url = COM_buildUrl( $url );

    }else{
        $sql = "SELECT ";

        $sql .= " *";

        $sql .= " FROM ";
        $sql .= $_TABLES['DATABOX_base'];
        $sql .= " WHERE ";
        $sql .= " id = $id";

        $result = DB_query ($sql);
        $numrows = DB_numRows ($result);

        if ($numrows > 0) {

            $A = DB_fetchArray ($result);
            $A = array_map('stripslashes', $A);

            //下書
            if ($A['draft_flag']==1) {
                $msg.=$LANG_DATABOX_ADMIN['draft'].LB;
            }

            //基本項目
            $msg.= $LANG_DATABOX_ADMIN['id'].":".$A['code'].LB;
            $msg.= $LANG_DATABOX_ADMIN['code'].":".$A['code'].LB;
            $msg.= $LANG_DATABOX_ADMIN['title'].":".$A['title'].LB;
            $msg.= $LANG_DATABOX_ADMIN['page_title'].":".$A['page_title'].LB;
            $msg.= $LANG_DATABOX_ADMIN['description'].":".$A['description'].LB;

            $msg.= $LANG_DATABOX_ADMIN['hits'].":".$A['hits'].LB;
            $msg.= $LANG_DATABOX_ADMIN['comments'].":".$A['comments'].LB;
            $msg.= $LANG_DATABOX_ADMIN['meta_description'].":".$A['meta_description'].LB;
            $msg.= $LANG_DATABOX_ADMIN['meta_keywords'].":".$A['meta_keywords'].LB;
            $msg.= $LANG_DATABOX_ADMIN['commentcode'].":".$A['commentcode'].LB;
            $msg.= $LANG_DATABOX_ADMIN['comment_expire'].":".$A['comment_expire'].LB;

            // 準備中　$msg.=  $LANG_DATABOX_ADMIN['language_id'].":".$A['language_id'].LB;
            $msg.= $LANG_DATABOX_ADMIN['owner_id'].":".$A['owner_id'].LB;
            $msg.= $LANG_DATABOX_ADMIN['group_id'].":".$A['group_id'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_owner'].":".$A['perm_owner'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_group'].":".$A['perm_group'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_members'].":".$A['perm_members'].LB;
            $msg.= $LANG_DATABOX_ADMIN['perm_anon'].":".$A['perm_anon'].LB;

            $msg.= $LANG_DATABOX_ADMIN['modified'].":".$A['modified'].LB;
            $msg.= $LANG_DATABOX_ADMIN['created'].":".$A['created'].LB;
            $msg.= $LANG_DATABOX_ADMIN['expired'].":".$A['expired'].LB;
            $msg.= $LANG_DATABOX_ADMIN['released'].":".$A['released'].LB;

            $msg.= $LANG_DATABOX_ADMIN['orderno'].":".$A['orderno'].LB;

            $msg.= $LANG_DATABOX_ADMIN['draft'].":".$A['draft'].LB;
            $msg.= $LANG_DATABOX_ADMIN['udatetime'].":".$A['udatetime'].LB;
            $msg.= $LANG_DATABOX_ADMIN['uuid'].":".$A['uuid'].LB;

            //カテゴリ
            $msg.=DATABOX_getcategoriesText($id ,0,"DATABOX");

            //追加項目
            $group_id = stripslashes($A['group_id']);
            $owner_id = stripslashes($A['owner_id']);
            $chk_user=DATABOX_chkuser($group_id,$owner_id,"databox.admin");
            $addition_def=DATABOX_getadditiondef();
            $additionfields = DATABOX_getadditiondatas($id);
            $msg.=DATABOX_getaddtionfieldsText($additionfields,$addition_def,$chk_user,$pi_name,$A['fieldset_id']);

            //タイムスタンプ　更新ユーザ
            $msg.= $LANG_DATABOX_ADMIN['udatetime'].":".$A['udatetime'].LB;
            $msg.= $LANG_DATABOX_ADMIN['uuid'].":".$A['uuid'].LB;


            //URL
            $url=$_CONF['site_url'] . "/databox/data.php";
            $url.="?";
            if ($_DATABOX_CONF['datacode']){
                $url.="m=code";
                $url.="&code=".$A['code'];
            }else{
                $url.="m=id";
                $url.="&id=".$A['id'];
            }
            $url = COM_buildUrl( $url );

        }
    }

    if  (($_DATABOX_CONF['mail_to_draft']==0) AND ($A['draft_flag']==1)){
    }else{
        $message.=$msg.LB;
        $message.=$url.LB;
        $message.=$LANG_DATABOX_MAIL['sig'].LB;

        $mail_to=$_DATABOX_CONF['mail_to'];
        //--- to owner
        if  ($_DATABOX_CONF['mail_to_owner']==1){
            $owner_email=DB_getItem($_TABLES['users'],"email","uid=".$A['owner_id']);
            if (array_search($owner_email,$mail_to)===false){
                $to=$owner_email;
                COM_mail ($to, $subject, $message);
            }
        }
        //--- mail_to
        if  ($mail_to<>""){
            $to=implode($mail_to,",");
            COM_mail ($to, $subject, $message);
        }
    }
    return;
}




?>
