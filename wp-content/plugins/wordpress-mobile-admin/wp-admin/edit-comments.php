<?php
if ( ! defined('ABSPATH') ) die();
// Admin bootstaps
require_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'admin.php');
require_once(ABSPATH.'wp-includes'.DIRECTORY_SEPARATOR.'pluggable.php');

if(isset($_REQUEST['bulk_type']))
{
	if(current_user_can('moderate_comments'))
	{
		if(isset($_REQUEST['bulk_type_bottom']) AND $_REQUEST['bulk_type_bottom'] != '')
		{
			$_REQUEST['bulk_type'] = $_REQUEST['bulk_type_bottom'];
		}
		switch($_REQUEST['bulk_type'])
		{
			case 'delete' :
				$i=0;
				$deleted = 0;
				$message = 0;
				foreach($_REQUEST as $key => $val)
				{
					if(preg_match('/^comment_([0-9]+)/', $key, $matches))
					{
						$i++;
						$deleted = 1;
						$message = 1;
						wp_delete_comment($matches[1]);
					}
				}
				if($i > 1)
				{
					$multiple = 1;
				} else
				{
					$multiple = 0;
				}
				header("Location:".admin_url().'edit-comments.php?comment_status='.$_REQUEST['comment_status'].'&message='.$message.'&deleted='.$deleted.'&multiple='.$multiple);
				break;
			case 'spam':
			case 'approve':
			case 'hold':
				$i=0;
				$message = 0;
				foreach($_REQUEST as $key => $val)
				{
					if(preg_match('/^comment_([0-9]+)/', $key, $matches))
					{
						$i++;
						$message = 1;
						wp_set_comment_status($matches[1], $_REQUEST['bulk_type']);
					}
				}
				header("Location:".admin_url().'edit-comments.php?comment_status='.$_REQUEST['comment_status'].'message='.$message);
				break;
		}
	}
}
if(isset($_REQUEST['action']) AND $_REQUEST['action'] == 'add-comment')
{
	if(isset($_POST) AND !empty($_POST))
	{
		// Get current user
		$current_user = wp_get_current_user();
		$comment = get_comment($_REQUEST['c']);
		
		$_POST['comment_post_ID'] = $comment->comment_post_ID;
		$_POST['comment_author'] = $current_user->display_name;
		$_POST['comment_author_email'] = $current_user->user_email;
		$_POST['comment_author_url'] = $current_user->user_url;
		$_POST['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
		$_POST['comment_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$_POST['comment_content'] = $_POST['comment'];
		$_POST['comment_date'] = date("Y-m-d H:i:s");
		$_POST['comment_date_gmt'] = get_gmt_from_date($_POST['comment_date']);
		$_POST['comment_karma'] = 0;
		$_POST['comment_approved'] = 1;
		$_POST['comment_parent'] = $_POST['c'];
		$_POST['user_id'] = $current_user->ID;
		
		if(wp_insert_comment($_POST))
		{
			header("Location:".admin_url().'edit-comments.php?message=1&added=1');
		}
	}
	require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'add-comment.php');
	die();
}

// Do this here just to be on the safe side
if(!is_user_logged_in())
{
	header("Location:".get_bloginfo('home').'/wp-login.php');
	exit();
}
require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'functions.php');

$waplString = '';
$pageTitle = __('Edit Comments');
$pageId = 'editComments';
$subNav = false;

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');

