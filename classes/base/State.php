<?
/**
 * A class to handle a State record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );

class State
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
	 * PK of the State Record.
	 * @var	int
	 */
	protected $m_state_id;
	
	/**
	 * Name of the State record.
	 * @var	string
	 */
	protected $m_full_name;
	
	/**
	 * Abbrv of the State record.
	 * @var	string
	 */
	protected $m_abbrv;
	
	/**
	 * Active flag.
	 * @var	boolean
	 */
	protected $m_active;
	
	/**
	 * Collection of objects associated with this record.
	 * @var	array
	 */
	protected $m_linked_objects;
	
	/**
	 * Constructs the object.
	 * @since	20100618, hafner
	 * @return	State
	 * @param	int				$state_id			id of the current record
	 * @param	boolean			$objects			whether or not to populate related objects.
	 */
	public function __construct( $state_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		
		$this->setMemberVars( $state_id, $objects );
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20100618, hafner
	 * @return	boolean
	 * @param	int				$state_id			id of the current record
	 * @param	boolean			$objects			whether or not to populate related objects.
	 */
	public function setMemberVars( $state_id, $ojects )
	{
		$chosen_id = ( is_numeric( $state_id ) ) ? $state_id : 0;
		
		//get member vars
		$sql = "
		SELECT 
			state_id,
			full_name,
			abbrv, 
			active
		FROM 
			common_States
		WHERE 
			state_id = " . $chosen_id;
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_state_id = $row['state_id'];
		$this->m_full_name = $row['full_name'];
		$this->m_abbrv = $row['abbrv'];
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects: array();
		
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
			'state_id' => $this->m_state_id,
			'full_name' => $this->m_full_name,
			'abbrv' => $this->m_abbrv,
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
			$input['state_id'] = $this->m_common->m_db->insertBlank( 'common_States', 'state_id' );
			$this->m_state_id = (int) $input['state_id'];
			$return = $this->m_state_id;
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
				common_States
			SET 
				full_name = '" . $this->m_common->m_db->fixDbString( $input['full_name'] ) . "',
				abbrv = '" . strtoupper( $input['abbrv'] ) . "'
			WHERE 
				state_id = " . $this->m_state_id;
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
			UPDATE common_States
			SET active = 0
			WHERE state_id = " . $this->m_state_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM common_States
			WHERE location_id IN(
				SELECT location_id
				FROM mdp_Locations
				WHERE state_id = " . $this->m_state_id . "
			)";
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM mdp_Locations
			WHERE state_id = " . $this->m_state_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM common_States
			WHERE state_id = " . $this->m_state_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );	
		}
		
		return TRUE;
		
	}//delete()
	
	/**
	 * Validates the form input for creating/modifying a new State record.
	 * Returns FALSE on success, error message string otherwise.
	 * @since	20100618, hafner
	 * @return	mixed
	 * @param	array			$input			array of data
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function checkInput( $input, $is_addition )
	{
		//check blank full name
		if( !$this->m_form->m_error )
		{
			$this->m_form->checkBlank( $input['full_name'], "State Name" );
		}
		
		//checks blank full abbrv
		if( !$this->m_form->m_error )
		{
			$this->m_form->checkBlank( $input['abbrv'], "State Abbreviation" );
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
		$exclusions = array( 'm_state_id' );

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
}//class State
?>