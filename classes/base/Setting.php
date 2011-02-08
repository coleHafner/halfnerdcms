<?
/**
 * A class to handle a File record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );
require_once( "base/File.php" );

class Setting
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
	protected $m_setting_id;
	
	/**
	 * Title of the current setting.
	 * @var	string
	 */
	protected $m_title;
	
	/**
	 * Value of the current setting.
	 * @var	string
	 */
	protected $m_value;
	
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
	 * @param	int				$setting_id			id of the current record
	 */
	public function __construct( $setting_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_setting_id = ( is_numeric( $setting_id ) ) ? $setting_id : 0; 
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
			setting_id,
			title,
			value,
			active
		FROM 
			common_Settings
		WHERE 
			setting_id = " . $this->m_setting_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		$row = array_change_key_case( $row, CASE_LOWER );

		//set member vars
		$this->m_setting_id = $row['setting_id'];
		$this->m_title = stripslashes( $row['title'] );
		$this->m_value = stripslashes( $row['value'] );
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
			'setting_id' => $this->m_setting_id,
			'title' => $this->m_title,
			'value' => $this->m_value,
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
			$input['setting_id'] = $this->m_common->m_db->insertBlank( 'common_Settings', 'setting_id' );
			$this->m_setting_id = (int) $input['setting_id'];
			$this->modify( $input, TRUE );
			$return = $this->m_setting_id;
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
				common_Settings
			SET 
				title = '" . $this->m_common->m_db->escapeString( $input['title'] ) . "',
				value = '" . $this->m_common->m_db->escapeString( $input['value'] ) . "' 
			WHERE 
				setting_id = " . $this->m_setting_id;
				
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_setting_id;
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
			UPDATE common_Settings
			SET active = 0
			WHERE setting_id = " . $this->m_setting_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM common_Settings
			WHERE setting_id = " . $this->m_setting_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );	
		}
		
		return TRUE;
		
	}//delete()
	
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
		//check missing alias
		if( !array_key_exists( "title", $input ) || 
			strlen( trim( $input['title'] ) ) == 0 ||
			strtolower( trim( $input['title'] ) ) == "setting title" )
		{
			$this->m_form->m_error = "You must select a title.";
		} 
		
		//check duplicate title
		if( !$this->m_form->m_error )
		{
			if( $is_addition )
			{
				$dup_check = array( 
					'table_name' => "common_Settings",
					'pk_name' => "setting_id",
					'check_values' => array( 'title' => strtolower( $input['title'] ) )
				);
				
				if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
				{
					$this->m_form->m_error = "That title already exists.";
				}
			}
		}
		
		if( !array_key_exists( "value", $input ) ||
			strlen( trim( $input['value'] ) ) == 0 ||
			strtolower( trim( $input['value'] ) ) == "setting value" )
		{
			$this->m_form->m_error = "You must select a value for this setting.";
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	/**
	 * Sets the linked objects for this Setting object.
	 * Returns array of linked objects.
	 * @since	20100907, Hafner
	 * @return	array
	 */
	public function setLinkedObjects()
	{
		return array();
		
	}//setLinkedObjects()
	
	/**
	 * Gets all views.
	 * Returns an array of view objects.
	 * @since	20100907, Hafner
	 * @return	array
	 * @param	boolean			$nav_only		if TRUE, constrains query to only views that 'show_in_nav' == 1
	 */
	public function getAllRecords()
	{
		$class = get_class( $this );
		$constraint = '';
		$return = FALSE;
		
		$sql = "
		SELECT 
			setting_id AS setting_id
		FROM 
			common_Settings
		WHERE
			active = 1 AND
			setting_id != 0
			" . $constraint . "
		ORDER BY 
			nav_priority ASC";
		
		$records = $this->m_common->m_db->doQuery( $sql );
		
		if( is_array( $records ) )
		{
			foreach( $records as $i => $info )
			{
				$return[] = new $class( $info['setting_id'], TRUE );
			}
		}
				
		return $return;
		
	}//getAllRecords()
	
	public static function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( trim( $cmd ) ) )
		{
			case "get-view-form":
			
				$s = $vars['active_record'];
				
				$html = '
				<div class="header color_accent">
					' . $s->m_title . '
				</div>
				
				<div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
			
			case "get-edit-form":
			
				$s = $vars['active_record'];
				
				if( $s->m_setting_id > 0 )
				{
					
					$process = "modify";
					$title = $s->m_title;
					$value = $s->m_value;
					$second_line_css = "";
					
					$formatted_title = ucwords( strtolower( str_replace( "-", " ", $s->m_title ) ) );
					$second_line_title = "";
					$main_title = "Edit " . $formatted_title;
					
					$first_line_html = '
					<input type="hidden" name="title" value="' . $s->m_title . '" />
					';
				}
				else
				{
					$process = "add";
					$title = "";
					$value = "";
					$second_line_css = "padder_no_top";
					$second_line_title = "Setting Value:";
					$main_title = ucWords( $process ) . " Setting";
					
					$first_line_html = '
					<div class="padder_10">
						<table style="margin:auto;">
							<tr>
								<td>
									<span class="title_span">
										Setting Title:
									</span>
									<input type="text" name="title" class="text_input text_long" value="' . $title . '" />
								</td>
							</tr>
						</table>
					</div>
					';
				}
				
				$html = '
				<div class="padder_10">
					' . Common::getHtml( "title-bar", array( 'title' => $main_title, 'classes' => '' ) ) . '
					
					<form id="setting_form_' . $s->m_setting_id . '">
					
						' . $first_line_html . '
						
						<div class="padder_10 ' . $second_line_css . '">
							
							<table style="margin:auto;">
								<tr>
									<td>
										<span class="title_span">
											' . $second_line_title . '
										</span>
										
										<input type="text" name="value" class="text_input text_long" value="' . $value . '" />
									</td>
								</tr>
							</table>
						</div>
						
						<div class="padder center">
							' . Common::getHtml( "get-button", array( 
								'pk_name' => "setting_id",
								'pk_value' => $s->m_setting_id,
								'process' => $process,
								'id' => "setting",
								'button_value' => ucwords( $process ),
								'extra_style' => 'style="width:41px;"' )
							) . '
						</div>
					</form>
					
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-delete-form":
			
				$s = $vars['active_record'];
				
				$return = array( 'html' => '
					<div class="padder_10">
						' . Common::getHtml( "title-bar", array( 'title' => "Really Delete Setting?", 'classes' => '' ) ) . '
						
						<div class="button_container">
							' . Common::getHtml( "get-form-buttons", array( 
						
								'left' => array( 
									'pk_name' => "setting_id",
									'pk_value' => $s->m_setting_id,
									'process' => "delete",
									'id' => "setting",
									'button_value' => "Delete" ),
									
								'right' => array(
									'pk_name' => "setting_id",
									'pk_value' => $s->m_setting_id,
									'process' => "cancel_delete",
									'id' => "setting",
									'button_value' => "Cancel" ) 
								) 
							) . '
						</div>
					</div>
					'
				);
				break;
				
			default:
				throw new Exception( "Error: Invalid HTML command." );
				break;
		}
		
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
		$exclusions = array( 'm_setting_id' );

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
}//class Setting
?>