<?php
if(isset($_REQUEST['post_type']) AND $_REQUEST['post_type'] == 'page')
{
	$thisMainMenu = 'Pages';
	$pageRedirect = true;
	include(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'page-new.php');
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

$descendants_and_self = 0;
$selected_cats = false;
$popular_cats = false;
$post_id = 0;

$descendants_and_self = (int) $descendants_and_self;
$args = array();

if ( is_array( $selected_cats ) )
	$args['selected_cats'] = $selected_cats;
elseif ( $post_id )
	$args['selected_cats'] = wp_get_post_categories($post_ID);
else
	$args['selected_cats'] = array();

if ( is_array( $popular_cats ) )
	$args['popular_cats'] = $popular_cats;
else
	$args['popular_cats'] = get_terms( 'category', array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );

if ( $descendants_and_self ) {
	$categories = get_categories( "child_of=$descendants_and_self&hierarchical=0&hide_empty=0" );
	$self = get_category( $descendants_and_self );
	array_unshift( $categories, $self );
} else {
	$categories = get_categories('get=all');
}

// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
$checked_categories = array();
$keys = array_keys( $categories );

foreach( $keys as $k ) {
	if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
		$checked_categories[] = $categories[$k];
		unset( $categories[$k] );
	}
}

// Get all tags
if(isset($_REQUEST['taxonomy']))
	$taxonomy = $_REQUEST['taxonomy'];
if ( empty($taxonomy) )
	$taxonomy = 'post_tag';
$searchterms = isset( $_GET['s'] ) ? trim( $_GET['s'] ) : '';

