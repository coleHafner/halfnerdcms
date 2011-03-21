<?
/**
 * A class to handle a JobTitle record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );

class FileType
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
	 * PK of the Email Record.
	 * @var	int
	 */
	protected $m_file_type_id;
	
	/**
	 * email_address of the Email record.
	 * @var	string
	 */
	protected $m_title;
	
	/**
	 * directory where this type of file lives.
	 * @var string
	 */
	protected $m_directory;
	
	/**
	 * Active flag.
	 * @var	boolean
	 */
	protected $m_active;
	
	/**
	 * Collections of objects related to this record
	 * @var	array
	 */
	protected $m_linked_objects;
	
	/**
	 * Constructs the object.
	 * @since	20100618, hafner
	 * @return	State
	 * @param	int				$file_type_id		id of the current record
	 * @param	boolean			$objects			whether or not to populate the objects collection
	 */
	public function __construct( $file_type_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_file_type_id = ( is_numeric( $file_type_id ) && $file_type_id > 0 ) ? $file_type_id : 0;
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
			file_type_id,
			title, 
			directory,
			active
		FROM 
			common_FileTypes
		WHERE 
			file_type_id = " . $this->m_file_type_id;
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//assign member vars
		$this->m_file_type_id = $row['file_type_id'];
		$this->m_title = $row['title'];
		$this->m_directory = $row['directory'];
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
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
			'file_type_id' => $this->m_file_type_id,
			'title' => $this->m_title,
			'directory' => $this->m_directory,
			'active' => $this->m_active,
			'linked_objects' => $this->m_linked_objects
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
			$input['file_type_id'] = $this->m_common->m_db->insertBlank( 'common_FileTypes', 'file_type_id' );
			$this->m_file_type_id = (int) $input['file_type_id'];
			$return = $this->m_file_type_id;
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
			UPDATE 
				common_FileTypes
			SET 
				title = '" . $this->m_common->m_db->escapeString( ucwords( $input['title'] ) ) . "',
				directory = '" . $input['directory'] . "' 
			WHERE 
				file_type_id = " . $this->m_file_type_id;
				
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
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data 
	 */
	public function delete( $deactivate = TRUE )
	{
		if( $deactivate )
		{
			$sql = "
			UPDATE common_FileTypes
			SET active = 0
			WHERE file_type_id = " . $this->m_file_type_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM common_Files
			WHERE file_type_id = " . $this->m_file_type_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM common_FileTypes
			WHERE file_type_id = " . $this->m_file_type_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );	
		}
		
		return TRUE;
		
	}//delete()
	
	/**
	 * Validates the form input for creating/modifying a new Email record.
	 * Returns FALSE on success, error message string otherwise.
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array			$input			array of data
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function checkInput( $input, $is_addition )
	{
		//check blank email_address
		if( !$this->m_form->m_error )
		{
			$this->m_form->checkBlank( $input['title'], "Title" );
		}
		
		//check duplicate title
		if( !$this->m_form->m_error )
		{
			$dup_check = array( 
				'table_name' => "common_FileTypes",
				'pk_name' => "file_type_id",
				'check_values' => array( 'title' => $input['title'] )
			);
			
			if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
			{
				$this->m_form->m_error = "That title already exists";
			}
		}
		
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "directory", $input ) ||
				strlen( $input['directory'] ) == 0 )
			{
				$this->m_form->m_error = "You must provide a directory.";
			}
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	/**
	 * Populates the ojects collection.
	 * @since	20100625, hafner
	 * @return	array
	 */
	public function setLinkedObjects()
	{
		return array();
	}//setLinkedObjects()
	
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
		$exclusions = array( 'm_email_id' );

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
}//class FileType
?>