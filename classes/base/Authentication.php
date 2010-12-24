<?
/**
 * A class to handle the site authentication.
 * @since	20100911, Hafner
 */

require_once( "base/Article.php" );
require_once( "base/Common.php" );
require_once( "base/Contact.php" );
require_once( "base/FormHandler.php" );
require_once( "base/Session.php" );
require_once( "base/File.php" );

class Authentication
{
	/**
	 * Instance of the Common class.
	 * @var	Common
	 */
	protected $m_common;
	
	/**
	 * Instance of the FormHandler class.
	 * @var	Form
	 */
	protected $m_form;
	
	/**
	 * PK of the Authentication Record.
	 * @var	int
	 */
	protected $m_authentication_id;
	
	/**
	 * Id of the contact linked to this auth record.
	 * @var	int
	 */
	protected $m_contact_id;
	
	/**
	 * Nickname.
	 * @var	string
	 */
	protected $m_username;
	
	/**
	 * Email address.
	 * @var	string
	 */
	protected $m_email;
	
	/**
	 * Password for the user.
	 * @var	string
	 */
	protected $m_password;
	
	/**
	 * Active flag.
	 * @var	boolean
	 */
	protected $m_active;
	
	/**
	 * Array of linked objects.
	 * @var	array
	 */
	protected $m_linked_objects;
	
	/**
	 * Array of permissions aliases for this auth user.
	 * @var	array
	 */
	protected $m_permissions;
	
