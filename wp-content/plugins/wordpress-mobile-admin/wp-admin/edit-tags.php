<?php
if(isset($_REQUEST['taxonomy']) AND $_REQUEST['taxonomy'] == 'category')
{
	$thisMainMenu = 'Posts';
	$pageId = 'editCategories';
	$pageRedirect = true;
	include(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'categories.php');
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

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'deafult';
	
switch($_REQUEST['action'])
{
	case 'edit':
		$pageTitle = __('Edit Tag');
		$pageId = 'editTag';
		$thisMainMenu = 'Posts';
		break;
		
	case 'editedtag':
	case 'add':
	case 'delete':
		break;
		
	default:
		$pageTitle = __('Tags');
		$pageId = 'editTags';
		$thisMainMenu = 'Posts';
		break;
}
 
// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'edit.php');

// Perform different actions
switch($_REQUEST['action'])
{
	case 'edit':
		// Edit a tag
		
		// Setup some variables
		if(isset($_REQUEST['taxonomy']))
			$taxonomy = $_REQUEST['taxonomy'];
		if ( empty($taxonomy) )
			$taxonomy = 'post_tag';
		
		$tag = get_tag($_REQUEST['tag_ID']);
		
		$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]edit tag[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';
		$waplString .= '
		<form>
			<action>edit-tags.php?action=editedtag</action>
			<formItem item_type="hidden"><name>_wp_http_referer</name><value>edit-tags.php</value></formItem>
			<formItem item_type="hidden"><name>tag_ID</name><value>'.$tag->term_id.'</value></formItem>
			<formItem item_type="hidden"><name>taxonomy</name><value>'.$taxonomy.'</value></formItem>
			<formItem item_type="hidden"><name>_wpnonce</name><value>'.wp_create_nonce('update-tag_'.$tag->term_ID).'</value></formItem>
			<formItem item_type="text"><label>'.__('Tag name').'</label><name>name</name><value>'.$tag->name.'</value></formItem>
			<formItem item_type="text"><label>'.__('Tag slug').'</label><name>slug</name><value>'.$tag->slug.'</value></formItem>
			<formItem item_type="textarea"><label>'.__('Description').'</label><name>description</name><value>'.$tag->description.'</value></formItem>
			<formItem item_type="submit"><label>'.__('Update Tag').'</label><name>submit</name></formItem>
		</form>';
		
		break;
	case 'editedtag':
		// Save edit tag changes
		$tag_ID = (int) $_REQUEST['tag_ID'];
		if ( !current_user_can('manage_categories') )
		{
			header("Location:".admin_url().$_POST['_wp_http_referer']);
		}
		
		// Setup some variables
		if(isset($_REQUEST['taxonomy']))
			$taxonomy = $_REQUEST['taxonomy'];
		if ( empty($taxonomy) )
			$taxonomy = 'post_tag';
		
		if(wp_update_term($tag_ID, $taxonomy, $_POST))
		{
			header("Location:".admin_url().$_POST['_wp_http_referer'].'?message=1');
		}
		
		break;
	case 'delete':
		// Delete a tag
		$tag_ID = (int) $_GET['tag_ID'];
		if ( !current_user_can('manage_categories') )
		{
			header("Location:".admin_url().'edit-tags.php');
		}
		
		// Setup some variables
		if(isset($_REQUEST['taxonomy']))
			$taxonomy = $_REQUEST['taxonomy'];
		if ( empty($taxonomy) )
			$taxonomy = 'post_tag';
			
		if(wp_delete_term( $tag_ID, $taxonomy))
		{
			header("Location:".admin_url().'edit-tags.php?message=1&deleted=1');
		}
		
		break;

	case 'add':
		// Add a tag
		if ( !current_user_can('manage_categories') )
		{
			header("Location:".admin_url().$_POST['_wp_http_referer']);
		}
		
		// Setup some variables
		if(isset($_REQUEST['taxonomy']))
			$taxonomy = $_REQUEST['taxonomy'];
		if ( empty($taxonomy) )
			$taxonomy = 'post_tag';
			
		if(wp_insert_term($_POST['name'], $taxonomy, $_POST))
		{
			header("Location:".admin_url().$_POST['_wp_http_referer'].'&message=1&added=1');
		}
		break;
		
	default:
		// List Tags
		 
		$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]tags[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';
		
		// Show update message
		if((isset($_REQUEST['message']) && $_REQUEST['message'] == true) || (isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true))
		{
			if(isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true)
			{
				$message = __('Tag deleted.');
			} else if(isset($_REQUEST['added']) && $_REQUEST['added'] == true)
			{
				$message = __('Tag added.');
			} else
			{
				$message = __('Tag updated.');
			}
			$waplString .= '<wordsChunk class="updated"><quick_text>'.$message.'</quick_text></wordsChunk>';
			$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
		}
		
		// Setup some variables
		if(isset($_REQUEST['taxonomy']))
			$taxonomy = $_REQUEST['taxonomy'];
		if ( empty($taxonomy) )
			$taxonomy = 'post_tag';
		
		// Pagination
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
		if ( empty($pagenum) )
			$pagenum = 1;
			
		$tags_per_page = get_user_option('edit_tags_per_page');
		if ( empty($tags_per_page) )
			$tags_per_page = 10;
		
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => ceil(wp_count_terms($taxonomy) / $tags_per_page),
			'current' => $pagenum,
			'type' => 'array'
		));
		
		// Navigation
		$navString = '';
		if($page_links)
		{
			$navString = '<wordsChunk class="paginatedNav"><quick_text>';
			
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
		
		// Pagination header
		$waplString .= '<wordsChunk class="dashboardSection"><quick_text>'.__('Name').'</quick_text><display_as>h3</display_as></wordsChunk>';
		$wapl = new WordPressAdminWapl;
		
		// Setup some initial variables
		$searchterms = isset( $_GET['s'] ) ? trim( $_GET['s'] ) : '';
		
		// Get a page worth of tags
		$start = ($pagenum - 1) * $tags_per_page;
		$args = array('offset' => $start, 'number' => $tags_per_page, 'hide_empty' => 0);
		if ( !empty( $searchterms ) ) {
			$args['search'] = $searchterms;
		}
		$tags = get_terms( $taxonomy, $args );
		
		// Loop through tags
		$i = 0;
		foreach($tags as $tag)
		{
			// Specify which class the row should display as
			$class = '';
			if($i%2)
			{
				$class .= 'odd';
			} else
			{
				$class .= 'even';
			}
			
			// Construct URLs
			$edit_url = 'edit-tags.php?action=edit&amp;taxonomy='.$taxonomy.'&amp;tag_ID='.$tag->term_id;
			$delete_url = wp_nonce_url("edit-tags.php?action=delete&amp;taxonomy=$taxonomy&amp;tag_ID=$tag->term_id", 'delete-tag_' . $tag->term_id);
			 
			// Show the tag title
			$waplString .= '<wordsChunk class="tagRow'.$class.'"><quick_text>';
			
			if (current_user_can('manage_categories'))
			{
				$waplString .= '[url='.$edit_url.']';
			}
		
			$waplString .= $wapl->format_text($tag->name);
		
			if (current_user_can('manage_categories'))
			{
				$waplString .= '[/url]';
			}
			$waplString .= ' - '.__('Posts').': '.$tag->count.'</quick_text></wordsChunk>';
			
			// Tag controls
			if(current_user_can('manage_categories'))
			{
				$waplString .= '<wordsChunk class="tagRowControls'.$class.'"><quick_text>';
				$waplString .= '[url='.$edit_url.']'.__('Edit').'[/url]';
				$waplString .= ' | [url='.$delete_url.'][color=#BC0B0B]'.__('Delete').'[/color][/url]'; 
				$waplString .= '</quick_text></wordsChunk>';
			}
			
			$i++;
		}
		
		if(empty($tags))
		{
			$waplString .= '<wordsChunk class="tagRoweven"><quick_text>'.__('None').'</quick_text></wordsChunk>';
		}
		
		// Navigation
		$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
		$waplString .= $navString;
		$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
		
		if(current_user_can('manage_categories'))
		{
			// Add a new tag
			$waplString .= '
			<wordsChunk><quick_text>'.__('Add a New Tag').'</quick_text><display_as>h4</display_as></wordsChunk>
			<form>
				<action>edit-tags.php?action=add</action>
				<formItem item_type="hidden"><name>taxonomy</name><value>'.$taxonomy.'</value></formItem>
				<formItem item_type="hidden"><name>_wpnonce</name><value>'.wp_create_nonce('add-tag').'</value></formItem>
				<formItem item_type="hidden"><name>_wp_http_referer</name><value>edit-tags.php</value></formItem>
				<formItem item_type="text"><label>'.__('Tag name').'</label><name>name</name><value></value></formItem>
				<formItem item_type="text"><label>'.__('Tag slug').'</label><name>slug</name><value></value></formItem>
				<formItem item_type="textarea"><label>'.__('Description').'</label><name>description</name><value></value></formItem>
				<formItem item_type="submit"><label>'.__('Add Tag').'</label><name>submit</name></formItem>
			</form>';
		}
		
		break;
}

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>