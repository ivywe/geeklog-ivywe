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
  , $title=''
  , $album_title_disp=''
  , $media_title_disp=''
  , $album_title_tag=''
  , $media_title_tag=''
  , $desc=''
  , $media=''
  , $tn=''
  , $grid=''
  , $grids=''
  , $gridm=''
  , $gridl='3-5'
  , $gridr='2-5'
  , $grid_s=''
  , $grid_m=''
  , $grid_l=''
  , $grid_left='3-5'
  , $grid_right='2-5'

  , $uks=''
  , $ukc=''
  , $reverse=''
  , $text=''
  , $text1=''
  , $text2=''
  , $text3=''
  , $target=''
  , $dotnav=''
  , $class_album_title='uk-text-center@m'
  , $class_album_desc='uk-text-center@m'
  , $class_media_title='uk-text-center@m'
  , $class_media_desc='uk-text-center@m'
  , $type=''
  , $bxslider=''

// uk-slideshow
  , $animation=''
  , $autoplay=''
  , $autoplayinterval=''
  , $draggable=''
  , $easing=''
  , $finite=''
  , $pauseonhover=''
  , $index=''
  , $velocity=''
  , $ratio=''
  , $minheight=''
  , $maxheight=''
  , $animationduration=''

// uk-slider
  , $center=''
  , $sets=''
  , $clsactivated=''