	/**
	 * Constructs the object.
	 * @since	20100618, hafner
	 * @return	State
	 * @param	int				$auth_id			id of the current user
	 */
	public function __construct( $auth_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_authentication_id = ( is_numeric( $auth_id ) ) ? $auth_id : 0;
		$this->setMemberVars( $objects );
		
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20100618, hafner
	 * @return	boolean
	 */
	public function setMemberVars( $objects )
	{
		//get member vars
		$sql = "
		SELECT 
			a.authentication_id,
			a.username,
			a.password,
			a.active,
			a.email,
			c.contact_id
			
		FROM 
			common_Authentication a
			
		LEFT JOIN common_Contacts c ON
			c.authentication_id = a.authentication_id	
			
		WHERE 
			a.authentication_id = " . $this->m_authentication_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_authentication_id = $row['authentication_id'];
		$this->m_contact_id = $row['contact_id'];
		$this->m_username = $row['username'];
		$this->m_email = $row['email'];
		$this->m_password = $row['password'];
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_permissions = $this->permissionsGet();
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects() : array(); 
		
		return TRUE;
		
	}//setMemberVars()
	
	/**
	* Get an array of data suitable to use in modify
	* @since 	20100618, hafner
	* @return 	array
	* @param 	boolean 		$fix_clob		whether or not to file member variables of CLOB type
	*/
	public function getDataArray( $fix_clob = TRUE ) 
	{
		return array(
			'authentication_id' => $this->m_authentication_id,
			'contact_id' => $this->m_contact_id,
			'username' => $this->m_username,
			'email' => $this->m_email,
			'password' => $this->m_password,
			'active' => $this->m_active
		);
		
	}//getDataArray()
	
	/**
	* Save with the current values of the instance variables
	* This is a wrapper to modify() to ease some methods of coding
	* @since 	20100618, hafner
	* @return	mixed
	*/
	public function save()
	{
		$input = $this->getDataArray();
		return $this->modify( $input, FALSE );
	}//save()
	
	/**
	 * Adds a new record.
	 * Returns ( int ) Id of record if form data is valid, ( string ) form error otherwise.
	 * @since	20100618,hafner
	 * @return	mixed
	 * @param	array				$input				array of input data
	 */
	public function add( $input )
	{
		$this->checkInput( $input, TRUE );
		
		if( !$this->m_form->m_error )
		{
			//only set upload_timestamp on add
			$input['authentication_id'] = $this->m_common->m_db->insertBlank( 'common_Authentication', 'authentication_id' );
			$this->m_authentication_id = (int) $input['authentication_id'];
			$return = $this->m_authentication_id;
			$this->modify( $input, TRUE );
		}
		else
		{
			$return = $this->m_form->m_error;
		}
		
		return $return;
		
	}//add()
	
	/**
	 * Modifies a record.
	 * Returns ( int ) Id of record if form data is valid, ( string ) form error otherwise. 
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data
	 * @param	boolean				$from_add			if we are adding a new record, from_add = TRUE, FALSE otherwise.
	 */
	public function modify( $input, $from_add )
	{
		if( !$from_add )
		{
			$this->checkInput( $input, FALSE );
		}

		if( !$this->m_form->m_error )
		{
			$sql = "
			UPDATE common_Authentication
			SET username = '" . $input['username'] . "',
				password = '" .  $this->passwordEncrypt( $this->passwordSalt(), $input['password'] ) . "'
			WHERE authentication_id = " . $this->m_authentication_id;
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_authentication_id;
			
			//update permissions
			$this->updatePermissions( $input, $from_add );
		}
		else
		{
			$return = $this->m_form->m_error;
		}
		
		return $return;
		
	}//modify()
	
	/**
	 * Modifies a record.
	 * Returns TRUE, always. 
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data 
	 */
	public function delete( $deactivate = TRUE )
	{
		//setup vars
		$articles = Article::getArticlesForAuth( $this->m_authentication_id );
		
		if( $deactivate )
		{
			foreach( $articles as $i => $article_id )
			{
				$a = new Article( $article_id );
				$a->delete( TRUE );
			}
			
			$sql = "
			UPDATE common_Authentication
			SET active = 0
			WHERE authentication_id = " . $this->m_authentication_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );	
		}
		else
		{
			$sql = "
			DELETE
			FROM common_AuthenticationToPermission
			WHERE authentication_id = " . $this->m_authentication_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			foreach( $articles as $i => $article_id )
			{
				$a = new Article( $article_id );
				$a->delete( FALSE );
			}
			
			$sql = "
			DELETE
			FROM common_Authentication
			WHERE authentication_id = " . $this->m_authentication_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		
		return TRUE;
		
	}//delete()
	
	public function permissionsUpdate( $input, $from_add )
	{
		if( !$from_add )
		{
			//delete all permissions for this user
			$sql = "
			DELETE
			FROM common_AuthenticationToPermission
			WHERE authentication_id = " . $this->m_authentication_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
			
		if( array_key_exists( "permissions", $input ) )
		{
			//add permissions
			foreach( $input['permissions'] as $i => $alias )
			{
				$sql = "
				INSERT INTO common_AuthenticationToPermission( authentication_id, permission_id )
				VALUES( " . $this->m_authentication_id . ", ( SELECT permission_id FROM common_Permissions WHERE LOWER( alias ) = '" . $alias . "'  ) )
				";
				
				$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			}
		}
		
		return TRUE;
		
	}//permissionsUpdate()
	
	public function checkLoginForm( $input )
	{
		//check missing username
		if( !array_key_exists( "username", $input ) || 
			strlen( trim( $input['username'] ) ) == 0 )
		{
			$this->m_form->m_error = "You must choose a username.";
		}
		
		//check missing password
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "password", $input ) || 
				strlen( trim( $input['password'] ) ) == 0 )
			{
				$this->m_form->m_error = "You must choose a password.";
			}
		}
		
		return $this->m_form->m_error;
		
	}//checkLoginForm()
	
