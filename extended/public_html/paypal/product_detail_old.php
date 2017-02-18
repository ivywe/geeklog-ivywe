<?php
// +--------------------------------------------------------------------------+
// | PayPal Plugin - geeklog CMS                                             |
// +--------------------------------------------------------------------------+
// | product_detail.php                                                       |
// |                                                                          |
// | Product View.  Displays single product with details.                     |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | Copyright (C) 2005-2006 by the following authors:                        |
// |                                                                          |
// | Authors: Vincent Furia     - vinny01 AT users DOT sourceforge DOT net    |
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

/**
 * Product View.  Displays single product with details.
 *
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @copyright Vincent Furia 2005 - 2006
 * @package paypal
 * @todo Add more complex logic to decide link display between:  purchase, login, download
 */

/**
 * require core geeklog code
 */
require_once '../lib-common.php';

// Incoming variable filter
$vars = array('product' => 'number');
paypal_filterVars($vars, $_REQUEST);

$pid = $_REQUEST['product'];

/*
//rewriting product url
if ($pid == '') {
    if (isset($_SERVER['SCRIPT_URI'])) {
		$url = strip_tags($_SERVER['SCRIPT_URI']);
	} else {
		$pos = strpos($_SERVER['REQUEST_URI'], '?');
		if ($pos === false) {
			$request = $_SERVER['REQUEST_URI'];
		} else {
			$request = substr($_SERVER['REQUEST_URI'], 0, $pos);
		}
		$url = 'http://' . $_SERVER['HTTP_HOST'] . strip_tags($request);
	}
    $request_page = substr($url, strlen($_PAY_CONF['site_url'])+1);
	$name = urldecode($request_page);
    $pid = DB_getItem( $_TABLES['paypal_products'], 'id', "name='$name'" );
}

//Create a .htaccess file with those 3 lines in it and place it under your public_html/paypal plugin
//ErrorDocument 404 /paypal/product_detail.php
//ErrorDocument 403 /paypal/product_detail.php
//Options -Indexes

//Change PAYPAL_displayProducts
//from $product->set_var('link_name', '<a class="product-details" href="' . $_PAY_CONF['site_url'] . '/product_detail.php?product=' . $A['id'] . '&amp;n='. urlencode($A['name']) . '">' . $A['name'] . '</a>');
//to $product->set_var('link_name', '<a class="product-details" href="' . $_PAY_CONF['site_url'] . '/'. urlencode($A['name']) . '">' . $A['name'] . '</a>');
*/

