<?php 
error_reporting(NONE);
ini_set('display_errors', false);

global $wappleArchitectLoadOverride;
$wappleArchitectLoadOverride = true;

define('ARCHITECTABSPATH', substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], 'wp-content')).DIRECTORY_SEPARATOR);
require_once(ARCHITECTABSPATH.'wp-load.php');

if(get_bloginfo('version') < '2.7')
{
	$imagePath = '../img/';
} else
{
	$imagePath = admin_url().'images/';
}

header("Content-type: text/css");

// Colour scheme
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$color = get_user_option('admin_color', $user_id);

switch($color)
{
	case 'classic':
		// Blue
		$headerBgColor = '1D507D';
		$headerBorderColor = '1D507D';
		$footerTextColor = 'B6D1E4';
		$footerLinkColor = 'ffffff';
		if(get_bloginfo('version') >= '3.0')
		{
			$headerLogo = 'wp-logo-vs.png';	
		} else
		{
			$headerLogo = 'wp-logo-vs.gif';
		}
		$headerLink = 'ffffff';
		$menuBgColor = 'eaf3fa';
		$menuSelectedBgColor = '3C6B95';
		$pageIcons = 'icons32-vs.png';
		$h2Color = '093E56';
		$gradTop = 'blue-grad.png';
		$bgColor = 'F7F6F1';
		break;
	case 'fresh':
	default:
		// Gray
		if(get_bloginfo('version') >= '3.0')
		{
			$headerLogo = 'wp-logo.png';
			$headerBgColor = 'd7d7d7';
			$headerBorderColor = 'C6C6C6';
			$headerLink = '464646';
			$footerBorderColor = 'd1d1d1';
			$footerTextColor = '777777';
			$footerLinkColor = '222222';
			$footerLinkHoverColor = '222222';
		} else
		{
			$headerLogo = 'wp-logo.gif';
			$headerBgColor = '464646';
			$headerBorderColor = '464646';
			$headerLink = 'ffffff';
			$footerBorderColor = '464646';
			$footerTextColor = '999999';
			$footerLinkColor = 'CCCCCC';
			$footerLinkHoverColor = 'ffffff';
		}
		
		$menuBgColor = 'F1F1F1';
		$menuSelectedBgColor = '6D6D6D';
		$pageIcons = 'icons32.png';
		$h2Color = '464646';
		$gradTop = 'gray-grad.png';
		$bgColor = 'F9F9F9';
		break;
}
?>
body{font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;font-size:14px;background:#<?php echo $bgColor;?>;color:#333333;margin:0;padding:0;}
table{clear:left;}
div.main div.main{clear:both;}
tr{display:block;}
td{padding-left:2px;display:block;}

a{color:#21759B;text-decoration:none;}
a:hover{color:#D54E21;text-decoration:none;}

#header{background:#<?php echo $headerBgColor;?> url(<?php echo $imagePath;?><?php echo $headerLogo;?>) no-repeat 2px 7px;border-bottom:solid 1px #<?php echo $headerBorderColor;?>;}
#header td{padding:10px 5px;}
#header td{font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;color:#ffffff;font-size:1.5em;margin-left:35px;}
#header td.words a{color:#<?php echo $headerLink;?>;}
#header td.words a:hover{text-decoration:underline;}

h2{font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;color:#<?php echo $h2Color;?>;font-style:italic;font-weight:normal;font-size:1.4em;}

#menu td{background:#<?php echo $menuBgColor;?>;padding:4px 2px;border:solid 1px #aaaaaa;border-width:0 1px;border-bottom:solid 1px #e3e3e3;}
#menu a,#subMenu a{font-size:0.8em;padding:0 2px;}
#menu td.noSubNav{border:solid 1px #aaaaaa;border-width:0 1px 1px;-moz-border-radius-bottomleft:3px;-webkit-border-bottom-left-radius:3px;-moz-border-radius-bottomright:3px;-webkit-border-bottom-right-radius:3px;}
#subMenu{background:#ffffff;padding:4px 0;border:solid 1px #aaaaaa;border-width:0 1px 1px;-moz-border-radius-bottomleft:3px;-webkit-border-bottom-left-radius:3px;-moz-border-radius-bottomright:3px;-webkit-border-bottom-right-radius:3px;}
.words_row td{padding:0 2px;}

.updated{border:solid 1px #E6DB55;background:#FFFBCC;padding:2px;width:90%;display:block;margin-left:10px;-moz-border-radius: 3px;-webkit-border-radius: 3px;font-size:0.9em;margin-top:5px;}
#login_error,.error{border:solid 1px #cc0000;background:#FFEBE8;padding:2px;width:90%;display:block;margin-left:10px;-moz-border-radius: 3px;-webkit-border-radius: 3px;font-size:0.9em;margin-top:5px;}

.dashboardSection{background:#DFDFDF url(<?php echo $imagePath;?><?php echo $gradTop;?>) repeat-x 0 0;border:solid 1px #dfdfdf;-moz-border-radius-topleft:6px;-webkit-border-top-left-radius:6px;-moz-border-radius-topright:6px;-webkit-border-top-right-radius:6px;height:25px;line-height:25px;}
.dashboardSection h3{color:#464646;font-size:0.8em;margin:0;text-shadow: 0px 1px 0px #FFFFFF;}
.formItemComment{font-style:italic;color:#666666;font-size:0.8em;}

.paginatedNav{text-align:right;font-style:italic;font-size:0.8em;color:#777777;font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;}
.paginatedNav a,.paginatedNav .current{padding:2px;margin:0 1px;font-style:normal;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;-moz-border-radius: 3px;-webkit-border-radius: 3px;}
.paginatedNav .current{background:#DFDFDF;border:solid 1px #d3d3d3;}
.paginatedNav a{background:#EEEEEE;border:solid 1px #e3e3e3;}

<?php 
// Page specific CSS
if(isset($_GET['page']))
{
	switch($_GET['page'])
	{
		case 'login' :
			echo '/* Login Page */
#header{background-image:none;}
#header td{margin-left:0;font-size:1em;padding:5px;}
#header td a{color:#'.$headerLink.';}
#header td a:hover{text-decoration:underline;}			
#wordpress td{text-align:center;}
#wordpress img{margin-top:5px;}
#f_log,#f_pwd{text-align:left; padding-left:7%;}
#f_rememberme{text-align:left;padding-left:7%;}
#f_wp-submit{text-align:center;}
#f_wp-submit {padding-top:8px;}
#f_log {padding-top:6px;}
#f_pwd {padding-top:3px;}
#f_rememberme{padding-top:3px;}
label{color:#777777;}
input{border:solid 1px #e5e5e5;font-size:1.5em;}
#log,#pwd{width:90%;color:#555555;padding:2px;}
#rememberme{margin:0;}
#wp-submit{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:5px;}				
			';
			break;
		
		case 'dashboard' :
			echo '/* Dashboard Page */
#dashboard p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -137px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#dashboard h2{padding-top:5px;text-indent:10px;margin:10px 0 0;}
#dashboard td{padding-bottom:15px;}
.commentAll a{color:#d54e21;}
.commentApproved a{color:green;}
.commentPending a{color:#E66F00;}
.commentSpam a{color:red;}

.dashboardCommentInfopending,.dashboardCommentInfoapproved{font-size:0.9em;padding-top:11px; }
.dashboardCommentInfopending img,.dashboardCommentInfoapproved img{margin-right:5px;width:16px;height:16px;}
.dashboardCommentContentpending,.dashboardCommentContentapproved{font-size:0.9em;padding:5px 0;}
.dashboardCommentControlsapproved,.dashboardCommentControlspending{padding:5px 0;font-size:0.8em;border-bottom:solid 1px #dfdfdf;}
.dashboardCommentInfopending,.dashboardCommentContentpending,.dashboardCommentControlspending{background:#ffffe0;}
			';
			break;
		case 'editPosts' :
			echo '/* Edit posts page */
#editPosts p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -555px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editPosts h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editPosts td{padding-bottom:15px;}
.postRowControlsodd,.postRowControlseven{border-bottom:solid 1px #DFDFDF;font-size:0.8em;padding:5px 2px;}
.postRoweven_row,.postRowControlseven_row{background:#F9F9F9;}
.postRowodd_row,.postRowControlsodd_row{background:#ffffff;}
.postRowodd,.postRoweven{padding-top:5px;}
			';
			break;
		case 'editPost' :
			echo '/* Edit post page */
#editPost p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -555px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editPost h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editPost td{padding-bottom:15px;}
label,#f_WAPL1{margin-left:2px;margin-bottom:3px;display:block;}
#post_title,#tags_input_input,#post_name{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#content{overflow:auto;overflow-y:scroll;border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#excerpt{border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:75px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#async-upload{border:solid 1px #dfdfdf;margin-bottom:5px;margin-left:2px;padding:4px;}
#f_async-upload-off,#f_architect_admin_show_categories_off,#f_architect_admin_show_tags_off,#f_architect_admin_show_custom_fields_off{border:solid 1px #E6DB55;background:#FFFBCC;border-width:1px 0;padding:5px;margin:5px 0;}
#file_upload_location{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#file_upload_location_horizontal{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#file_upload_size{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#post_status{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#edit_post_save{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:5px;margin-bottom:10px;}
#f_categories{padding:10px 0 5px 0;clear:both;}

.dashboardSection{display:block;color:#464646;font-size:0.8em;margin:0;text-shadow: 0px 1px 0px #FFFFFF;font-weight:bold;padding-left:5px;}
.post_date{float:left;width:20px;border:solid 1px #dddddd;-moz-border-radius: 2px;-webkit-border-radius: 2px;margin:0 3px;padding:4px;}
#f_post_date_Y label,#f_post_date_H label,#f_post_date_Mi label{float:left;margin-top:5px;margin-right:2px;}
#f_post_date_Y input{width:30px;}
#f_post_date_M select{width:60px;}
#f_tags{margin-top:10px;clear:both;}
#f_custom_fields{margin-top:10px;clear:both;padding:10px 0 5px;}
label.checkbox_input_label{float:left;clear:both;}
.checkbox_input_container{float:left;margin-left:5px;}
label.custom_field{}
label.add_custom_field{margin-top:10px;}
input.custom_field{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
input.custom_field_value{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:0px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
.custom_field_link{font-size:0.9em;margin-bottom:5px;display:block;margin-left:2px;}
span.small{font-size:0.8em;display:block;}
';
			break;
		case 'addPost' :
			echo '/* Add post page */
#addPost p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -555px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#addPost h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#addPost td{padding-bottom:15px;}
label,#f_WAPL1{margin-left:2px;margin-bottom:3px;display:block;}
#post_title,#tags_input_input,#post_name{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#content{overflow:auto;overflow-y:scroll;border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#excerpt{border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:75px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#async-upload{border:solid 1px #dfdfdf;margin-bottom:5px;margin-left:2px;padding:4px;}
#f_async-upload-off,#f_architect_admin_show_categories_off,#f_architect_admin_show_tags_off,#f_architect_admin_show_custom_fields_off{border:solid 1px #E6DB55;background:#FFFBCC;border-width:1px 0;padding:5px;margin:5px 0;}
#file_upload_location{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#file_upload_location_horizontal{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#file_upload_size{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#post_status{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#add_new_save{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:10px;margin-bottom:10px;}
#f_categories{padding:10px 0 5px 0;clear:both;}
.dashboardSection{display:block;color:#464646;font-size:0.8em;margin:0;text-shadow: 0px 1px 0px #FFFFFF;font-weight:bold;padding-left:5px;}
.post_date{float:left;width:20px;border:solid 1px #dddddd;-moz-border-radius: 2px;-webkit-border-radius: 2px;margin:0 3px;padding:4px;}
#f_post_date_Y label,#f_post_date_H label,#f_post_date_Mi label{float:left;margin-top:5px;margin-right:2px;}
#f_post_date_Y input{width:30px;}
#f_post_date_M select{width:60px;}
#f_tags{margin-top:10px;clear:both;}
#f_custom_fields{margin-top:10px;clear:both;padding:10px 0 5px;}
label.checkbox_input_label{float:left;clear:both;}
.checkbox_input_container{float:left;margin-left:5px;}
label.custom_field{}
label.add_custom_field{margin-top:10px;}
input.custom_field{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
';
			break;
		case 'editComments':
			echo '/* Comments Page */
#editComments p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -73px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editComments h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editComments td{padding-bottom:15px;}
.commentRowControlspending,.commentRowControlsapproved{border-bottom:solid 1px #dfdfdf;font-size:0.8em;padding-bottom:5px;}
.commentRowAuthorpending,.commentRowAuthorapproved{padding-top:11px;font-size:0.9em;}
.commentRowAvatarpending,.commentRowAvatarapproved{padding-top:11px;font-size:0.9em;}
.commentRowAvatarpending td,.commentRowAvatarapproved td{padding-left:5px;padding-left:25px !important;}
.commentRowAvatarpending img,.commentRowAvatarapproved img{width:16px;height:16px;}
.commentRowCommentpending,.commentRowCommentapproved{font-size:0.9em;padding:5px 2px;}
.commentRowAuthorpending,.commentRowControlspending,.commentRowCommentpending,.commentRowAvatarpending{background:#ffffe0;}
.commentRowCommentapproved {padding-top:8px;}
.commentNavLinks{font-size:0.9em;}
.commentRowAvatarpending img, .commentRowAvatarapproved img{margin-right:5px;}
#f_bulk_type,#f_bulk_type_bottom{float:left;margin:0 5px 0 2px;}
#bulk_type,#bulk_type_bottom{border:solid 1px #DFDFDF;font-size:0.8em;padding:2px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#f_bulk_submit{margin:8px 0;position:static;}
#bulk_submit{background:#EEEEEE;color:#464646;border:solid 1px #BBBBBB;-moz-border-radius:11px;-webkit-border-radius:11px;font-size:0.8em;padding:2px 10px;cursor:pointer;}
.dashboardSection_notice,.commentRowControlsapproved_notice,.commentRowControlspending_notice{position:relative;}
.comment_checkbox{position:absolute;left:0;top:12px;}
			';
			break;
			
		case 'editPages' :
			echo '/* Edit Pages */
#editPages p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -312px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editPages h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editPages td{padding-bottom:15px;}
.pageRowControlsodd,.pageRowControlseven{border-bottom:solid 1px #DFDFDF;font-size:0.8em;padding:5px 2px;}
.pageRoweven_row,.pageRowControlseven_row{background:#F9F9F9;}
.pageRowodd_row,.pageRowControlsodd_row{background:#ffffff;}
.pageRowodd,.pageRoweven{padding-top:5px;}
			';
			break;
		case 'editPage' :
			echo '/* Edit Page */
#editPage p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -312px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editPage h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editPage td{padding-bottom:15px;}
label{margin-left:2px;margin-bottom:3px;display:block;}
#post_title,#post_name{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#content{overflow:auto;overflow-y:scroll;border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#async-upload{border:solid 1px #dfdfdf;margin-bottom:5px;margin-left:2px;padding:4px;}
#file_upload_location{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#post_status{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#edit_post_save{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:5px;margin-bottom:10px;}
			';
			break;
		case 'addPage' :
			echo '/* Add Page */
#addPage p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -312px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#addPage h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#addPage td{padding-bottom:15px;}
label{margin-left:2px;margin-bottom:3px;display:block;}
#post_title,#post_name{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#content{overflow:auto;overflow-y:scroll;border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#async-upload{border:solid 1px #dfdfdf;margin-bottom:5px;margin-left:2px;padding:4px;}
#file_upload_location{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#post_status{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#add_new_save{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:5px;margin-bottom:10px;}
			';
			break;
		case 'profile' :
			echo '/* Profile Page */
#profile p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -602px -7px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#profile h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#profile td{padding-bottom:15px;}
#edit_profile_save{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:10px;margin-bottom:10px;}
#file_upload,#admin_colour_scheme,#admin_show_categories,#admin_show_tags,#admin_show_custom_fields,#admin_show_menu_posts,#admin_show_menu_pages,#admin_show_menu_comments{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#f_menuOptionsHeader{background:#DFDFDF url('.$imagePath.$gradTop.') repeat-x 0 0;border:solid 1px #dfdfdf;-moz-border-radius-topleft:6px;-webkit-border-top-left-radius:6px;-moz-border-radius-topright:6px;-webkit-border-top-right-radius:6px;height:25px;line-height:25px;color:#464646;font-size:0.8em;margin:10px 0;text-shadow: 0px 1px 0px #FFFFFF;font-weight:bold;}
';
			break;
		case 'permissions' :
			echo '/* Permissions page */
#permissions{border:solid 1px #dfdfdf;background:#ffffff;-moz-border-radius: 5px;-webkit-border-radius: 5px;padding:10px;margin:20px 5px;width:90%;font-size:0.9em;}
			';
			break;
		case 'addComment' :
			echo '/* Add Comment page */
#addComment p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -73px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#addComment h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#addComment td{padding-bottom:15px;}
.commentRowAuthor{padding-top:11px;font-size:0.9em;}
.commentRowAvatar{padding-top:11px;font-size:0.9em;border-top:solid 1px #dfdfdf;}
.commentRowAvatar img{width:16px;height:16px;margin-right:5px;}
.commentRowComment{font-size:0.9em;padding:5px 2px;border-bottom:solid 1px #dfdfdf;margin-bottom:10px;}
#comment{border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.9em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#add_comment_save{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:5px;margin-bottom:10px;}
';
			break;
		case 'editTags' :
			echo '/* Edit Tags page */
#editTags p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -555px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editTags h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editTags td{padding-bottom:15px;}
.tagRowControlsodd,.tagRowControlseven{border-bottom:solid 1px #DFDFDF;font-size:0.8em;padding:5px 2px;}
.tagRoweven_row,.tagRowControlseven_row{background:#F9F9F9;}
.tagRowodd_row,.tagRowControlsodd_row{background:#ffffff;}
.tagRowodd,.tagRoweven{padding-top:5px;}
label{margin-left:2px;margin-bottom:3px;display:block;}
#name,#slug{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#description{border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#submit{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:10px;margin-bottom:10px;}

';
			break;
		case 'editTag' :
			echo '/* Edit Tag page */
#editTag p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -555px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editTag h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editTag td{padding-bottom:15px;}
label{margin-left:2px;margin-bottom:3px;display:block;}
#name,#slug{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#description{border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#submit{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:10px;margin-bottom:10px;}
';
			break;
		case 'editCategories' :
			echo '/* Edit Categories page */
#editCategories p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -555px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editCategories h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editCategories td{padding-bottom:15px;}
.categoryRowControlsodd,.categoryRowControlseven{border-bottom:solid 1px #DFDFDF;font-size:0.8em;padding:5px 2px;}
.categoryRoweven_row,.categoryRowControlseven_row{background:#F9F9F9;}
.categoryRowodd_row,.categoryRowControlsodd_row{background:#ffffff;}
.categoryRowodd,.categoryRoweven{padding-top:5px;}
label{margin-left:2px;margin-bottom:3px;display:block;}
#cat_name,#category_nicename{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#category_description{border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#category_parent{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#submit{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:10px;margin-bottom:10px;}
';
			break;
		case 'editCategory' :
			echo '/* Edit Category page */
#editCategory p{background:transparent url('.$imagePath.$pageIcons.') no-repeat -555px -5px;height:32px;width:32px;float:left;text-indent:-1000px;margin:0;}
#editCategory h2{padding-top:4px;text-indent:10px;margin:10px 0 0;}
#editCategory td{padding-bottom:15px;}
label{margin-left:2px;margin-bottom:3px;display:block;}
#cat_name,#category_nicename{border:solid 1px #DFDFDF;width:95%;margin-left:2px;padding:4px;margin-bottom:5px;-moz-border-radius: 2px;-webkit-border-radius: 2px;font-size:0.9em;}
#category_description{border:solid 1px #dfdfdf;width:95%;margin-left:2px;padding:4px;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;height:175px;margin-bottom:5px;font-size:0.8em;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#category_parent{border:solid 1px #dfdfdf;margin-bottom:5px;width:50%;margin-left:2px;padding:4px;-moz-border-radius: 2px;-webkit-border-radius: 2px;}
#submit{background:#21759B url('.$imagePath.'button-grad.png) repeat-x 0 0;border:none;color:#ffffff;font-size:1em;font-weight:bold;padding:4px 10px;cursor:pointer;-moz-border-radius: 11px;-webkit-border-radius: 11px;margin-top:10px;margin-bottom:10px;}
';
			break;
	}
}
?>

#version{height:0;}
#footer{background:#<?php echo $headerBgColor;?>;color:#<?php echo $footerTextColor;?>;border-top:solid 1px #<?php echo $footerBorderColor;?>;}
#footer td{padding:4px 2px;font-size:0.8em;font-style:italic;font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;}
#footer a{color:#<?php echo $footerLinkColor;?>;}
#footer a:hover{color:#<?php echo $footerLinkHoverColor;?>;text-decoration:underline;}

#switchToDesktop{background:#<?php echo $headerBgColor;?>;padding:5px 5px 5px 0;color:#<?php echo $footerTextColor;?>;font-style:italic;font-size:0.8em;font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;}
#switchToDesktop a{color:#<?php echo $footerLinkColor;?>;text-decoration:none;}
#switchToDesktop a:hover{color:#<?php echo $footerLinkColor;?>;text-decoration:underline;}