	/**
	 * Validates the form input for creating/modifying a new File record.
	 * Returns FALSE on success, error message string otherwise.
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array			$input			array of data
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function checkInput( $input, $is_addition )
	{
		//check missing username
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "username", $input ) || strlen( trim( $input['username'] ) ) == 0 )
			{
				$this->m_form->m_error = "You must choose a username.";
			}
		}
		
		//check missing email
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "email", $input ) || strlen( trim( $input['email'] ) ) == 0 )
			{
				$this->m_form->m_error = "You must choose an email.";
			}
		}
		
		//check missing password
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "password", $input ) || strlen( trim( $input['password'] ) ) == 0 )
			{
				$this->m_form->m_error = "You must choose a password.";
			}
		}
		
		//check valid email
		if( !$this->m_form->m_error )
		{
			$this->m_form->m_error = $this->m_common->validateEmailAddress( $input['email'] );
		}
			
		//check valid password
		if( !$this->m_form->m_error )
		{
			if( !$this->passwordValidate( $password ) )
			{
				$this->m_form->m_error = "Password is invalid. It may not contain any of the following characters: semicolons (;), single quotes ('), double quotes (\"), or spaces.";
			}
		}
		
		//check duplicate email
		if( $is_addition )
		{
			if( !$this->m_form->m_error )
			{
				$dup_check = array( 
					'table_name' => "common_Authentication",
					'pk_name' => "authentication_id",
					'check_values' => array( 'email' => strtolower( $input['email'] ) )
				);
				
				if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
				{
					$this->m_form->m_error = "That email already exists. You must choose a unique email address.";
				}
			}
		}
			
		return $this->m_form->m_error;
		
	}//checkInput()
	
	public function setLinkedObjects()
	{
		return array( 'contact' => new Contact( $this->m_contact_id, FALSE ) );
	}//setLinkedObjects()
	
	/**
	* Adds a permission to this auth record. 
	* @return	boolean
	* @since	20101018, hafner
	* @param	int			$permission_id		pk of common_Permissions
	*/
	public function permissionsAdd( $permission_id )
	{
		$sql = "
		INSERT INTO common_AuthenticationToPermission( authentication_id, permission_id )
		VALUES( " . $this->m_authentication_id . ", " . $permission_id . " )";
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );

