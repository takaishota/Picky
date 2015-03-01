<?php
if ( ! defined('ABSPATH') ) die();
// Admin bootstaps
require_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'admin.php');
require_once(ABSPATH.'wp-includes'.DIRECTORY_SEPARATOR.'pluggable.php');

// Do this here just to be on the safe side
if(!is_user_logged_in())
{
	header("Location:".get_bloginfo('home').'/wp-login.php');
	exit();
}
require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'functions.php');

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$current_color = get_user_option('admin_color', $user_id);

$waplString = '';
$pageTitle = __('Reply to Comment');
$pageId = 'addComment';
$subNav = false;

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');

$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]add comment[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

// Existing comment
$comment = get_comment($_REQUEST['c']);

preg_match('/\<img(.*?)src=[\"\'](.*?)[\"\'](.*?)\/\>/', get_avatar( $comment->comment_author_email, 16 ), $src);

$waplString .= '<row class="commentRowAvatar"><cell><externalImage filetype="jpg" scale="0"><url>'.htmlspecialchars($src[2]).'</url></externalImage><chars make_safe="1"><value>[b]'.architectAdminCharsOther($comment->comment_author).'[/b]</value></chars></cell></row>';
$waplString .= '<wordsChunk class="commentRowAuthor"><quick_text>'.strip_tags(sprintf(__('Submitted on <a href="%1$s">%2$s at %3$s</a>'), get_comment_link($comment->comment_ID),mysql2date(__('Y/m/d'), $comment->comment_date ), mysql2date(__('g:i A'), $comment->comment_date ))).'</quick_text></wordsChunk>';

$content = str_replace('&amp;', '&amp; ', $comment->comment_content);
$content = str_replace(
	array('</A>'),
	array('</a>'),
	$content
);
$waplString .= '<wordsChunk class="commentRowComment"><quick_text>'.$content.'</quick_text></wordsChunk>';


$waplString .= '
<form>
	<action>edit-comments.php?action=add-comment</action>
	<formItem item_type="hidden"><name>c</name><value>'.$comment->comment_ID.'</value></formItem>
	<formItem item_type="textarea"><name>comment</name><label>'.__('Your Comment').'</label><event><name>onkeyup</name><action>grow(this);</action></event></formItem>
	<formItem item_type="submit"><name>add_comment_save</name><label>'.__('Add').'</label></formItem>
</form>';

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>