// Handle the creation of a post
if(isset($_POST) && !empty($_POST))
{
	if(isset($_FILES) && !empty($_FILES) && $_FILES['async-upload']['name'] != '')
	{
		$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
		
		$file_size = (isset($_REQUEST['file_upload_size'])) ? $_REQUEST['file_upload_size']: '';
		$file = wp_get_attachment_image_src($id, $file_size);
		
		// Construct file source
		$fileClass = '';
		if(isset($_REQUEST['file_upload_location_horizontal']) AND $_REQUEST['file_upload_location_horizontal'] != 'none')
		{
			$fileClass .= 'align'.$_REQUEST['file_upload_location_horizontal'].' size-'.$file_size.' wp-image-'.$id;
		}
		// Get filename
		$fileName = substr($_FILES['async-upload']['name'],0,strrpos($_FILES['async-upload']['name'],'.'));
		$fileSrc = '<img src="'.$file[0].'" class="'.$fileClass.'" alt="'.$fileName.'" />';
		
		if($_POST['file_upload_location'] == 'top')
		{
			$_POST['content'] = $fileSrc.'
'.$_POST['content'];
		} else if($_POST['file_upload_location'] == 'bottom')
		{
			$_POST['content'] .= '
'.$fileSrc;
		}
	}

	if(get_option('architect_admin_show_postedfrom'))
	{
		$text = get_option('architect_admin_postedfrom');
		if($info = architect_mobile_info())
		{
			$text = str_replace(
				array('{manufacturer}', '{model}'),
				array(ucwords($info['manufacturer']), ucwords(strtolower(str_replace('-', ' ', $info['model'])))),
				$text
			);
			
			$_POST['content'] .= '
	
'.$text;
		} else
		{
			$_POST['content'] .= '
'.str_replace(array('{manufacturer}', '{model}'), '', $text);
		}
	}

	// Setup correct categories array for saving
	$_POST['post_category'] = array();
	foreach(array_merge($checked_categories, $categories) as $cat)
	{
		if(isset($_POST['post_category_'.$cat->cat_ID]) AND $_POST['post_category_'.$cat->cat_ID] == true)
		{
			$_POST['post_category'][] = $cat->cat_ID;
		}
	}
	
	// Setup correct tags array for saving
	$_POST['tags_input'] = array();
	if(isset($_POST['tags_input_input']) AND $_POST['tags_input_input'] != '')
	{
		foreach(explode(',', $_POST['tags_input_input']) as $tag)
		{
			$_POST['tags_input'][] = trim($tag);
		}
	}
	
	if(isset($_POST['post_date_Y']) AND is_numeric($_POST['post_date_Y']) AND is_numeric($_POST['post_date_M']) AND is_numeric($_POST['post_date_D']) AND is_numeric($_POST['post_date_H']) AND is_numeric($_POST['post_date_Mi']))
	{
		$_POST['post_date'] = sprintf("%04s", $_POST['post_date_Y']).'-'.
			sprintf("%02s", $_POST['post_date_M']).'-'.
			sprintf("%02s", $_POST['post_date_D']).' '.
			sprintf("%02s", $_POST['post_date_H']).':'.
			sprintf("%02s", $_POST['post_date_Mi']).':00';
		$_POST['post_date_gmt'] = get_gmt_from_date($_POST['post_date']);
	}
	
	if($post_id  = write_post())
	{
		// Assign any custom fields with this post
		foreach($_POST as $key => $post)
		{
			if(strpos($key,'custom_') === 0 AND $post != '')
			{
				if($key == 'custom_architect_new')
				{
					foreach(explode('|', $post) as $val)
					{
						list($newKey,$newVal) = explode('=', $val);
						
						if($newKey != '' AND $newVal != '')
						{
							if ( !add_post_meta( $post_id, str_replace('custom_', '', $newKey), $newVal, true ) )
								update_post_meta( $post_id, str_replace('custom_', '', $newKey), $newVal );
						}
					}
				} else
				{
					if ( !add_post_meta( $post_id, str_replace('custom_', '', $key), $post, true ) )
							update_post_meta( $post_id, str_replace('custom_', '', $key), $post );
				}
			}
		}
	
		if(isset($_FILES) && !empty($_FILES) && $_FILES['async-upload']['name'] != '')
		{
			// Attach uploaded file to post
			global $wpdb;
			$attached = $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID = $id", $post_id));
		}
		header("Location:".admin_url().$_POST['_wp_http_referer'].'?message=1');
		exit();
	}
}
$waplString = '';
$pageTitle = __('Add New Post');
$pageId = 'addPost';
$thisMainMenu = 'Posts';

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'edit.php');
$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]add new post[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

global $user_ID;
$form_action = 'post';
$post_ID = isset($post_ID) ? (int) $post_ID : 0;
$post = get_default_post_to_edit();
$temp_ID = -1 * time();

// Form to write a post
$waplString .= '
<form>
	<action>post-new.php</action>
	<formItem item_type="hidden"><name>action</name><value>post-quickpress-save</value></formItem>
	<formItem item_type="hidden"><name>comment_status</name><value>open</value></formItem>
	<formItem item_type="hidden"><name>ping_status</name><value>open</value></formItem>
	<formItem item_type="hidden"><name>quickpress_post_ID</name><value>0</value></formItem>
	<formItem item_type="hidden"><name>_wpnonce</name><value>'.wp_create_nonce('add-post').'</value></formItem>
	<formItem item_type="hidden"><name>_wp_http_referer</name><value>edit.php</value></formItem>
	<formItem item_type="hidden"><name>post_type</name><value>post</value></formItem>
	<formItem item_type="hidden"><name>user_ID</name><value>'.(int) $user_ID.'</value></formItem>
	<formItem item_type="hidden"><name>post_id</name><value>0</value></formItem>
	<formItem item_type="hidden"><name>temp_ID</name><value>'.$temp_ID.'</value></formItem>
	<formItem item_type="text"><label>'.__('Title').'</label><name>post_title</name><value></value></formItem>
	<formItem item_type="text"><label>'.__('Permalink').'</label><name>post_name</name><value></value></formItem>
	<formItem item_type="textarea"><label>'.__('Content').'</label><event><name>onkeyup</name><action>grow(this);</action></event><name>content</name><value></value></formItem>
	<formItem item_type="textarea"><label>'.__('Excerpt').'</label><name>excerpt</name><value></value></formItem>';

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$can_upload = get_option('architect_admin_file_upload_'.$user_id);

if($can_upload === '0')
{
	$waplString .= '<formItem item_type="title"><name>async-upload-off</name><label>'.__('File uploading is disabled for this user. To re-enable it, switch it back on in your ').' [url=profile.php]'.__('Profile').'[/url]</label></formItem>';
} else
{
	$waplString .= '<formItem item_type="file"><label>'.__('Upload New Media').'</label><name>async-upload</name></formItem>
	<formItem item_type="select">
		<label>'.__('Alignment').'</label><name>file_upload_location</name>
		<possibility>
			<label>'.__('Top').'</label>
			<value>top</value>
		</possibility>
		<possibility>
			<label>'.__('Bottom').'</label>
			<value>bottom</value>
		</possibility>
	</formItem>
	<formItem item_type="select">
		<label></label>
		<name>file_upload_location_horizontal</name>';
	
	$alignments = array('none' => __('None'), 'left' => __('Left'), 'center' => __('Center'), 'right' => __('Right'));
	
	foreach($alignments as $key => $val)
	{
		$waplString .= '
		<possibility>
			<label>'.$val.'</label>
			<value>'.$key.'</value>
		</possibility>';
	}
	$waplString .= '<value>none</value></formItem>';
	
	if(function_exists('gd_info'))
	{
		$waplString .= '
	<formItem item_type="select">
		<label>'.__('Image sizes').'</label><name>file_upload_size</name>
		<value>full</value>
		<possibility>
			<label>'.__('Thumbnail').'</label>
			<value>thumbnail</value>
		</possibility>
		<possibility>
			<label>'.__('Medium').'</label>
			<value>medium</value>
		</possibility>
		<possibility>
			<label>'.__('Large').'</label>
			<value>large</value>
		</possibility>
		<possibility>
			<label>'.__('Full size').'</label>
			<value>full</value>
		</possibility>
	</formItem>';
	} else
	{
		$waplString .= '<formItem item_type="hidden"><name>file_upload_size</name><value>full</value></formItem>';
	}
}

// Post status
$waplString .= '
	<formItem item_type="select"><name>post_status</name>
	<label>'.__('Status').'</label>
		<possibility>
			<label>'.__('Draft').'</label>
			<value>draft</value>
		</possibility>
		<possibility>
			<label>'.__('Pending Review').'</label>
			<value>pending</value>
		</possibility>';
if(current_user_can('publish_posts'))
{
	$waplString .= '<possibility>
			<label>'.__('Published').'</label>
			<value>publish</value>
		</possibility>
		<possibility>
			<label>'.__('Future').'</label>
			<value>future</value>
		</possibility>';
}
 
$waplString .= '
	</formItem>';

// Publish date
$waplString .= '</form></layout><layout start_stack="div"><form><formItem item_type="title"><name></name><label>'.__('Publish').'</label></formItem>';
$waplString .= '<formItem item_type="select" class="post_date"><name>post_date_M</name>
	<label></label>
	<value>'.date("m").'</value>';

global $wp_locale;
for ( $i = 1; $i < 13; $i = $i +1 ) 
{
	$waplString .= '<possibility><label>'.$wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ).'</label><value>'.zeroise($i, 2).'</value></possibility>';
}