// uk-scrollspy
  , $cls=''
  , $closest=''
  , $scroll=''
  , $overflow=''
  , $offset=''

  , $section=''
  , $container=''
  , $masonry=''
  , $imgclass=''
)
{
    global $MG_albums;
  global $_TABLES;
    global $_MG_CONF;
  global $_IMAGE_TYPE;
  global $LANG_MG01;
    global $_CONF;

    global $gallery_tn_size;

  if  (empty($tn)){
        $gallery_tn_size="400";
    } else {
    $gallery_tn_size=$tn;
  }

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
    $sql .= " ,m.media_type ".LB;
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
        $wk=$_MG_CONF['path_mediaobjects']."tn".$fn.'_'.$gallery_tn_size;
        $tnext = MG_getMediaExt($wk);
    $media_size = @getimagesize($wk . $tnext);
    $ext = substr($tnext,1,3);
    if ($media_size == false) {
            $filename_tn="";
        }else{
            $filename_tn=$_MG_CONF['mediaobjects_url'].'/'."tn".$fn.'_'.$gallery_tn_size. '.' . $ext;
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

        $T->set_var('title',$title);
    $T->set_var('album_title_disp',$album_title_disp);
    $T->set_var('media_title_disp',$media_title_disp);
    $T->set_var('album_title_tag',$album_title_tag);
    $T->set_var('media_title_tag',$media_title_tag);
        $T->set_var('src',$src);
        $T->set_var('desc',$desc);
        $T->set_var('theme',$theme);
        $T->set_var('limitcnt',$limitcnt);
    $T->set_var('media',$media);
    $T->set_var('tn',$tn);
        $T->set_var('grid',$grid);
        $T->set_var('grids',$grids);
        $T->set_var('gridm',$gridm);
        $T->set_var('gridl',$gridl);
        $T->set_var('gridr',$gridr);
    $T->set_var('grid_s',$grid_s);
    $T->set_var('grid_m',$grid_m);
    $T->set_var('grid_l',$grid_l);
    $T->set_var('grid_left',$grid_left);
    $T->set_var('grid_right',$grid_right);
    $T->set_var('uks',$uks);
        $T->set_var('ukc',$ukc);
        $T->set_var('reverse',$reverse);
        $T->set_var('text',$text);
        $T->set_var('text1',$text1);
        $T->set_var('text2',$text2);
        $T->set_var('text3',$text3);
        $T->set_var('target',$target);
        $T->set_var('dotnav',$dotnav);
        $T->set_var('class_album_title',$class_album_title);
        $T->set_var('class_album_desc',$class_album_desc);
        $T->set_var('class_media_title',$class_media_title);
        $T->set_var('class_media_desc',$class_media_desc);
        $T->set_var('type',$type);
        $T->set_var('bxslider',$bxslider);

    // uk-slideshow
    $T->set_var('animation',$animation);
    $T->set_var('autoplay',$autoplay);
    $T->set_var('autoplayinterval',$autoplayinterval);
    $T->set_var('draggable',$draggable);
    $T->set_var('easing',$easing);
    $T->set_var('finite',$finite);
    $T->set_var('pauseonhover',$pauseonhover);
    $T->set_var('index',$index);
    $T->set_var('velocity',$velocity);
    $T->set_var('ratio',$ratio);
    $T->set_var('minheight',$minheight);
    $T->set_var('maxheight',$maxheight);
    $T->set_var('animationduration',$animationduration);

    // uk-slider
    $T->set_var('center',$center);
    $T->set_var('sets',$sets);
    $T->set_var('clsactivated',$clsactivated);

    // uk-scrollspy
    $T->set_var('cls',$cls);
    $T->set_var('closest',$closest);
    $T->set_var('scroll',$scroll);
    $T->set_var('overflow',$overflow);
    $T->set_var('offset',$offset);

        $T->set_var('section',$section);
        $T->set_var('container',$container);
        $T->set_var('masonry',$masonry);
        $T->set_var('imgclass',$imgclass);

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
    global $_MG_CONF,$gallery_tn_size;

    if ( $filename == '' ) {
        $album_last_image = $_MG_CONF['mediaobjects_url'] . '/empty.svg';
        //$mediasize = @getimagesize($_MG_CONF['path_mediaobjects'] . 'empty.svg');
    } else {
        $mediasize = false;
        foreach ($_MG_CONF['validExtensions'] as $ext ) {
           $fn='tn/' . $filename[0] .'/' . $filename .'_'.$gallery_tn_size. $ext;
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

    $parm2_ary[]="title";
    $parm2_ary[]="album_title_disp";
    $parm2_ary[]="media_title_disp";
    $parm2_ary[]="album_title_tag";
    $parm2_ary[]="media_title_tag";
    $parm2_ary[]="desc";
    $parm2_ary[]="uks";
    $parm2_ary[]="ukc";
    $parm2_ary[]="media";
    $parm2_ary[]="tn";
    $parm2_ary[]="grid";
    $parm2_ary[]="grids";
    $parm2_ary[]="gridm";
    $parm2_ary[]="gridl";
    $parm2_ary[]="gridr";
    $parm2_ary[]="grid_s";
    $parm2_ary[]="grid_m";
    $parm2_ary[]="grid_l";
    $parm2_ary[]="grid_left";
    $parm2_ary[]="grid_right";
    $parm2_ary[]="reverse";
    $parm2_ary[]="text";
    $parm2_ary[]="text1";
    $parm2_ary[]="text2";
    $parm2_ary[]="text3";
    $parm2_ary[]="target";
    $parm2_ary[]="dotnav";
    $parm2_ary[]="class_album_title";
    $parm2_ary[]="class_album_desc";
    $parm2_ary[]="class_media_title";
    $parm2_ary[]="class_media_desc";
    $parm2_ary[]="type";
    $parm2_ary[]="bxslider";

    // uk-slideshow
    $parm2_ary[]="animation";
    $parm2_ary[]="autoplay";
    $parm2_ary[]="autoplayinterval";
    $parm2_ary[]="draggable";
    $parm2_ary[]="easing";
    $parm2_ary[]="finite";
    $parm2_ary[]="pauseonhover";
    $parm2_ary[]="index";
    $parm2_ary[]="velocity";
    $parm2_ary[]="ratio";
    $parm2_ary[]="minheight";
    $parm2_ary[]="maxheight";
    $parm2_ary[]="animationduration";

    // uk-slider
    $parm2_ary[]="center";
    $parm2_ary[]="sets";
    $parm2_ary[]="clsactivated";

    // uk-scrollspy
    $parm2_ary[]="cls";
    $parm2_ary[]="closest";
    $parm2_ary[]="scroll";
    $parm2_ary[]="overflow";
    $parm2_ary[]="offset";

    $parm2_ary[]="section";
    $parm2_ary[]="container";
    $parm2_ary[]="masonry";
    $parm2_ary[]="imgclass";

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
    foreach ($parm2_ary as $k => $v) {
        $ary[$v] = '';
    }
    $px = explode (' ', trim ($p2));
    if (is_array ($px)) {
        $skip = 0;
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
        } else {
            $ary["lastparm2"] = '';
        }

    }else{
        $ary["lastparm2"]=trim($p2);
    }
    return $ary;

}

?>
