<?php
/* Reminder: always indent with 4 spaces (no tabs). */

if (strpos ($_SERVER['PHP_SELF'], 'maps_markersupdate.inc') !== false) {
    die ('This file can not be used on its own.');
}
// +---------------------------------------------------------------------------+
// | 機能 maps markers truncate
// | （特定マップIDのマーカー削除）
// | 特定マップID：アトリビュート テーマ変数=「マップIDを登録するマスターの種別」
// | のマスターのvalue2
// | のマーカーをすべて削除する
// | 書式 fncmarkersdelete()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+

function fncmarkersclear()
{
    global $_TABLES;
    global $_CONF;
    global $_DATABOX_CONF;
    $maps_mid=$_DATABOX_CONF['maps_mid'];
    
    $sql = "SELECT ".LB;
    $sql .= " value2 ".LB;
    $sql .= " FROM ".LB;
    $sql .= " {$_TABLES['DATABOX_mst']} AS t1 ".LB;
    $sql .= " WHERE kind='{$maps_mid}'".LB;
    
    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    if ($numrows > 0) {
        $w="";
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
            $A = array_map('stripslashes', $A);
            if  ($A[value2]<>""){
                if ($w<>""){
                    $w.=",";
                }
                $w.=$A[value2];
            }
        }
        if  ($w<>""){
            $sql = "DELETE FROM {$_TABLES['maps_markers']} WHERE mid IN (".$w.")";
            DB_query($sql);
            if (DB_error()) {
                COM_errorLog("error DataBox maps markers clear ",1);
                $rt="error DataBox maps markers clear <br".XHTML.">";
            }
            COM_errorLog("Success - DataBox maps markers clear",1);
            $rt="Success - DataBox maps markers clear<br".XHTML.">";
        }else{
            $rt="Success - DataBox maps markers clear<br".XHTML.">";
        }
    }
    
    return $rt;
}

