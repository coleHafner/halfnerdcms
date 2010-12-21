<?
/**
 * A class to handle a ContactType record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );

class ContactType
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
	protected $m_contact_type_id;
	
	/**
	 * email_address of the Email record.
	 * @var	string
	 */
	protected $m_title;
	
	/**
	 * Active flag.
	 * @var	boolean
	 */
	protected $m_active;
	
	/**
	 * Collections of objects related to this record
	 * @var	array
	 */
	protected $m_objects_collection;
	
	/**
	 * Constructs the object.
	 * @since	20100618, hafner
	 * @return	State
	 * @param	int				$contact_type_id		id of the current record
	 * @param	boolean			$objects			whether or not to populate the objects collection
	 */
	public function __construct( $contact_type_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->setMemberVars( $contact_type_id, $objects );
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20100618, hafner
	 * @return	boolean
	 
	 */
	public function setMemberVars( $contact_type_id, $objects )
	{
		$chosen_id = ( is_numeric( $contact_type_id ) ) ? $contact_type_id : 0;
		
		//get member vars
		$sql = "
		SELECT 
			contact_type_id,
			title, 
			active
		FROM 
			common_ContactTypes
		WHERE 
			contact_type_id = " . $chosen_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//assign member vars
		$this->m_contact_type_id = $row['contact_type_id'];
		$this->m_title = $row['title'];
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_objects_collection = ( $objects ) ? $this->populateObjectsCollection() : array();
		
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
			'contact_type_id' => $this->m_contact_type_id,
			'title' => $this->m_title,
			'active' => $this->m_active,
			'objects_collection' => $this->m_objects_collection
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
			$input['contact_type_id'] = $this->m_common->m_db->insertBlank( 'common_ContactTypes', 'contact_type_id' );
			$this->m_contact_type_id = (int) $input['contact_type_id'];
			$return = $this->m_contact_type_id;
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
				common_ContactTypes
			SET 
				title = '" . $this->m_common->m_db->escapeString( $input['title'] ) . "'
			WHERE 
				contact_type_id = " . $this->m_contact_type_id;
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
			UPDATE common_ContactTypes
			SET active = 0
			WHERE contact_type_id = " . $this->m_contact_type_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM mdp_Contacts
			WHERE contact_type_id = " . $this->m_contact_type_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM common_ContactTypes
			WHERE contact_type_id = " . $this->m_contact_type_id;
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
			$this->m_form->checkBlank( $input['title'], "Job Title" );
		}
		
		
		if( $is_addition )
		{
			//check duplicate email_address
			if( !$this->m_form->m_error )
			{
				$dup_check = array( 
					'table_name' => "common_ContactTypes",
					'pk_name' => "contact_type_id",
					'check_values' => array( 'title' => $input['title'] )
				);
				
				if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
				{
					$this->m_form->m_error = "That Job Title already exists";
				}
			}
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	/**
	 * Populates the ojects collection.
	 * @since	20100625, hafner
	 * @return	array
	 */
	public function populateObjectsCollection()
	{
		return array();
		
	}//populateObjectsCollection()
	
	/**
	 * Gets all records.
	 * If records found, returns array of objects, FALSE otherwise.
	 * @since	20100923, Hafner
	 * @return	mixed
	 */
	public function getAllRecords( $constraints = array() )
	{
		$class = get_class( $this );
		$return = FALSE;
		
		$sql = "
		SELECT contact_type_id
		FROM common_ContactTypes
		WHERE contact_type_id > 0 AND
		active = 1
		ORDER BY title ASC";
		
		$records = $this->m_common->m_db->doQuery( $sql );
		
		if( is_array( $records ) )
		{
			$return = array();
			
			foreach( $records as $info )
			{
				$return[] = new $class( $info['contact_type_id'], TRUE );
			}
		}
		
		return $return;
		
	}//getAllRecords()
	
	/**
	 * Gets HTML
	 * @since	20101007
	 * @author	20101007, hafner
	 * @param	string			$cmd			command html to get
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function getHtml( $cmd, $vars = array() )
	{
		$steps = array( 1 => "Set Details", 2 => "Set Thumbnail", 3 => "Set Full Image" );
		
		switch( strtolower( $cmd ) )
		{
			case "contact-type-drop-down":
				$records = $this->getAllRecords();
				
				$body = '
				<select name="contact_type_id" id="contact_type_id">
				';
				if( is_array( $records ) )
				{	
					$body .= '
					<option value="0">
						Select a Contact Type
					</option>
					';
					
					foreach( $records as $i => $ct )
					{
						$selected = ( $ct->m_contact_type_id == $vars['contact_type_id'] ) ? 'selected="selected"' : "";
						
						$body .= '
					<option value="' . $ct->m_contact_type_id . '" ' . $selected . '>
						' . $ct->m_title . '
					</option>
					';
					}
				}
				else
				{
					$body .= '
					<option value="0">
						There are no Job Titles
					</option>
					';
				}
				
				$body .= '
				</select>
				';
				
				$return = array( 'body' => $body );
				
				break;
				
			case "show-mod":
				$return = array(
					'mod_form' => '
						<form id="contact_type_form_' . $vars['active_ct']->m_contact_type_id . '" style="display:inline;">
							<input type="text" name="title" id="ct_title_mod_' . $vars['active_ct']->m_contact_type_id . '" value="' . $vars['active_ct']->m_title . '"/>
						</form>
						',
				
					'save_buttons' => '
						<a href="#" id="contact_type" contact_type_id="' . $vars['active_ct']->m_contact_type_id . '" action="modify">
							<img src="images/btn_save.gif" alt="save" style="border:0px;"/>
						</a>
						&nbsp;&nbsp;
						<a href="#" id="contact_type" contact_type_id="' . $vars['active_ct']->m_contact_type_id . '" action="hide-mod">
							<img src="images/btn_cancel.gif" alt="save" style="border:0px;"/>
						</a>'
				);
				break;
				
			case "hide-mod":
				$return = array(
					'mod_form' => $vars['active_ct']->m_title,
				
					'save_buttons' => '
						<a href="#" id="contact_type" contact_type_id="' . $vars['active_ct']->m_contact_type_id . '" action="show-mod">
							Edit
						</a>'
				);
				break;
				
			case "show-add":
				$return = array(
					'title' => 'Add Job Title',
				
					'body' => '
						<div style="width:320px;height:100px;text-align:center;">
							<div id="result_contact_type_add" class="result">
							</div>
							
							<form id="contact_type_form_0">
								<input type="text" name="title" id="ct_title_mod_' . $vars['active_ct']->m_contact_type_id . '" value="' . $vars['active_ct']->m_title . '" class="text_input"/>
							</form>
							
							<p style="margin-top:10px;">
								<a href="#" id="contact_type" contact_type_id="' . $vars['active_ct']->m_contact_type_id . '" action="add">
									<img src="images/btn_save.gif" alt="save" style="border:0px;"/>
								</a>
								&nbsp;&nbsp;
								<a href="#" id="close_colorbox" contact_type_id="' . $vars['active_ct']->m_contact_type_id . '">
									<img src="images/btn_cancel.gif" alt="save" style="border:0px;"/>
								</a>
							</p>
							
						</div>
						'
					);
				break;
				
		}//end switch
		
		return $return;
		
	}//getHtml()
	
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
}//class ContactType
?>