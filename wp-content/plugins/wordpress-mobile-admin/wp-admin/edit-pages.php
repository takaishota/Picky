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
$pageTitle = __('Edit Pages');
$pageId = 'editPages';

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'page.php');

$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]edit pages[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

// Show update message
if((isset($_REQUEST['message']) && $_REQUEST['message'] == true) || (isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true))
{
	if(isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true)
	{
		$message = __('Page deleted');
	} else
	{
		$message = __('Page updated.');
	}
	$waplString .= '<wordsChunk class="updated"><quick_text>'.$message.'</quick_text></wordsChunk>';
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
}

// Get pages to edit
$post_stati  = array(	);

$post_stati = apply_filters('page_stati', $post_stati);

$pagesPerPage = 10;
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
	if ( empty($pagenum) )
		$pagenum = 1;

$offset = (($pagenum-1) * $pagesPerPage);
		
$query = array('post_type' => 'page', 'orderby' => 'menu_order title',
	'offset' => $offset, 'posts_per_page' => $pagesPerPage, 'posts_per_archive_page' => -1, 'order' => 'asc');

$post_status_label = __('Pages');

if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$query['post_status'] = $_GET['post_status'];
	$query['perm'] = 'readable';
}

$query = apply_filters('manage_pages_query', $query);
wp($query);

global $wp_query;
$posts = $wp_query->posts;

$navString = '';
$waplString .= '<wordsChunk class="dashboardSection"><quick_text>'.__('Page').'</quick_text><display_as>h3</display_as></wordsChunk>';

if($posts)
{

	$total_query = array('post_type' => 'page', 'orderby' => 'menu_order title',
	'posts_per_page' => -1, 'posts_per_archive_page' => -1, 'order' => 'asc');
	$total_query = apply_filters('manage_pages_query', $total_query);
	
	wp($total_query);
	
	$num_pages = ceil($wp_query->post_count / $pagesPerPage);
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'pagenum', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $num_pages,
		'current' => $pagenum,
		'type' => 'array'
	));
	
	if($page_links)
	{
		$page_links_text = sprintf(__( 'Displaying %s&#8211;%s of %s' ),
			number_format_i18n( ( $pagenum - 1 ) * $pagesPerPage + 1 ),
			number_format_i18n( min( $pagenum * $pagesPerPage, $wp_query->post_count ) ),
			number_format_i18n( $wp_query->post_count ),
			$page_links
		);
		
		$navString = '<wordsChunk class="paginatedNav"><quick_text>'.$page_links_text.' ';
		
		foreach($page_links as $val)
		{
			preg_match('/<a(.*?)href=[\'\"](.*?)[\'\"](.*?)>(.*?)<\/a>/', $val, $url);
			
			if(empty($url))
			{
				preg_match('/<span(.*?)>(.*?)<\/span>/', $val, $span);
				$navString .= '[span=current]'.$span[2].'[/span]';
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
}

$wapl = new WordPressAdminWapl;
if($posts)
{
	$i = 0;
	
	foreach($posts as $post)
	{
		
		$class = '';
		if($i%2)
		{
			$class .= 'odd';
		} else
		{
			$class .= 'even';
		}
		
		$edit_link = get_edit_post_link( $post->ID );
		
		$waplString .= '<wordsChunk class="pageRow'.$class.'"><quick_text>';
		if ( current_user_can('edit_page', $post->ID) )
		{
			$waplString .= '[url='.$edit_link.']';
		}
		
		$waplString .= $wapl->format_text($post->post_title);
		
		if($post->post_status == 'draft')
		{
			$waplString .= ' [b]- '.__('Draft').'[/b]';
		}
		
		if ( current_user_can('edit_page', $post->ID) )
		{
			$waplString .= '[/url]';
		}
		
		$waplString .= '</quick_text></wordsChunk>';
		
		
		if ( current_user_can('edit_page', $post->ID) ) 
		{
			$editString = '[url='.$edit_link.'&amp;post_type=page]'.__('Edit').'[/url]';
		} else
		{
			$editString = '';
		}
		
		if ( current_user_can('delete_page', $post->ID) ) 
		{
			if($editString != '')
			{
				$deleteString = ' | ';
			}
			if(get_bloginfo('version') >= '3.0')
			{
				$deleteString .= '[url='.wp_nonce_url("post.php?action=delete&amp;post_type=page&amp;post=$post->ID&amp;architectBypass=true", 'delete-page_' . $post->ID).'][color=#BC0B0B]'.__('Delete').'[/color][/url]';
			} else
			{
				$deleteString .= '[url='.wp_nonce_url("page.php?action=delete&amp;post=$post->ID&amp;architectBypass=true", 'delete-page_' . $post->ID).'][color=#BC0B0B]'.__('Delete').'[/color][/url]';
			}
		} else
		{
			$deleteString = '';
		}
		$waplString .= '<wordsChunk class="pageRowControls'.$class.'"><quick_text>'.$editString.$deleteString.'</quick_text></wordsChunk>';
		
		$i++;	
	}
} else
{
	$waplString .= '<wordsChunk class="postRoweven"><quick_text>No Pages Found</quick_text></wordsChunk>'; 
}

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>