		return TRUE;
		
	}//permissionsAdd()
	
	/**
	* Returns HTML
	* @author	20100908, Hafner
	* @return	array
	* @param	string		$cmd		determines which HTML snippet to return
	* @param	array		$vars		array of variables for the html
	*/
	public static function getHtml( $cmd, $vars = array() )
	{
		$common = new Common();
		
		switch( strtolower( $cmd ) )
		{
			case "show-login-form":
				
				$divs = $common->getHtml( "full-div", array() );
				
				$return = array( 'body' => '
					<div class="padder rounded_corners login_form_container bg_color_light_tan border_dark_grey">
						<form id="auth_login_form">
							
							<div class="center header">
								Login to manage the site\'s content.
							</div>
							
							<div id="result_login_attempt" class="result"></div>
							
							<div class="center bottom_spacer">
								<input type="text" class="input text_input text_long font_normal input_clear" name="username" value="Username or Email" clear_if="Username or Email" />
							</div>
							
							<div class="center bottom_spacer">
								<input type="password" class="input text_input text_long font_normal input_clear" name="password" value="passwd" clear_if="passwd" id="auto_login" />
							</div>
							
							<div class="center">
								' . Common::getHtml( 'get-button', array(
									'pk_name' => "auth_id",
									'pk_value' => "0",
									'id' => "authentication",
									'process' => "login",
									'button_value' => "Login",
									'extra_style' => 'style="width:70px;"'
									
								) ) . '
							</div>
							
						</form>
					</div>
					'
				);
				break;
				
			case "get-access-restricted-message":
				$return = array(
					'body' =>
						'<div style="position:relative;margin-top:50px;text-align:center;color:#FF0000;font-weight:bold;line-height:1.8em;">
							You do not have access to this section <br/>
							Please contact your administrator.
						</div>
						'
				);
				break;
				
			case "login-string":
				
				$admin_link = $common->makeLink( array( 'v' => 'admin' ) );
				$auth = $vars['active_record'];
				
				$body = '
				<div class="login_string_container bg_color_white font_small">
					<div>
						Howdy, ' . ucwords( $auth->m_username ) . '
						&nbsp;|&nbsp
						<a href="' . $common->makeLink( array( 'v' => "admin", 'sub' => 'manage-account' ) ) . '">My Account</a>
						&nbsp;|&nbsp
						<a href="#" id="authentication" process="kill_login">Logout</a>
					</div>
				</div>
				';
				$return = array( 'body' => $body );
				break;
				
			case "auth-add-mod-form":
				
				$auth = $vars['active_auth'];
				$con = $vars['active_contact'];
				$adm_checked = ( is_array( $auth->m_permissions ) && in_array( 'ADM', $auth->m_permissions ) ) ? 'checked="checked"' : "";
				$mov_checked = ( is_array( $auth->m_permissions ) && in_array( 'MOV', $auth->m_permissions ) ) ? 'checked="checked"' : "";
				$process = ( $auth->m_authentication_id > 0 ) ? "modify" : "add";
				
				$body = '
				<div class="cc_main_content" style="text-align:center;height:230px;">
					
					<div class="result" id="result">
					</div>
				
					<div style="font-weight:bold;margin-bottom:10px;">
						To allow this user to login and manage the site\'s content, you must create a login.
					</div>
					
					<form id="auth_add_mod_form">
						<table cellspacing="10" style="margin:auto;">
							
							<tr>
								<td style="color:#FF0000;text-align:right;">
									Email Address:
								</td>
								<td>
									<input type="text" class="text_input" name="username" value="' . $auth->m_username . '" style="width:225px;" clear_if=""/>
								</td>
							</tr>
							
							<tr>
								<td style="color:#FF0000;text-align:right;">
									Password:
								</td>
								<td>
									<input type="password" class="text_input" name="password" value="' . $auth->m_password . '" style="width:225px;" clear_if=""/>
								</td>
							</tr>
							
							<tr>
								<td style="text-align:right;">
									<input type="checkbox" name="permissions[]" value="adm" ' . $adm_checked . '/>
								</td>
								<td style="color:#FF0000;text-align:left;">
									User can manage login permissions.
								</td>
							</tr>
							
							<tr>
								<td style="text-align:right;">
									<input type="checkbox" name="permissions[]" value="mov" ' . $mov_checked . '/>
								</td>
								<td style="color:#FF0000;text-align:left;">
									User can add new movies
								</td>
							</tr>
							
						</table>
						
					</form>
					
					<div>
						<img src="/images/btn_save.gif" id="authentication" process="' . $process . '" contact_id="' . $con->m_contact_id . '" authentication_id="' . $auth->m_authentication_id . '"/>
					</div>							
					 	
				</div>
				';
				
				$return = array(
					'title' => "Add User Login",
					'body' => $body
				);
				break;
				
			case "show-password-form":
				$body = '
				<div class="center header">
					Change Password
				</div>
				
				<div id="result" class="result"></div>
				<form id="auth_password_form">
					<table class="bottom_spacer">
						<tr>
							<td class="right">
								Current Password:
							</td>
							<td>
								<input type="password" class="input text_input text_long normal_font form_input" name="cur_pass" value="" />
							</td>
						</tr>
						
						<tr>
							<td class="right">
								New Password:
							</td>
							<td>
								<input type="password" class="input text_input text_long normal_font form_input" name="new_pass" value="" />
							</td>
						</tr>
						
						<tr>
							<td class="right">
								Re-type New Password:
							</td>
							<td>
								<input type="password" class="input text_input text_long normal_font form_input" name="new_pass_copy" value="" />
							</td>
						</tr>
					</table>
				</form>
				
				<div class="center">
					<a href="#" id="authentication" process="change_password" class="no_hover">
						<div class="button rounded_corners padder">
							Save
						</div>
					</a>
				</div>
				';
				
				$return = array( 'body' => $body );
				break;
				
			case "get-view-form":
			
				$u = $vars['active_record'];
				$c = $u->m_linked_objects['contact'];
				$ct = new ContactType( $c->m_contact_type_id );
				$contact_type_title = ( strlen( $ct->m_title ) > 0 ) ? $ct->m_title : "User";
				
				//get user image
				if( !$c->m_use_gravatar )
				{
					$thumb_id = ( $c->m_thumb_id > 0 ) ? $c->m_thumb_id : $common->m_db->getIdFromTitle( "default.jpg", array( 
						'pk_name' => "file_id", 
						'title_field' => "file_name", 
						'table' => "common_Files" ) 
					);
					
					$thumb = new File( $thumb_id );
					$img_src = $thumb->m_relative_path . "/" . $thumb->m_file_name ; 
				}
				else
				{
					$img_src = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $u->m_email ) ) ) . '&s=80';
				}
				
				$html = '
				<div class="padder">
					
					<div>
						<div class="thumb_holder bg_color_white user_holder padder border_dark_grey" >
							<img src="' . $img_src . '" />
						</div>
						
						<div class="user_holder">
							<div class="header color_accent user_name">
								' . ucwords( $u->m_username ) . '
							</div>
							' . $contact_type_title . '
							' . Common::getHtml( 'get-button', array(
								'button_value' => "Modify",
								'extra_style' => 'style="width:81px;margin-top:15px;"',
								'href' => $common->makeLink( array( 
									'v' => "admin",
									'sub' => 'manage-user',
									'id1' => $u->m_authentication_id ) ) ) 
							) . '
						</div>
						
						<div class="clear"></div>
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-add-form":
				
				break;
								
			case "get-edit-form":
			
				$user = $vars['active_record'];
				
				if( $u->m_authentication_id > 0 )
				{
					$process = "modify";
					$title = $u->m_username;
					$email = $u->m_email;
					$from_add = "0";
				}
				else
				{
					$auth_id = Authentication::getAuthId();
					$process = "add";
					$title = "User Nickname";
					$body = "User Email";
					$from_add = "1";
				}
				
				$html = '
				<input type="text" name="username" class="" clear_if="" />
				<input type="text" name="email" class="" clear_if="" />
				<input type="password" name="password" class="" clear_if="" />
				<input type="password" name="password_copy" class="" clear_if="" />

				<div class="padder_10">
					' . Common::getHtml( "title-bar", array( 'title' => ucWords( $process ) . " User", 'classes' => '' ) ) . '
					
					<div id="result_' . $process . '_' . $u->m_authentication_id . '" class="result">
					</div>
	
					<form id="article_form_' . $u->m_authentication_id . '">
						
						<div class="padder_10">
							<input type="text" name="title" class="text_input input_clear text_extra_long" value="' . $title  . '" clear_if="Article Title">
						</div>
						
						<div class="padder_10 padder_no_top">
							<textarea name="body" id="body" class="text_input input_clear text_extra_long text_area" clear_if="Article Body">' . $body .'</textarea>
						</div>
						
						
						<div class="padder_10">
							' . Common::getHtml( "selector-module", array( 
								'title' => "View", 
								'content' => $view_selector,
								'content_class' => "" ) ) . '
								
							' . Common::getHtml( "selector-module-spacer", array() ) . '
							
							' . Common::getHtml( "selector-module", array( 
								'title' => "Section", 
								'content' => $section_selector,
								'content_class' => "article_section_selector_" . $a->m_article_id ) ) . '
								
							<div class="clear"></div>
							
						</div>
						
						' . Common::getHtml( "get-form-buttons", array( 
						
							'left' => array( 
								'pk_name' => "article_id",
								'pk_value' => $a->m_article_id,
								'process' => $process,
								'id' => "article",
								'button_value' => ucwords( $process ),
								'extra_style' => 'style="width:41px;"' ),
								
							'right' => array(
								'pk_name' => "article_id",
								'pk_value' => $a->m_article_id,
								'process' => "cancel_" . $process,
								'id' => "article",
								'button_value' => "Cancel" ) 
							) 
						) . '
													
						<input type="hidden" name="authentication_id" value="' . $auth_id . '"/>
						<input type="hidden" name="from_add" value="' . $from_add . '"/>
						
					</form>
				</div>
				';
								
				$return = array( 'html' => $html );
				break;
								
			case "get-delete-form":
				break;
				
			default:
				throw new Exception( "Error: Command '" . $cmd . "' is invalid." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	/**
	 * Viadates the auth login.
	 * Returns TRUE if login username/password is valid, FALSE otherwise.
	 * @since	20100912, Hafner
	 * @return	boolean
	 * @param	string		$username		username (email address)
	 * @param	string		$password		password
	 */
	public function validateLogin( $username, $password )
	{
		$return = FALSE;
		
		if( strlen( trim( $username ) ) > 0 &&
			strlen( trim( $password ) ) > 0 )
		{
			$sql = "
			SELECT authentication_id, password
			FROM common_Authentication
			WHERE ( LOWER( username ) = '" . strtolower( $username ) . "' OR LOWER( email ) = '" . strtolower( $username ) . "' ) AND
			active = 1";
			
			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			if( $this->m_common->m_db->numRows( $result ) == 1 )
			{	
				//grab encrypted password
				$row = $this->m_common->m_db->fetchRow( $result );
				$encrypted_password = $row[1];
				
				//compare passwords
				if( $this->passwordCompare( $password, $encrypted_password ) )
				{
					$return = TRUE;
				}
			}
		}
		
		return $return;
		
	}//validateLogin()
	
	/**
	 * Validates a password string.
	 * Returns TRUE if password is valid, FALSE otherwise.
	 * @since	20100912, Hafner
	 * @return	boolean
	 * @param 	string 		$password		password string
	 */
	public function passwordValidate( $password )
	{
		$return = TRUE;
		
		if( strpos( $password, "'" ) !== FALSE || 
			strpos( $password, '"' ) !== FALSE ||
			strpos( $password, " " ) !== FALSE ||
			strpos( $password, ";" ) !== FALSE )
		{
			$return = FALSE;
		}
		
		return $return;
		
	}//passwordValidate()
	
	/**
	 * Determines if the current login is valid or if there is a current login. 
	 * Returns TRUE if there is a current login and it's valid, FALSE otherwise.
	 * @since	20100912, Hafner
	 * @return	boolean
	 */
	public function validateCurrentLogin()
	{
		$return = FALSE;
		
		if( array_key_exists( "sid", $_SESSION ) &&
			Session::validateSessionId( $_SESSION['sid'] ) )
		{
			$return = TRUE;
		}
		
		return $return;
		
	}//validateCurrentLogin()
	
	/**
	 * Controls the page access. 
	 * Returns HTML string.
	 * @since	20100912, Hafner
	 * @return	string
	 * @param 	object		$controller		instance of the current controller
	 */
	public function controlPageAccess( $controller )
	{
		$show_page_content = TRUE;
		
		if( $controller->getAuthStatus() )
		{
			if( !$this->validateCurrentLogin() )	
			{
				$show_page_content = FALSE;
			}
		}
		
		if( $show_page_content )
		{
			//if we have a current login, set the auth object.
			if( $this->validateCurrentLogin() )
			{
				$auth_id = self::getAuthId();
				$controller->setAuth( $auth_id );
			}

			$controller->setContent();
			$return = $controller->getContent();
		}
		else
		{
			$html = self::getHtml( "show-login-form", array() );
			$return = $html['body'];
		}
		
		return $return;
		
	}//controlPageAccess()
	
   /**
	* Gets a string message if login is invalid.
	* Returns FALSE if login is valid, string error message otherwise.
	* @author	Version 20100912, Hafner
	* @return	mixed
	* @param	string		$username		username
	* @param	string		$password		password
	*/
	public function getLoginMessage( $username, $password )
	{
		$return = FALSE;
		
		if( !$this->validateLogin( $username, $password ) )
		{
			$return = "Login invalid. Please try again.";
		}
		
		return $return;
		
	}//getLoginMessage()
	
	/**
	 * Collects array of permissions for this user.
	 * Returns array of permission aliases.
	 * @return	array
	 * @since	20101020, hafner
	 */
	public function permissionsGet()
	{
		$return = FALSE;
		//$auth_id = ( ( $this->m_authentication_id ) > 0 ) ? $this->m_authentication_id : 0;
		
		$sql = "
		SELECT 
			p.alias AS alias
		FROM 
			common_AuthenticationToPermission a2p
		JOIN common_Permissions p ON
			p.permission_id = a2p.permission_id  
		WHERE 
			a2p.authentication_id = " . $this->m_authentication_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		if( $this->m_common->m_db->numRows( $result ) > 0 )
		{
			$return = array();
			while( $row = $this->m_common->m_db->fetchAssoc( $result ) )
			{
				$return[] = $row['alias'];
			}
		}
		
		return $return;
		
	}//permissionsGet()
	
	/**
	 * Validates the current session.
	 * Returns authentication_id ( int ) if session is valid, FALSE otherwise.
	 * @since	20100922, Hafner
	 * @return 	mixed
	 */
	public static function getAuthId()
	{
		$return = FALSE;
		$common = new Common();
		
		//get auth id
		$sql = "
		SELECT authentication_id
		FROM common_Sessions
		WHERE session_id = '" . $_SESSION['sid'] . "' AND
		end_timestamp IS NULL";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		if( $common->m_db->numRows( $result ) > 0 )
		{
			$row = $common->m_db->fetchRow( $result );
			$return = $row[0];
		}
		
		return $return;
		
	}//getAuthId()
	
	public function passwordValidateChange( $post )
	{
		$return = FALSE;
		
		if( strlen( $post['cur_pass'] ) == 0 || 
			strlen( $post['new_pass'] ) == 0 ||
			strlen( $post['new_pass_copy'] ) == 0 )
		{
			$return = "You must fill in all fields.";	
		}
		
		if( $return === FALSE )
		{
			if( !$this->passwordCompare( $post['cur_pass'], $this->m_password ) )
			{
				$return = "Invalid current password.";	
			}
		}
		
		if( $return === FALSE )
		{
			if( $post['new_pass'] != $post['new_pass_copy'] )
			{
				$return = "New passwords do not match";
			}
		}
		
		if( $return === FALSE )
		{
			if( strlen( $post['new_pass'] ) < 7 )
			{
				$return = "New password must be at least 7 characters.";
			}
		}
		
		if( $return === FALSE )
		{
			if( !$this->passwordValidate( $post['new_pass'] ) )
			{
				$return = "New password cannot contain spaces.";
			}
		}
		
		return $return;
		
	}//passwordValidateChange()
	
	public function passwordUpdate( $new_pass )
	{
		$sql = "
		UPDATE common_Authentication 
		SET password = '" . $this->passwordEncrypt( $this->passwordSalt(), $new_pass ) . "'
		WHERE authentication_id = " . $this->m_authentication_id;
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
	}//passwordUpdate()
	
	public function passwordEncrypt( $salt, $plain_text_password )
	{
		return $salt . ( hash( 'whirlpool', $salt . $plain_text_password ) );	
	}//passwordEncrypt()
	
	public function passwordSalt()
	{
		return substr( str_pad( hexdec( mt_rand() ), 8, '0', STR_PAD_LEFT ), 0, 8 );
	}//passwordSalt()
	
	public function passwordCompare( $plain_text_password, $encrypted_password )
	{
		$salt = substr( $encrypted_password, 0, 8 );
		$plain_encrypted = $this->passwordEncrypt( $salt, $plain_text_password );

		return $encrypted_password == $plain_encrypted;
		
	}//passwordCompare()
	
	public static function getUsers( $field, $value )
	{
		$i = 1;
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT authentication_id
		FROM common_Authentication
		WHERE authentication_id > 0 AND
		" . $field . " = " . $value . "
		ORDER BY username ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[$i] = new Authentication( $row[0], TRUE );
			$i++;
		}
		
		return $return;
		
	}//getUsers()
	
   /**
	* Get a member variable's value
	* @author	Version 20100618, hafner
	* @return	mixed
	* @param	string		$var_name		Variable name to get
	*/
	public function __get( $var_name )
	{
		$exclusions = array();

		if( !in_array( $var_name, $exclusions ) )
		{
			return $this->$var_name;
		}
		else
		{
			throw new exception( "Error: Access to member variable '" . $var_name . "' for class '" . get_class( $this ) . "' is denied" );
		}
	}//__get()
	
	/**
	* Set a member variable's value
	* @since	20100618, hafner
	* @return	mixed
	* @param	string		$var_name		Variable name to set
	* @param	string		$var_value		Value to set
	*/
	public function __set( $var_name, $var_value )
	{
		$exclusions = array( 'm_article_id' );

		if( !in_array( $var_name, $exclusions ) )
		{
			$this->$var_name = $var_value;
			return TRUE;
		}
		else
		{
			throw new exception( "Error: Access to member variable '" . $var_name . "' for class '" . get_class( $this ) . "' is denied" );
		}
	}//__set()
	
}//class View
?>