$waplString .= '</formItem>';

if ( !wp_timezone_supported() )
{
	$waplString .= '<formItem item_type="text" class="post_date"><label></label><name>post_date_D</name><maxlength>2</maxlength><value>'.date("d").'</value><input_mask>*N</input_mask></formItem>';
	$waplString .= '<formItem item_type="text" class="post_date"><label>,</label><name>post_date_Y</name><maxlength>4</maxlength><value>'.date("Y").'</value><input_mask>*N</input_mask></formItem>';
	$waplString .= '<formItem item_type="text" class="post_date"><label>@</label><name>post_date_H</name><maxlength>2</maxlength><value>'.date("H").'</value><input_mask>*N</input_mask></formItem>';
	$waplString .= '<formItem item_type="text" class="post_date"><label>:</label><name>post_date_Mi</name><maxlength>2</maxlength><value>'.date("i").'</value><input_mask>*N</input_mask></formItem>';
} else
{
	$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
	$date = date_i18n($timezone_format);
	
	$waplString .= '<formItem item_type="text" class="post_date"><label></label><name>post_date_D</name><maxlength>2</maxlength><value>'.date("d", strtotime($date)).'</value><input_mask>*N</input_mask></formItem>';
	$waplString .= '<formItem item_type="text" class="post_date"><label>,</label><name>post_date_Y</name><maxlength>4</maxlength><value>'.date("Y", strtotime($date)).'</value><input_mask>*N</input_mask></formItem>';
	$waplString .= '<formItem item_type="text" class="post_date"><label>@</label><name>post_date_H</name><maxlength>2</maxlength><value>'.date("H", strtotime($date)).'</value><input_mask>*N</input_mask></formItem>';
	$waplString .= '<formItem item_type="text" class="post_date"><label>:</label><name>post_date_Mi</name><maxlength>2</maxlength><value>'.date("i", strtotime($date)).'</value><input_mask>*N</input_mask></formItem>';
}