// Ensure sufficient privs to read this page
if (($_USER['uid'] < 2) && ($_PAY_CONF['anonymous_buy'] == 0)) {
    $display .= COM_siteHeader();
	if (SEC_hasRights('paypal.user', 'paypal.admin')) {
        $display .= paypal_user_menu();
    } else {
        $display .= paypal_viewer_menu();
    }
    $display .= COM_startBlock($LANG_PAYPAL_1['access_reserved']);
    $display .= $LANG_PAYPAL_1['you_must_log_in'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter();
    COM_output($display);
    exit;
}

/**
 * Handles a comment view request
 *
 * @copyright Vincent Furia 2005
 * @author Vincent Furia <vinny01 AT users DOT sourceforge DOT net>
 * @param boolean $view View or display (true for view)
 * @return string HTML (possibly a refresh)
 */
function PAYPAL_handleView($url, $product)
{
    global $_CONF, $_TABLES, $_USER, $LANG_ACCESS, $LANG_RATING;

    $rt_id = COM_applyFilter ('paypal_' . $product, false);

    $sql = 'SELECT COUNT(*) AS count, owner_id, group_id, perm_owner, perm_group, '
         . "perm_members, perm_anon FROM {$_TABLES['rating']} WHERE (rt_id = '$rt_id') "
         . 'AND (created <= NOW())' . COM_getPermSQL('AND') 
         . 'GROUP BY rt_id';
    $result = DB_query ($sql);
    $B = DB_fetchArray ($result);
    $allowed = $B['count'];

    if ( $allowed >= 1 ) {
        $delete_option = ( SEC_hasRights( 'rating.edit' ) &&
            ( SEC_hasAccess( $B['owner_id'], $B['group_id'],
                $B['perm_owner'], $B['perm_group'], $B['perm_members'],
                $B['perm_anon'] ) == 3 ) );
        $retval .= PAYPAL_user_reviews ($rt_id, $title, 
                COM_applyFilter ($_REQUEST['order']),
                COM_applyFilter ($_REQUEST['page'], true), $delete_option, $url);
    } else {
        $retval .= PAYPAL_user_reviews ($rt_id, $title, 
                COM_applyFilter ($_REQUEST['order']),
                COM_applyFilter ($_REQUEST['page'], true), $delete_option, $url);
    }
    return $retval;
}

/**
* This function displays the comments in a high level format.
*
* Begins displaying user comments for an item
*
* @param        string      $rt_id       ID for rating to show reviews for
* @param        string      $title     Title of item
* @param        string      $order     How to order the comments 'ASC' or 'DESC'
* @param        int         $page      page number of comments to display
* @param        boolean     $delete_option   if current user can delete comments
* @see function CMT_commentBar
* @see function CMT_commentChildren
* @return     string  HTML Formated Comments
*
*/
function PAYPAL_user_reviews( $rt_id, $title, $order='', $page = 1, $delete_option = false, $url )
{
    global $_CONF, $_TABLES, $_USER, $LANG01, $_RATING_CONF;

		$limit = $_RATING_CONF['ratinglimit'];
		
		If (empty($order))
		{
			$order = $_RATING_CONF['ratingorder'];
		}
    if( $order != 'ASC' && $order != 'DESC' ) {
        $order = 'ASC';
    }

    if( empty( $limit )) {
        $limit = 10;
    }
    
    if( !is_numeric($page) || $page < 1 ) {
        $page = 1;
    }

    $start = $limit * ( $page - 1 );

    $template = COM_newTemplate($_CONF['path'] . 'plugins/rating/templates');
    $template->set_file( array( 'commentarea' => 'startreview.thtml' ));
    $template->set_var( 'site_url', $_CONF['site_url'] );
    $template->set_var( 'layout_url', $_CONF['layout_url'] );
    $template->set_var( 'commentbar',
                        PAYPAL_review_bar( $rt_id, $title, $order, $url));
    $template->set_var( 'rt_id', $rt_id );
    
    
    // build query
    $count = DB_count( $_TABLES['rating'], 'rt_id', $rt_id );

    $q = "SELECT r.*, u.username, u.fullname, u.photo, " 
         . "unix_timestamp(r.created) AS nice_date "
       . "FROM {$_TABLES['rating']} as r, {$_TABLES['users']} as u "
       . "WHERE r.owner_id = u.uid AND r.rt_id = '$rt_id' "
       . COM_getPermSql ('AND')
       . "ORDER BY created $order LIMIT $start, $limit";

    $thecomments = '';
    $result = DB_query( $q );

    $thecomments .= PAYPAL_get_review ($result, $order, $url, $delete_option );

    // Pagination
    $tot_pages =  ceil( $count / $limit );
    //$pLink = $_CONF['site_url'] . "/article.php?story=$sid&amp;type=$type&amp;order=$order&amp;mode=$mode";
    $pLink = $_CONF['site_url'] . "/rating/review.php?rt_id=$rt_id&amp;order=$order&amp;mode=view&amp;url=$url";
    $template->set_var( 'pagenav',
                     COM_printPageNavigation($pLink, $page, $tot_pages));
    
    $template->set_var( 'comments', $thecomments );
    $retval = $template->parse( 'output', 'commentarea' );

    return $retval;
}

/**
* This function prints &$comments (db results set of comments) in comment format
* -For previews, &$comments is assumed to be an associative array containing
*  data for a single comment.
* 
* @param     array      &$comments Database result set of comments to be printed
* @param     string     $order     How to order the comments 'ASC' or 'DESC'
* @param     boolean    $delete_option   if current user can delete comments
* @param     boolean    $preview   Preview display (for edit) or not
* @return    string     HTML       Formated Comment 
*
*/
function PAYPAL_get_review( &$comments, $order, $url, $delete_option = false, $preview = false )
{
    global $_CONF, $_TABLES, $_USER, $LANG01, $_IMAGE_TYPE, $LANG_RATING, $_RATING_CONF;

    $indent = 0;  // begin with 0 indent
    $retval = ''; // initialize return value

    //$template = COM_newTemplate( $_CONF['path_layout'] . 'comment' );
    $template = COM_newTemplate($_CONF['path'] . 'plugins/rating/templates');
    $template->set_file( array( 'comment' => 'review.thtml' ));

    // generic template variables
    $template->set_var( 'site_url', $_CONF['site_url'] );
    $template->set_var( 'layout_url', $_CONF['layout_url'] );
    $template->set_var( 'lang_replytothis', $LANG01[43] );
    $template->set_var( 'lang_reply', $LANG01[25] );
    $template->set_var( 'lang_authoredby', $LANG01[42] );
    $template->set_var( 'lang_on', $LANG01[36] );
    $template->set_var( 'order', $order );    

    // Make sure we have a default value for comment indentation
    if( !isset( $_CONF['comment_indent'] )) {
        $_CONF['comment_indent'] = 25;
    }

    if( $preview ) {
        $A = $comments;   
        if( empty( $A['nice_date'] )) {
            $A['nice_date'] = time();
        }
        //$mode = 'flat';
    } else {
        $A = DB_fetchArray($comments);
    }

		if ( empty($A['comment']) ) {
			$NotReview = true;
		} else {
			$NotReview = false;
		}

    if( empty( $A ) ) {
        return '';
    }

		if (rating_rt_is_digg($A['rt_id'])) { $is_digg = true;}

    $row = 1;
    do {


        // comment variables
        $template->set_var( 'indent', $indent );
        $template->set_var( 'author', $A['username'] );
        $template->set_var( 'author_id', $A['owner_id'] );
        $template->set_var( 'rt_id', $A['rt_id'] );
        $template->set_var( 'cssid', $row % 2 );

        if( $A['owner_id'] > 1 ) {
            if( empty( $A['fullname'] )) {
                $template->set_var( 'author_fullname', $A['username'] );
                $alttext = $A['username'];
            } else {
                $template->set_var( 'author_fullname', $A['fullname'] );
                $alttext = $A['fullname'];
            }

            $photo = '';
            if( $_CONF['allow_user_photo'] ) {
//                $photo = USER_getPhoto( $A['owner_id'], $A['photo'] );
            }
            if( !empty( $photo )) {
                $template->set_var( 'author_photo', $photo );
                $template->set_var( 'camera_icon', '<a href="'
                        . $_CONF['site_url']
                        . '/users.php?mode=profile&amp;uid=' . $A['owner_id']
                        . '"><img src="' . $_CONF['layout_url']
                        . '/images/smallcamera.' . $_IMAGE_TYPE
                        . '" border="0" alt=""></a>' );
            } else {
                $template->set_var( 'author_photo', '' );
                $template->set_var( 'camera_icon', '' );
            }

            $template->set_var( 'start_author_anchortag', '<a href="'
                    . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid='
                    . $A['owner_id'] . '">' );
            $template->set_var( 'end_author_anchortag', '</a>' );
        } else {
            $template->set_var( 'author_fullname', $A['username'] );
            $template->set_var( 'author_photo', '' );
            $template->set_var( 'camera_icon', '' );
            $template->set_var( 'start_author_anchortag', '' );
            $template->set_var( 'end_author_anchortag', '' );
        }

        // this will hide HTML that should not be viewed in preview mode
        if( $preview || $hidefromanon ) {
            $template->set_var( 'hide_if_preview', 'style="display:none"' );
        } else {
            $template->set_var( 'hide_if_preview', '' );
        }

        $template->set_var( 'date', strftime( $_CONF['date'], $A['nice_date'] ));

        // If deletion is allowed, displays delete link (this varible is for the owner of the rating)
        // Now check if you show individual rating or review
		    // never trust $uid ...
		    if (empty ($_USER['uid'])) {
		        $uid = 1;
		    } else {
		        $uid = $_USER['uid'];
		    }        

        // this will hide HTML that should not be viewed when user logged in
        if( $uid > 1 ) {
            $template->set_var( 'hide_if_user', 'style="display:none"' );
        } else {
            $template->set_var( 'hide_if_user', '' );
        }


        // *****************************************************
        // Edit & delete links
        $UserFlag = false;
        if ($uid > 1) {
			    $sql = 'SELECT COUNT(*) AS count, owner_id, group_id, perm_owner, perm_group, '
			         . "perm_members, perm_anon FROM {$_TABLES['rating']} WHERE (rid = '" . $A['rid'] . "') "
			         . 'AND (created <= NOW())' . COM_getPermSQL('AND') 
			         . 'GROUP BY rid';
			    $result = DB_query ($sql);
			    $B = DB_fetchArray ($result);
			    $allowed = $B['count'];
			
			    if ( $allowed >= 1 ) {
			        $access = SEC_hasAccess( $B['owner_id'], $B['group_id'],
			                $B['perm_owner'], $B['perm_group'], $B['perm_members'],
			                $B['perm_anon'] );    
			        if ($access == 3) {$UserFlag = true;}
	        }
				}        
        
        
        if( $delete_option || ($UserFlag)) {
        		$deloption = '[ ';
        		If ($delete_option) {
							$onclick = 'onclick="return confirm(' . "'" . $LANG_RATING['delete_confirm'] . "'" . ');"'; // Delete Confirm
							
	            $deloption .= '<a href="' . $_CONF['site_admin_url']
	                       . '/plugins/rating/index.php?mode=edit&amp;rid=' . $A['rid'] . '&amp;rt_id=' . $A['rt_id'] 
	                       . '">' . $LANG01[04] . '</a>'
	            					 . ' | <a href="' . $_CONF['site_url']
	                       . '/rating/review.php?mode=delete&amp;rid='
	                       . $A['rid'] . '&amp;rt_id=' . $A['rt_id'] . '" ' . $onclick . '>' . $LANG01[28] . '</a> ';

	            if( !empty( $A['ipaddress'] )) {
	                if( empty( $_CONF['ip_lookup'] )) {
	                    $deloption .= '| ' . $A['ipaddress'] . ' ';
	                } else {
	                    $iplookup = str_replace( '*', $A['ipaddress'],
	                                             $_CONF['ip_lookup'] );
	                    $deloption .= '| <a href="' . $iplookup . '">'
	                               . $A['ipaddress'] . '</a> ';
	                }
	            }
             } elseif (!$delete_option && $UserFlag) {          

            	$deloption .= '<a href="' . $_CONF['site_url']                       
                       . '/rating/review.php?mode=edit&amp;rid=' . $A['rid'] . '&amp;rt_id=' . $A['rt_id'] 
                       . '">' . $LANG01[04] . '</a>';
             }          
				// ***************************************************** 
                      

            $deloption .= ' ]';
            $template->set_var( 'delete_option', $deloption );
        } else {
            $template->set_var( 'delete_option', '' );
        }

        // and finally: format the actual text of the comment
        if( preg_match( '/<.*>/', $A['comment'] ) == 0 ) {
            $A['comment'] = nl2br( $A['comment'] );
        }

        // highlight search terms if specified
        if( !empty( $_REQUEST['query'] )) {
            $A['comment'] = COM_highlightQuery( $A['comment'],
                                                $_REQUEST['query'] );
        }

				$review_for = DB_getItem($_TABLES['rating_totals'], 'title_link', "rt_id='" . $A['rt_id'] . "'");
				if( $review_for == '' ) {
					$review_for = DB_getItem($_TABLES['rating_totals'], 'title', "rt_id='" . $A['rt_id'] . "'");
				}
        if( $review_for == '' ) {
        	$template->set_var( 'hide_no_review_for', 'style="display:none"' );
					$template->set_var( 'lang_review_for', '');
	        $template->set_var( 'review_for', '' );

        } else {
					$template->set_var( 'lang_review_for', $LANG_RATING[340]);
	        $template->set_var( 'review_for', $review_for );
	      }

      	$template->set_var( 'lang_location', $LANG_RATING[339]);
        if( $A['location'] == '' ) {
          $template->set_var( 'hide_no_location', 'style="display:none"' );
          $template->set_var( 'location', '' );
        } else {
        	$template->set_var( 'hide_no_location', '' );
	        $template->set_var( 'location', $A['location'] );
	      }
        if( $A['nickname'] == '' ) {
          $template->set_var( 'nickname', '' );
        } else {
	        $template->set_var( 'nickname', '(' . $A['nickname'] . ')');
	      }


        $A['comment'] = str_replace( '$', '&#36;',  $A['comment'] );
        $A['comment'] = str_replace( '{', '&#123;', $A['comment'] );
        $A['comment'] = str_replace( '}', '&#125;', $A['comment'] );

        // Replace any plugin autolink tags
        $A['comment'] = PLG_replaceTags( $A['comment'] );
        
        
        // format title for display, must happen after reply_link is created
        $A['title'] = htmlspecialchars( $A['title'] );
        $A['title'] = str_replace( '$', '&#36;', $A['title'] );
        $A['title'] = COM_stripslashes ($A['title']);
        $A['pros'] = htmlspecialchars( $A['pros'] );
        $A['pros'] = str_replace( '$', '&#36;', $A['pros'] );
        $A['pros'] = COM_stripslashes ($A['pros']);
        $A['cons'] = htmlspecialchars( $A['cons'] );
        $A['cons'] = str_replace( '$', '&#36;', $A['cons'] );
        $A['cons'] = COM_stripslashes ($A['cons']);
        $A['bottomline'] = htmlspecialchars( $A['bottomline'] );
        $A['bottomline'] = str_replace( '$', '&#36;', $A['bottomline'] );
				$A['bottomline'] = COM_stripslashes ($A['bottomline']);

        $template->set_var( 'title', $A['title'] );
        if( $A['pros'] == '' ) {
            $template->set_var( 'hide_no_pros', 'style="display:none"' );
            $template->set_var( 'pros', '' );
        } else {
        	$template->set_var( 'hide_no_pros', '' );
	        $template->set_var( 'pros', $A['pros'] );
	      }
        if( $A['cons'] == '' ) {
            $template->set_var( 'hide_no_cons', 'style="display:none"' );
            $template->set_var( 'cons', '' );
        } else {
        	$template->set_var( 'hide_no_cons', '' );
	        $template->set_var( 'cons', $A['cons'] );
	      }
        if( $A['bottomline'] == '' ) {
            $template->set_var( 'hide_no_bottomline', 'style="display:none"' );
            $template->set_var( 'bottomline', '' );
        } else {
        	$template->set_var( 'hide_no_bottomline', '' );
		      $template->set_var( 'bottomline', $A['bottomline'] );
        }
        $template->set_var( 'lang_pros', $LANG_RATING[335] );
        $template->set_var( 'lang_cons', $LANG_RATING[336] );
        $template->set_var( 'lang_bottomline', $LANG_RATING[337] );


        $template->set_var( 'comments', $A['comment'] );
        
        
				$template->set_var( 'lang_rating', $LANG_RATING[111]);
    		if ($is_digg) {
    			$template->set_var( 'lang_rating', $LANG_RATING[119]);
    			$template->set_var( 'rating','');
    			
    			$template->set_var( 'hide_question', 'style="display:none"' );
    			$template->set_var( 'review_question', '' );
    			$template->set_var( 'buttons_yes_no', '' );
    		} else { 	
    			$template->set_var( 'lang_rating', $LANG_RATING[111]);
					$template->set_var( 'rating', RATING_rating_as_html($A['score'], DB_getItem($_TABLES['rating_totals'], 'score_max', "rt_id='" . $A['rt_id'] . "'"),$_RATING_CONF['user_review_default_display']));
					
					// Helpful Question	
					if($preview || $NotReview) {
	    			$template->set_var( 'hide_question', 'style="display:none"' );
	    			$template->set_var( 'review_question', '' );
	    			$template->set_var( 'buttons_yes_no', '' );
					} else {
						$template->set_var( 'hide_question', '' );
		        $review_question = $A['helpful_yes'] . $LANG_RATING[345] . ($A['helpful_yes']+$A['helpful_no']) . $LANG_RATING[346];
		        $template->set_var( 'review_question', $review_question );
						$template->set_var( 'rid', $A['rid'] );
						$template->set_var( 'rt_id', $A['rt_id'] );
						$template->set_var( 'url', $url );
						
						if ($_RATING_CONF['use_image_buttons'] == 1) {
							$button_info = '&nbsp;<input type="image" value="' . $LANG_RATING[347] . '" name="mode" border="0" src="' . $_CONF["site_url"] . '/rating/images/yesbutton.gif">';
							$button_info .= '&nbsp;<input type="image" value="' . $LANG_RATING[348] . '" name="mode" border="0" src="' . $_CONF["site_url"] . '/rating/images/nobutton.gif">';
						} else {
							$button_info = '&nbsp;<input type="submit" value="' . $LANG_RATING[347] . '" name="mode">';
							$button_info .= '&nbsp;<input type="submit" value="' . $LANG_RATING[348] . '" name="mode">';
						}
						$template->set_var( 'buttons_yes_no', $button_info );
					}
				}
        // parse the templates
      	//$template->set_var( 'pid', $A['cid'] );
	      $retval .= $template->parse( 'output', 'comment' );   

        $row++;
    } while( $A = DB_fetchArray( $comments ));

    return $retval;
}

/**
* This function displays the comment control bar
*
* Prints the control that allows the user to interact with Geeklog Comments
*
* @param        string      $rt_id        ID of item in question
* @param        string      $title      Title of item
* @param        string      $order      Order that comments are displayed in
* @see CMT_userComments
* @see CMT_commentChildren
* @return     string   HTML Formated comment bar
*
*/
function PAYPAL_review_bar( $rt_id, $title, $order, $url )
{
    global $_CONF, $_TABLES, $_USER, $LANG01, $_REQUEST, $LANG_RATING;

    $parts = explode( '/', $_SERVER['PHP_SELF'] );
    $page = array_pop( $parts );
    $nrows = DB_count( $_TABLES['rating'], 'rt_id', $rt_id );

    //$commentbar = COM_newTemplate( $_CONF['path_layout'] . 'comment' );
    $commentbar = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates/rating');
    $commentbar->set_file( array( 'commentbar' => 'reviewbar.thtml' ));
    $commentbar->set_var( 'site_url', $_CONF['site_url'] );
    $commentbar->set_var( 'layout_url', $_CONF['layout_url'] );

    $commentbar->set_var( 'lang_reviews', $LANG_RATING[201] );
    $commentbar->set_var( 'lang_refresh', $LANG01[39] );
    $commentbar->set_var( 'lang_disclaimer', $LANG_RATING[202] );

    $commentbar->set_var( 'num_reviews', COM_numberFormat( $nrows ));
    $commentbar->set_var( 'comment_type', $type );
    $commentbar->set_Var( 'rt_id', $rt_id );


	// Link to Rating Location
	$rt_link = RATING_return_parent_info($rt_id, $url, true);
	$commentbar->set_var( 'start_storylink_anchortag', '<a href="' . $rt_link . '">' );
	$commentbar->set_var( 'story_title', $LANG_RATING[203]);
	$commentbar->set_var( 'end_storylink_anchortag', '</a>' );


		// User
    if( !empty( $_USER['uid'] ) && ( $_USER['uid'] > 1 )) {
    		$uid = $_USER['uid'];
        $username = $_USER['username'];
        $fullname = DB_getItem( $_TABLES['users'], 'fullname',
                                "uid = '{$_USER['uid']}'" ); 
    } else {
        $result = DB_query( "SELECT username,fullname FROM {$_TABLES['users']} WHERE uid = 1" );
        $N = DB_fetchArray( $result );
        $username = $N['username'];
        $fullname = $N['fullname'];
        $uid = 1;
    }
    
    if( empty( $fullname )) {
        $fullname = $username;
    }
    $commentbar->set_var( 'user_name', $username );   
    $commentbar->set_var( 'user_fullname', $fullname );    

    if( !empty( $_USER['username'] )) {
        $commentbar->set_var( 'user_nullname', $username );
        $commentbar->set_var( 'login_logout_url',
                              $_CONF['site_url'] . '/users.php?mode=logout' );
        $commentbar->set_var( 'lang_login_logout', $LANG01[35] );
    } else {
        $commentbar->set_var( 'user_nullname', '' );
        $commentbar->set_var( 'login_logout_url',
                              $_CONF['site_url'] . '/users.php?mode=new' );
        $commentbar->set_var( 'lang_login_logout', $LANG01[61] );
    }
		
		// Edit a Review total
    if ($uid > 1) {
	    $sql = 'SELECT COUNT(*) AS count, owner_id, group_id, perm_owner, perm_group, '
	         . "perm_members, perm_anon FROM {$_TABLES['rating_totals']} WHERE (rt_id = '" . $rt_id . "') "
	         . 'AND (created <= NOW())' . COM_getPermSQL('AND') 
	         . 'GROUP BY rt_id';
	    $result = DB_query ($sql);
	    $B = DB_fetchArray ($result);
	    $allowed = $B['count'];
	
	    if ( $allowed >= 1 ) {
	        $access = SEC_hasAccess( $B['owner_id'], $B['group_id'], $B['perm_owner'], $B['perm_group'], $B['perm_members'], $B['perm_anon'] );    
	        if ($access == 3) {$UserFlag = true;}
        }
	}  		
	if ($UserFlag)   // Allow adding of reviews
	{
		$editoption = " | <a href=\"{$_CONF['site_admin_url']}/plugins/rating/index.php?mode=edit&amp;rt_id={$rt_id}\">" . $LANG01[04] . "</a>";
	} else {
		$editoption ='';
	}		
	$commentbar->set_var( 'edit_option', $editoption );
	
	// Add a Review
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	$rated = RATING_check_user_rated($rt_id, $uid, $ipaddress,false);
	if ($rated == 0) {$rated = RATING_check_user_rated($rt_id, $uid, $ipaddress,true);}
	If ($rated == 0)   // Allow adding of reviews
	{
		$addoption = ' | <a href="' . $_CONF['site_url'] . '/rating/review.php?rt_id=' . $rt_id . '">' . $LANG_RATING[104] . '</a> ';
	} else {
		$addoption ='';
	}
	$commentbar->set_var( 'add_option', $addoption );

	// Rating Information
	$commentbar->set_var('ratingtotalsinfo', RATING_display_rating_total($rt_id, $url));

    $commentbar->set_var( 'parent_url', 
                          $_CONF['site_url'] . '/rating/review.php' );
    $hidden = '';
    $hidden .= '<input type="hidden" name="rt_id" value="' . $_REQUEST['rt_id'] . '">';
    $commentbar->set_var( 'hidden_field', $hidden . 
            '<input type="hidden" name="mode" value="' . $_REQUEST['mode'] . '">' );



    // Order
    $selector = '<select name="order">' . LB
              . COM_optionList( $_TABLES['sortcodes'], 'code,name', $order )
              . LB . '</select>';
    $commentbar->set_var( 'order_selector', $selector);


    return $commentbar->finish( $commentbar->parse( 'output', 'commentbar' ));
}    

/*
Main
*/

if ($_PAY_CONF['anonymous_buy'] == 0) paypal_access_check('paypal.user');

// query database for product
$res = DB_query("SELECT p.*, c.cat_name
                 FROM {$_TABLES['paypal_products']} AS p
				 LEFT JOIN {$_TABLES['paypal_categories']} AS c
				 ON p.cat_id = c.cat_id
				 WHERE id = {$pid}");

// count number of returned results, if unexpected redirect to product list
if (DB_numRows($res) != 1) {
    echo COM_refresh($_PAY_CONF['site_url'] . '/index.php');
	exit;
}

$A = DB_fetchArray($res);

if ($A['customisable'] != 0 && !function_exists('PAYPALPRO_displayAttributes') ) {
    echo COM_refresh($_PAY_CONF['site_url'] . '/index.php');
	exit;
}

$display .= PAYPAL_siteHeader($A['name'] . ' - '  . $A['cat_name']);

if (SEC_hasRights('paypal.user', 'paypal.admin')) {
    $display .= paypal_user_menu();
} else {
    $display .= paypal_viewer_menu();
}

$breadcrumbs = PAYPAL_Breadcrumbs($A['cat_id']);
if ($breadcrumbs != '') {
				   $display .= '<p><small>' . $breadcrumbs . '</small></p>';
				}

$product = COM_newTemplate($_CONF['path'] . 'plugins/paypal/templates');
$product->set_file(array('product' => 'product_detail.thtml',
                         'buy'     => 'buy_now_button.thtml',
                         'cart'    => 'add_to_cart_button.thtml',
						 'custom'   => 'customised_button.thtml'
						 ));
$product->set_var('site_url', $_CONF['site_url']);
$product->set_var('paypal_folder', $_PAY_CONF['site_url']);

//Edit link
if (SEC_hasRights('paypal.admin')) {
$product->set_var('edit', "<form action=\"{$_CONF['site_url']}/admin/plugins/paypal/product_edit.php?op=edit&amp;id={$A['id']}\" method=\"POST\"><div style=\"float:right\">&nbsp; <input type=\"image\" src=\"{$_PAY_CONF['site_url']}/images/edit.png\" name=\"id\" value=\"{$A['id']}\" align=\"absmiddle\" /></div><input type=\"hidden\" name=\"op\" value=\"edit\" /></form>");
}else {
$product->set_var('edit', '');
}

//rating
if (function_exists('RATING_display_rating') && $_PAY_CONF['view_review'] == 1) {
    $product->set_var('rating',  RATING_display_rating('paypal_' . $A['id'], 'paypal', $A['id'], 1, 5, 32) );
    $product->set_var('review',  PAYPAL_handleView($_PAY_CONF['site_url'] . '/product_detail.php?product=' . $A['id'], $pid) );
}else {
    $product->set_var('rating', '');
    $product->set_var('review', '');
}

$product->set_var(array ('paypal_url' => 'https://' . $_PAY_CONF['paypalURL'],
	'buy_now_url' => $_PAY_CONF['site_url'] . '/buy_now.php',
    'user_id' => $_USER['uid'],
    'business' => $_PAY_CONF['receiverEmailAddr'],
    'currency' => $_PAY_CONF['currency'],
    'return' => $_PAY_CONF['site_url'] . '/index.php?mode=endTransaction',
    'cancel_return' => $_PAY_CONF['site_url'] . '/index.php?mode=cancel',
    'cbt' => $LANG_PAYPAL_1['cbt'] . ' ' . $_CONF['site_name'],
    'notify_url' => $_PAY_CONF['site_url'] . '/ipn.php',
    'image_url' => $_PAY_CONF['image_url'],
    'cpp_header_image' => $_PAY_CONF['cpp_header_image'],
    'cpp_headerback_color' => $_PAY_CONF['cpp_headerback_color'],
    'cpp_headerborder_color' => $_PAY_CONF['cpp_headerborder_color'],
    'cpp_payflow_color' => $_PAY_CONF['cpp_payflow_color'],
    'cs' => $_PAY_CONF['cs'],
    'id' => $A['id'],
	'free_shipping' => ''
) );

$product->set_var('item_ref', $A['item_id']);

if ($_PAY_CONF['display_item_id'] == 1 && $A['customisable'] == 0) {
	$product->set_var(array (
		'name_button' => $A['name'] . ' | ' . $A['item_id'],
		'name' => $A['name'])
		);
} else {
	 $product->set_var(array (
		'name' => $A['name'],
		'name_button' => $A['name'])
		);
}
if (($A['active'] == 0) && SEC_hasRights('paypal.admin')) {
  $product->set_var('active','<strong><font color="red">' . $LANG_PAYPAL_1['active'] . '</font></strong><br/>');
} else {
  $product->set_var('active','');
}
$product->set_var('short_description', PLG_replacetags($A['short_description']));
if ($A['item_id'] != '' && $_PAY_CONF['display_item_id'] == 1) {
	$product->set_var('item_id', '<p class="product-item-id">' . $A['item_id'] . '</p>');
} else {
    $product->set_var('item_id', '');
}
$product->set_var('description', PLG_replacetags($A['description']));
$product->set_var('price_label', $LANG_PAYPAL_1['price_label']);
$product->set_var('price2', PAYPAL_productPrice($A));
$product->set_var('price', number_format(PAYPAL_productPrice($A), $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']));
$product->set_var(array('price_ref' => '',
						'discount'        => ''));
if ($A['price_ref'] != '' && $A['price_ref'] != 0) $product->set_var('price_ref', '<span class="price_deleted">' . number_format($A['price_ref'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']) . '</span>');
if ($A['discount_a'] != '' && $A['discount_a'] != 0) {
	 $product->set_var('discount', '<span class="price_promo">-' . number_format($A['discount_a'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']) . $_PAY_CONF['currency'] . '</span>');
	 $product->set_var('price_ref', '<span class="price_deleted">' . number_format($A['price'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']) . '</span>');
} else if ($A['discount_p'] != '' && $A['discount_p'] != 0) {
	 $product->set_var('discount', '<span class="price_promo">-' . number_format($A['discount_p'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']) . '%' . ' </span>');
	 $product->set_var('price_ref', '<span class="price_deleted">' . number_format($A['price'], $_CONF['decimal_count'], $_CONF['decimal_separator'], $_CONF['thousand_separator']) . '</span>');
}

//Weight
if ($A['shipping_type'] == 0) {
	$product->set_var('item_weight', '0.00');
	if ($A['product_type'] == 0 && $A['type'] == 'product') {
	    $product->set_var('free_shipping', '<small><font style="color:red">' . $LANG_PAYPAL_CART['free_shipping'] . '</font></small>');
	} else {
	    $product->set_var('free_shipping', '');
	}
} else {
	$product->set_var('item_weight', $A['weight']);
}

//images
$icount = DB_count($_TABLES['paypal_images'],'pi_pid', $pid);
$wsize = $_PAY_CONF['thumb_width'];
$hsize = $_PAY_CONF['thumb_height'];
if ($icount > 0) {
	$result_products = DB_query("SELECT * FROM {$_TABLES['paypal_images']} WHERE pi_pid = '". $pid ."' ORDER BY pi_img_num");
	for ($z = 1; $z <= $icount; $z++) {
		$I = DB_fetchArray($result_products);

		$saved_images .= '<img src="' . $_PAY_CONF['images_url'] . $I['pi_filename'] . " alt="' . $A['name'] . '" />';

	}
}
if ($icount == 0) {
	$product->set_var('pictures', '');
} else {
    $info_picture = '<li><img src="' .  $_PAY_CONF['site_url'] . '/images/enlarge.png" alt="" align="absmiddle" /> ' . $LANG_PAYPAL_1['info_picture'] .'</li>';
	$product->set_var('pictures', '<div data-uk-slideshow><ul class="uk-slideshow">' . $saved_images . $info_picture . '</ul><a href="" data-uk-slideshow-item="previous"></a><a href="" data-uk-slideshow-item="next"></a>');
}

// FIXME: If a user purchased once with no expiration query will not operate correctly
$time = DB_getItem($_TABLES['paypal_purchases'], 'MAX(UNIX_TIMESTAMP(expiration))',
                   "user_id = {$_USER['uid']} AND product_id = {$A['id']}");
// Setup purchase links. If anonymous, ask for login. If free or purchased display
// download link
$product->set_var(array('buy_now' => '',
				'buy_now2' => '',
				'add2cart' => '',
				'add2cart2' => '',
				'login' => '',
				'customisable' => '',
				'add_to_cart' => $LANG_PAYPAL_1['add_to_cart']
				));

if (( $A['price'] > 0 && ($_USER['uid'] < 2 && $_PAY_CONF['anonymous_buy'] == 0)) || ($A['logged'] == 1 && $_USER['uid'] < 2)) {
    /* For anymous users, login */
    $loginlink = $_CONF['site_url'] . '/users.php';
    $createlink = $_CONF['site_url'] . '/users.php?mode=new';
    $product->set_var('add2cart', "<a href=\"$loginlink\">Login</a> or <a href=\"$createlink\">"
                               . "Create an Account</a> to purchase");
} else if ( $A['product_type'] == 1 && ( $A['price'] == 0 || $time > time() ) ) {
    /* Free items or items purchases and not expired, download */
    $product->set_var('add2cart', "<a href=\"{$_PAY_CONF['site_url']}/download.php"
                               . "?id={$A['id']}\">" . $LANG_PAYPAL_1['Download'] . "</a>");
} else if ($A['customisable'] == 1) {
    /*Customisable product*/
	$product->set_var('attributes', PAYPALPRO_displayCustomAttributes($A['id']));
    $product->set_var('customisable', $product->parse('output', 'custom'));
} else {
    /* buttons for everyone else */
    $product->set_var('buy_now_button', $LANG_PAYPAL_1['buy_now_button']);
	if ( $_PAY_CONF['enable_buy_now'] == 1 ) {
        $product->set_var('buy_now', $product->parse('output', 'buy'));
	}
    $product->set_var('add2cart', $product->parse('output', 'cart'));
    if ($A['description'] == '' || $_PAY_CONF['display_2nd_buttons'] == 0) {
	    $product->set_var('buy_now2', '');
    } else {
		if ( $_PAY_CONF['enable_buy_now'] == 1 ) {
            $product->set_var('buy_now2', $product->parse('output', 'buy'));
		} else {
		    $product->set_var('buy_now2', '');
		}
	}
	($A['description'] == '' || $_PAY_CONF['display_2nd_buttons'] == 0) ? $product->set_var('add2cart2', '') : $product->set_var('add2cart2', $product->parse('output', 'cart'));
	$product->set_var('login', '');
	if ($A['logged'] == 1 && $_USER['uid'] < 2) {
	    $product->set_var('add2cart', '');
		$product->set_var('add2cart2', '');
		$product->set_var('buy_now', '');
		$product->set_var('buy_now2', '');
	    $loginlink = $_CONF['site_url'] . '/users.php';
		$createlink = $_CONF['site_url'] . '/users.php?mode=new';
		$product->set_var('login', "<em>" . $LANG_PAYPAL_1['you_must'] . ' ' . "<a href=\"$loginlink\">" . $LANG_PAYPAL_1['login'] . "</a> " . $LANG_PAYPAL_1['or'] . " <a href=\"$createlink\">" . $LANG_PAYPAL_1['create_account'] . "</a> " . $LANG_PAYPAL_1['to_purchase'] . "</em>");
	}
}
//subscrition
$product->set_var('subscription', '');

//Donation
$product->set_var('donation', '');

//Rent
$product->set_var('rent', '');

switch ($type) {
    case 'subscription':
        break;

    case 'donation':
        break;

    case 'rent':
        break;
		
    default:
        break;
}

if ( ( $A['active'] == 1   && SEC_hasAccess2($A) ) || SEC_hasRights('paypal.admin')) {
    $display .= $product->parse('output', 'product');
} else {
	$display .= COM_showMessageText($LANG_PAYPAL_1['not_active_message'], $LANG_PAYPAL_1['active']);
}

//Display cart
$display .= '<div id="cart">' . PAYPAL_displayCart() .'</div>';

$display .= PAYPAL_siteFooter();

//hit +1
hitProduct($A['id']);

COM_output($display);

?>