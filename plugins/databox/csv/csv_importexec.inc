<?php
/* Reminder: always indent with 4 spaces (no tabs). */

if (strpos ($_SERVER['PHP_SELF'], 'csv_importexec.inc') !== false) {
    die ('This file can not be used on its own.');
}

function databox_importexec(
    $pi_name
    ,$mode=""

)
/// +---------------------------------------------------------------------------+
// | 機能  インポート実行
// | 書式 fnc_importexec ($pi_name)
// +---------------------------------------------------------------------------+
// | 戻値 nomal: 結果画面
// +---------------------------------------------------------------------------+

{
    global $_CONF;

    global $logfile;
    global $logmode;
    global $_TABLES;
    
    $box_conf="_".strtoupper($pi_name)."_CONF";
    global $$box_conf;
    $box_conf=$$box_conf;

    $lang_box_admin="LANG_".strtoupper($pi_name)."_ADMIN";
    global $$lang_box_admin;
    $lang_box_admin=$$lang_box_admin;

    if  ($mode=="scheduled" OR $mode=="CRON"){
        $logentry="---->DataBox Csv import ".$mode." processing Start";
        $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
        $importurl = "";
        $csvsel = $box_conf['csv_cron_schedule_sel_id'];
    }else{
        $importurl = COM_applyFilter($_POST['importurl']);
        $csvsel = COM_applyFilter($_POST['csvsel']);
        $logentry="---->DataBox Csv import URL processing Start (".$csvsel.")";
        $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
    }
    
    if ($importurl===""){
        $importurl=$box_conf['path_csv'];
    }

    if (@fopen($importurl,'r')){
              $fchk=true;
        //fclose($importurl);
    }else{
        if  (file_exists($importurl)){
            $fchk=true;
        }else{
            $fchk=false;
        }
    }
    $cnt_ok=0;
    $cnt_ng=0;
    $cnt_ex=0;
    $rt_msg="";
    $tbl=$_TABLES[strtoupper($pi_name).'_def_csv'];
    
    $cnt=DB_count($tbl);
    if ($cnt>0){
        if  ($fchk){
            if (is_dir($importurl)){
                $fd=$importurl;
               $files=DATABOX_getfilelist($fd,"csv");
                for ($i = 1; $i <= count($files); $i++) {
                    $fl=$fd."/".current($files);

                    $logentry=$fl;
                    $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
                    $rt1=databox_csvimport($pi_name,$fl,$defcsv,$csvsel);
                                   
                    $cnt_ok=$cnt_ok+$rt1['ok'];
                    $cnt_ng=$cnt_ng+$rt1['ng'];
                    $cnt_ex=$cnt_ex+$rt1['ex'];
                    $wmsg=current($files)." ".$rt1['msg'];
                    //$dummy = DATABOX_OutLog( $wmsg ,$logfile,$logmode);
                    $rt_msg.=current($files)."<br ".XHTML .">".$rt1['msg']."<br ".XHTML .">";
                    if  ((($mode=="scheduled") AND  ($box_conf['csv_cron_schedule_unlink'] ==1)) 
                        OR ($mode=="unlink") 
						OR ($mode=="CRON") ){
                        $wrt=unlink ( $fl) ;
                        $wmsg=$fl." Unlink";
                        $dummy = DATABOX_OutLog( $wmsg ,$logfile,$logmode);
                    }
                    next($files);
                }
            }else{
                
                $rt1=databox_csvimport($pi_name,$fl,$defcsv,$csvsel);
                
                $cnt_ok=$cnt_ok+$rt1['ok'];
                $cnt_ng=$cnt_ng+$rt1['ng'];
                $cnt_ex=$cnt_ex+$rt1['ex'];
                $rt_msg.=$rt1['msg']."<br ".XHTML .">";
                $dummy = DATABOX_OutLog( $rt['msg'] ,$logfile,$logmode);
            }
        }else{
            $rt=$importurl." ". $lang_box_admin['err_not_exist'];
            $dummy = DATABOX_OutLog( $rt ,$logfile,$logmode);
        }
    }else{
        $rt="csv def data not exist";
    }
    if  ($mode=="scheduled"  OR  $mode=="CRON"){
        $logentry="<----DataBox Csv import ".$mode." processing End";
        $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
    }else{
        $logentry="<----DataBox Csv import URL processing End ";
        $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
    }
    
    $rt.=$LANG_DATABOX_ADMIN['jobend'];
    $rt.=sprintf ($LANG_DATABOX_ADMIN['cnt_ok'] , $cnt_ok);
    $rt.=sprintf ($LANG_DATABOX_ADMIN['cnt_ng'] , $cnt_ng);
    $rt.=sprintf ($LANG_DATABOX_ADMIN['cnt_ex'] , $cnt_ex);
    $rt.=$rt_msg;
    return $rt;

}
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | 機能  CSVを読み込みデータ出力
// | 書式 fncimportexec2 ($pi_name,$importfile,$defcsv)
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function databox_csvimport(
    $pi_name
    ,$importfile
    ,$defcsv
    ,$csvsel
)
{
    $retval['ok']=0;
    $retval['ng']=0;
    $retval['ex']=0;
    $retval['msg']="";
    $pi_name="databox";
    global $_TABLES;
    $table=$_TABLES[strtoupper($pi_name).'_base'];
    $addition_def=DATABOX_getadditiondef($pi_name);

    // CSV OPEN
     $fp   = fopen($importfile, "r");
    
    // CSV READ 
    $line1=true;
    while (($vals = fgetcsv($fp)) !== FALSE) {
        $base=array();
        $additionfields=array();
        $category=array();
        if  ($line1){
            $defcsv=array();
            $field_ary=array();
            fnc_GetFieldAry1($pi_name,$field_ary);
            fnc_GetFieldAry2($pi_name,$field_ary);
            for ($i = 0; $i < count($vals); $i++) {
                //-----
                $tbl1=$_TABLES[strtoupper($pi_name).'_def_csv'];
                //-----
                $sql = "SELECT ".LB;
                $sql .= " csvheader ".LB;
                $sql .= " ,value ".LB;
                $sql .= " ,field ".LB;
                //$sql .= " ,seq ".LB;
                $sql .= " FROM ".LB;
                $sql .= " {$tbl1} AS t1 ".LB;
                $sql .= " WHERE csvheader='$vals[$i]'".LB;
                $result = DB_query ($sql);
                $cnt = DB_numRows ($result);
                $result = DB_query ($sql);
                $numrows = DB_numRows ($result);
                if ($numrows > 0) {
                    $A = DB_fetchArray ($result);
                    $field=COM_applyFilter($A['field']);
                    if ($field_ary[$field]<>""){
                        $fld=explode(":",$field);
                        $defcsv[$i]['type']=$fld[0];
                        $defcsv[$i]['field']=$fld[1];
                        $defcsv[$i]['csvheader']=COM_applyFilter($A['csvheader']);
                        $defcsv[$i]['value']=COM_applyFilter($A['value']);
                    }
                }
            }
            $csvseldtl=array();
            if  ($csvsel<>0){
                for ($i = 0; $i < count($vals); $i++) {
                    //-----
                    $tbl2=$_TABLES[strtoupper($pi_name).'_def_csv_sel_dtl'];
                    //-----
                    $sql = "SELECT ".LB;
                    $sql .= " csvheader ".LB;
                    $sql .= " ,value ".LB;
                    $sql .= " FROM ".LB;
                    $sql .= " {$tbl2} AS t2 ".LB;
                    $sql .= " WHERE csv_sel_id =".$csvsel." AND csvheader='$vals[$i]'".LB;
                    $result = DB_query ($sql);
                    $numrows = DB_numRows ($result);
                    if ($numrows > 0) {
                        $A = DB_fetchArray ($result);
                        $w=COM_stripslashes($A['value']);
                        $w=explode(",",$w);
                        $csvseldtl[$i]=$w;
                   }
                }
            }
            $line1=false;
        }else{
            $selflg=true;
            if  ($csvsel<>0){
                for ($i = 0; $i < count($vals); $i++) {
                    $value=$vals[$i];
                    $ary=$csvseldtl[$i];
                    if  (is_array($ary)){ 
                        if  (in_array($value,$ary)==false){
                            $selflg=false;
                        }
                    }
                }
            }

            if  ($selflg){
                for ($i = 0; $i < count($vals); $i++) {
                    $type=    $defcsv[$i]['type'];
                    $field=    $defcsv[$i]['field'];
                    $csvheader=    $defcsv[$i]['csvheader'];
                    //基本項目
                    if ($type==="base"){
                        $base[$field]=$vals[$i];
                        //追加項目
                    }else if ($type==="addition"  OR $type==="addition_v"){
                        $value=$vals[$i];
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
                        if  ($vals[$i]<>""){
                            $field2=DB_getItem($tbl1,"field"
                                  ,"csvheader='".$defcsv[$i]['csvheader']."' AND value='".$vals[$i]."'");
                            if ($field2<>""){
                                $fld2=explode(":",$field2);
                                $category[]=$fld2[1];
                            }
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

                //存在すれば、データを更新
                $id=DB_getItem( $table   ,"id","id={$id}");
                if ($id>0){
                    //$dummy=databox_deletedata ($id);//入替モードの時は削除する
                    $rt=databox_Update($base,$additionfields,$category,$addition_def,$id);
                }else{
                    $rt=databox_Insert($base,$additionfields,$category,$addition_def);
                }
                $retval['ok']=$retval['ok']+$rt['ok'];
                $retval['ng']=$retval['ng']+$rt['ng'];
                $retval['msg'].=$rt['msg']."<br>";
            }else{
                $retval['ex']=$retval['ex']+1;
            }
        }
    }
    fclose($fp);
    return $retval;
}

// +---------------------------------------------------------------------------+
// | 機能  CSVを読み込みデータ出力
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
        $fieldset_id=$_DATABOX_CONF['csv_default_fieldset_id'];
    }
    $page_title = $base['page_title'];
    $description=$base['description'];
    $defaulttemplatesdirectory=$base['defaulttemplatesdirectory'];
    if ($base['draft_flag']<>""){
        $draft_flag = $base['draft_flag'];
    }else{
        $draft_flag=$_DATABOX_CONF['admin_draft_default'];
    }
    if ($base['owner_id']<>""){
        $owner_id = $base['owner_id'];
    }else{
        $owner_id=$_DATABOX_CONF['csv_default_owner_id'];
    }

    //コメント終了日
    $comment_expire_flag = 0;
    $comment_expire_month = 0;
    $comment_expire_day = 0;
    $comment_expire_year = 0;
    $comment_expire_hour = 0;
    $comment_expire_minute = 0;
    
    $commentcode =$_DATABOX_CONF['commentcode'];
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
    $ary_released=date_parse( $base['released']);
    if  ($ary_released[error_count]==0){
        $released_month = $ary_released[month];
        $released_day = $ary_released[day];
        $released_year = $ary_released[year];
        $released_hour = $ary_released[hour];
        $released_minute = $ary_released[minute];
    }else{
        $released_month = $modified_month;
        $released_day = $modified_day;
        $released_year = $modified_year;
        $released_hour = $modified_hour;
        $released_minute = $modified_minute;
    }
    
    //公開終了日
    if ($base['expired']==="0000-00-00 00:00:00"){
        $expired_flag=0;
    }else{    
        $ary_expired=date_parse( $base['expired']);
        if  ($ary_expired[error_count]==0){
            $expired_flag=true;
            $expired_month = $ary_expired[month];
            $expired_day = $ary_expired[day];
            $expired_year = $ary_expired[year];
            $expired_hour = $ary_expired[hour];
            $expired_minute = $ary_expired[minute];
            if  ($expired_hour==0 AND $expired_minute==0){
                $expired_hour=23;
                $expired_minute=59;
            }
        }else{
            $expired_flag=0;
        }
    }
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
    if (empty($_USER['uid'])) {
        $uuid = 1;
    } else {
        $uuid = $_USER['uid'];
    }

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
    $modified=COM_convertDate2Timestamp(
        $modified_year."-".$modified_month."-".$modified_day
        , $modified_hour.":".$modified_minute."::00"
        );

    //公開日
    $released=$released_year."-".$released_month."-".$released_day;
    if (checkdate($released_month, $released_day, $released_year)==false) {
       $err.=$LANG_DATABOX_ADMIN['err_released']." ";
    }
    $released=COM_convertDate2Timestamp(
        $released_year."-".$released_month."-".$released_day
        , $released_hour.":".$released_minute."::00"
        );

    //コメント受付終了日時
    IF ($comment_expire_flag){
        if (checkdate($comment_expire_month, $comment_expire_day, $comment_expire_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_comment_expire']."<br/>"." ";
        }
        $comment_expire=COM_convertDate2Timestamp(
            $comment_expire_year."-".$comment_expire_month."-".$comment_expire_day
            , $comment_expire_hour.":".$comment_expire_minute."::00"
            );

    }else{
        $comment_expire='0000-00-00 00:00:00';

    }

    //公開終了日
    IF ($expired_flag){
        if (checkdate($expired_month, $expired_day, $expired_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_expired']."<br/>"." ";
        }
        $expired=COM_convertDate2Timestamp(
            $expired_year."-".$expired_month."-".$expired_day
            , $expired_hour.":".$expired_minute."::00"
            );
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
        $values.=",FROM_UNIXTIME($comment_expire)";
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
    $values.=",FROM_UNIXTIME($modified)";
    if  ($created<>""){
        $fields.=",created";
        $values.=",FROM_UNIXTIME($created)";
    }

    $fields.=",expired";
    if ($expired=='0000-00-00 00:00:00'){
        $values.=",'$expired'";
    }else{
        $values.=",FROM_UNIXTIME($expired)";
    }
    $fields.=",released";
    $values.=",FROM_UNIXTIME($released)";
    
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
    $values.=",NOW()";
    DB_save($_TABLES['DATABOX_base'],$fields,$values);

    //カテゴリ
    $dummy=DATABOX_savecategorydatas($id,$category);

    //追加項目
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
        if (!empty ($mail_to)){
            $to=implode($mail_to,",");
            COM_mail ($to, $subject, $message);
        }
    }
    return;
}

// +---------------------------------------------------------------------------+
// | 機能  CSVを読み込みデータ更新
// | 書式 fncUpdate()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
//追加
function databox_Update(
    $base
    ,$additionfields
    ,$category
    ,$addition_def
    ,$id
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
    
    //現データ読
    $sql = "SELECT ".LB;

    $sql .= " t.*".LB;
            
    $sql .= " ,UNIX_TIMESTAMP(t.released) AS released_un".LB;
    $sql .= " ,UNIX_TIMESTAMP(t.comment_expire) AS comment_expire_un".LB;
    $sql .= " ,UNIX_TIMESTAMP(t.expired) AS expired_un".LB;
    $sql .= " ,UNIX_TIMESTAMP(t.created) AS created_un".LB;
            
    $sql .= " FROM ".LB;
    $sql .= $_TABLES['DATABOX_base'] ." AS t ".LB;
            
    $sql .= " WHERE ".LB;
    $sql .= " id = $id".LB;
            
    $result = DB_query($sql);

    $A = DB_fetchArray($result);
            
    $fieldset_id = COM_stripslashes($A['fieldset_id']);

    $code = COM_stripslashes($A['code']);
    $title=COM_stripslashes($A['title']);
    $page_title=COM_stripslashes($A['page_title']);
    $description=COM_stripslashes($A['description']);
    $defaulttemplatesdirectory=COM_stripslashes($A['defaulttemplatesdirectory']);
    $eyechatchiimage=COM_stripslashes($A['eyechatchimage']);

    $comments = COM_stripslashes($A['comments']);
    $comment_expire = COM_stripslashes($A['comment_expire']);
    if ($comment_expire==="0000-00-00 00:00:00"){
        $comment_expire_flag=0;
        $w = mktime(0, 0, 0, date('m'),
        date('d') + $_CONF['article_comment_close_days'], date('Y'));
        $comment_expire_year=date('Y', $w);
        $comment_expire_month=date('m', $w);
        $comment_expire_day=date('d', $w);
        $comment_expire_hour=0;
        $comment_expire_minute=0;
    }else{
        $comment_expire_flag=1;
        $wary = COM_getUserDateTimeFormat(COM_stripslashes($A['comment_expire_un']));
        $comment_expire = $wary[1];
        $comment_expire_year=date('Y', $comment_expire);
        $comment_expire_month=date('m', $comment_expire);
        $comment_expire_day=date('d', $comment_expire);
        $comment_expire_hour=date('H', $comment_expire);
        $comment_expire_minute=date('i', $comment_expire);
    }

    $commentcode = COM_stripslashes($A['commentcode']);
    $trackbackcode = COM_stripslashes($A['trackbackcode']);

    $meta_description = COM_stripslashes($A['meta_description']);
    $meta_keywords = COM_stripslashes($A['meta_keywords']);

    $language_id = COM_stripslashes($A['language_id']);

    $owner_id = COM_stripslashes($A['owner_id']);
    $group_id = COM_stripslashes($A['group_id']);

    $perm_owner = COM_stripslashes($A['perm_owner']);
    $perm_group = COM_stripslashes($A['perm_group']);
    $perm_members = COM_stripslashes($A['perm_members']);
    $perm_anon = COM_stripslashes($A['perm_anon']);
    
    $draft_flag=COM_stripslashes($A['draft_flag']);

    //公開日
    $wary = COM_getUserDateTimeFormat(COM_stripslashes($A['released_un']));
    $released = $wary[1];
    //$released = strtotime(COM_stripslashes($A['released']));
    $released_month = date('m', $released);
    $released_day = date('d', $released);
    $released_year = date('Y', $released);
    $released_hour = date('H', $released);
    $released_minute = date('i', $released);
    //公開終了日
    $expired = COM_stripslashes($A['expired']);
    if ($expired==="0000-00-00 00:00:00"){
        $expired_flag=0;
        $w = mktime(0, 0, 0, date('m'),
        date('d') + $_CONF['article_comment_close_days'], date('Y'));
        $expired_year=date('Y', $w);
        $expired_month=date('m', $w);
        $expired_day=date('d', $w);
        $expired_hour=0;
        $expired_minute=0;
    }else{
        $expired_flag=1;
        $wary = COM_getUserDateTimeFormat(COM_stripslashes($A['expired_un']));
        $expired = $wary[1];
        $expired_year=date('Y', $expired);
        $expired_month=date('m', $expired);
        $expired_day=date('d', $expired);
        $expired_hour=date('H', $expired);
        $expired_minute=date('i', $expired);
    }

    //作成日付
    $created = COM_applyFilter($A['created_un']);
    
    $orderno=COM_stripslashes($A['orderno']);

    //-----コード、タイトル、公開日、公開終了日
    if  ($base['title']<>""){
        $title = $base['title'];
    }
    if  ($base['code']<>""){
        $code= $base['code'];
    }
    //公開日
    if  ($base['released']<>""){
        $ary_released=date_parse( $base['released']);
        if  ($ary_released[error_count]==0){
            $released_month = $ary_released[month];
            $released_day = $ary_released[day];
            $released_year = $ary_released[year];
            $released_hour = $ary_released[hour];
            $released_minute = $ary_released[minute];
        }
    }
    //公開終了日
    if  ($base['expired']<>""){
        if ($base['expired']==="0000-00-00 00:00:00"){
            $expired_flag=0;
        }else{    
            $expired_flag=1;
            $ary_expired=date_parse( $base['expired']);
            if  ($ary_expired[error_count]==0){
                $expired_flag=true;
                $expired_month = $ary_expired[month];
                $expired_day = $ary_expired[day];
                $expired_year = $ary_expired[year];
                $expired_hour = $ary_expired[hour];
                $expired_minute = $ary_expired[minute];
                if  ($expired_hour==0 AND $expired_minute==0){
                    $expired_hour=23;
                    $expired_minute=59;
                }
            }
        }
    }
    //ドラフト、ページタイトル、説明、テンプレートディレクトリ
    if  ($base['draft_flag']<>""){
        $draft_flag = $base['draft_flag'];
    }
    if  ($base['page_title']<>""){
        $page_title = $base['page_title'];
    }
    if  ($base['description']<>""){
        $description = $base['description'];
    }
    if  ($base['defaulttemplatesdirectory']<>""){
        $defaulttemplatesdirectory = $base['defaulttemplatesdirectory'];
    }
    if  ($base['owner_id']<>""){
        $owner_id = $base['owner_id'];
    }

    //attribute 追加項目
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

    //-----
    if (empty($_USER['uid'])) {
        $uuid = 1;
    } else {
        $uuid = $_USER['uid'];
    }

    // CHECK　はじめ
    $err="";

    //コード必須
    if ($_DATABOX_CONF['datacode']){
        if (empty($code)){
            $err.=$LANG_DATABOX_ADMIN['err_code']." ";
        }
    }
    if ($code<>""){
        $code=rtrim(ltrim($code));
        $newcode=COM_sanitizeID($code,false);
        if  ($code<>$newcode){
            $err.=$code.":".$LANG_DATABOX_ADMIN['err_code_x']."<br/>".LB;
        }else{
            $cntsql="SELECT code FROM {$_TABLES['DATABOX_base']} ";
            $cntsql.=" WHERE ";
            $cntsql.=" code='{$code}' ";
            $cntsql.=" AND id<>'{$id}' ";
            $result = DB_query ($cntsql);
            $numrows = DB_numRows ($result);
            if ($numrows<>0 ) {
                $err.=$code.":".$LANG_DATABOX_ADMIN['err_code_w']." ";
            }
        }
    }

    //----追加項目チェック
    $err.=DATABOX_checkaddtiondatas($additionfields,$addition_def,$pi_name);

    //編集日付
    $modified=COM_convertDate2Timestamp(date("Y-m-d"),date("H:i::00"));

    //公開日
    $released=$released_year."-".$released_month."-".$released_day;
    if (checkdate($released_month, $released_day, $released_year)==false) {
       $err.=$LANG_DATABOX_ADMIN['err_released']." ";
    }
    $released=COM_convertDate2Timestamp(
        $released_year."-".$released_month."-".$released_day
        , $released_hour.":".$released_minute."::00"
        );

    //コメント受付終了日時
    IF ($comment_expire_flag){
        if (checkdate($comment_expire_month, $comment_expire_day, $comment_expire_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_comment_expire']."<br/>"." ";
        }
        $comment_expire=COM_convertDate2Timestamp(
            $comment_expire_year."-".$comment_expire_month."-".$comment_expire_day
            , $comment_expire_hour.":".$comment_expire_minute."::00"
            );

    }else{
        $comment_expire='0000-00-00 00:00:00';

    }

    //公開終了日
    IF ($expired_flag){
        if (checkdate($expired_month, $expired_day, $expired_year)==false) {

           $err.=$LANG_DATABOX_ADMIN['err_expired']."<br/>"." ";
        }
        $expired=COM_convertDate2Timestamp(
            $expired_year."-".$expired_month."-".$expired_day
            , $expired_hour.":".$expired_minute."::00"
            );
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

    //
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
        $values.=",FROM_UNIXTIME($comment_expire)";
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
    $values.=",FROM_UNIXTIME($modified)";
    
    $fields.=",created";
    $values.=",FROM_UNIXTIME($created)";

    $fields.=",expired";
    if ($expired=='0000-00-00 00:00:00'){
        $values.=",'$expired'";
    }else{
        $values.=",FROM_UNIXTIME($expired)";
    }
    $fields.=",released";
    $values.=",FROM_UNIXTIME($released)";
    
    $fields.=",orderno";//
    $values.=",$orderno";
    
    $fields.=",fieldset_id";//
    $values.=",$fieldset_id";

    $fields.=",uuid";
    $values.=",$uuid";

    $fields.=",draft_flag";
    $values.=",$draft_flag";

    $fields.=",udatetime";
    $values.=",NOW()";
    DB_save($_TABLES['DATABOX_base'],$fields,$values);

    //カテゴリ
    $dummy=DATABOX_savecategorydatas($id,$category);

    //追加項目
    DATABOX_uploadaddtiondatas_xml
        ($additionfields,$addition_def,$pi_name,$id);
    $dummy=DATABOX_saveaddtiondatas_update($id,$additionfields,$addition_def,$pi_name);
    $dummy=databox_sendmail ('data',$id);
	
    $cacheInstance = 'databox__' . $id . '__' ;
    CACHE_remove_instance($cacheInstance); 
    
    $retval["ok"]=1;
    $retval["msg"]="Update id: ".$id." title:".$title;

    return $retval;
}

?>
