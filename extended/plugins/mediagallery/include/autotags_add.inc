<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | autotags_add.php                                                         |
// |                                                                          |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2013 by the following authors:                             |
// |                                                                          |
// | Tsuchi          tsuchitani AT ivywe DOT co DOT jp                        |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== false) {
    die('This file can not be used on its own!');
}

function MG_medialist (
	$album_id
	, $class=''
	, $theme='default'
    , $limitcnt='0'
    , $sort='asc'
    , $src=''
)
{
	global $MG_albums;
    global $_TABLES;
	global $_MG_CONF;
    global $_IMAGE_TYPE;
    global $LANG_MG01;
	global $_CONF;
	
	$rt="";
    if (empty($album_id)) return $rt;
	if  (empty($theme)){
		$theme='default';
	}
	if  (empty($limitcnt)){
		$limitcnt=0;
	}
	if  (strtoupper($sort)=="DESC"){
		$sort="DESC";
	}else{
		$sort="ASC";
	}
	
	$tmplfld=$_MG_CONF['template_path']."/medialist/".$theme;
    if (is_dir($tmplfld)) {
    }else{
        $tmplfld=$_MG_CONF['template_path']."/medialist/default";
	} 
	$src=trim($src);
	if  ($src=='orig' OR $src=='disp' OR $src=='tn'){
	}else{
        $src = $_MG_CONF['at_src'];
    }
    $T = COM_newTemplate($tmplfld);
    $T->set_file (array (
        'tag' => 'autotag_medialist.thtml',
        'col' => 'autotag_medialist_col.thtml',
        ));
	
	$sql = "SELECT ";
	$sql .= " m.media_filename".LB;
	$sql .= " ,m.media_id".LB;
	$sql .= " ,m.media_mime_ext".LB;
	$sql .= " ,m.media_title ".LB;
	$sql .= " ,m.media_desc ".LB;
	$sql .= " ,m.media_keywords ".LB;
	$sql .= " ,m.media_category ".LB;
	$sql .= " ,m.media_time ".LB;
	$sql .= " ,a.album_title ".LB;
	$sql .= " ,a.album_desc ".LB;
	$sql .= " ,c.cat_name ".LB;
	$sql .= " ,c.cat_description ".LB;
    $sql .= " FROM {$_TABLES['mg_media_albums']} as ma ".LB;
    $sql .= " INNER JOIN " . $_TABLES['mg_media'] . " as m ON ma.media_id=m.media_id ".LB;
    $sql .= " INNER JOIN " . $_TABLES['mg_albums'] . " as a ON ma.album_id=a.album_id ".LB;
    $sql .= " LEFT JOIN " . $_TABLES['mg_category'] . " as c ON m.media_category=c.cat_id ".LB;
	$sql .= " WHERE ".LB;
	$sql .= " ma.album_id='" . addslashes($album_id) . "' ".LB;
	$sql .= " AND m.media_type=0 ".LB;
	$sql .= " AND m.include_ss=1 ".LB;
	$sql .= " ORDER BY ma.media_order ".$sort.LB;
	if  ($limitcnt==0){
	}else{
		$sql .= " LIMIT $limitcnt".LB;
	}
	$result = DB_query($sql);
	while ($row = DB_fetchArray($result)) {
        if  ($row['media_type']==0)  {
        }else{
            continue;
        }
		$fn ='/'.$row['media_filename'][0].'/'.$row['media_filename'];
		$filename_orig=$_MG_CONF['mediaobjects_url'].'/'."orig".$fn.".".$row['media_mime_ext'];
		//
		$wk=$_MG_CONF['path_mediaobjects']."disp".$fn;
		$tnext = MG_getMediaExt($wk);
        $media_size = @getimagesize($wk . $tnext);
        $ext = substr($tnext,1,3); 
        if ($media_size == false) {
			$filename_disp="";
		}else{
			$filename_disp=$_MG_CONF['mediaobjects_url'].'/'."disp".$fn. '.' . $ext;
		}
		//
		$wk=$_MG_CONF['path_mediaobjects']."tn".$fn.'_cropcustom';
		$tnext = MG_getMediaExt($wk);
        $media_size = @getimagesize($wk . $tnext);
        $ext = substr($tnext,1,3); 
        if ($media_size == false) {
			$filename_tn="";
		}else{
			$filename_tn=$_MG_CONF['mediaobjects_url'].'/'."tn".$fn.'_cropcustom'. '.' . $ext;
		}
		if  ($src=="orig"){
			$filename=$filename_orig;
		}else if ($src=="disp"){
			$filename=$filename_disp;
		}else{
			$filename=$filename_tn;
		}
		$media_id=$row['media_id'];
		$media_filename=$row['media_filename'];
		$media_title= $row['media_title'];
		$media_desc= $row['media_desc'];
		$media_keywords= $row['media_keywords'];
		$media_category= $row['media_category'];
		$media_time=MG_getUserDateTimeFormat($row['media_time']);
        $album_title = $row['album_title'];
        $album_desc = $row['album_desc'];
        $media_category_name = $row['cat_name'];
        $media_category_description= $row['cat_description'];

		$T->set_var('class',$class);
		$T->set_var('album_id',$album_id);
		$T->set_var('album_title',$album_title);
		$T->set_var('album_desc',$album_desc);
		if ( SEC_hasRights('mediagallery.admin') ){
			$icon_url = $_CONF['layout_url'] . '/images/edit.' . $_IMAGE_TYPE;
            $attr = array('title' => $album_title." ".$LANG_MG01['edit']);
            $editiconhtml = COM_createImage($icon_url, $LANG_MG01['edit'], $attr);
            $attr = array('class' => 'editlink', 'title' => $album_title." ".$LANG_MG01['edit']);
            $url = $_CONF['site_url'];
            $url .= "/mediagallery/admin.php?album_id={$album_id}&amp;mode=";
            $edit = '&nbsp;' ;
            $edit .=  COM_createLink( $editiconhtml,  $url . "media",   $attr   );
            $edit_album = '&nbsp;' ;
            $edit_album .=  COM_createLink( $editiconhtml,  $url . "edit",   $attr   );
		}else{
			$edit="";
			$edit_album="";
		}
		$T->set_var('edit',$edit);
		$T->set_var('edit_album',$edit_album);
		
		$T->set_var('filename',$filename);
		$T->set_var('filename_tn',$filename_tn);
		$T->set_var('filename_disp',$filename_disp);
		$T->set_var('filename_orig',$filename_orig);
		$T->set_var('media_id',$media_id);
		$T->set_var('media_filename',$media_filename);
		$T->set_var('mediatitle',$media_title);
		$T->set_var('media_title',$media_title);
		$T->set_var('media_desc',$media_desc);
		$T->set_var('media_keywords',$media_keywords);
		$T->set_var('media_category',$media_category);
		$T->set_var('media_category_name',$media_category_name);
		$T->set_var('media_category_description',$media_category_description);
		$T->set_var('released',$media_time[0]);
		
		//=====
        $T->parse ('medialist_col', 'col', true);

	}
	$T->parse ('output', 'tag');
    $rt= $T->finish ($T->get_var ('output'));
	return $rt;
}