// +---------------------------------------------------------------------------+
// | 機能 maps markers truncate
// | （DataBox のデータでmapsプラグインのmarkerを作成する ）
// | （抽出条件）
// | 公開日＜＝現在＜公開終了日
// | 下書　オフ
// | （編集）
// | マーカー名：
// | 緯度：アトリビュート テーマ変数=lat のデータ
// | 経度：アトリビュート テーマ変数=lng のデータ
// | マップID：アトリビュート テーマ変数=「マップIDを登録するマスターの種別」
// |           のマスターのvalue2
// | 住所：アトリビュート テーマ変数=pref のvalue
// |       +" " +アトリビュート テーマ変数=address1 のvalue
// |       +" " +アトリビュート テーマ変数=address2 のvalue
// |      +" " +アトリビュート テーマ変数=address3 のvalue
// | Ressource ＃10：コード
// | mkid：自動採番     
// | 書式 fncmarkersupdate()
// +---------------------------------------------------------------------------+
// | 戻値 nomal:
// +---------------------------------------------------------------------------+
function fncmarkersupdate(
    $mode=""
    )
{
    $retval="";

    global $_CONF;
    global $_TABLES;

    global $_MAPS_CONF;
    global $_USER;
    global $_GROUPS;

    global $LANG_DATABOX_ADMIN;
    global $LANG_DATABOX;
     
    global $logmode,$logfile;
    
    global $_DATABOX_CONF;
    $maps_mid=$_DATABOX_CONF['maps_mid'];
    $maps_lat=$_DATABOX_CONF['maps_lat'];
    $maps_lng=$_DATABOX_CONF['maps_lng'];
    $maps_pref=$_DATABOX_CONF['maps_pref'];
    $maps_address1=$_DATABOX_CONF['maps_address1'];
    $maps_address2=$_DATABOX_CONF['maps_address2'];
    $maps_address3=$_DATABOX_CONF['maps_address3'];
    
    $owner_id=$_USER['uid'];
    $group_id=$_GROUPS['Maps Admin'];

    $array = array();
    SEC_setDefaultPermissions($array, $_MAPS_CONF['default_permissions']);
    $perm_owner = $array['perm_owner'];
    $perm_group = $array['perm_group'];
    $perm_anon = $array['perm_anon'];
    $perm_members = $array['perm_members'];
    
    $created = date("YmdHis");
    $modified = date("YmdHis");
    
    $pi_name="databox";
	
	if  ($mode=="scheduled" OR "CRON"){
	    $logentry="---->DataBox MapsUpdate ".$mode." processing Start";
	    $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
	}else{
	    $logentry="---->DataBox MapsUpdate URL processing Start";
	    $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
	}
    //-----
    $tbl1=$_TABLES['DATABOX_base'];
    $tbl2=$_TABLES['DATABOX_addition'];
    $tbl3=$_TABLES['DATABOX_def_field'];
    $tbl4=$_TABLES['DATABOX_mst'];
    //-----
    $field_id_mid=DB_getItem( $tbl3   ,"field_id","templatesetvar='{$maps_mid}'");
    $field_id_lat=DB_getItem( $tbl3   ,"field_id","templatesetvar='{$maps_lat}'");
    $field_id_lng=DB_getItem( $tbl3   ,"field_id","templatesetvar='{$maps_lng}'");
    $field_id_pref=DB_getItem( $tbl3   ,"field_id","templatesetvar='{$maps_pref}'");
    $field_id_address1=DB_getItem( $tbl3   ,"field_id","templatesetvar='{$maps_address1}'");
    $field_id_address2=DB_getItem( $tbl3   ,"field_id","templatesetvar='{$maps_address2}'");
    $field_id_address3=DB_getItem( $tbl3   ,"field_id","templatesetvar='{$maps_address3}'");
    //
    $err="";
    if  ($field_id_mid==""){
        COM_errorLog("error DataBox maps (".$maps_mid.") attribute missing",1);
        $err.="error DataBox maps (".$maps_mid.") attribute missing<br".XHTML.">";
    }
    if  ($field_id_pref==""){
        COM_errorLog("error DataBox maps pref attribute missing",1);
        $err.="error DataBox maps pref attribute missing<br".XHTML.">";
    }
    if  ($field_id_address1==""){
        COM_errorLog("error DataBox maps address1 attribute missing",1);
        $err.="error DataBox maps address1 attribute missing<br".XHTML.">";
    }
    if  ($err<>""){
        return $err;
    }
    //-----
    $sql = "SELECT DISTINCT".LB;
    $sql .= " t1.id ".LB;
    $sql .= " ,t1.code ".LB;
    $sql .= " ,t1.title ".LB;
    $sql .= " ,t1.description ".LB;
    
    $sql .= " ,t41.value2 AS mid".LB;
    $sql .= " ,t41.disp AS mk_icon".LB;
    $sql .= " ,t24.value AS pref".LB;
    $sql .= " ,t25.value AS address1".LB;
    $sql .= " FROM ".LB;
    $sql .= " {$tbl1} AS t1 ".LB;
    $sql .= " ,{$tbl2} AS t21 ".LB;
    $sql .= " ,{$tbl4} AS t41 ".LB;
    $sql .= " ,{$tbl2} AS t24 ".LB;
    $sql .= " ,{$tbl2} AS t25 ".LB;
    $sql .= " WHERE ".LB;
    //下書データはのぞく
    $sql .= " t1.draft_flag=0".LB;
    //公開日以前のデータはのぞく
    $sql .= " AND (t1.released <= NOW())".LB;
    //公開終了日を過ぎたデータはのぞく
    $sql .= " AND (t1.expired=0 OR t1.expired > NOW())";
    
    $sql .= " AND (t21.field_id=".$field_id_mid ." AND t21.id=t1.id ";
    $sql .= "     AND t41.kind = '{$maps_mid}' AND t21.value=t41.no)".LB;
    
    $sql .= " AND (t24.field_id=".$field_id_pref ." AND t24.id=t1.id )".LB;
    
    $sql .= " AND (t25.field_id=".$field_id_address1 ." AND t25.id=t1.id )".LB;
    
    $result = DB_query ($sql);
    $numrows = DB_numRows ($result);
    if ($numrows > 0) {
          $cnt=0;
          $cnt_cal=0;
          $cnt_upd=0;
          $cnt_err=0;
          $rt="";
          for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);

            $name=$A['title'];
            $description=$A['description'];//詳細

            $validity=$_MAPS_CONF['map_hidden'];//有効期限
            $active=$_MAPS_CONF['map_active'];
            $payed=$_MAPS_CONF['payed'];//ペイドマーカー

            if  ($field_id_address2==""){
                $address2="";
            }else{
                $address2=DB_getItem( $tbl2 ,"value","id=".$A['id']." AND field_id=".$field_id_address2);
            }
            if  ($field_id_address3==""){
                $address3="";
            }else{
                $address3=DB_getItem( $tbl2 ,"value","id=".$A['id']." AND field_id=".$field_id_address3);
            }
            $address=$A['pref'];
            $address.=" ".$A['address1'];
            $address.=" ".$address2;
            $address.=" ".$address3;
            
            if  ($field_id_lat==""){
                $lat="";
            }else{
                $lat=DB_getItem( $tbl2 ,"value","id=".$A['id']." AND field_id=".$field_id_lat);
            }
            
            if  ($field_id_lng==""){
                $lng="";
            }else{
                $lng=DB_getItem( $tbl2 ,"value","id=".$A['id']." AND field_id=".$field_id_lng);
            }
            $mid=$A['mid'];
            if  ($mid==0){
                continue;
            }
            $chkmid=DB_getItem($_TABLES['maps_maps'] ,"mid","mid=".$mid);
            if  ($chkmid==""){
                $cnt_err=$cnt_err+1;
                $logentry="Maps marker Mapid(".$mid.") INVALID "."id=".$A['id'];
                $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
                $rt.=$logentry."<br".XHTML.">";
                continue;
            }
            $mk_icon=$A['mk_icon'];
            if  ($mk_icon==""){
                $mk_icon=0;
            }
            // lat, lng can only contain numbers and a decimal
            if (empty($lat) || empty($lng)) {
                            $coords = MAPS_getCoords($address, $lat, $lng);
                if ($lat == 0 && $lng == 0) {
                    $cnt_err=$cnt_err+1;
                    $logentry="Maps marker Can not be calculated latitude and longitude "."id=".$A['id'];
                    $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
                    $rt.=$logentry."<br".XHTML.">";
                    continue;
                }else{
                    $cnt_cal=$cnt_cal+1;
                    $upd_flg=0;
                    //lat lng update
                    if  ($field_id_lat<>""){
                        $seq=DB_getItem( $tbl2 ,"seq","id=".$A['id']." AND field_id=".$field_id_lat);
                        if  ($seq<>""){
                            $upd_flg=1;
                            $sql="UPDATE ".$tbl2 ." SET ";
                            $sql.=" value ='".$lat."'";
                            $sql.=" WHERE seq=".$seq;
                            DB_query($sql);
                        }else{
                            $sql="INSERT INTO ".$tbl2 ." ( seq,id,field_id,value) ";
                            $sql.=" values ( NULL ,'".$A['id']."','".$field_id_lat."','".$lat."')";
                            DB_query($sql);
                        }
                    }
                    if  ($field_id_lng<>""){
                        $seq=DB_getItem( $tbl2 ,"seq","id=".$A['id']." AND field_id=".$field_id_lng);
                        if  ($seq<>""){
                            $upd_flg=1;
                            $sql="UPDATE ".$tbl2 ." SET ";
                            $sql.=" value ='".$lng."'";
                            $sql.=" WHERE seq=".$seq;
                            DB_query($sql);
                        }else{
                            $sql="INSERT INTO ".$tbl2 ." ( seq,id,field_id,value) ";
                            $sql.=" values ( NULL ,'".$A['id']."','".$field_id_lng."','".$lng."')";
                            DB_query($sql);
                        }
                    }
                    $cnt_upd=$cnt_upd+$upd_flg;
                }
            }
            $cnt=$cnt+1;
            $newmkid = date( 'YmdHis' ) .str_pad($cnt,4,"0",STR_PAD_LEFT);

            $remark="";//注意
            $street="";//番地
            $city="";//市
            $code="";//〒
            $state="";//都道府県
            $country="";//国
            $tel="";//TEL
            $fax="";//FAX
            $web="";//WEB
            $item_1="";//リソース #1
            $item_2="";//リソース #2
            $item_3="";//リソース #3
            $item_4="";//リソース #4
            $item_5="";//リソース #5
            $item_6="";//リソース #6
            $item_7="";//リソース #7
            $item_8="";//リソース #8
            $item_9="";//リソース #9
            $item_10=$A['code'];//リソース #10

            //-------
            $fields="mkid".LB;
            $values="'$newmkid'".LB;

            $fields.=",name".LB;
            $values.=",'$name'".LB;

            $fields.=",description".LB;
            $values.=",'$description'".LB;

            $fields.=",created".LB;
            $values.=",'$created'".LB;

            $fields.=",modified".LB;
            $values.=",'$modified'".LB;

            $fields.=",payed".LB;
            $values.=",'$payed'".LB;

            $fields.=",validity".LB;
            $values.=",'$validity'".LB;

            $fields.=",active".LB;
            $values.=",'$active'".LB;

            $fields.=",address".LB;
            $values.=",'$address'".LB;

            $fields.=",lat".LB;
            $values.=",'$lat'".LB;

            $fields.=",lng".LB;
            $values.=",'$lng'".LB;

            $fields.=",mid".LB;
            $values.=",'$mid'".LB;
            $fields.=",mk_icon".LB;
            $values.=",'$mk_icon'".LB;
            $fields.=",mk_default".LB;
            $values.=",'0'".LB;

            $fields.=",remark".LB;
            $values.=",'$remark'".LB;

            $fields.=",street".LB;
            $values.=",'$street'".LB;

            $fields.=",city".LB;
            $values.=",'$city'".LB;

            $fields.=",code".LB;
            $values.=",'$code'".LB;

            $fields.=",state".LB;
            $values.=",'$state'".LB;

            $fields.=",country".LB;
            $values.=",'$country'".LB;

            $fields.=",tel".LB;
            $values.=",'$tel'".LB;

            $fields.=",fax".LB;
            $values.=",'$fax'".LB;

            $fields.=",web".LB;
            $values.=",'$web'";

            $fields.=",item_1".LB;
            $values.=",'$item_1'".LB;
            $fields.=",item_2".LB;
            $values.=",'$item_2'".LB;
            $fields.=",item_3".LB;
            $values.=",'$item_3'".LB;
            $fields.=",item_4".LB;
            $values.=",'$item_4'".LB;
            $fields.=",item_5".LB;
            $values.=",'$item_5'".LB;
            $fields.=",item_6".LB;
            $values.=",'$item_6'".LB;
            $fields.=",item_7".LB;
            $values.=",'$item_7'".LB;
            $fields.=",item_8".LB;
            $values.=",'$item_8'".LB;
            $fields.=",item_9".LB;
            $values.=",'$item_9'".LB;
            $fields.=",item_10".LB;
            $values.=",'$item_10'".LB;

            $fields.=",owner_id".LB;
            $values.=",'$owner_id'".LB;

            $fields.=",group_id".LB;
            $values.=",'$group_id'".LB;

            $fields.=",perm_owner".LB;
            $values.=",'$perm_owner'".LB;

            $fields.=",perm_group".LB;
            $values.=",'$perm_group'".LB;

            $fields.=",perm_members".LB;
            $values.=",$perm_members".LB;

            $fields.=",perm_anon".LB;
            $values.=",$perm_anon".LB;

            $fields.=",submission".LB;
            $values.=",0".LB;
    
            //追加
            updateMap($mid);//maps plugins function
            DB_save($_TABLES['maps_markers'],$fields,$values);
            //$dummy=fncsendmail ('data',$id);
            $logentry="Maps marker Insert id: ".$newmkid." code:".$A['code'];
            //$dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
            $rt.=$logentry."<br".XHTML.">";
        }
        $logentry=sprintf($LANG_DATABOX_ADMIN['mapsmarkersupdateend'],$cnt,$cnt_err);
        $logentry.=" ".sprintf($LANG_DATABOX_ADMIN['mapsmarkersupdateend2'],$cnt_cal,$cnt_upd);
        $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
        $rt=$LANG_DATABOX_ADMIN['endmessage'].$logentry."<br".XHTML.">".$rt;
        $retval=$rt;
     }else{
          $retval=$LANG_DATABOX['notapplicable'];
     }
     if  ($mode=="scheduled"  OR $mode=="CRON"){
        $logentry="<----DataBox MapsUpdate ".$mode." processing End";
        $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
     }else{
        $logentry="<----DataBox MapsUpdate URL processing End";
        $dummy = DATABOX_OutLog( $logentry ,$logfile,$logmode);
     }
    
     return $retval;
}

?>
