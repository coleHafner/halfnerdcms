<?

/**
 * A class to handle the current user session.
 * @since	20101025, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );
require_once( "base/User.php" );

class Session
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
	protected $m_session_id;
	
	/**
	 * Id of the authentication record associated with this object.
	 * @var	string
	 */
	protected $m_user_id;
	
	/**
	 * Ip address of the session.
	 * @var	string
	 */
	protected $m_ip;
	
	/**
	 * Browser for current session.
	 * @var	string
	 */
	protected $m_browser;
	
	/**
	 * Initialization timestamp for the current session
	 * @var	string
	 */
	protected $m_start_timestamp;
	
	/**
	 * End timestamp for the current session.
	 * Defaults to FALSE. If value is set, this session is inactive.
	 * @var	string
	 */
	protected $m_end_timestamp;
	
	/**
	 * Array of linked objects.
	 * @var	array
	 */
	protected $m_linked_objects;
	
	/**
	 * Constructs the object.
	 * @since	20101025, hafner
	 * @return	State
	 * @param	int				$session_id			id of the current user
	 */
	public function __construct( $session_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_session_id = $session_id;
		$this->setMemberVars( $objects );
		
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20101025, hafner
	 * @return	boolean
	 */
	public function setMemberVars( $objects )
	{
		//get member vars
		$sql = "
		SELECT 
			session_id,
			user_id,
			ip,
			browser,
			start_timestamp,
			end_timestamp
		FROM 
			common_Sessions
		WHERE 
			session_id = '" . $this->m_session_id . "'";
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_session_id = $row['session_id'];
		$this->m_user_id = $row['user_id'];
		$this->m_ip = $row['ip'];
		$this->m_browser - $row['browser'];
		$this->m_start_timestamp = $row['start_timestamp'];
		$this->m_end_timestamp = ( !is_null( $row['end_timestamp'] ) ) ? $row['end_timestamp'] : FALSE;
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects() : array(); 
		
		return TRUE;
		
	}//setMemberVars()
	
	/**
	* Get an array of data suitable to use in modify
	* @since 	20101025, hafner
	* @return 	array
	* @param 	boolean 		$fix_clob		whether or not to file member variables of CLOB type
	*/
	public function getDataArray( $fix_clob = TRUE ) 
	{
		return array(
			'session_id' => $this->m_session_id,
			'user_id' => $this->m_user_id,
			'ip' => $this->m_ip,
			'browser' => $this->m_browser,
			'start_timestamp' => $this->m_username,
			'end_timestamp' => $this->m_password
		);
		
	}//getDataArray()
	
	/**
	* Save with the current values of the instance variables
	* This is a wrapper to modify() to ease some methods of coding
	* @since 	20101025, hafner
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
	 * @since	20101025,hafner
	 * @return	mixed
	 * @param	array				$input				array of input data
	 */
	public function add( $input )
	{
		$this->checkInput( $input, TRUE );
		
		if( !$this->m_form->m_error )
		{
			//get unique sid
			$sid = $this->getUniqueSessionId();
			
			//insert blank record
			$sql = "
			INSERT INTO common_Sessions( session_id, user_id, start_timestamp, ip )
			VALUES( '" . $sid . "', 0, " . strtotime( "now" ) . ", '" . $_SERVER['REMOTE_ADDR'] . "' )";
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$this->m_session_id = $sid;
			$return = $this->m_session_id;
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
	 * @since	20101025, hafner
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
			UPDATE common_Sessions
			SET user_id = " . $input['user_id'] . ",
			ip = '" . $_SERVER['REMOTE_ADDR'] . "',
			browser = '" . $_SERVER['HTTP_USER_AGENT'] . "'
			WHERE session_id = '" . $this->m_session_id . "'";
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_session_id;
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
	 * @since	20101025, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data 
	 */
	public function delete( $deactivate = TRUE )
	{
		if( $deactivate )
		{
			$sql = "
			UPDATE common_Sessions
			SET end_timestamp = " . strtotime( "now" ) . "
			WHERE session_id = '" . $this->m_session_id . "'";	
		}
		else
		{
			$sql = "
			DELETE
			FROM common_Sessions
			WHERE session_id = '" . $this->m_session_id . "'";
		}
		
		//run query
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//delete()
	
	
	/**
	 * Validates the form input for creating/modifying a new File record.
	 * Returns FALSE on success, error message string otherwise.
	 * @since	20101025, hafner
	 * @return	mixed
	 * @param	array			$input			array of data
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function checkInput( $input, $is_addition )
	{
		//check valid auth_id
		if( !$this->m_form->m_error )
		{
			$cons = array( 
				'table_name' => "common_Users",
				'pk_name' => "user_id",
				'pk_value' => $input['user_id']
			);
			
			if( !$this->m_common->m_db->keyExists( $cons ) )
			{
				$this->m_form->m_error = "Error: Invalid user id '" . $input['user_id'] . "'. This should not happen. . . ever.";
			}
			
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	public function setLinkedObjects()
	{
		return array( 'user' => new User( $this->m_user_id, TRUE ) );
		
	}//setLinkedObjects()
	
	/**
	* Returns HTML
	* @author	20100908, Hafner
	* @return	array
	* @param	string		$cmd		determines which HTML snippet to return
	* @param	array		$vars		array of variables for the html
	*/
	public function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( $cmd ) )
		{
			default:
				throw new Exception( "Error: Command '" . $cmd . "' is invalid." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	/**
	 * Validates the current session.
	 * Returns session_id ( int ) if session is valid, FALSE otherwise.
	 * @since	20100922, Hafner
	 * @return 	mixed
	 */
	public static function validateSessionId( $sid )
	{
		$return = FALSE;
		$common = new Common();
		
		if( strlen( $sid ) > 0 )
		{
			//get auth id
			$sql = "
			SELECT session_id
			FROM common_Sessions
			WHERE session_id = '" . $sid . "' AND
			TRIM( ip ) = '" . trim( $_SERVER['REMOTE_ADDR'] ) . "' AND
			LOWER( TRIM( browser ) ) = '" . strtolower( trim( $_SERVER['HTTP_USER_AGENT'] ) ) . "' AND
			end_timestamp IS NULL";
			
			$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
			
			if( $common->m_db->numRows( $result ) > 0 )
			{
				$return = TRUE;
			}
		}
		
		return $return;
		
	}//validateSessionId()
	
	/**
	* Gets a unique session id.
	* @since 20101025, hafner
	* @return	string
	*/
	public function getUniqueSessionId()
	{
		$id_is_unique = FALSE;
		
		while( !$id_is_unique )
		{
			$sid = md5( uniqid( microtime() ) . $_SERVER['HTTP_USER_AGENT'] );
			
			$sql = "
			SELECT count(*)
			FROM common_Sessions
			WHERE session_id = '" . $sid . "'";
			
			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $this->m_common->m_db->fetchRow( $result );
			  
			if( $row[0] == 0 )
			{
				$id_is_unique = TRUE;
			}
		}
		
		return $sid;
			
	}//getUniqueSessionId()
	
	public static function createSession( $username )
	{
		$common = new Common();
		
		//get auth id ( it has already been validated @ validate_login, so no need for the password )
		$sql = "
		SELECT user_id
		FROM common_Users
		WHERE ( LOWER( username ) = '" . strtolower( $username ) . "' OR
		LOWER( email ) = '" . strtolower( $username  ) . "' ) AND
		active = 1";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $common->m_db->fetchAssoc( $result );
		$user_id = $row['user_id'];
		
		$s = new Session( 0 );
		$sid = $s->add( array( 'user_id' => $user_id ) );
		$s->setMemberVars( FALSE );
		
		return $s->m_session_id;
		
	}//createSession()
	
   /**
	* Get a member variable's value
	* @author	Version 20101025, hafner
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
	* @since	20101025, hafner
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
	
}//class Session
?>