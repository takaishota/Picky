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
$pageTitle = __('Profile');
$pageId = 'profile';
$updated = false;

if(isset($_POST) AND !empty($_POST))
{
	// Save file upload option
	if(isset($_POST['file_upload']))
	{
		if(update_option('architect_admin_file_upload_'.$user_id, $_POST['file_upload']))
		{
			$updated = true;
		}
	}
	// Save admin colour scheme
	if(isset($_POST['admin_colour_scheme']))
	{
		// Delete CSS cache
		if(update_user_option($user_id, 'admin_color', $_POST['admin_colour_scheme']))
		{
			$updated = true;
		}
	}
	// Save show categories
	if(isset($_POST['admin_show_categories']))
	{
		if(update_option('architect_admin_show_categories_'.$user_id, $_POST['admin_show_categories']))
		{
			$updated = true;
		}
	}
	// Save show tags
	if(isset($_POST['admin_show_tags']))
	{
		if(update_option('architect_admin_show_tags_'.$user_id, $_POST['admin_show_tags']))
		{
			$updated = true;
		}
	}
	// Save show custom fields
	if(isset($_POST['admin_show_custom_fields']))
	{
		if(update_option('architect_admin_show_custom_fields_'.$user_id, $_POST['admin_show_custom_fields']))
		{
			$updated = true;
		}
	}
	// Save show posts menu item
	if(isset($_POST['admin_show_menu_posts']))
	{
		if(update_option('architect_admin_show_menu_posts_'.$user_id, $_POST['admin_show_menu_posts']))
		{
			$updated = true;
		}
	}
	// Save show pages menu item
	if(isset($_POST['admin_show_menu_pages']))
	{
		if(update_option('architect_admin_show_menu_pages_'.$user_id, $_POST['admin_show_menu_pages']))
		{
			$updated = true;
		}
	}
	// Save show comments menu item
	if(isset($_POST['admin_show_menu_comments']))
	{
		if(update_option('architect_admin_show_menu_comments_'.$user_id, $_POST['admin_show_menu_comments']))
		{
			$updated = true;
		}
	}
}

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'dashboard.php');

$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]profile[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

if($updated)
{
	$waplString .= '<wordsChunk class="updated"><quick_text>'.__('User updated.').'</quick_text></wordsChunk>';
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
}

$waplString .= '
<form>
	<action>profile.php</action>
	<formItem item_type="select"><name>file_upload</name><label>'.__('Uploading Files').'</label>
		<value>'.get_option('architect_admin_file_upload_'.$user_id).'</value>
		<possibility>
			<label>'.__('Yes').'</label>
			<value>1</value>
		</possibility>
		<possibility>
			<label>'.__('No').'</label>
			<value>0</value>
		</possibility>
	</formItem>
	
	<formItem item_type="select"><name>admin_colour_scheme</name><label>'.__('Admin Color Scheme').'</label>
		<value>'.get_user_option('admin_color', $user_id).'</value>
		<possibility>
			<label>'.__('Blue').'</label>
			<value>classic</value>
		</possibility>
		<possibility>
			<label>'.__('Gray').'</label>
			<value>fresh</value>
		</possibility>
	</formItem>
	
	<formItem item_type="select"><name>admin_show_categories</name><label>'.__('Show Categories in Posts').'</label>
		<value>'.get_option('architect_admin_show_categories_'.$user_id).'</value>
		<possibility>
			<label>'.__('Yes').'</label>
			<value>1</value>
		</possibility>
		<possibility>
			<label>'.__('No').'</label>
			<value>0</value>
		</possibility>
	</formItem>
	
	<formItem item_type="select"><name>admin_show_tags</name><label>'.__('Show Tags in Posts').'</label>
		<value>'.get_option('architect_admin_show_tags_'.$user_id).'</value>
		<possibility>
			<label>'.__('Yes').'</label>
			<value>1</value>
		</possibility>
		<possibility>
			<label>'.__('No').'</label>
			<value>0</value>
		</possibility>
	</formItem>
	
	<formItem item_type="select"><name>admin_show_custom_fields</name><label>'.__('Show Custom Fields in Posts').'</label>
		<value>'.get_option('architect_admin_show_custom_fields_'.$user_id).'</value>
		<possibility>
			<label>'.__('Yes').'</label>
			<value>1</value>
		</possibility>
		<possibility>
			<label>'.__('No').'</label>
			<value>0</value>
		</possibility>
	</formItem>
	
	<formItem item_type="title"><name>menuOptionsHeader</name><label>'.__('Menu Options').'</label></formItem>';

$waplString .= '
	<formItem item_type="select"><name>admin_show_menu_posts</name><label>'.__('Show Posts').'</label>
		<value>'.get_option('architect_admin_show_menu_posts_'.$user_id).'</value>
		<possibility>
			<label>'.__('Yes').'</label>
			<value>1</value>
		</possibility>
		<possibility>
			<label>'.__('No').'</label>
			<value>0</value>
		</possibility>
	</formItem>
	<formItem item_type="select"><name>admin_show_menu_pages</name><label>'.__('Show Pages').'</label>
		<value>'.get_option('architect_admin_show_menu_pages_'.$user_id).'</value>
		<possibility>
			<label>'.__('Yes').'</label>
			<value>1</value>
		</possibility>
		<possibility>
			<label>'.__('No').'</label>
			<value>0</value>
		</possibility>
	</formItem>
	<formItem item_type="select"><name>admin_show_menu_comments</name><label>'.__('Show Comments').'</label>
		<value>'.get_option('architect_admin_show_menu_comments_'.$user_id).'</value>
		<possibility>
			<label>'.__('Yes').'</label>
			<value>1</value>
		</possibility>
		<possibility>
			<label>'.__('No').'</label>
			<value>0</value>
		</possibility>
	</formItem>
	
	
	<formItem item_type="submit"><name>edit_profile_save</name><label>'.__('Save').'</label></formItem>
</form>';

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>