$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]edit comments[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

$waplString .= '<wordsChunk class="commentNavLinks"><quick_text>[url=edit-comments.php]'.__('All').'[/url] | [url=edit-comments.php?comment_status=approved][color=green]'.__('Approved').'[/color][/url] | [url=edit-comments.php?comment_status=moderated][color=#E66F00]'.__('Pending').'[/color][/url] | [url=edit-comments.php?comment_status=spam][color=red]'.__('Spam').'[/color][/url]</quick_text></wordsChunk>';
$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';

if((isset($_REQUEST['message']) && $_REQUEST['message'] == true) || (isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true))
{
	$class = 'updated';
	if(isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true)
	{
		if(isset($_REQUEST['multiple']) AND $_REQUEST['multiple'] == 1)
		{
			$commentMessageCount = 2;
		} else
		{
			$commentMessageCount = 1;
		}
		$message = sprintf( _n( 'Comment deleted', 'Comments deleted', $commentMessageCount ), $_REQUEST['deleted'] );
	} else if(isset($_REQUEST['added']) && $_REQUEST['added'] == true)
	{
		$message = __('Comment added');
	} else if(isset($_REQUEST['error']) && $_REQUEST['error'] == true)
	{
		$message = $_REQUEST['errorMsg'];
		$class = ' error';
	} else
	{
		$message = __('Comment updated');
	}
	$waplString .= '<wordsChunk class="'.$class.'"><quick_text>'.$message.'</quick_text></wordsChunk>';
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
}

$i=0;

$comment_status = isset($_REQUEST['comment_status']) ? $_REQUEST['comment_status'] : 'all';
$search_dirty = ( isset($_GET['s']) ) ? $_GET['s'] : '';
if ( isset( $_GET['apage'] ) )
	$page = abs( (int) $_GET['apage'] );
else
	$page = 1;

$comments_per_page = 10;

$start = $offset = ( $page - 1 ) * $comments_per_page;
$post_id = isset($_REQUEST['p']) ? (int) $_REQUEST['p'] : 0;
$comment_type = !empty($_GET['comment_type']) ? esc_attr($_GET['comment_type']) : '';

list($_comments, $total) = _wp_get_comment_list( $comment_status, $search_dirty, $start, $comments_per_page + 8, $post_id, $comment_type ); // Grab a few extra

$_comment_post_ids = array();
foreach ( $_comments as $_c ) {
	$_comment_post_ids[] = $_c->comment_post_ID;
}
$_comment_pending_count_temp = (array) get_pending_comments_num($_comment_post_ids);
foreach ( (array) $_comment_post_ids as $_cpid )
	$_comment_pending_count[$_cpid] = isset( $_comment_pending_count_temp[$_cpid] ) ? $_comment_pending_count_temp[$_cpid] : 0;
if ( empty($_comment_pending_count) )
	$_comment_pending_count = array();

$comments = array_slice($_comments, 0, $comments_per_page);
$extra_comments = array_slice($_comments, $comments_per_page);

$page_links = paginate_links( array(
	'base' => add_query_arg( 'apage', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil($total / $comments_per_page),
	'current' => $page,
	'type' => 'array'
));

// Navigation
$navString = '';
if($page_links)
{
	$page_links_text = sprintf( __( 'Displaying %s&#8211;%s of %s' ),
		number_format_i18n( $start + 1 ),
		number_format_i18n( min( $page * $comments_per_page, $total ) ),
		number_format_i18n( $total ),
		$page_links
	);
	$navString = '<wordsChunk class="paginatedNav"><quick_text>[span=text]'.$page_links_text.'[/span] ';
	
	foreach($page_links as $val)
	{
		preg_match('/<a(.*?)href=[\'\"](.*?)[\'\"](.*?)>(.*?)<\/a>/', $val, $url);
		
		if(empty($url))
		{
			preg_match('/<span(.*?)>(.*?)<\/span>/', $val, $span);
			$navString .= '[span=current]'.architectAdminCharsOther($span[2]).'[/span]';
		} else
		{
			$navString .= '[url='.$url[2].']'.architectAdminCharsOther($url[4]).'[/url]';
		}
		
	}
	$navString .= '</quick_text></wordsChunk>';
	
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
	$waplString .= $navString;
}

if(current_user_can('moderate_comments'))
{
	$waplString .= '</layout><layout start_stack="div"><row><cell><form class="bulk_type"><action>edit-comments.php</action>
		<formItem item_type="hidden"><name>comment_status</name><value>'.$comment_status.'</value></formItem>
		<formItem item_type="select"><name>bulk_type</name>
			<possibility><label>'.__('Bulk Actions').'</label><value></value></possibility>
			<possibility><label>'.__('Unapprove').'</label><value>hold</value></possibility>
			<possibility><label>'.__('Approve').'</label><value>approve</value></possibility>
			<possibility><label>'.__('Mark as Spam').'</label><value>spam</value></possibility>
			<possibility><label>'.__('Delete').'</label><value>delete</value></possibility>
		</formItem>
		<formItem item_type="submit"><name>bulk_submit</name><label>'.__('Apply').'</label></formItem></form></cell></row></layout><layout start_stack="table">';
}

$waplString .= '<wordsChunk class="dashboardSection"><quick_text>'.__('Comment').'</quick_text><display_as>h3</display_as></wordsChunk>';

$wapl = new WordPressAdminWapl;
foreach($comments as $val)
{
	if(current_user_can('moderate_comments'))
	{
		$waplString .= '<row><cell><form><formItem class="comment_checkbox" item_type="checkbox"><name>comment_'.$val->comment_ID.'</name></formItem></form></cell></row>';
	}
	if($val->comment_approved == 0)
	{
		$class = 'pending';
	} else
	{
		$class = 'approved';
	}
	
	preg_match('/\<img(.*?)src=[\"\'](.*?)[\"\'](.*?)\/\>/', get_avatar( $val->comment_author_email, 16 ), $src);

	$waplString .= '<row class="commentRowAvatar'.$class.'"><cell><externalImage filetype="jpg" scale="0"><url>'.htmlspecialchars($src[2]).'</url></externalImage><chars make_safe="1"><value>[b]'.architectAdminCharsOther($wapl->format_text($val->comment_author)).'[/b]</value></chars></cell></row>';
	$waplString .= '<wordsChunk class="commentRowAuthor'.$class.'"><quick_text>'.strip_tags(sprintf(__('Submitted on <a href="%1$s">%2$s at %3$s</a>'), get_comment_link($val->comment_ID),mysql2date(__('Y/m/d'), $val->comment_date ), mysql2date(__('g:i A'), $val->comment_date ))).'</quick_text></wordsChunk>';
	
	// Split up long comments
	$content = architectAdminSplit($wapl->format_text($val->comment_content));
	
	$waplString .= '<wordsChunk class="commentRowComment'.$class.'"><quick_text>'.$content.'</quick_text></wordsChunk>';
	
	if(current_user_can('moderate_comments'))
	{
		$post = get_post($val->comment_post_ID);
		
		$delete_url = esc_url( wp_nonce_url( "comment.php?comment_status=".$comment_status."&action=deletecomment&p=$post->ID&c=$val->comment_ID", "delete-comment_$val->comment_ID" ) );
		$approve_url = esc_url( wp_nonce_url( "comment.php?comment_status=".$comment_status."&action=approvecomment&p=$post->ID&c=$val->comment_ID", "approve-comment_$val->comment_ID" ) );
		$unapprove_url = esc_url( wp_nonce_url( "comment.php?comment_status=".$comment_status."&action=unapprovecomment&p=$post->ID&c=$val->comment_ID", "unapprove-comment_$val->comment_ID" ) );
		$spam_url = esc_url( wp_nonce_url( "comment.php?comment_status=".$comment_status."&action=deletecomment&dt=spam&p=$post->ID&c=$val->comment_ID", "delete-comment_$val->comment_ID" ) );
		
		if($val->comment_approved == 0)
		{
			$urlString = '[url='.$approve_url.'][color=#006505]'.__('Approve').'[/color][/url] | ';
		} else
		{
			$urlString = '[url='.$unapprove_url.'][color=#D98500]'.__('Unapprove').'[/color][/url] | ';
		}
		
		$urlString .= '[url=edit-comments.php?action=add-comment&amp;c='.$val->comment_ID.']'.__('Reply').'[/url] | ';
		$urlString .= '[url='.$spam_url.']'.__('Spam').'[/url] | ';
		$urlString .= '[url='.$delete_url.'][color=#BC0B0B]'.__('Delete').'[/color][/url]';
		
		$waplString .= '<wordsChunk class="commentRowControls'.$class.'"><quick_text>'.$urlString.'</quick_text></wordsChunk>';
	} else
	{
		$waplString .= '<wordsChunk class="commentRowControls'.$class.'"><quick_text></quick_text></wordsChunk>';
	}
}

if(current_user_can('moderate_comments'))
{
	
	$waplString .= '</layout><layout start_stack="div"><row><cell><form class="bulk_type"><action>edit-comments.php</action>
		<formItem item_type="select"><name>bulk_type_bottom</name>
			<possibility><label>'.__('Bulk Actions').'</label><value></value></possibility>
			<possibility><label>'.__('Unapprove').'</label><value>hold</value></possibility>
			<possibility><label>'.__('Approve').'</label><value>approve</value></possibility>
			<possibility><label>'.__('Mark as Spam').'</label><value>spam</value></possibility>
			<possibility><label>'.__('Delete').'</label><value>delete</value></possibility>
		</formItem>
		<formItem item_type="submit"><name>bulk_submit</name><label>'.__('Apply').'</label></formItem></form></cell></row></layout><layout start_stack="table">';
}

if(empty($comments))
{
	$waplString .= '<wordsChunk class="commentRowAuthor"><quick_text>No Comments Found</quick_text></wordsChunk>';
}

// Navigation
$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
$waplString .= $navString;
$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>