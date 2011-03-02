<?

//start session
session_start();

//require classes
require_once( "base/Article.php" );
require_once( "base/Authentication.php" );
require_once( "controllers/Admin.php" );
require_once( "base/Common.php" );
require_once( "base/Session.php" );
require_once( "base/User.php" );
require_once( "base/UserType.php" );

$common = new Common();
$active_user = new User( Authentication::getLoginUserId() );

$task = strtolower( trim( $_GET['task'] ) );
$process = strtolower( trim( $_GET['process'] ) );
$echo_content = FALSE;

//determine action
switch( $task )
{
	case "session":
	
		switch( $process )
		{
			case "store_login":
				$_SESSION['sid'] = Session::createSession( $_POST['username'] );
				break;
				
			case "validate_login":
				
				$auth = new Authentication( 0 );
				$form_result = $auth->checkLoginForm( $_POST );
				
				if( !$form_result )
				{
					$form_result = $auth->getLoginMessage( $_POST['username'], $_POST['password'] );
				}
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Authenticated Successfully. Redirecting. . ." : $form_result;
				
				echo $result . "^" . $message;
				break;
				
			case "get_var":
				$value = 0;
				if( isset( $_SESSION ) &&
					strtolower( trim ( $_POST['key'] ) ) != "sid" &&
					array_key_exists( $_POST['key'], $_SESSION ) )
				{
					$value = $_SESSION[$_POST['key']];
				}
				
				//send results
				header( 'Content-type: application/x-json' );
				echo json_encode( $value );
				
				break;
				
			case "set_var":
				if( isset( $_SESSION ) &&
					strtolower( trim ( $_POST['key'] ) ) != "sid" )
				{
					$_SESSION[$_POST['key']] = $_POST['value'];
				}
				break;
				
			case "kill_login":
				$s = new Session( $_SESSION['sid'] );
				$s->delete( TRUE );
				$_SESSION['sid'] = "";
				unset( $_SESSION['sid'] );
				break;
		}
		break;
		
	case "authentication":
		
		$a = new Authentication( $_GET['authentication_id'] );
		
		switch( $process )
		{
			case "check_current_login":
				echo ( $auth->validateCurrentLogin() ) ? 1 : 0;
				break;
				
			case "show_login_form":
				$login_form = $auth->getHtml( "show_login_form", array() );
				echo '
				<div class="colorbox_login_container">
					' . $login_form['body'] . '
				</div>
				';
				break;
				
			case "show_password_form":
				$pass_form = $auth->getHtml( "show_password_form", array() );
				
				echo '
				<div class="colorbox_login_container" style="width:auto;">
					' . $pass_form['body'] . '
				</div>
				';
				break;
				
			case "validate_change_password":
				$form_result = $auth->passwordValidateChange( $_POST );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Password changed successfully. Logging out..." : $form_result;
				
				echo $result . "^" . $message;
				break;
				
			case "change_password":
				$auth->passwordUpdate( $_POST['new_pass'] );
				break;
		}
		break;
		
	case "article":
		
		$art = new Article( $_GET['article_id'] );
		
		switch( $process )
		{
			case "validate":
				$_POST['authentication_id'] = $auth->m_authentication_id;
				$form_result = $art->checkInput( $_POST, $art->m_common->m_db->fixBoolean( $_POST['from_add'] ) );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Article Saved" : $form_result;
				
				echo $result . "^" . $message; 
				break;
				
			case "add":
				echo $art->add( $_POST );
				break;
				
			case "modify":
				echo trim( $art->modify( $_POST, FALSE ) );
				break;
				
			case "delete":
				echo $art->delete( TRUE );
				break;
				
			case "refresh_section_selector":
				$selector = Article::getHtml( "get-section-selector", array( 'active_record' => $art ) );
				echo $selector['html'];
				break;
				
			case "manage_sections":
				$section_html = Section::getHtml( "get-section-manager-module", array() );
				echo $section_html;
				break;	
				
			case "format_body":
				$a = new Article( $_GET['article_id'] );
				echo $a->m_common->formatText( $a->m_body );
				break;
				
			case "save_title":
				
				$a = new Article( $_GET['article_id'] );
				
				if( strlen( trim( $_POST['title'] ) ) > 0 )
				{
					$input = $a->getDataArray();
					$input['title'] = $_POST['title'];
					$a->modify( $input, FALSE );
					$a->setMemberVars( $a->m_article_id, FALSE );
					$title = $a->m_title;
				}
				else 
				{
					$title = $a->m_title;
				}
				
				echo $title;
				break;
				
			case "save_body":
				
				$a = new Article( $_GET['article_id'] );
				
				if( strlen( trim( $_POST['body'] ) ) > 0 )
				{
					$input = $a->getDataArray();
					$input['body'] = $_POST['body'];
					
					$a->modify( $input, FALSE );
					$a->setMemberVars( $a->m_article_id, FALSE );
				}
				break;
				
			case "format_body_for_text_box":
				$a = new Article( $_GET['article_id'] );
				echo $a->m_body;
				break;
				
			case "get_title":
				$a = new Article( $_GET['article_id'] );
				echo $a->m_title;
				break;
				
			case "update_file":
				$a = new Article( $_GET['article_id'] );
				$a->updateFile( $_POST['file_id'] );
				break;
		}
		break;
		
	case "section":
		
		$s = new Section( $_GET['section_id'] );
		
		switch( $process )
		{
			case "validate":
				$form_result = $s->checkInput( $_POST, $s->m_common->m_db->fixBoolean( $_POST['from_add'] ) );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Section Saved" : $form_result;
				
				echo $result . "^" . $message; 
				break;
				
			case "add":
				echo $s->add( $_POST );
				break;
				
			case "modify":
				echo trim( $s->modify( $_POST, FALSE ) );
				break;
				
			case "delete":
				echo $s->delete( TRUE );
				break;
				
			case "show_section_list":
				$sections = Section::getSections( "active", "1" );
				$section_list = Section::getHtml( 'get-section-list', array( 'records' => $sections ) );
				echo $section_list['html'];
		}		break;
		break;
		
	case "user_type":
		
		$ut = new UserType( $_GET['user_type_id'] );
		
		switch( $process )
		{
			case "validate":
				$form_result = $ut->checkInput( $_POST, $ut->m_common->m_db->fixBoolean( $_POST['from_add'] ) );
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "User Type Saved" : $form_result;
				
				echo $result . "^" . $message; 
				break;
				
			case "add":
				echo $ut->add( $_POST );
				break;
				
			case "modify":
				echo trim( $ut->modify( $_POST, FALSE ) );
				break;
				
			case "delete":
				echo $ut->delete( TRUE );
				break;
				
			case "get-type-list":
				$records = UserType::getUserTypes( "active", "1" );
				$list = UserType::getHtml( 'get-type-list', array( 'records' => $records ) );
				echo $list['html'];
		}		break;
		break;
		
	case "view":
	
		$view = new View( $_GET['view_id'] );
		
		switch( $process )
		{
			case "validate":
				$form_result = $view->checkInput( $_POST, $view->m_common->m_db->fixBoolean( $_POST['from_add'] ) );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "View Saved" : $form_result;
				
				echo $result . "^" . $message; 
				break;
				
			case "add":
				echo $view->add( $_POST );
				break;
				
			case "modify":
				echo trim( $view->modify( $_POST, FALSE ) );
				break;
				
			case "delete":
				echo $view->delete( TRUE );
				break;
				
			case "reorder":
				echo $view->updateViewPriorities( $_POST['view_priorities'] );
				break;
				
			case "show_reorder_views":
				
				$admin = new Admin( array() );
				
				$view_html = $admin->getHtml( "manage-pages", array( 
					'records' => View::getNavViews(), 
					'hover_enabled' => FALSE,
					'list_type' => "reorder" ) 
				);
				echo $view_html['html'];
				break;
				
			case "show_normal_views":
				
				$admin = new Admin( array() );
				
				$view_html = $admin->getHtml( "manage-pages", array( 
					'records' => View::getViews( "active", "1" ), 
					'hover_enabled' => TRUE,
					'list_type' => "normal" ) 
				);
				
				echo $view_html['html'];
				break;
		}
		break;
		
	case "user":
		
		$u = new User( $_GET['user_id'] );
		$is_addition = ( $u->m_user_id == 0 ) ? TRUE : FALSE;
		
		switch( $process )
		{
			case "validate":
				$form_result = $u->checkInput( $_POST, $is_addition );				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "User Updated." : $form_result;
				echo $result . "^" . $message;
				break;
				
			case "add":
				echo $u->add( $_POST );
				break;
				
			case "modify":
				echo $u->modify( $_POST, $is_addition );
				break;
				
			case "delete":
				echo $u->delete( TRUE );
				break;
				
			case "update_photo":
				$u->updatePhoto( $_POST, $_FILES );
				break;
				
			case "refresh_user_type_selector":
				$user_types = UserType::getHtml( 'get-radio-selectors', array( 
					'active_record' => new UserType( 0 ),
					'active_user' => new User( $_GET['user_id'] ) )
				);
				
				echo $user_types['html'];
				break;
				
			case "refresh_user_list":
				$admin = new Admin( array() );
				$user_list_html = $admin->getHtml( "get-user-list", array( 'records' => User::getUsers( "active", "1" ) ) );
				echo $user_list_html['html'];
				break;
		}
		break;
		
	case "permission":
		
		$p = new Permission( $_GET['permission_id'] );
		
		switch( $process )
		{
			case "validate":
				$form_result = $p->checkInput( $_POST, $p->m_common->m_db->fixBoolean( $_POST['from_add'] ) );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Permission Saved" : $form_result;
				
				echo $result . "^" . $message; 
				break;
				
			case "add":
				echo $p->add( $_POST );
				break;
				
			case "modify":
				echo trim( $p->modify( $_POST, FALSE ) );
				break;
				
			case "delete":
				echo $p->delete( TRUE );
				break;
				
			case "show_permission_list":
				$records = Permission::getPermissions( "active", "1" );
				$list = Permission::getHtml( 'get-permission-list', array( 'records' => $records ) );
				echo $list['html'];
		}		break;
		break;
		
	case "file":
		
		$file = new File( 0 );
		
		switch( $process )
		{
			case "upload_file":
				
				foreach( $_FILES as $input_name => $file_info )
				{
					$file->doFileUpload( $_POST, $file_info );
				}
				break;
				
			case "validate":
				$return = "0";
				if( strlen($_POST['file_path_and_name'] ) > 0 )
				{
					//extract file name
					$file_split = explode( "\\\\", $_POST['file_path_and_name'] );
					$file_name = trim( $file_split[ count( $file_split ) - 1 ] );
					
					if( is_string( $file->checkDuplicateFile( $file_name ) ) )
					{
						$return = "That file name is already taken.";
					}
					
					if( $_POST['is_image'] == "1" )
					{
						if( $return == "0" )
						{
							$valid_extensions = array( "png", "jpg", "jpeg", "gif", "bmp" );
							$file_name_split =  explode( ".", $file_name );
							$ex = strtolower( trim( $file_name_split[ count( $file_name_split ) - 1 ] ) );
							
							if( !in_array( $ex, $valid_extensions) )
							{
								$return = "File must be an image.";
							}
						}
					}
				}
				else
				{
					$return = "You must select a file.";
				}
				
				echo $return;
				break;
				
			case "show_file_edit":
				$file = new File( $_GET['file_id'] );
				$file_html = $file->getHtml( "get_file_upload_form", $_GET );
				$lib_html = $file->getHtml( "get_image_library", $_GET );
				
				echo '
				<div class="colorbox_login_container">
					' . $file_html['body'] . ' 
					' . $lib_html['body'] . '
				</div>
				';
				break;
				
			case "confirm-upload":
				$sql = "
				SELECT count(*)
				FROM common_Files
				WHERE LOWER( file_name ) LIKE '" . strtolower( $_POST['file_name'] ) . "%'";
				
				$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
				$row = $common->m_db->fetchRow( $result );
				$return = ( $row[0] > 0 ) ? "1" : "0";
				echo $return;
				break;
		}
		break;
		
	case "setting":
		
		$s = new Setting( $_GET['setting_id'] );
		$is_addition = ( $s->m_setting_id == 0 ) ? TRUE : FALSE;
		
		switch( $process )
		{
			case "validate":
				$form_result = $s->checkInput( $_POST, $is_addition );
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Setting Saved" : $form_result;
				echo $result . "^" . $message; 
				break;
				
			case "add":
				echo trim( $s->add( $_POST ) );
				break;
				
			case "modify":
				echo trim( $s->modify( $_POST, FALSE ) );
				break;
				
			case "delete":
				$s->delete( TRUE );
				break;
		}
		break;
		
	case "mail":
	
		switch( $process )
		{
			case "validate":
				//set vars
				$email = new EmailMessage();
				$form_result = $email->validateEmailForm( $_POST );
				$result = ( !$form_result['result'] ) ? 1:0; 
				echo $result . "^" . $form_result['message']; 
				break;
				
			case "send":
				$email = new EmailMessage();
				$email->sendMail( $_POST );
				break;
		}
		break;
		
	default:
		echo "Error: Task '" . $task . "' is invalid.";
		break;
}