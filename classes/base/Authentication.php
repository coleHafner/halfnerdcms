<?
/**
 * A class to handle the site authentication.
 * @since	20100911, Hafner
 */

require_once( "base/Article.php" );
require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );
require_once( "base/Session.php" );
require_once( "base/File.php" );
require_once( "base/User.php" );

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
	 * @param	boolean		$objects		whether or not to populate linked objects.
	 */
	public function __construct( $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
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
		if( $objects )
		{
			$this->m_linked_objects = $this->setLinkedObjects();
		}
	}//setMemberVars()

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
	
	public function setLinkedObjects()
	{
		return array();
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
			case "get-login-form":
				
				$html = '
					<div class="padder rounded_corners login_form_container bg_color_light_tan border_dark_grey">
						<form id="auth_login_form">
							
							<div class="center header">
								Login to manage the site\'s content.
							</div>
							
							<div id="result_login_attempt" class="result"></div>
							
							<div class="center bottom_spacer">
								<input id="auth_auto_login" type="text" class="input text_input text_long font_normal input_clear" name="username" value="Username or Email" clear_if="Username or Email" />
							</div>
							
							<div class="center bottom_spacer">
								<input id="auth_auto_login" type="password" class="input text_input text_long font_normal input_clear" name="password" value="passwd" clear_if="passwd" />
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
					';
				
				$return = array( 'html' => $html );
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
				
			case "get-login-string":
				
				$admin_link = $common->makeLink( array( 'v' => 'admin' ) );
				$user = $vars['user'];
				
				$html = '
				<div class="login_string_container bg_color_white font_small border_dark_grey">
					<div>
						Howdy, ' . ucwords( $user->m_username ) . '
						&nbsp;|&nbsp
						<a href="' . $common->makeLink( array(  'v' => "account", 'sub' => "update-contact" ) ) . '">
							My Account
						</a>
						&nbsp;|&nbsp
						<a href="#" id="authentication" process="kill_login">Logout</a>
					</div>
				</div>
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
			SELECT user_id, password
			FROM common_Users
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
		$show_page_content = FALSE;
		
		//if page requires login
		if( $controller->getAuthStatus() )
		{
			if( $this->validateCurrentLogin() )	
			{
				//if we have a current login, set the user object.
				if( User::userExists( self::getLoginUserId() ) )
				{
					$controller->setUser( self::getLoginUserId() );
					$show_page_content = TRUE;
				}
			}
		}
		else 
		{
			$show_page_content = TRUE;
		}
		
		if( $show_page_content )
		{
			//if we have a current login, set the user object.
			$controller->setUser( self::getLoginUserId() );
			
			$controller->setContent();
			$return = $controller->getContent();
		}
		else
		{
			$html = self::getHtml( "get-login-form", array() );
			$return = $html['html'];
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
	 * Validates the current session.
	 * Returns authentication_id ( int ) if session is valid, FALSE otherwise.
	 * @since	20100922, Hafner
	 * @return 	mixed
	 */
	public static function getLoginUserId()
	{
		$return = 0;
		$common = new Common();
		
		//get auth id
		$sql = "
		SELECT user_id
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
		
	}//getLoginUserId()
	
	public static function passwordEncrypt( $salt, $plain_text_password )
	{
		return $salt . ( hash( 'whirlpool', $salt . $plain_text_password ) );	
	}//passwordEncrypt()
	
	public static function passwordSalt()
	{
		return substr( str_pad( hexdec( mt_rand() ), 8, '0', STR_PAD_LEFT ), 0, 8 );
	}//passwordSalt()
	
	public static function passwordCompare( $plain_text_password, $encrypted_password )
	{
		$salt = substr( $encrypted_password, 0, 8 );
		$plain_encrypted = self::passwordEncrypt( $salt, $plain_text_password );

		return $encrypted_password == $plain_encrypted;
		
	}//passwordCompare()
	
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
		$exclusions = array( 'm_authentication_id' );

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
	
}//class Authentication

?>