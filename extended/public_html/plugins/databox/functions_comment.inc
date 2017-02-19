<?php


if (strpos ($_SERVER['PHP_SELF'], 'functuins_comment.inc') !== false) {
    die ('This file can not be used on its own.');
}

function plugin_getcommenturlid_databox()
{
    global $_CONF;

    $tmp = array(
        $_CONF['site_url'] . '/databox/data.php',
        'id'
    );

    return $tmp;
}

/**
 * Poll saves a comment
 *
 * @param   string  $type   Plugin to save comment
 * @param   string  $title  comment title
 * @param   string  $comment comment text
 * @param   string  $id     Item id to which $cid belongs
 * @param   int     $pid    comment parent
 * @param   string  $postmode 'html' or 'text'
 * @return  mixed   false for failure, HTML string (redirect?) for success
 */
function plugin_savecomment_databox(
	$title
	, $comment
	, $id
	, $pid
	, $postmode
)
{
	global $_CONF;
	global $_TABLES;
	global $LANG03;
	global $_USER;

    $retval = '';

    $commentcode = DB_getItem ($_TABLES['DATABOX_base'], 'commentcode',
                               "id = '$id'");
    if ($commentcode != 0) {
        return COM_refresh ($_CONF['site_url'] . '/index.php');
    }

    $ret = CMT_saveComment ($title, $comment, $id, $pid, 'databox', $postmode);
    if ($ret > 0) { // failure //FIXME: some failures should not return to comment form
        $retval .= COM_siteHeader('menu', $LANG03[1])
                . CMT_commentForm ($title, $comment, $id, $pid, 'databox',
                                   $LANG03[14], $postmode)
                . COM_siteFooter();
    } else { // success
        $retval = COM_refresh ($_CONF['site_url']
                                . "/databox/data.php?id=$id");
    }

    return $retval;
}
/**
 * polls: delete a comment
 *
 * @param   int     $cid    Comment to be deleted
 * @param   string  $id     Item id to which $cid belongs
 * @return  mixed   false for failure, HTML string (redirect?) for success
 */
function plugin_deletecomment_databox(
	$cid
	, $id
)
{
	global $_CONF;
	global $_TABLES;
	global $_USER;

    $retval = '';

    $has_editPermissions = SEC_hasRights ('databox.edit');
	
	$sql = "SELECT owner_id,group_id,perm_owner,perm_group,perm_members,perm_anon ";
	$sql .= " FROM {$_TABLES['DATABOX_base']} WHERE id = '{$id}'";
	
	$result = DB_query ($sql);
    $A = DB_fetchArray ($result);

    if ($has_editPermissions && SEC_hasAccess ($A['owner_id'],
            $A['group_id'], $A['perm_owner'], $A['perm_group'],
            $A['perm_members'], $A['perm_anon']) == 3) {
        CMT_deleteComment($cid, $id, 'databox');
        $retval .= COM_refresh ($_CONF['site_url']
                                 . "/databox/data.php?id=$id&amp;aid=-1");
    } else {
        COM_errorLog ("User {$_USER['username']} (IP: {$_SERVER['REMOTE_ADDR']}) "
                    . "tried to illegally delete comment $cid from databox $id");
        $retval .= COM_refresh ($_CONF['site_url'] . '/index.php');
    }

    return $retval;
}

/**
* Plugin should display [a] comment[s]
*
* @param   string  $id     Unique idenifier for item comment belongs to
* @param   int     $cid    Comment id to display (possibly including sub-comments)
* @param   string  $title  Page/comment title
* @param   string  $order  'ASC' or 'DESC' or blank
* @param   string  $format 'threaded', 'nested', or 'flat'
* @param   int     $page   Page number of comments to display
* @param   boolean $view   True to view comment (by cid), false to display (by $pid)
* @return  mixed   results of calling the plugin_displaycomment_ function
*/
function plugin_displaycomment_databox (
	$id
	, $cid
	, $title
	, $order
	, $format
	, $page
	, $view
	)
{
	global $_TABLES;
	global $LANG_ACCESS;
	global $LANG_DATABOX;

    $retval = '';

    $sql = "SELECT COUNT(*) AS count, commentcode, owner_id, group_id, perm_owner, "
        . "perm_group, perm_members, perm_anon "
        . "FROM {$_TABLES['DATABOX_base']} "
		. "WHERE (id = '$id')" 
		. COM_getPermSQL('AND')
        . ' GROUP BY id';
    $result = DB_query ($sql);
    $A = DB_fetchArray ($result);
    $allowed = $A['count'];

    if ($allowed == 1) {
        $delete_option = (SEC_hasRights ('databox.edit') &&
                (SEC_hasAccess ($A['owner_id'], $A['group_id'],
                    $A['perm_owner'], $A['perm_group'], $A['perm_members'],
                    $A['perm_anon']) == 3));
        $retval .= CMT_userComments ($id, $title, 'databox', $order, $format,
                                     $cid, $page, $view, $delete_option,
                                     $A['commentcode']);
    } else {
        $retval .= COM_startBlock ($LANG_ACCESS['accessdenied'], '',
                        COM_getBlockTemplate ('_msg_block', 'header'))
                . $LANG_DATABOX['deny_msg']
                . COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    }

    return $retval;
}



?>
