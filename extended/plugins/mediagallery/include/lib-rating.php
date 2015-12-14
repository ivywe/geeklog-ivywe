<?php
// +--------------------------------------------------------------------------+
// | Media Gallery Plugin - glFusion CMS                                      |
// +--------------------------------------------------------------------------+
// | lib-rating.php                                                           |
// |                                                                          |
// | Rating interface library                                                 |
// +--------------------------------------------------------------------------+
// | $Id:: lib-rating.php 2869 2008-07-31 14:38:32Z mevans0263               $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2002-2008 by the following authors:                        |
// |                                                                          |
// | Mark R. Evans          mark AT glfusion DOT org                          |
// |                                                                          |
// | Based on prior work by:                                                  |
// | Copyright (C) 2006,2007, 2008 by the following authors:                  |
// | Authors:                                                                 |
// | Ryan Masuga, masugadesign.com  - ryan@masugadesign.com                   |
// | Masuga Design                                                            |
// |http://masugadesign.com/the-lab/scripts/unobtrusive-ajax-star-rating-bar  |
// | Komodo Media (http://komodomedia.com)                                    |
// | Climax Designs (http://slim.climaxdesigns.com/)                          |
// | Ben Nolan (http://bennolan.com/behaviour/) for Behavio(u)r!              |
// |                                                                          |
// | Homepage for this script:                                                |
// |http://www.masugadesign.com/the-lab/scripts/unobtrusive-ajax-star-rating-bar/
// +--------------------------------------------------------------------------+
// | This (Unobtusive) AJAX Rating Bar script is licensed under the           |
// | Creative Commons Attribution 3.0 License                                 |
// |  http://creativecommons.org/licenses/by/3.0/                             |
// |                                                                          |
// | What that means is: Use these files however you want, but don't          |
// | redistribute without the proper credits, please. I'd appreciate hearing  |
// | from you if you're using this script.                                    |
// |                                                                          |
// | Suggestions or improvements welcome - they only serve to make the script |
// | better.                                                                  |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | Licensed under a Creative Commons Attribution 3.0 License.               |
// | http://creativecommons.org/licenses/by/3.0/                              |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), strtolower(basename(__FILE__))) !== false) {
    die('This file can not be used on its own!');
}

global $ratedIds;

/*
 * Pull all rated media
 */
function MG_getRatedMedia()
{
    global $_USER, $_TABLES, $ratedIds;

    if (isset($ratedIds)) return $ratedIds;

    $ip = addslashes($_SERVER['REMOTE_ADDR']);
    $uid = isset($_USER['uid']) ? $_USER['uid'] : 1;
    $ratedIds = array();
    $sql = "SELECT media_id FROM {$_TABLES['mg_rating']} WHERE "
         . (($uid == 1) ? "(ip_address='$ip')" : "(uid='$uid' OR ip_address='$ip')");
    $result = DB_query($sql, 1);
    while ($row = DB_fetchArray($result)) {
        $ratedIds[] = $row['media_id'];
    }
    return $ratedIds;
}

/*
 * build the rating box
 */
function MG_getRatingBar($aid, $media_user_id, $media_id, $media_votes, $media_rating, $size= '')
{
    global $MG_albums, $_USER, $ratedIds;

    $rating_box = '';
    if ($MG_albums[$aid]->enable_rating > 0) {
        $ip     = $_SERVER['REMOTE_ADDR'];
        $uid    = isset($_USER['uid']) ? $_USER['uid'] : 1;
        // check to see if we are the owner, if so, no rating for us...
        if (isset($_USER['uid']) && $_USER['uid'] == $media_user_id) {
            $static = 'static';
            $voted = 0;
        } else {
            $ratedIds = MG_getRatedMedia();
            if (in_array($media_id, $ratedIds)) {
                $static = 'static';
                $voted  = 1;
            } else {
                $static = '';
                $voted = 0;
            }
        }
        if ($MG_albums[$aid]->enable_rating == 1 && (!isset($_USER['uid']) || $_USER['uid'] < 2)) {
            $static = 'static';
            $voted = 0;
        }
        $rating_box = mgRating_bar($media_id, $media_votes, ($media_rating * $media_votes)/2, $voted, 5, $static, $size);
    }
    return $rating_box;
}

function mgRating_bar($id, $total_votes, $total_value, $voted=0, $units='', $static='', $size='')
{
    global $_USER, $LANG_MG03;

    $rating_unitwidth = ($size == 'sm') ? 10 : 30;
    $class = ($size == 'sm') ? 'small-unit-rating' : 'unit-rating';

    //set some variables
    $ip  = $_SERVER['REMOTE_ADDR'];
    $uid = isset($_USER['uid']) ? $_USER['uid'] : 1;
    if (!$units) {
        $units = 10;
    }
    if (!$static) {
        $static = FALSE;
    }
    $count = ($total_votes < 1) ? 0 : $total_votes; // how many votes total

    $current_rating = $total_value; //total number of rating added together and stored
    $tense = ($count == 1) ? $LANG_MG03['vote'] : $LANG_MG03['votes']; //plural form votes/vote

    // determine whether the user has voted, so we know how to draw the ul/li

    // now draw the rating bar
    $rating_width = @number_format($current_rating / $count, 2) * $rating_unitwidth;
    $rating1      = @number_format($current_rating / $count, 1);
    $rating2      = @number_format($current_rating / $count, 2);

    $rater = '';
    $rater .= '<div class="ratingblock">'.LB;
    $rater .= '<div id="unit_long'.$id.'">'.LB;
    $rater .= '  <ul id="unit_ul'.$id.'" class="' . $class . '" style="width:'.$rating_unitwidth*$units.'px;">'.LB;
    $rater .= '    <li class="current-rating" style="width:'.$rating_width.'px;">Currently '.$rating2.'/'.$units.'</li>'.LB;
    if ($static == '') {
        if (!$voted) { // if the user hasn't yet voted, draw the voting stars
            for ($ncount = 1; $ncount <= $units; $ncount++) { // loop from 1 to the number of units
                $rater .= '    <li><a href="rater.php?j='.$ncount.'&amp;q='.$id.'&amp;t='.$ip.'&amp;c='.$units.'&amp;s='.$size.'" title="'.$ncount.' out of '.$units.'" class="r'.$ncount.'-unit rater" rel="nofollow">'.$ncount.'</a></li>'.LB;
            }
        }
    }
    $rater .= '  </ul>'.LB;
    if ($static == 'static') {
        $rater .= '  <span class="static">';
    } else {
        $rater .= '  <span' . ($voted ? ' class="voted"' : '') . '>';
    }
    $rater .= $LANG_MG03['rating'] . ': <strong>'.$rating1.'</strong>/'.$units.' ('.$count.' '.$tense.' '.$LANG_MG03['cast'].')</span>'.LB;
    $rater .= '</div>'.LB;
    $rater .= '</div>'.LB;
    return $rater;
}
?>