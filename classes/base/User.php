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
require_once( "base/FileHandler.php" );
require_once( "base/FormHandler.php" );
require_once( "base/Permission.php" );
require_once( "base/Authentication.php" );

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
		$this->m_user_id = ( is_numeric( $user_id ) && self::userExists( $user_id ) ) ? $user_id : 0;
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
			thumb_id,
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
		$this->m_thumb_id = $row['thumb_id'];
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
			$bio = ( array_key_exists( "bio", $input ) && strlen( $input['bio'] ) > 0 ) ? "'" . addslashes( $input['bio'] ) . "'" : "NULL";
			$last_name = ( array_key_exists( "last_name", $input ) && strlen( $input['last_name'] ) > 0 ) ? "'" . addslashes( $input['last_name'] ) . "'" : "NULL";
			$first_name = ( array_key_exists( "first_name", $input ) && strlen( $input['first_name'] ) > 0 ) ? "'" . addslashes( $input['first_name'] ) . "'" : "NULL";
			$passwd = ( array_key_exists( "first_name", $input ) && strlen( $input['password'] ) > 0 ) ?  "password = '" .  $this->passwordEncrypt( $this->passwordSalt(), $input['password'] ) . "'," : "";
			
			$sql = "
			UPDATE 
				common_Users
			SET " . $passwd . "
				user_type_id = " . $input['user_type_id'] . ",
				username = '" . $input['username'] . "',
				email = '" . $input['email'] . "',
				first_name = " . $first_name . ",
				last_name = " . $last_name . ",
				bio = " . $bio . "
			WHERE 
				user_id = " . $this->m_user_id;
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_user_id;
			
			//update permissions
			$this->permissionsUpdate( $input );
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
		$articles = Article::getArticles( "user_id", $this->m_user_id );
		
		if( $deactivate )
		{
			foreach( $articles as $i => $a )
			{
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
	
	function updatePhoto( $post, $files )
	{
		if( !array_key_exists( "file_to_upload", $files ) || 
			count( $files['file_to_upload'] ) == 0 )
		{
			$use_gravatar = ( array_key_exists( "use_gravatar", $post ) ) ? "1" : "0";
			
			$sql ="
			UPDATE common_Users
			SET use_gravatar = " . $use_gravatar . "
			WHERE user_id = " . $this->m_user_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			//upload file
			$file = new File( 0 );
			$file_id = $file->doFileUpload( $post, $files );
			
			if( $file_id > 0 )
			{
				$sql = "
				UPDATE common_Users
				SET thumb_id = " . $file_id . ",
					use_gravatar = 0
				WHERE user_id = " . $this->m_user_id;
				
				$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			}
		}
		
		return TRUE;
		
	}//updatePhoto()
	
	/**
	 * Collects array of permissions for this user.
	 * Returns array of permission aliases.
	 * @return	array
	 * @since	20101020, hafner
	 */
	public function permissionsGet()
	{
		$return = FALSE;
		
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
				$return[] = strtolower( $row['alias'] );
			}
		}
		
		return $return;
		
	}//permissionsGet()
	
	public function permissionsUpdate( $input )
	{
		$permission_update_attempted = FALSE;
		
		//determine if permission update was attempted
		foreach( $input as $k => $v )
		{
			if( strpos( $k, "permission_" ) !== FALSE )
			{
				$permission_update_attempted = TRUE;
			}
		}
		
		if( $permission_update_attempted )
		{
			//delete all permissions for this user
			$sql = "
			DELETE
			FROM common_UserToPermission
			WHERE user_id = " . $this->m_user_id;			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
				
			//add permissions
			foreach( $input as $k => $v )
			{
				if( strpos( $k, "permission_" ) !== FALSE )
				{
					$this->permissionAdd( $input[$k] );
				}
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
	
	public function permissionUserHasAny( $permissions )
	{
		$return = FALSE; 
		
		if( in_array( "spr", $this->m_permissions ) )
		{
			$return = TRUE;
		}
		else
		{
			if( is_array( $permissions ) )
			{
				foreach( $permissions as $permissions )
				{
					if( in_array( strtolower( $permission ), $this->m_permissions  ) )
					{
						$return = TRUE;
						break;
					}
				}
			}
		}
		
		return $return;
		
	}//permissionUserHasAny()
	
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
	
	public function getDefaultFormVars( &$u = FALSE )
	{
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
		
		return array(
			'process' => $process,
			'username' => $username,
			'email' => $email,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'bio' => $bio,
			'password_label' => $password_label
		);
		
	}//getDefaultFormVars()
	
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
				
			case "get-view-form-full":
			
				$u = $vars['active_record'];
				$ut = new UserType( $u->m_user_type_id );
				$user_type_title = ( strlen( $ut->m_title ) > 0 ) ? $ut->m_title : "User";
				
				$img_src_html = self::getHtml( "get-user-image-url", array( 'active_record' => &$u ) );
				$img_src = $img_src_html['html'];
				
				if( strlen( $u->m_first_name ) > 0 ||
					strlen( $u->m_last_name ) > 0 )
				{
					$title = '
					<span class="header_mega color_orange">
						' . ucfirst( $u->m_first_name ) . ' ' . ucfirst( $u->m_last_name ) . '
					</span>
					
					<span class="header color_accent">
						' . $u->m_username . '
					</span>
					';
				}
				else
				{
					$title = '
					<span class="header_mega color_orange">
						' . $u->m_username . '
					</span>
					';	
				}
				
				$html = '
				<div style="position:relative;width:70%;float:left;">
					<div class="padder">
						<table>
							<tr>
								<td>
									<div class="thumb_holder bg_color_white padder border_dark_grey">
										<img src="' . $img_src . '" />
									</div>
								</td>
								<td valign="top">
									<div class=" padder_10_top">
										' . $title . '
									</div>
									
									<div class="header_sub color_terciary padder_left">
										' . $user_type_title . '
									</div>
								</td>
							</tr>
						</table>
						
						<div class="padder_20_top">
							<div class="bg_color_white color_black rounded_corners border_dark_grey" style="min-height:250px;">
								<div class="padder_10">
									' . $u->m_bio . '
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div style="position:relative;width:30%;float:left;">
					<div class="padder">
						<div class="border_bottom_dotted border_color_orange">
							<div class="padder_10_left color_accent">
								User Data 1
							</div>
						</div>
						<div class="padder_10_left" style="min-height:150px;">
							&nbsp;
						</div>
						
						<div class="border_bottom_dotted border_color_orange">
							<div class="padder_10_left color_accent">
								User Data 2
							</div>
						</div>
					</div>
				</div>
				<div class="clear"></div>
				';
				
				$return = array( 'html' => $html );
				break;
					
			case "get-view-form-badge":
				
				$u = $vars['active_record'];
				$ut = new UserType( $u->m_user_type_id );
				$user_type_title = ( strlen( $ut->m_title ) > 0 ) ? $ut->m_title : "User";
				
				$img_src = self::getHtml( "get-user-image-url", array( 'active_record' => &$u ) );
				$hover_enabled = ( array_key_exists( "hover_enabled", $vars['options'] ) && $vars['options']['hover_enabled'] === TRUE ) ? "1" : "0";
				$show_controls = ( array_key_exists( "show_controls", $vars['options'] ) && $vars['options']['show_controls'] === FALSE ) ? FALSE : TRUE;
				
				//shorten bio
				$truncated_bio = ( strlen( $u->m_bio ) > 75 ) ? substr( $u->m_bio, 0, 73 ) . "..." : $u->m_bio;
				
				$html = '
				<div class="item_container bg_color_light_tan border_dark_tan" hover_enabled="' . $hover_enabled . '" style="margin-top:0px;">
							
					<div class="padder">
						
						<div class="thumb_holder bg_color_white user_holder padder border_dark_grey">
							<img src="' . $img_src['html'] . '" />
						</div>
						
						<div class="user_holder" style="width:50%;">
							
							<div class="header color_black user_name">
								<a href="' . $common->makeLink( array( 'v' => "users", 'sub' => $u->m_username ) ) . '">
									' . ucwords( $u->m_username ) . '
								</a>
							</div>
							
							<div class="header_sub color_terciary">
								' . $user_type_title . '
							</div>
							
							<div id="item_view_' . $u->m_user_id . '" class="padder_10_top color_black">
								' . $truncated_bio . '
							</div>
							';
				
					if( $show_controls )
					{
						$html .= '
						<div id="item_delete_' . $u->m_user_id . '" class="padder_10_top" style="display:none;">
							' . Common::getHtml( "get-form-buttons", array( 
			
								'left' => array( 
									'pk_name' => "user_id",
									'pk_value' => $u->m_user_id,
									'process' => "delete",
									'id' => "user",
									'button_value' => "Delete",
									'extra_style' => 'style="width:41px;"' ),
									
								'right' => array(
									'pk_name' => "item_id",
									'pk_value' => $u->m_user_id,
									'process' => "view",
									'id' => "list_item",
									'button_value' => "Cancel",
									'extra_style' => 'style="width:41px;"' ),
								
								'table_style' => 'style="position:relative;width:150px;left:-5px;"'
								
								)
								 
							) . '								
						</div>
						';
					}

					$html .= '	
					</div>
						
					<div class="clear"></div>
					';
				
				if( $show_controls )
				{
					$common = new Common();
					$buttons = Common::getHtml( "get-admin-item-buttons", array( 'item_id' => $u->m_user_id ) );
					
					$html .= '
					<div class="title_button_container" id="item_control" style="display:none;">
						' . Common::getHtml( "get-button-round", array(
								'href' => $common->makeLink( array( 'v' => "account", 'sub' => $u->m_username ) ), 
								'button_value' => "m",
								'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
								'link_style' => 'style="float:right;"' ) ) . '
							
						' . Common::getHtml( "get-button-round", array(
								'id' => "list_item",
								'process' => "delete",
								'pk_name' => "item_id",
								'pk_value' => $u->m_user_id,
								'button_value' => "x",
								'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
								'link_style' => 'style="float:right;"' ) ) . '
					</div>
					';
				}
				
				$html .= '
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-edit-form":
			
				//user for which to display details
				$u = $vars['active_record'];
				
				//user currently logged in
				$active_user = new User( Authentication::getLoginUserId() );
				
				//get default vars
				$form_vars = $u->getDefaultFormVars();
				
				//get login form
				$login_form = self::getHtml( "get-login-form", array( 
					'render_form_tag' => FALSE,
					'active_record' => &$u ) 
				);
				
				//get contact form
				$contact_form = self::getHtml( "get-contact-form", array( 
					'render_form_tag' => FALSE,
					'active_record' => &$u ) 
				);
				
				//get permissions form
				$permission_form = self::getHtml( "get-permission-form", array( 
					'render_form_tag' => FALSE,
					'active_record' => &$u ) 
				);
				
				if( $u->m_user_id == 0 ||
					$u->m_user_id == $active_user->m_user_id )
				{
					$html = '
					<form id="user_add_mod_form_' . $u->m_user_id . '">
						<div class="padder center header color_accent">
							' . ucfirst( $form_vars['process'] ) . ' User
						</div>
						
						<div class="padder center result" id="result_' . $form_vars['process'] . '_' . $u->m_user_id . '">
						</div>
						
						<div class="header_sub color_terciary">
							Login Info
						</div>
						
						' . $login_form['html'] . '
						
						<div class="header_sub color_terciary padder_10_top">
							Personal Info
						</div>
						
						' . $contact_form['html'];
						
					if( $active_user->permissionUserHasAny( array( 'usr' ) ) )
					{
						$html .= '
						<div class="header_sub color_terciary padder_10_top">
							User Access Info
						</div>
						
						' . $permission_form['html'];
					}
					else
					{
						$reg_user_id = $common->m_db->getIdFromTitle( "regular user", array(
							'table' => "common_UserTypes",
							'pk_name' => "user_type_id",
							'title_field' => "title" )
						);
						
						$html .= '
						<input type="hidden" name="user_type_id" value="' . $reg_user_id . '" />
						';
					}
					
					$html .= '	
						<div class="padder_10_top"> ' .
							Common::getHtml( "get-form-buttons", array( 
								
								'left' => array( 
									'pk_name' => "user_id",
									'pk_value' => $u->m_user_id,
									'process' => $form_vars['process'],
									'id' => "user",
									'button_value' => "Save",
									'extra_style' => 'style="width:41px;"' ),
									
								'right' => array(
									'pk_name' => "user_id",
									'pk_value' => $u->m_user_id,
									'process' => "cancel_" . $form_vars['process'],
									'id' => "user",
									'button_value' => "Cancel",
									'extra_style' => 'style="width:41px;"' ), 
								)
								 
							) . '
						</div>
					</form>
					';
				}
				else
				{
					$html = '
					<div class="padder_10 center font_no">
						You are not authorized to edit this user.
					</div>
					';	
				}
						
				$return = array( 'html' => $html );
				break;
				
			case "get-login-form":
				
				//set user
				$u = $vars['active_record'];
				
				//get default vars
				$form_vars = $u->getDefaultFormVars( &$u );
				
				//form tags
				if( array_key_exists( "render_form_tag", $vars ) &&
					$vars['render_form_tag'] )
				{
					$form = self::getHtml( "get-form-open-close", array(
						'active_record' => &$u,
						'title' => "My Login Info",
						'hidden_fields' => '
						<input type="hidden" name="user_type_id" value="' . $u->m_user_type_id . '" />
						<input type="hidden" name="first_name" value="' . $u->m_first_name . '" />
						<input type="hidden" name="last_name" value="' . $u->m_last_name . '" />
						<input type="hidden" name="bio" value="' . $u->m_bio . '" />' ) 
					);
				}
				
				$html = 
				$form['form_open'] . '
					<div class="selector_module_basic">
						<div class="padder">
							<span class="title_span">
								Username:
							</span>
							<input name="username" type="text" class="text_input text_extra_long" value="' . $form_vars['username'] . '" />
						</div>
												
						<div class="padder">
							<span class="title_span">
								Email:
							</span>
							<input name="email" type="text" class="text_input text_extra_long" value="' . $form_vars['email'] . '" />
						</div>
					</div>
					
					<div class="selector_module_basic" style="width:4%;">
						&nbsp;
					</div>
					
					<div class="selector_module_basic">
						<div class="padder">
							<span class="title_span">
								' . $form_vars['password_label'] . '
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
				' . 
				$form['form_close'];
				
				$return = array( 'html' => $html );
				break;
				
			case "get-contact-form":
				
				//set user
				$u = $vars['active_record'];
				
				//get default vars
				$form_vars = $u->getDefaultFormVars( &$u );
				
				//form tags
				if( array_key_exists( "render_form_tag", $vars ) &&
					$vars['render_form_tag'] )
				{
					$form = self::getHtml( "get-form-open-close", array(
						'active_record' => &$u,
						'title' => "My Contact Info",
						'hidden_fields' => '
							<input type="hidden" name="username" value="' . $u->m_username . '" />
							<input type="hidden" name="email" value="' . $u->m_email . '" />
							<input type="hidden" name="user_type_id" value="' . $u->m_user_type_id . '" />' ) 
					);
				}
				
				$html = 
				$form['form_open'] . '
					<div class="padder">
						<span class="title_span">
							First Name:
						</span>
						
						<input name="first_name" type="text" class="text_input text_extra_long" value="' . $form_vars['first_name'] . '" />
					</div>
					
					<div class="padder">
						
						<span class="title_span">
							Last Name:
						</span>
						
						<input name="last_name" type="text" class="text_input text_extra_long" value="' . $form_vars['last_name'] . '" />
					</div>
					
					<div class="padder">
						<span class="title_span">
							About:
						</span>
						
						<div class="padder_10_right">
							<textarea name="bio" class="text_input text_extra_long text_area">' . $form_vars['bio'] . '</textarea>
						</div>
					</div>' . 
				$form['form_close'];
				
				$return = array( 'html' => $html );
				break;

			case "get-permission-form":
				
				//set user
				$u = $vars['active_record'];
				
				//user currently logged in
				$active_user = new User( Authentication::getLoginUserId() );
				
				//form tags
				if( array_key_exists( "render_form_tag", $vars ) &&
					$vars['render_form_tag'] )
				{
					$form = self::getHtml( "get-form-open-close", array(
						'active_record' => &$u,
						'title' => "My Permissions Info",
						'hidden_fields' => '
							<input type="hidden" name="username" value="' . $u->m_username . '" />
							<input type="hidden" name="email" value="' . $u->m_email . '" />
							<input type="hidden" name="first_name" value="' . $u->m_first_name . '" />
							<input type="hidden" name="last_name" value="' . $u->m_last_name . '" />
							<input type="hidden" name="bio" value="' . $u->m_bio . '" />' ) 
					);
				}
				
				if( $active_user->permissionUserHasAny( array( 'usr' ) ) &&
					$u->m_user_id != $active_user->m_user_id )
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
				
					$html = 
					$form['form_open'] . '
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
						</div>' .
					$form['form_close'];
				}
				else 
				{
					$html = '
					<div class="padder_10 font_no center">
						You are not authorized to edit permissions for this user.
					</div>
					';
				}
				
				$return = array( 'html' => $html );
				break;
				
			case "get-photo-form":
				
				//set user
				$u = $vars['active_record'];
				$unique_file_name = File::getUniqueFileName();
				$checked = ( $u->m_use_gravatar ) ? 'checked="checked"' : "";
				$img_src = self::getHtml( "get-user-image-url", array( 'active_record' => &$u ) );
				
				$file_type_id = $common->m_db->getIdFromTitle( "user image", array(
					'table' => "common_FileTypes",
					'pk_name' => "file_type_id",
					'title_field' => "title" )
				);
				
				$html = '
				<div class="padder center header color_accent">
					Update My Photo
				</div>
				
				<table style="position:relative;">
					<tr>
						<td>
							<div class="thumb_holder bg_color_white user_holder padder border_dark_grey">
								<img src="' . $img_src['html'] . '" />
							</div>
						</td>
						<td>
							<form 	method="post" 
									target="hidden_frame"
									enctype="multipart/form-data"
									id="user_image_upload_form" 
									action="/ajax/halfnerd_helper.php?task=user&process=update_photo&user_id=' . $u->m_user_id . '">
								
								<div class="padder_10">
									<input id="user_photo_file" class="user_toggle_photo_options" active_option="file" type="file" name="file_to_upload" />
								</div>
								
								<div class="padder" style="padding-left:50px;" >
									---- OR ----
								</div>
								
								<div class="padder">
									<input id="user_photo_gravatar" class="user_toggle_photo_options" active_option="gravatar" type="checkbox" ' . $checked . ' name="use_gravatar" /> Use Gravatar For Image
								</div>
								
								<input type="hidden" name="file_type_id" id="file_type_id" value="' . $file_type_id . '" />
								<input type="hidden" name="unique_file_name" id="unique_file_name" value="' . $unique_file_name . '" />	
								
							</form>	
						</td>
					</tr>
				</table>
				
				<div class="padder_10">' .	
					Common::getHtml( "get-form-buttons", array( 
						
						'left' => array( 
							'pk_name' => "user_id",
							'pk_value' => $u->m_user_id,
							'process' => "update_photo",
							'id' => "user",
							'button_value' => "Save",
							'extra_style' => 'style="width:41px;"' ),
							
						'right' => array(
							'href' => $common->makeLink( array(
								'v' => "users",
								'sub' => $u->m_username ) ),
							'button_value' => "Cancel",
							'extra_style' => 'style="width:41px;"' ),
						 
						'table_style' => 'style="position:relative;margin:auto auto auto 95px;"' ) 
					) . '
				</div>
				<div class="bg_color_light_tan loader_div loading" id="file_loader"></div>
				';
					
				$return = array( 'html' => $html );
				break;
				
			case "get-delete-form":
				$return = array( 'html' => "" );
				break;
				
			case "get-user-submit-buttons":
				
				//set user
				$u = $vars['active_record'];
				
				//get default vars
				$form_vars = $u->getDefaultFormVars( &$u );
				
				$html = '
				<div class="padder_10_top"> ' .
					Common::getHtml( "get-form-buttons", array( 
						
						'left' => array( 
							'pk_name' => "user_id",
							'pk_value' => $u->m_user_id,
							'process' => $form_vars['process'],
							'id' => "user",
							'button_value' => "Save",
							'extra_style' => 'style="width:41px;"' ),
							
						'right' => array(
							'href' => $common->makeLink( array(
								'v' => "users",
								'sub' => $u->m_username ) ),
							'button_value' => "Cancel",
							'extra_style' => 'style="width:41px;"' ) 
						)
						 
					) . '
				</div>
				';
					
				$return = array( 'html' => $html );
				break;
				
			case "get-form-open-close":
				
				//active user
				$u = $vars['active_record'];
				
				//determine hidden fields
				$hidden_fields = ( array_key_exists( "hidden_fields", $vars )  ) ? $vars['hidden_fields'] : "";
				
				//get form submit buttons
				$submit_buttons_html = self::getHtml( "get-user-submit-buttons", array(
					'active_record' => &$u ) 
				);
					
				$form_open = '
				<form id="user_add_mod_form_' . $u->m_user_id . '">
					<div class="padder center header color_accent">
						' . $vars['title'] . '
					</div>
				';
					$form_close = 
					$submit_buttons_html['html'] . '
					' . $hidden_fields . '
				</form>
				';
					
				$return = array( 'form_open' => $form_open, 'form_close' => $form_close );
				break;
				
			case "get-user-image-url":
				
				$u = $vars['active_record'];
				
				//get user image
				if( !$u->m_use_gravatar )
				{
					if( $u->m_thumb_id > 0 )
					{
						$thumb_id = $u->m_thumb_id;	
					}
					else 
					{
						$thumb_id = $common->m_db->getIdFromTitle( "default.jpg", array( 
							'pk_name' => "file_id", 
							'title_field' => "file_name", 
							'table' => "common_Files" ) 
						);
					}
					
					$thumb = new File( $thumb_id );
					$html = $thumb->m_relative_path . "/" . $thumb->m_file_name ;
					 
				}
				else
				{
					$html = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $u->m_email ) ) ) . '&s=80';
				}
				
				$return = array( 'html' => $html );
				break;
				
			case "get-user-grid":
				
				$users = $vars['records'];
				$items_per_row = 2;
				$num_items = count( $users );
				
				$num_rows = ceil( $num_items / $items_per_row );
				$active_user = new User( Authentication::getLoginUserId() );
				$container_style = ( array_key_exists( "container_style", $vars ) ) ? $vars['container_style'] : "";
				
				$empty_message = ( array_key_exists( "empty_message" , $vars ) ) ? $vars['empty_message'] : "There are 0 users... how are you logged in?";
				
				$html = '
				<table class="user_grid">
				';
				
				if( $num_items > 0 )
				{
				
					for( $i = 0; $i < $num_rows; $i++ )
					{
						$html .= '
					<tr>			
								';
							
						for( $j = 1; $j <= $items_per_row; $j++ )
						{
						
							$key = $j + ( $items_per_row * $i );
							
							if( $key > $num_items )
							{
								//add empty cell
								$html .= '
						<td>
							&nbsp;
						</td>
						';
								break;
							}
							
							$item = $users[$key];
							$view_form = User::getHtml( "get-view-form-badge", array( 
								'active_record' => $item,
								'active_user' => $active_user,
								'show_controls' => $vars['show_controls'] ) 
							);
							
							$html .= '
						<td valign="top">
							<div id="user_info_' . $item->m_user_id . '" class="' . $vars['container_class'] . ' bg_color_light_tan border_dark_grey" ' . $container_style . ' hover_enabled="' . $vars['hover_enabled'] . '">
								' . $view_form['html'];
							
							if( $vars['show_controls'] )
							{
								$html .= '
								<div class="title_button_container" id="item_control" style="display:none;width:100px;height:40px;">
									' . Common::getHtml( "get-button-round", array(
										'href' => $common->makeLink( array(
											'v' => "account",
											'sub' => $item->m_username, 
											'id1' => "update-contact" ) ),
										'button_value' => "m",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"') 
									) . '
									' . Common::getHtml( "get-button-round", array(
										'id' => "user",
										'process' => "show_delete",
										'pk_name' => "user_id",
										'pk_value' => $item->m_user_id,
										'button_value' => "x",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"') 
									) . '
								</div>
								';
							}
							
							$html .= '
							</div>
						</td>
						';
						
						}
						
						$html .= '
					</tr>
					';	
								
					}		
				}
				else
				{
					$html .= '
					<tr>
						<td class="center" colspan="2">
							' . $empty_message . '
						</td>
					</tr>
					';
				}
				
				$html .= '
				</table>
				';
				
				$return = array( 'html' => $html );
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
	
	public function passwordValidateChange( $post )
	{
		$return = FALSE;
		
		if( strlen( $post['cur_pass'] ) == 0 || 
			strlen( $post['new_pass'] ) == 0 ||
			strlen( $post['new_pass_copy'] ) == 0 )
		{
			$return = "You must fill in all fields.";	
		}
		
		if( !$return )
		{
			if( !$this->passwordCompare( $post['cur_pass'], $this->m_password ) )
			{
				$return = "Invalid current password.";	
			}
		}
		
		if( !$return )
		{
			if( $post['new_pass'] != $post['new_pass_copy'] )
			{
				$return = "New passwords do not match";
			}
		}
		
		if( !$return )
		{
			if( strlen( $post['new_pass'] ) < 7 )
			{
				$return = "New password must be at least 7 characters.";
			}
		}
		
		if( !$return )
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
	
	public static function getUserSearchResults( $search_term )
	{
		$i = 1;
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT 
			user_id
		FROM 
			common_Users
		WHERE 
			user_id > 0 AND
			active = 1 AND
			LOWER( username ) LIKE '%" . strtolower( trim( $search_term ) ) . "%'
		ORDER BY 
			username ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[$i] = new User( $row[0], FALSE );
			$i++;
		}
		
		return $return;
		
	}//getUserSearchResults()
	
	public static function userExists( $user_id )
	{
		$return = FALSE;
		$common = new Common();
		
		//check user id exists
		$sql = "
		SELECT count(*)
		FROM common_Users
		WHERE user_id = " . $user_id;
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $common->m_db->fetchRow( $result );
		
		if( $row[0] == 1 )
		{
			$return = TRUE;
		}
		
		return $return;
		
	}//userExists()
	
	public static function usernameExists( $username )
	{
		$return = FALSE;
		$common = new Common();
		
		if( strlen( $username ) > 0 )
		{
			$sql = "
			SELECT count(*)
			FROM common_Users
			WHERE LOWER( TRIM( username ) ) = '" . strtolower( trim( $username ) ) . "' AND
			active = 1";
			
			$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $common->m_db->fetchRow( $result );
			
			if( $row[0] == 1 )
			{
				$return = TRUE;
			}
		}
		
		return $return;
		
	}//usernameExists()
	
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