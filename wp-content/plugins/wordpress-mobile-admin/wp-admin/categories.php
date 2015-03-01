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

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'default'; 

switch($_REQUEST['action'])
{
	case 'edit':
		$pageTitle = __('Edit Category');
		$pageId = 'editCategory';
		$thisMainMenu = 'Posts';
		break;
		
	case 'editedcategory':
	case 'add':
	case 'delete':
		break;
		
	default:
		$pageTitle = __('Categories');
		$pageId = 'editCategories';
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
		// Edit a category
		
		$cat = get_category($_REQUEST['cat_ID']);
		
		$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]edit category[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';
		$waplString .= '
		<form>
			<action>edit-tags.php?action=editedcat</action>
			<formItem item_type="hidden"><name>taxonomy</name><value>category</value></formItem>
			<formItem item_type="hidden"><name>_wp_http_referer</name><value>edit-tags.php?taxonomy=category</value></formItem>
			<formItem item_type="hidden"><name>cat_ID</name><value>'.$cat->term_id.'</value></formItem>
			<formItem item_type="hidden"><name>_wpnonce</name><value>'.wp_create_nonce('update-category_'.$cat->term_id).'</value></formItem>
			<formItem item_type="text"><label>'.__('Category Name').'</label><name>cat_name</name><value>'.$cat->name.'</value></formItem>
			<formItem item_type="text"><label>'.__('Category Slug').'</label><name>category_nicename</name><value>'.$cat->slug.'</value></formItem>
			<formItem item_type="select"><label>'.__('Category Parent').'</label><name>category_parent</name><value>'.$cat->category_parent.'</value>
			<possibility><label>None</label><value>-1</value></possibility>';
			
		$args = array('hide_empty' => 0);
		if ( !empty($_GET['s']) )
			$args['search'] = $_GET['s'];

		$categories = get_categories( $args );

		foreach($categories as $catselect)
		{
			if($catselect->term_id != $cat->term_id)
			{
				$waplString .= '<possibility><label>'.$catselect->name.'</label><value>'.$catselect->term_id.'</value></possibility>';
			}
		}
		
		$waplString .= '
			</formItem>
			
			<formItem item_type="textarea"><label>'.__('Description').'</label><name>category_description</name><value>'.$cat->description.'</value></formItem>
			<formItem item_type="submit"><label>'.__('Update Category').'</label><name>submit</name></formItem>
		</form>';
		
		break;
	case 'editedcat':
		// Save edit category changes
		$cat_ID = (int) $_POST['cat_ID'];
		if (!current_user_can('manage_categories'))
		{
			header("Location:".admin_url().$_POST['_wp_http_referer']);
		}
		
		if(wp_update_category($_POST))
		{
			header("Location:".admin_url().$_POST['_wp_http_referer'].'&message=1');
		}
		
		break;
	case 'delete':
		// Delete a category
		$cat_ID = (int) $_GET['cat_ID'];
		if (!current_user_can('manage_categories'))
		{
			header("Location:".admin_url().'edit-tags.php?taxonomy=category');
		}
		
		if ( $cat_ID == get_option('default_category') )
		{
			header("Location:".admin_url().'edit-tags.php?taxonomy=category&message=1&failed=1&failedmessage=Can\'t delete the default category');
		}
			
		if(wp_delete_category($cat_ID))
		{
			header("Location:".admin_url().'edit-tags.php?taxonomy=category&message=1&deleted=1');
		}
		
		break;

	case 'add':
		// Add a category
		if ( !current_user_can('manage_categories') )
		{
			header("Location:".admin_url().$_POST['_wp_http_referer']);
		}
		
					
		if(wp_insert_category($_POST ))
		{
			header("Location:".admin_url().$_POST['_wp_http_referer'].'&message=1&added=1');
		}
		break;
		
	default:
		// List Categories
		 
		$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]categories[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';
		
		// Show update message
		if((isset($_REQUEST['message']) && $_REQUEST['message'] == true) || (isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true))
		{
			if(isset($_REQUEST['deleted']) && $_REQUEST['deleted'] == true)
			{
				$message = __('Category deleted.');
			} else if(isset($_REQUEST['added']) && $_REQUEST['added'] == true)
			{
				$message = __('Category added.');
			} else if(isset($_REQUEST['failed']) && $_REQUEST['failed'] == true)
			{
				$message = $_REQUEST['failedmessage'];
			} else
			{
				$message = __('Category updated.');
			}
			$waplString .= '<wordsChunk class="updated"><quick_text>'.$message.'</quick_text></wordsChunk>';
			$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
		}
		
		// Pagination
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 0;
		if ( empty($pagenum) )
			$pagenum = 1;
		
		$cats_per_page = 10;
		
		if ( !empty($_GET['s']) )
			$num_cats = count(get_categories(array('hide_empty' => 0, 'search' => $_GET['s'])));
		else
			$num_cats = wp_count_terms('category');
		
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => ceil($num_cats / $cats_per_page),
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
		
		$args = array('hide_empty' => 0);
		if ( !empty($_GET['s']) )
			$args['search'] = $_GET['s'];

		$start = ($pagenum - 1) * $cats_per_page;
		$end = $start + $cats_per_page;
	
		$categories = get_categories( $args );
		$wapl = new WordPressAdminWapl;
		
		// Loop through categories
		$i = 0;
		foreach($categories as $category)
		{
			if ( $i >= $end)
				break;
				
			$i++;
			
			if($i <= $start)
				continue;
				
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
			$edit_url = 'edit-tags.php?taxonomy=category&amp;action=edit&amp;cat_ID='.$category->term_id;
			$delete_url = wp_nonce_url("edit-tags.php?action=delete&amp;taxonomy=category&amp;cat_ID=$category->term_id", 'delete-category_' . $category->term_id);
			 
			// Show the category title
			$waplString .= '<wordsChunk class="categoryRow'.$class.'"><quick_text>';
			
			if (current_user_can('manage_categories'))
			{
				$waplString .= '[url='.$edit_url.']';
			}
		
			$waplString .= $wapl->format_text($category->name);
		
			if (current_user_can('manage_categories'))
			{
				$waplString .= '[/url]';
			}
			$waplString .= ' - '.__('Posts').': '.$category->count.'</quick_text></wordsChunk>';
			
			// Tag controls
			if(current_user_can('manage_categories'))
			{
				$waplString .= '<wordsChunk class="categoryRowControls'.$class.'"><quick_text>';
				$waplString .= '[url='.$edit_url.']'.__('Edit').'[/url]';
				$waplString .= ' | [url='.$delete_url.'][color=#BC0B0B]'.__('Delete').'[/color][/url]'; 
				$waplString .= '</quick_text></wordsChunk>';
			}
		}
		
		if(empty($categories))
		{
			$waplString .= '<wordsChunk class="categoryRoweven"><quick_text>'.__('None').'</quick_text></wordsChunk>';
		}
		
		// Navigation
		$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
		$waplString .= $navString;
		$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
		
		if(current_user_can('manage_categories'))
		{
			// Add a new category
			$waplString .= '
			<wordsChunk><quick_text>'.__('Add Category').'</quick_text><display_as>h4</display_as></wordsChunk>
			<form>
				<action>edit-tags.php?action=add</action>
				<formItem item_type="hidden"><name>taxonomy</name><value>category</value></formItem>
				<formItem item_type="hidden"><name>_wpnonce</name><value>'.wp_create_nonce('add-category').'</value></formItem>
				<formItem item_type="hidden"><name>_wp_http_referer</name><value>edit-tags.php?taxonomy=category</value></formItem>
				<formItem item_type="text"><label>'.__('Category Name').'</label><name>cat_name</name><value></value></formItem>
				<formItem item_type="text"><label>'.__('Category Slug').'</label><name>category_nicename</name><value></value></formItem>
				<formItem item_type="select"><label>'.__('Category Parent').'</label><name>category_parent</name>
				<possibility><label>None</label><value>-1</value></possibility>';
			foreach($categories as $catselect)
			{
				$waplString .= '<possibility><label>'.$catselect->name.'</label><value>'.$catselect->term_id.'</value></possibility>';
			}
			
			$waplString .= '</formItem>
				<formItem item_type="textarea"><label>'.__('Description').'</label><name>category_description</name><value></value></formItem>
				<formItem item_type="submit"><label>'.__('Add Category').'</label><name>submit</name></formItem>
			</form>';
		}
		
		break;
}

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>