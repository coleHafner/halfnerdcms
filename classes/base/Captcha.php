<?
/**
 * A class to handle a Captcha record.
 * @since	20100728, hafner
 */

require_once( "base/File.php" );
require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );

class Captcha
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
	 * PK of the File Record.
	 * @var	int
	 */
	protected $m_captcha_id;
	
	/**
	 * Id of the file related to this record.
	 * @var	int
	 */
	protected $m_file_id;
	
	/**
	 * Captcha String
	 * @var	string
	 */
	protected $m_captcha_string;
	
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
	 * @since	20100728, hafner
	 * @return	State
	 * @param	int				$captcha_id			id of the current record
	 */
	public function __construct( $captcha_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->setMemberVars( $captcha_id, $objects );
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20100728, hafner
	 * @return	boolean
	 */
	public function setMemberVars( $captcha_id, $objects )
	{
		$chosen_id = ( is_numeric( $captcha_id ) ) ? $captcha_id : 0;
		
		//get member vars
		$sql = "
		SELECT 
			captcha_id,
			file_id,
			captcha_string,
			active
		FROM 
			common_Captcha
		WHERE 
			captcha_id = " . $chosen_id;
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_captcha_id = $row['captcha_id'];
		$this->m_file_id = $row['file_id'];
		$this->m_captcha_string = $row['captcha_string'];
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects() : array();
		
		return TRUE;
		
	}//setMemberVars()
	
	/**
	* Get an array of data suitable to use in modify
	* @since 	20100728, hafner
	* @return 	array
	* @param 	boolean 		$fix_clob		whether or not to file member variables of CLOB type
	*/
	public function getDataArray( $fix_clob = TRUE ) 
	{
		return array(
			'captcha_id' => $this->m_captcha_id,
			'file_id' => $this->m_file_id,
			'captcha_string' => $this->m_captcha_string,
			'active' => $this->m_active,
			'linked_objects' => $this->m_linked_objects
		);
		
	}//getDataArray()
	
	/**
	* Save with the current values of the instance variables
	* This is a wrapper to modify() to ease some methods of coding
	* @since 	20100728, hafner
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
	 * @since	20100728,hafner
	 * @return	mixed
	 * @param	array				$input				array of input data
	 */
	public function add( $input )
	{
		$this->checkInput( $input, TRUE );
		
		if( !$this->m_form->m_error )
		{
			//only set upload_timestamp on add
			$req_fields = array( 'file_id' => 0 );
			$input['captcha_id'] = $this->m_common->m_db->insertBlank( 'common_Captcha', 'captcha_id', $req_fields );
			$this->m_captcha_id = (int) $input['captcha_id'];
			$return = $this->m_captcha_id;
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
	 * @since	20100728, hafner
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
			UPDATE 
				common_Captcha
			SET 
				file_id = " . $input['file_id'] . ",
				captcha_string = '" . $input['captcha_string'] . "'
			WHERE 
				captcha_id = " . $this->m_captcha_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
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
	 * @since	20100728, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data 
	 */
	public function delete( $deactivate = TRUE )
	{
		if( $deactivate )
		{
			$sql = "
			UPDATE common_Captcha
			SET active = 0
			WHERE captcha_id = " . $this->m_captcha_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{	
			$sql = "
			DELETE
			FROM common_Captcha
			WHERE captcha_id = " . $this->m_captcha_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );	
		}
		
		return TRUE;
		
	}//delete()
	
	/**
	 * Validates the form input for creating/modifying a new File record.
	 * Returns FALSE on success, error message string otherwise.
	 * @since	20100728, hafner
	 * @return	mixed
	 * @param	array			$input			array of data
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function checkInput( $input, $is_addition )
	{
		//check captcha string
		if( !array_key_exists( "captcha_string", $input ) ||
			strlen( trim( $input['captcha_string'] ) )  < 1 )
		{
			$this->m_form->m_error = "You must provide a captcha string";
		}
		
		//check valid file
		if( !array_key_exists( "file_id", $input ) ||
			$input['file_id'] == 0 )
		{
			$this->m_form->m_error = "You must select a file.";
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	/**
	 * Sets linked objects.
	 * @since	20100718, hafner
	 * @return	array
	 */
	public function setLinkedObjects()
	{
		return array(
			'file' => new File( $this->m_file_id )
		);
	}//setLinkedObjects()
	
	/**
	 * Get random captcha.
	 * @since	20100729, hafner
	 * @return	Captcha Object
	 */
	public function getRandomCaptcha()
	{
		$sql = "
		SELECT captcha_id
		FROM common_Captcha
		WHERE active = 1
		ORDER BY RAND()
		LIMIT 1";
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $this->m_common->m_db->fetchRow( $result );
		return new Captcha( $row[0], TRUE );
		 
	}//getRandomCaptcha()
	
	/**
	 * Validates a user input captcha_string with an existing captcha string.
	 * @since	2010729, hafner
	 * @return	boolean
	 * @param	string			$user_string			string input by the user
	 * @param	int				$captcha_id				id of the current captcha
	 */
	public function validateCaptcha( $user_string, $captcha_id )
	{
		$c = new Captcha( $captcha_id );
		$c_string = strtolower( trim( $c->m_captcha_string ) );
		//return "cid: " . $captcha_id . " cs: " . $c->m_captcha_string . " ucs: " . $user_string;	
		$user_string = strtolower( trim( $user_string ) ); 
		return ( $user_string == $c_string ) ? TRUE : FALSE;
			
	}//validateCaptcha()
	
   /**
	* Get a member variable's value
	* @author	Version 20100728, hafner
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
	* @since	20100728, hafner
	* @return	mixed
	* @param	string		$var_name		Variable name to set
	* @param	string		$var_value		Value to set
	*/
	public function __set( $var_name, $var_value )
	{
		$exclusions = array( 'm_captcha_id' );

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
}//class Captcha
?>