function MG_albumlist (
	$album_id=0
	, $limitcnt = 0
	, $order = ''
	, $class=''
	, $theme='default'
)
{
    global $_CONF;
	global $_TABLES;
	global $LANG_MG01;
	global $_MG_CONF;
    global $_IMAGE_TYPE;
	
	//global $MG_albums;
	
	$rt="";
	
	if  ($album_id==0 OR $album_id=="") {
		return $rt;
	}
	if  ($limitcnt=="") {
		$limitcnt=0;
	}
	if  (strtoupper($order) == "RANDOM"  OR $order =="") {
		$orderby="RAND()";
	}else{
		$orderby="album_id";
	}
	
	if  (empty($theme)){
		$theme='default';
	}
	$tmplfld=$_MG_CONF['template_path']."/albumlist/".$theme;
    if (is_dir($tmplfld)) {
    }else{
        $tmplfld=$_MG_CONF['template_path']."/albumlist/default";
    }
    //$T = MG_templateInstance($tmplfld);
    $T = COM_newTemplate($tmplfld);
    $T->set_file (array (
        'tag' => 'autotag_albumlist.thtml',
        'col' => 'autotag_albumlist_col.thtml',
        ));
	
	$sql = "SELECT ".LB;
	$sql .= " album_id ".LB;
	$sql .= " ,album_title ".LB;
	$sql .= " ,album_desc ".LB;
	$sql .= " ,album_cover ".LB;
	$sql .= " ,album_cover_filename ".LB;
	$sql .= " ,last_update ".LB;
	
	$sql .= " FROM {$_TABLES['mg_albums']} ".LB;
	$sql .= " WHERE album_parent=" . $album_id .LB;
	$sql .= " AND hidden=0".LB;
	$sql .= " ORDER BY $orderby".LB;
	if  ($limitcnt==0){
	}else{
        $sql .= " LIMIT $limitcnt".LB;
	}
	$result = DB_query ($sql);
    $numrows = DB_numRows ($result);
	$display="";
	if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
            $A = DB_fetchArray ($result);
			$A = array_map('stripslashes', $A);
			$img=MG_albumlastimage($A['album_cover_filename']);
			$album_last_update = MG_getUserDateTimeFormat($A['last_update']);
			
	        $url=$_CONF['site_url'] . "/mediagallery/album.php";
			$url.="?";
            $url.="aid=".$A['album_id'];
            $url.="&amp;page=1";
			
			$T->set_var('class',$class);
			$T->set_var('album_url',$url);
			$T->set_var('album_img',$img);
			$T->set_var('album_id',$A['album_id']);
			$T->set_var('album_title',$A['album_title']);
			$T->set_var('album_desc',$A['album_desc']);
			$T->set_var('album_cover',$A['album_cover']);
			$T->set_var('album_cover_filename',$A['album_cover_filename']);
			$T->set_var('released',$album_last_update[0]);
			if ( SEC_hasRights('mediagallery.admin') ){
				$icon_url = $_CONF['layout_url'] . '/images/edit.' . $_IMAGE_TYPE;
				$attr = array('title' => $A['album_title']." ".$LANG_MG01['edit']);
				$editiconhtml = COM_createImage($icon_url, $LANG_MG01['edit'], $attr);
				$attr = array('class' => 'editlink'
				              , 'title' => $A['album_title']." ".$LANG_MG01['edit']);
				$url = $_CONF['site_url'];
				$url .= "/mediagallery/admin.php?album_id={$album_id}&amp;mode=";
				$edit = '&nbsp;' ;
				$edit .=  COM_createLink( $editiconhtml,  $url . "edit",   $attr   );
			}else{
				$edit="";
			}
			$T->set_var('edit',$edit);
		}
		
		//=====
        $T->parse ('albumlist_col', 'col', true);

	}
	$T->parse ('output', 'tag');
    $rt= $T->finish ($T->get_var ('output'));
	return $rt;
	
}
function MG_albumlastimage(
	$filename
)
{
	global $_MG_CONF;
	
    if ( $filename == '' ) {
		$album_last_image = $_MG_CONF['mediaobjects_url'] . '/empty.svg';
        //$mediasize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'empty.svg');
    } else {
        $mediasize = false;
		foreach ($_MG_CONF['validExtensions'] as $ext ) {
           $fn='tn/' . $filename[0] .'/' . $filename .'_cropcustom'. $ext;
           if ( file_exists($_MG_CONF['path_mediaobjects'] . $fn) ) {
                $album_last_image = $_MG_CONF['mediaobjects_url'] ."/". $fn;
                $mediasize = @getimagesize($_MG_CONF['path_mediaobjects'] . $fn);
                break;
           }
        }
        if ($mediasize == false ) {
             $album_last_image = $_MG_CONF['mediaobjects_url'] . '/missing.svg';
             $mediasize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'missing.svg');
        }
    }
	return $album_last_image;

}
//Split second argument
function MG_parm2 ($p2)
{
			
	$parm2_ary[]="limitcnt";
	$parm2_ary[]="order";
	
	$parm2_ary[]="width";
	$parm2_ary[]="height";
	$parm2_ary[]="border";
	$parm2_ary[]="align";
	$parm2_ary[]="src";
	$parm2_ary[]="autoplay";
	$parm2_ary[]="delay";
	$parm2_ary[]="transition";
	$parm2_ary[]="title";
	$parm2_ary[]="dest";
	$parm2_ary[]="linkid";
	$parm2_ary[]="alt";
	$parm2_ary[]="target";
	$parm2_ary[]="type";
	$parm2_ary[]="linksrc";
	$parm2_ary[]="theme";
	
	$parm2_ary[]="sort";
	 
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
		$ary["lastparm2"]=trim($p2);
	}
    return $ary;

}

?>