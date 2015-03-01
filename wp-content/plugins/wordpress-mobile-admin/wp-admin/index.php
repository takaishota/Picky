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

$waplString = '';
$pageTitle = __('Dashboard');
$pageId = 'dashboard';
//$subNav = false;

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'dashboard.php');

$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]dashboard[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

$waplString .= '<wordsChunk class="dashboardSection"><quick_text>'.__('Right Now').'</quick_text><display_as>h3</display_as></wordsChunk>';

$num_posts = wp_count_posts( 'post' );
$num_pages = wp_count_posts( 'page' );
$num_comm = wp_count_comments( );
	
// Number of posts
if ( current_user_can( 'edit_posts' ) ) 
{
	$waplString .= '<row class=""><cell><externalLink><label>'.$num_posts->publish.' '._n( 'Post', 'Posts', intval($num_posts->publish)).'</label><url>edit.php</url></externalLink></cell></row>';
} else
{
	$waplString .= '<row class=""><cell><chars><value>'.$num_posts->publish.' '._n( 'Post', 'Posts', intval($num_posts->publish)).'</value></chars></cell></row>';
}

if ( current_user_can( 'edit_pages' ) ) 
{
	$waplString .= '<row class=""><cell><externalLink><label>'.$num_pages->publish.' '._n('Page', 'Pages', intval($num_pages->publis)).'</label><url>edit.php?post_type=page</url></externalLink></cell></row>';
} else
{
	$waplString .= '<row class=""><cell><chars><value>'.$num_pages->publish.' '._n('Page', 'Pages', intval($num_pages->publis)).'</value></chars></cell></row>';
}
$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';

// Number of Comments
if ( current_user_can( 'moderate_comments' ) ) 
{
	$waplString .= '<row class=""><cell class="commentAll"><externalLink><label>'.$num_comm->total_comments.' '._n( 'Comment', 'Comments', intval($num_comm->total_comments)).'</label><url>edit-comments.php</url></externalLink></cell></row>';
	$waplString .= '<row class=""><cell class="commentApproved"><externalLink><label>'.$num_comm->approved.' '._n( 'Approved', 'Approved', intval($num_comm->approved)).'</label><url>edit-comments.php?comment_status=approved</url></externalLink></cell></row>';
	$waplString .= '<row class=""><cell class="commentPending"><externalLink><label>'.$num_comm->moderated.' '._n( 'Pending', 'Pending', intval($num_comm->moderated)).'</label><url>edit-comments.php?comment_status=moderated</url></externalLink></cell></row>';
	$waplString .= '<row class=""><cell class="commentSpam"><externalLink><label>'.$num_comm->spam.' '._n( 'Spam', 'Spam', intval($num_comm->spam)).'</label><url>edit-comments.php?comment_status=spam</url></externalLink></cell></row>';
} else
{
	$waplString .= '<row class=""><cell><chars><value>'.$num_comm->total_comments.' '._n( 'Comment', 'Comments', intval($num_comm->total_comments)).'</value></chars></cell></row>';
	$waplString .= '<row class=""><cell class="commentApproved"><chars><value>'.$num_comm->approved.' '._n( 'Approved', 'Approved', intval($num_comm->approved)).'</value></chars></cell></row>';
	$waplString .= '<row class=""><cell class="commentPending"><chars><value>'.$num_comm->moderated.' '._n( 'Pending', 'Pending', intval($num_comm->moderated)).'</value></chars></cell></row>';
	$waplString .= '<row class=""><cell class="commentSpam"><chars><value>'.$num_comm->spam.' '._n( 'Spam', 'Spam', intval($num_comm->spam)).'</value></chars></cell></row>';
}
$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';

// Recent Comments
$waplString .= '<wordsChunk class="dashboardSection"><quick_text>'.__('Recent Comments').'</quick_text><display_as>h3</display_as></wordsChunk>';

global $wpdb;

if ( current_user_can('edit_posts') )
	$allowed_states = array('0', '1');
else
	$allowed_states = array('1');

// Select all comment types and filter out spam later for better query performance.
$comments = array();
$start = 0;

while ( count( $comments ) < 3 && $possible = $wpdb->get_results( "SELECT * FROM $wpdb->comments ORDER BY comment_date_gmt DESC LIMIT $start, 50" ) ) {

	foreach ( $possible as $comment ) {
		if ( count( $comments ) >= 3 )
			break;
		if ( in_array( $comment->comment_approved, $allowed_states ) )
			$comments[] = $comment;
	}

	$start = $start + 50;
}

if ( $comments )
{
	$wapl = new WordPressAdminWapl;
	foreach ( $comments as $comment )
	{
		$GLOBALS['comment'] =$comment;
		$comment_post_url = get_edit_post_link( $comment->comment_post_ID );
		$comment_post_title = $wapl->format_text(strip_tags(get_the_title( $comment->comment_post_ID )));
		
		$approvedString = '';
		$dashboardClass = '';
		if($comment->comment_approved == 0)
		{
			$approvedString .= ' [i][size=-2]['.__('Pending').'][/size][/i] ';
			$dashboardClass .= 'pending';
		} else
		{
			$dashboardClass .= 'approved';
		}
		
		preg_match('/\<img(.*?)src=[\"\'](.*?)[\"\'](.*?)\/\>/', get_avatar( $comment->comment_author_email, 16 ), $src);
		
		$waplString .= '<row class="dashboardCommentInfo'.$dashboardClass.'"><cell><externalImage filetype="jpg" scale="0"><url>'.htmlspecialchars($src[2]).'</url></externalImage><chars make_safe="1"><value>[color=#999999]'.sprintf( __( 'From %1$s on %2$s%3$s' ),architectAdminCharsOther(get_comment_author()).'[/color]',$comment_post_title,$approvedString).'</value></chars></cell></row>';
		
		$content = architectAdminSplit($wapl->format_text($comment->comment_content));
		
		$waplString .= '<wordsChunk class="dashboardCommentContent'.$dashboardClass.'"><quick_text>'.$content.'</quick_text></wordsChunk>';
		
		if ( current_user_can('edit_post', $comment->comment_post_ID) ) 
		{
			$delete_url = esc_url( wp_nonce_url( "comment.php?return=index&action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );
			$spam_url = esc_url( wp_nonce_url( "comment.php?return=index&action=deletecomment&dt=spam&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );
			
			if($comment->comment_approved == 0)
			{
				$approve_url = esc_url( wp_nonce_url( "comment.php?return=index&action=approvecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "approve-comment_$comment->comment_ID" ) );
				$waplString .= '<wordsChunk class="dashboardCommentControls'.$dashboardClass.'"><quick_text>[url='.$approve_url.'][color=#006505]'.__('Approve').'[/color][/url] | [url='.$spam_url.'][color=#21759B]'.__('Spam').'[/color][/url] | [url='.$delete_url.'][color=#BC0B0B]'.__('Delete').'[/color][/url] | [url=edit-comments.php?action=add-comment&amp;c='.$comment->comment_ID.']'.__('Reply').'[/url]</quick_text></wordsChunk>';
			} else
			{
				$unapprove_url = esc_url( wp_nonce_url( "comment.php?return=index&action=unapprovecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "unapprove-comment_$comment->comment_ID" ) );
				$waplString .= '<wordsChunk class="dashboardCommentControls'.$dashboardClass.'"><quick_text>[url='.$unapprove_url.'][color=#D98500]'.__('Unapprove').'[/color][/url] | [url='.$spam_url.'][color=#21759B]'.__('Spam').'[/color][/url] | [url='.$delete_url.'][color=#BC0B0B]'.__('Delete').'[/color][/url] | [url=edit-comments.php?action=add-comment&amp;c='.$comment->comment_ID.']'.__('Reply').'[/url] </quick_text></wordsChunk>';
			}
		} else
		{
			$waplString .= '<wordsChunk class="dashboardCommentControls'.$dashboardClass.'"><quick_text></quick_text></wordsChunk>';
		}
	}
}
			
// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>