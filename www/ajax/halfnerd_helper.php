<?

//start session
session_start();

//require classes
require_once( "base/Article.php" );
require_once( "base/Authentication.php" );
require_once( "base/Common.php" );
require_once( "base/Session.php" );
require_once( "controllers/Admin.php" );

$common = new Common();
$auth = new Authentication( Authentication::getUserId() );

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
				echo $view->delete( FALSE );
				break;
				
			case "reorder":
				echo $view->updateViewPriorities( $_POST['view_priorities'] );
				break;
				
			case "show_reorder_views":
				
				$admin = new Admin( array() );
				
				$view_html = $admin->getHtml( "get-view-list", array( 
					'records' => View::getNavViews(), 
					'hover_enabled' => FALSE,
					'list_type' => "reorder" ) 
				);
				echo $view_html['html'];
				break;
				
			case "show_normal_views":
				
				$admin = new Admin( array() );
				
				$view_html = $admin->getHtml( "get-view-list", array( 
					'records' => View::getViews( "active", "1" ), 
					'hover_enabled' => TRUE,
					'list_type' => "normal" ) 
				);
				
				echo $view_html['html'];
				break;
		}
		break;
		
	case "user":
		
		$a = new Authentication( $_GET['authentication_id'] );
		
		switch( $process )
		{
			case "validate":
				$form_result = $a->checkInput( $_POST, $a->m_common->m_db->fixBoolean( $_GET['from_add'] ) );
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Crew member updated." : $form_result;
				echo $result . "^" . $message;
				break;
				
			case "add":
				echo $a->add( $_POST );
				break;
				
			case "modify":
				echo $a->modify( $_POST, $a->m_common->m_db->fixBoolean( $_GET['from_add'] ) );
				break;
				
			case "delete":
				echo $a->delete( TRUE );
				break;
		}
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
				
			case "check-dup-file-name":
				break;
		}
		break;
		
	case "single_value":
		
		switch( $process )
		{
			case "validate":
				//set vars
				$env_var = new EnvVar( 0 );
				$form_result = $env_var->checkInput( $_POST, $env_var->m_common->m_db->fixBoolean( $_GET['from_add'] ) );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Value Saved" : $form_result;
				
				echo $result . "^" . $message; 
				break;
				
			case "modify":
				$env_var = new EnvVar( $_GET['env_var_id'] );
				$env_var->modify( $_POST, FALSE );
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
		
	case "contact":
	
		$c = new Contact( $_GET['contact_id'], TRUE );
		
		switch( $process )
		{
			case "validate":
				$form_result = $c->checkInput( $_POST, $c->m_common->m_db->fixBoolean( $_GET['from_add'] ) );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Contact saved." : $form_result;
				
				echo $result . "^" . $message;
				break;
				
			case "add":
				echo $c->add( $_POST );
				break;
				
			case "modify":
				echo $c->modify( $_POST, FALSE );
				break;
				
			case "delete":
				$c->delete( TRUE );
				break;
				
			case "show-add-mod":
				//setup vars
				$echo_content = TRUE;
				$action = ( $c->m_contact_id > 0 ) ? "modify" : "add";
				
				//compile params
				$details_vars = array( 'active_contact' => $c, 'action' => $action, 'authentication_id' => $auth->m_authentication_id );
				$step_vars = array( 'current_step' => 1, 'total_steps' => 3 );
				
				//grab html
				$step_meter_html = $c->getHtml( "get-step-meter", $step_vars );
				$step_nav_html = $c->getHtml( "get-step-nav", $step_vars );
				$details_form_html = $c->getHtml( "get-contact-details-form",  $details_vars );
				
				//compile body
				$body = '
				<div id="step-meter">
					' .  $step_meter_html['body'] . '
				</div>
				
				<div id="main-content" class="cc_main_content">
					' . $details_form_html['body'] . '
				</div>
				
				<div id="step-nav">
					' . $step_nav_html['body'] . '
				</div>
				
				<input type="hidden" id="current-step" value="' . $step_vars['current_step'] . '"/>
				<input type="hidden" id="total-steps" value="' . $step_vars['total_steps'] . '"/>
				
				<input type="hidden" id="active-contact-id" value="' . $c->m_contact_id . '"/>
				<input type="hidden" id="active-thumb-id" value="' . $c->m_objects_collection['thumb_file']->m_file_id . '"/>
				<input type="hidden" id="active-full-img-id" value="' . $c->m_objects_collection['full_img_file']->m_file_id . '"/>
				
				<input type="hidden" id="link-trigger-id" value="contact-step"/>
				<input type="hidden" id="step-is-complete" value="0"/>
				';
				
				$html = array(
					'title' => ucfirst( $action ) . ' Crew Member',
					'body' => $body
				);
				break;
				
			case "show-auth-set":
				
				$echo_content = TRUE;
				$auth = $c->m_objects_collection['authentication'];
				$action = ( $auth->m_authentication_id > 0 ) ? "modify" : "add"; 
				
				$auth_vars = array( 'active_contact' => $c, 'action' => $action, 'active_auth' => $auth );
				$html = $c->m_objects_collection['authentication']->getHtml( "auth-add-mod-form", $auth_vars );
				break;
				
			case "link_auth":  
				echo $c->addAuth( $_POST['authentication_id'] );
				break;
				
			case "show_crew_member":
				$echo_content = TRUE;
				$html = $c->getHtml( "show_crew_member", array( 'active_contact' => &$c ) );
				break;
				
			case "show-delete":
				$echo_content = TRUE;
				$html = $c->getHtml( "show-delete", array( 'active_contact' => $c ) ); 
				break;
		}
		break;
		
	case "contact_type":
		
		$ct = new ContactType( $_GET['contact_type_id'], TRUE );
		
		switch( $process )
		{
			case "validate":
				$form_result = $ct->checkInput( $_POST, $ct->m_common->m_db->fixBoolean( $_GET['from_add'] ) );
				
				$result = ( !$form_result ) ? 1:0;
				$message =   ( !$form_result ) ? "Job Title Saved." : $form_result;
				
				echo $result . "^" . $message;
				break;
				
			case "add":
				echo $ct->add( $_POST );
				break;
				
			case "modify":
				echo $ct->modify( $_POST, FALSE );
				break;
				
			case "delete":
				$ct->delete( TRUE );
				break;
			
			case "show-add":
				$ct_vars['active_ct'] = $ct;
				$html = $ct->getHtml( "show-add", $ct_vars );

				echo '
				<div class="admin_title_bar">
					' .  $html['title'] . '
				</div>
				' . $html['body'];
				break;
				
			case "show-mod":
				
				$ct_vars['active_ct'] = $ct;
				$html = $ct->getHtml( "show-mod", $ct_vars );
				
				echo $html['mod_form'] . "^" . $html['save_buttons'];
				break;	
				
			case "hide-mod":
				
				$ct_vars['active_ct'] = $ct;
				$html = $ct->getHtml( "hide-mod", $ct_vars );
				
				echo $html['mod_form'] . "^" . $html['save_buttons'];
				break;
				
			case "get-count":
				$records = $ct->getAllRecords( array() );
				echo ( !is_array( $records ) ) ? "0" : "1";
				break;
		}
		break;
		
	default:
		echo "Error: Task '" . $task . "' is invalid.";
		break;
}