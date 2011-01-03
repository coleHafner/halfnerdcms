<?
/**
 * A class to handle the site User.
 * @since	20100911, Hafner
 */

require_once( "base/File.php" );
require_once( "base/Common.php" );
require_once( "base/Article.php" );
require_once( "base/Session.php" );
require_once( "base/UserType.php" );
require_once( "base/FormHandler.php" );

class User
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
	 * PK of the User Record.
	 * @var	int
	 */
	protected $m_user_id;
	
	/**
	 * PK of the User Record.
	 * @var	int
	 */
	protected $m_user_type_id;
	
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
	 * Id of the thumbnail file for this contact.
	 * @var	int
	 */
	protected $m_thumb_id;
	
	/**
	 * first name of the contact record.
	 * @var	string
	 */
	protected $m_first_name;
	
	/**
	 * last name of the contact record.
	 * @var	string
	 */
	protected $m_last_name;
	
	/**
	 * Description of the current contact.
	 * @var	string
	 */
	protected $m_bio;
	
	/**
	 * Array of permissions aliases for this auth user.
	 * @var	array
	 */
	protected $m_permissions;
	
	/**
	 * Use Gravatar flag.
	 * @var	boolean
	 */
	protected $m_use_gravatar;
	
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
	 * Constructs the object.
	 * @since	20100618, hafner
	 * @return	State
	 * @param	int				$user_id			id of the current user
	 */
	public function __construct( $user_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_user_id = ( is_numeric( $user_id ) ) ? $user_id : 0;
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
			user_id,
			user_type_id,
			username,
			email,
			password,
			first_name,
			last_name,
			bio,
			use_gravatar,
			active
		FROM 
			common_Users			
		WHERE 
			user_id = " . $this->m_user_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_user_id = $row['user_id'];
		$this->m_user_type_id = $row['user_type_id'];
		$this->m_username = $row['username'];
		$this->m_email = $row['email'];
		$this->m_password = $row['password'];
		$this->m_first_name = stripslashes( $row['first_name'] );
		$this->m_last_name = stripslashes( $row['last_name'] );
		$this->m_bio = stripslashes( $row['bio'] );
		$this->m_use_gravatar = $this->m_common->m_db->fixBoolean( $row['use_gravatar'] );
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
			'user_id' => $this->m_user_id,
			'user_type_id' => $this->m_user_type_id,
			'contact_id' => $this->m_contact_id,
			'username' => $this->m_username,
			'email' => $this->m_email,
			'password' => $this->m_password,
			'first_name' => $this->m_first_name,
			'last_name' => $this->m_last_name,
			'bio' => $this->m_bio,
			'use_gravatar' => $this->m_use_gravatar,
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
			$input['user_id'] = $this->m_common->m_db->insertBlank( 'common_Users', 'user_id' );
			$this->m_user_id = (int) $input['user_id'];
			$return = $this->m_user_id;
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
			//optional params
			$first_name = ( strlen( $input['first_name'] ) > 0 ) ? "'" . addslashes( $input['first_name'] ) . "'" : "NULL";
			$last_name = ( strlen( $input['last_name'] ) > 0 ) ? "'" . addslashes( $input['last_name'] ) . "'" : "NULL";
			$bio = ( strlen( $input['bio'] ) > 0 ) ? "'" . addslashes( $input['bio'] ) . "'" : "NULL";
			$password_update = "";
			
			//password
			if( strlen( $input['password'] ) > 0 )
			{
				$password_update = "
				password = '" .  $this->passwordEncrypt( $this->passwordSalt(), $input['password'] ) . "',";
			}
			
			$sql = "
			UPDATE common_Users
			SET user_type_id = " . $input['user_type_id'] . ",
				username = '" . $input['username'] . "',
				email = '" . $input['email'] . "',
				" . $password_update . "
				first_name = " . $first_name . ",
				last_name = " . $last_name . ",
				bio = " . $bio . "
			WHERE user_id = " . $this->m_user_id;

			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_user_id;
			
			//update permissions
			$this->permissionsUpdate( $input, $from_add );
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
		$articles = Article::getArticlesForAuth( $this->m_user_id );
		
		if( $deactivate )
		{
			foreach( $articles as $i => $article_id )
			{
				$a = new Article( $article_id );
				$a->delete( TRUE );
			}
			
			$sql = "
			UPDATE common_Users
			SET active = 0
			WHERE user_id = " . $this->m_user_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );	
		}
		else
		{
			$sql = "
			DELETE
			FROM common_UsersToPermission
			WHERE user_id = " . $this->m_user_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			foreach( $articles as $i => $article_id )
			{
				$a = new Article( $article_id );
				$a->delete( FALSE );
			}
			
			$sql = "
			DELETE
			FROM common_Users
			WHERE user_id = " . $this->m_user_id;
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
			FROM common_UserToPermission
			WHERE user_id = " . $this->m_user_id;			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
			
		//add permissions
		foreach( $input as $k => $v )
		{
			if( strpos( $k, "permission_" ) !== FALSE )
			{
				$this->permissionAdd( $input[$k] );
			}
		}
		
		return TRUE;
		
	}//permissionsUpdate()
	
   /**
	* Adds a permission to this auth record. 
	* @return	boolean
	* @since	20101018, hafner
	* @param	int			$permission_id		pk of common_Permissions
	*/
	public function permissionAdd( $permission_id )
	{
		$sql = "
		INSERT INTO common_UserToPermission( user_id, permission_id )
		VALUES( " . $this->m_user_id . ", " . $permission_id . " )";	
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );

		return TRUE;
		
	}//permissionAdd()
	
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
		
		//check valid email
		if( !$this->m_form->m_error )
		{
			$this->m_form->m_error = $this->m_common->validateEmailAddress( $input['email'] );
		}
		
		//check user type
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "user_type_id", $input ) || 
				$input['user_type_id'] == 0 )
			{
				$this->m_form->m_error = "You select a user title.";
			}
		}
		
		//check valid user type
		if( !$this->m_form->m_error )
		{
			$sql = "
			SELECT count(*)
			FROM common_UserTypes
			WHERE user_type_id = " . $input['user_type_id'] . " AND
			active = 1";
			
			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $this->m_common->m_db->fetchRow( $result );
			
			if( $row[0] == 0 )
			{
				$this->m_form->m_error = "Error: invalid user type. This should not happen.";
			}
		}

		if( $is_addition )
		{
			//check duplicate email
			if( $is_addition )
			{
				if( !$this->m_form->m_error )
				{
					$dup_check = array( 
						'table_name' => "common_Users",
						'pk_name' => "user_id",
						'check_values' => array( 'email' => strtolower( $input['email'] ) )
					);
					
					if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
					{
						$this->m_form->m_error = "That email already exists. You must choose a unique email address.";
					}
				}
			}
			
			//check duplicate username
			if( $is_addition )
			{
				if( !$this->m_form->m_error )
				{
					$dup_check = array( 
						'table_name' => "common_Users",
						'pk_name' => "user_id",
						'check_values' => array( 'username' => strtolower( $input['username'] ) )
					);
					
					if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
					{
						$this->m_form->m_error = "That username already exists. You must choose a unique username.";
					}
				}
			}
			
		}//check duplicates if is_addition
			
		//check password input
		if( $is_addition || strlen( $input['password'] ) > 0 )
		{
			//check missing password
			if( !$this->m_form->m_error )
			{
				if( !array_key_exists( "password", $input ) || 
					strlen( trim( $input['password'] ) ) == 0 )
				{
					$this->m_form->m_error = "You must choose a password.";
				}
			}
			
			//check password match
			if( !$this->m_form->m_error )
			{
				if( $input['password'] != $input['password_copy'] )
				{
					$this->m_form->m_error = "Password do not match. Please re-type.";
				}
			}
			
			//check valid password
			if( !$this->m_form->m_error )
			{
				if( !$this->passwordValidate( $password ) )
				{
					$this->m_form->m_error = "Password is invalid. It may not contain any of the following characters: semicolons (;), single quotes ('), double quotes (\"), or spaces.";
				}
			}
		}
			
		return $this->m_form->m_error;
		
	}//checkInput()
	
	public function setLinkedObjects()
	{
		return array( 'user_type' => new UserType( $this->m_user_type_id ) );
		
	}//setLinkedObjects()
	
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
			case "get-password-form":
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
					<a href="#" id="User" process="change_password" class="no_hover">
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
				$ut = new UserType( $u->m_user_type_id );
				$user_type_title = ( strlen( $ut->m_title ) > 0 ) ? $ut->m_title : "User";
				
				//get user image
				if( !$u->m_use_gravatar )
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
				
				//shorten bio
				$truncated_bio = ( strlen( $u->m_bio ) > 75 ) ? substr( $u->m_bio, 0, 73 ) . "..." : $u->m_bio;
				
				$html = '
				<div class="padder">
					
					<div class="thumb_holder bg_color_white user_holder padder border_dark_grey">
						<img src="' . $img_src . '" />
					</div>
					
					<div class="user_holder" style="width:50%;">
						
						<div class="header color_black user_name">
							' . ucwords( $u->m_username ) . '
						</div>
						
						<div class="header_sub color_terciary">
							' . $user_type_title . '
						</div>
						
						<div id="user_bio_' . $u->m_user_id . '" class="padder_10_top color_black">
							' . $truncated_bio . '
						</div>
						
						<div id="user_delete_controls_' . $u->m_user_id . '" class="padder_10_top" style="display:none;">
							' . Common::getHtml( "get-form-buttons", array( 
			
								'left' => array( 
									'pk_name' => "user_id",
									'pk_value' => $u->m_user_id,
									'process' => "delete",
									'id' => "user",
									'button_value' => "Delete",
									'extra_style' => 'style="width:41px;"' ),
									
								'right' => array(
									'pk_name' => "user_id",
									'pk_value' => $u->m_user_id,
									'process' => "cancel_delete",
									'id' => "user",
									'button_value' => "Cancel",
									'extra_style' => 'style="width:41px;"' ),
								
								'table_style' => 'style="position:relative;width:150px;left:-5px;"'
								
								)
								 
							) . '								
						</div>

						
					</div>
						
					<div class="clear"></div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-edit-form":
			
				//user for which to display details
				$u = $vars['active_record'];
				
				//user object of the currently logged in user
				$active_user = $vars['active_user'];
				
				if( $u->m_user_id > 0 )
				{
					$process = "modify";
					$username = $u->m_username;
					$email = $u->m_email;
					
					$first_name = $u->m_first_name;
					$last_name = $u->m_last_name;
					$bio = $u->m_bio;
					
					$password_label = "Change Password:";
					
				}
				else
				{
					$process = "add";
					$username = "";
					$email = "";
					
					$first_name = "";
					$last_name = "";
					$bio = "";
					
					$password_label = "Password:";
				}
				
				$html = '
				<form id="user_add_mod_form_' . $u->m_user_id . '">
				
					<div class="padder center header color_accent">
						' . ucfirst( $process ) . ' User
					</div>
					
					<div class="padder center result" id="result_add_0">
					</div>
					
					<div class="header_sub color_terciary">
						Login Info
					</div>
					
					<div class="padder_10_left">
						<div class="selector_module_basic">
							<div class="padder">
								<span class="title_span">
									Username:
								</span>
								<input name="username" type="text" class="text_input text_extra_long" value="' . $username . '" />
							</div>
													
							<div class="padder">
								<span class="title_span">
									Email:
								</span>
								<input name="email" type="text" class="text_input text_extra_long" value="' . $email . '" />
							</div>
						</div>
						
						<div class="selector_module_basic" style="width:15px;">
							&nbsp;
						</div>
						
						<div class="selector_module_basic">
							<div class="padder">
								<span class="title_span">
									' . $password_label . '
								</span>
								<input name="password" type="password" class="text_input text_extra_long" value="" />
							</div>
													
							<div class="padder">
								<span class="title_span">
									Re-type Password:
								</span>
								<input name="password_copy" type="password" class="text_input text_extra_long" value="" />
							</div>
						</div>
						
						<div class="clear"></div>
					</div>
	
					<div class="header_sub color_terciary padder_10_top">
						Personal Info
					</div>

					<div class="padder_10_left">
						<div class="padder">
							<span class="title_span">
								First Name:
							</span>
							<input name="first_name" type="text" class="text_input text_extra_long" value="' . $first_name . '" />
						</div>
						
						<div class="padder">
							
							<span class="title_span">
								Last Name:
							</span>
							<input name="last_name" type="text" class="text_input text_extra_long" value="' . $last_name . '" />
						</div>
						
						<div class="padder">
							<span class="title_span">
								About:
							</span>
							<textarea name="bio" class="text_input text_extra_long text_area">' . $bio . '</textarea>
						</div>
						
					</div>
					';
					
				if( in_array( 'SPR', $active_user->m_permissions ) )
				{
					//user permission html
					$user_permissions = Permission::getHtml( 'get-permissions-list-readonly', array( 
						'active_user_record' => $u ) 
					);
					
					//user type html
					$user_types = UserType::getHtml( 'get-radio-selectors', array( 
						'active_record' => new UserType( $u->m_user_type_id ),
						'active_user' => $u )
					);
	
					$html .= '
					<div class="header_sub color_terciary padder_10_top">
						User Access Info
					</div>
					
					<div class="padder_10_left">
						<div class="padder padder_10_top">
							' . Common::getHtml( "selector-module", array( 
								'title' => "User Title", 
								'content' => $user_types['html'],
								'content_class' => "user_type_selector_" . $u->m_user_id . " padder_10_top",
								'container_style' => 'style="height:auto;"',
								'content_style' => 'style="text-align:left;"' ) ) . '
								
							' . Common::getHtml( "selector-module-spacer", array() ) . '
							
							' . Common::getHtml( "selector-module", array( 
								'title' => "User Permissions", 
								'content' => $user_permissions['html'],
								'content_class' => "",
								'container_style' => 'style="height:auto;"',
								'content_style' => 'style="text-align:left;"' ) ) . '
								
							<div class="clear"></div>
							
						</div>
					</div>
					';
				}
			
				$html .= '
					<div class="padder">
						
						' . Common::getHtml( "get-form-buttons", array( 
						
							'left' => array( 
								'pk_name' => "user_id",
								'pk_value' => $u->m_user_id,
								'process' => $process,
								'id' => "user",
								'button_value' => ucwords( $process ),
								'extra_style' => 'style="width:41px;"' ),
								
							'right' => array(
								'pk_name' => "user_id",
								'pk_value' => $u->m_user_id,
								'process' => "cancel_" . $process,
								'id' => "user",
								'button_value' => "Cancel",
								'extra_style' => 'style="width:41px;"' ) 
							) 
						) . '
							
					</div>

				</form>
				';
						
				$return = array( 'html' => $html );
				break;
								
			case "get-delete-form":
				$return = array( 'html' => "" );
				break;
				
			default:
				throw new Exception( "Error: Command '" . $cmd . "' is invalid." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
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
	 * Collects array of permissions for this user.
	 * Returns array of permission aliases.
	 * @return	array
	 * @since	20101020, hafner
	 */
	public function permissionsGet()
	{
		$return = FALSE;
		//$user_id = ( ( $this->m_user_id ) > 0 ) ? $this->m_user_id : 0;
		
		$sql = "
		SELECT 
			p.alias AS alias
		FROM 
			common_UserToPermission a2p
		JOIN common_Permissions p ON
			p.permission_id = a2p.permission_id  
		WHERE 
			a2p.user_id = " . $this->m_user_id;
			
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
		UPDATE common_Users 
		SET password = '" . $this->passwordEncrypt( $this->passwordSalt(), $new_pass ) . "'
		WHERE user_id = " . $this->m_user_id;
		
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
		SELECT user_id
		FROM common_Users
		WHERE user_id > 0 AND
		" . $field . " = " . $value . "
		ORDER BY username ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[$i] = new User( $row[0], FALSE );
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
	
}//class User
?>