$waplString .= '</form></layout><layout start_stack="table"><form>';

// Custom fields
if(get_user_option('architect_admin_show_custom_fields_'.$user_id) === '0')
{
	$waplString .= '<formItem item_type="title"><name>architect_admin_show_custom_fields_off</name><label>'.__('Editing custom fields inside a post is disabled. To re-enable it, switch it back on in your ').' [url=profile.php]'.__('Profile').'[/url]</label></formItem>';
} else
{
	$waplString .= '<formItem class="dashboardSection" item_type="title"><label>'.__('Custom Fields').'</label><name>custom_fields</name></formItem>';
	$waplString .= '<formItem class="formItemComment" item_type="title"><label>'.preg_replace('/<a(.*?)>(.*?)<\/a>/', '${2}', __('Custom fields can be used to add extra metadata to a post that you can <a href="http://codex.wordpress.org/Using_Custom_Fields" target="_blank">use in your theme</a>.')).'</label></formItem>';
	global $wpdb;
	$limit = (int) apply_filters( 'postmeta_form_limit', 30 );
	$keys = $wpdb->get_col( "
		SELECT meta_key
		FROM $wpdb->postmeta
		GROUP BY meta_key
		HAVING meta_key NOT LIKE '\_%'
		ORDER BY LOWER(meta_key)
		LIMIT $limit");
	
	if($keys)
		natcasesort($keys);
			
	foreach($keys as $key)
	{
		if($key != '')
		{
			$waplString .= '<formItem item_type="text" class="custom_field"><label_class>custom_field</label_class><name>custom_'.$key.'</name><label>'.$key.'</label><value></value></formItem>';
		}
	}
	
	$waplString .= '<formItem item_type="text" class="custom_field"><label_class>add_custom_field</label_class><name>custom_architect_new</name><label>'.__('Add Custom Field').'</label><value></value></formItem>';
	$waplString .= '<formItem item_type="title" class="formItemComment"><label>'.__('Format for adding new custom fields is name=value|name2=value2').'</label></formItem>';
}

if(get_user_option('architect_admin_show_categories_'.$user_id) === '0')
{
	$waplString .= '<formItem item_type="title"><name>architect_admin_show_categories_off</name><label>'.__('Editing categories inside a post is disabled. To re-enable it, switch it back on in your ').' [url=profile.php]'.__('Profile').'[/url]</label></formItem>';
} else
{
	// Assign categories
	$waplString .= '<formItem class="dashboardSection" item_type="title"><label>'.__('Categories').'</label><name>categories</name></formItem>';
	
	foreach($checked_categories as $val)
	{
		$waplString .= '<formItem class="postCheckbox" item_type="checkbox"><label>'.$val->name.'</label><name>post_category_'.$val->cat_ID.'</name><value>0</value></formItem>';
	}
	foreach($categories as $val)
	{
		$waplString .= '<formItem class="postCheckbox" item_type="checkbox"><label>'.$val->name.'</label><name>post_category_'.$val->cat_ID.'</name><value>0</value></formItem>';
	}
}

// Assign tags
if(get_user_option('architect_admin_show_tags_'.$user_id) === '0')
{
	$waplString .= '<formItem item_type="title"><name>architect_admin_show_tags_off</name><label>'.__('Editing tags inside a post is disabled. To re-enable it, switch it back on in your ').' [url=profile.php]'.__('Profile').'[/url]</label></formItem>';
} else
{
	$waplString .= '<formItem class="dashboardSection" item_type="title"><label>'.__('Tags').'</label><name>tags</name></formItem>';
	$waplString .= '<formItem item_type="text"><name>tags_input_input</name><label>'.__('Add new tag').'</label></formItem>';
	$waplString .= '<formItem class="formItemComment" item_type="title"><label>'.__('Separate tags with commas.').'</label></formItem>';
}

// Close form
$waplString .= '
	<formItem item_type="submit"><label>'.__('Add').'</label><name>add_new_save</name></formItem>
</form>';

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>