<?php
if(isset($_REQUEST['post_type']) AND $_REQUEST['post_type'] == 'page')
{
	$thisMainMenu = 'Pages';
	$pageRedirect = true;
	include(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'edit-pages.php');
	exit;
}

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
$pageTitle = __('Edit Posts');
$pageId = 'editPosts';
$thisMainMenu = 'Posts';

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'edit.php');

$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]edit posts[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

// Navigation string
$waplString .= '<wordsChunk class="commentNavLinks"><quick_text>[url=edit.php]'.__('All').'[/url] | [url=edit.php?post_status=publish][color=green]'.__('Published').'[/color][/url] | [url=edit.php?post_status=draft][color=#E66F00]'.__('Draft').'[/color][/url]</quick_text></wordsChunk>';
$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';

// Show update message
if((isset($_REQUEST['message']) && $_REQUEST['message'] == true) || (isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true))
{
	if(isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true)
	{
		$message = __('Post deleted.');
	} else
	{
		$message = __('Post updated.');
	}
	$waplString .= '<wordsChunk class="updated"><quick_text>'.$message.'</quick_text></wordsChunk>';
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
}

global $wp_query, $wp;

if ( !isset( $_GET['paged'] ) )
	$_GET['paged'] = 1;

add_filter('edit_posts_per_page', 'architect_return_posts_per_page');
list($post_stati, $avail_post_stati) = wp_edit_posts_query();

$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $wp_query->max_num_pages,
	'current' => $_GET['paged'],
	'type' => 'array'
));

// Navigation
$navString = '';
if($page_links)
{
	$page_links_text = sprintf(__( 'Displaying %s&#8211;%s of %s' ),
		number_format_i18n( ( $_GET['paged'] - 1 ) * $wp_query->query_vars['posts_per_page'] + 1 ),
		number_format_i18n( min( $_GET['paged'] * $wp_query->query_vars['posts_per_page'], $wp_query->found_posts ) ),
		number_format_i18n( $wp_query->found_posts ),
		$page_links
	);
	$navString = '<wordsChunk class="paginatedNav"><quick_text>'.$page_links_text.' ';
	
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
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
}

$i=0;
$waplString .= '<wordsChunk class="dashboardSection"><quick_text>'.__('Post').'</quick_text><display_as>h3</display_as></wordsChunk>';
$wapl = new WordPressAdminWapl;

$posts = architect_post_rows();
foreach($posts as $val)
{
	$class = '';
	if($i%2)
	{
		$class .= 'odd';
	} else
	{
		$class .= 'even';
	}
	$waplString .= '<wordsChunk class="postRow'.$class.'"><quick_text>';
	
	if (current_user_can('edit_post', $val->ID))
	{
		$waplString .= '[url=post.php?action=edit&amp;post='.$val->ID.']';
	}

	$waplString .= $wapl->format_text($val->post_title);

	if($val->post_status == 'draft')
	{
		$waplString .= ' [b]- '.__('Draft').'[/b]';
	}
	
	if(current_user_can('edit_post', $val->ID))
	{
		$waplString .= '[/url]';
	}
	$waplString .= '</quick_text></wordsChunk>';
	$waplString .= '<wordsChunk class="postRowControls'.$class.'"><quick_text>';
	
	if(current_user_can('edit_post', $val->ID))
	{
		$waplString .= '[url=post.php?action=edit&amp;post='.$val->ID.']'.__('Edit').'[/url]';
	}

	if (current_user_can('delete_post', $val->ID)) 
	{
		$waplString .= ' | [url='.wp_nonce_url("post.php?action=delete&amp;post=$val->ID&amp;architectBypass=true", 'delete-post_' . $val->ID).'][color=#BC0B0B]'.__('Delete').'[/color][/url]'; 
	}
	
	$waplString .= '</quick_text></wordsChunk>';
	
	$i++;
}

if(empty($posts))
{
	$waplString .= '<wordsChunk class="postRoweven"><quick_text>'.__('No posts found').'</quick_text></wordsChunk>';
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
