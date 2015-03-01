<?php
require_once('admin.php');
wp_reset_vars( array('action') );

switch($_REQUEST['action'])
{
	case 'unapprovecomment':
		$comment_id = absint( $_GET['c'] );
		$comment_status = (isset($_REQUEST['comment_status'])) ? '&comment_status='.$_REQUEST['comment_status'] : '';
		
		if (!$comment = get_comment($comment_id))
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status.'&error=1&errorMsg='.urlencode('Oops, no comment with this ID')));
			exit();
		}

		if (!current_user_can('edit_post', $comment->comment_post_ID))
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status.'&error=1&errorMsg='.urlencode('You are not allowed to edit comments on this post')));
			exit();
		}
		
		wp_set_comment_status( $comment_id, 'hold' );
		if(isset($_REQUEST['return']))
		{
			wp_redirect(admin_url($_REQUEST['return'].'.php?message=1'.$comment_status));
		} else
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status));
		}
		break;
	case 'approvecomment':
		$comment_id = absint( $_GET['c'] );
		$comment_status = (isset($_REQUEST['comment_status'])) ? '&comment_status='.$_REQUEST['comment_status'] : '';
		
		if (!$comment = get_comment($comment_id))
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status.'&error=1&errorMsg='.urlencode('Oops, no comment with this ID')));
			exit();
		}

		if (!current_user_can('edit_post', $comment->comment_post_ID))
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status.'&error=1&errorMsg='.urlencode('You are not allowed to edit comments on this post')));
			exit();
		}
		
		wp_set_comment_status( $comment->comment_ID, 'approve' );
		if(isset($_REQUEST['return']))
		{
			wp_redirect(admin_url($_REQUEST['return'].'.php?message=1'.$comment_status));
		} else
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status));
		}
		break;
		
	case 'deletecomment':
		$comment_id = absint($_GET['c']);
		$comment_status = (isset($_REQUEST['comment_status'])) ? '&comment_status='.$_REQUEST['comment_status'] : '';
		
		if (!$comment = get_comment($comment_id))
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status.'&error=1&errorMsg='.urlencode('Oops, no comment with this ID')));
			exit();
		}

		if (!current_user_can('edit_post', $comment->comment_post_ID))
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status.'&error=1&errorMsg='.urlencode('You are not allowed to edit comments on this post')));
			exit();
		}
		
		if ( 'spam' == $_REQUEST['dt'] )
		{
			wp_set_comment_status( $comment->comment_ID, 'spam' );
		} else 
		{
			wp_delete_comment( $comment->comment_ID );
		}
		
		if(isset($_REQUEST['return']))
		{
			wp_redirect(admin_url($_REQUEST['return'].'.php?message=1'.$comment_status));
		} else
		{
			wp_redirect(admin_url('edit-comments.php?message=1'.$comment_status));
		}
		break;
}

?>