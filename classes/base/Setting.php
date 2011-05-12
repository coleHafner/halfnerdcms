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
		
		//check value
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "value", $input ) ||
				strlen( trim( $input['value'] ) ) == 0 ||
				strtolower( trim( $input['value'] ) ) == "setting value" )
			{
				$this->m_form->m_error = "You must select a value.";
			}
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
	
	public static function getSettings( $field, $value )
	{
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT setting_id
		FROM common_Settings
		WHERE setting_id > 0 AND
		" . $field . " = " . $value . "
		ORDER BY title ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[] = new Setting( $row[0], FALSE );
		}
		
		return $return;
		
	}//getSettings()
	
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
		SELECT setting_id
		FROM common_Settings
		WHERE setting_id != 0 AND
		active = 1
		ORDER BY title ASC";
		
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
				<div class="padder">
					<div class="header color_accent">
						' . $s->m_title . '
					</div>
					<div class="padder_10_top color_black" >
						' . $s->m_value . '
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-add-form":
				
				$s = $vars['active_record'];
				
				$html = '
				' . Common::getHtml( "title-bar", array( 'title' => "Add Setting", 'classes' => '' ) ) . '
				<div class="padder_10_top">
					<form id="setting_form_0">
						<div style="position:relative;width:35%;float:left;">
							<span class="title_span">
								Setting Title:
							</span>
							<input type="text" name="title" class="text_input text_long" value="" />
						</div>
						
						<div style="position:relative;width:35%;float:left;">
							<span class="title_span">
								Setting Value:
							</span>
							<input type="text" name="value" class="text_input text_long" value="" />
						</div>
						
						<div style="position:relative;width:30%;float:left;">
							' . Common::getHtml( "get-form-buttons", array( 
									'left' => array( 
										'pk_name' => "setting_id",
										'pk_value' => 0,
										'process' => "add",
										'id' => "setting",
										'button_value' => "Add",
										'extra_style' => 'style="width:41px;"' ),
										
									'right' => array(
										'pk_name' => "item_id",
										'pk_value' => $s->m_setting_id,
										'process' => "view",
										'id' => "list_item",
										'button_value' => "Cancel",
										'extra_style' => 'style="width:41px;"' ),
	
									'table_style' => 'style="margin-top:18px;margin-left:15px;"'
									
									)
								) . '
						</div>
					</div>
					
					<div class="clear"></div>
				</form>
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
					$second_line_title = "Setting Value:";
					$main_title = "Modify " . $s->m_title;
					
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
					$main_title = "Add Setting";
				}
				
				$html = '
				<div class="padder_10">
					' . Common::getHtml( "title-bar", array( 'title' => $main_title, 'classes' => '', 'center' => FALSE ) ) . '
					<form id="setting_form_' . $s->m_setting_id . '">
					
						<div class="padder_10_bottom">
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
						
						<div class="padder_10_bottom ' . $second_line_css . '">
							
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
						
						' . 
						Common::getHtml( "get-form-buttons", array( 
							'left' => array( 
								'pk_name' => "setting_id",
								'pk_value' => $s->m_setting_id,
								'process' => "modify",
								'id' => "setting",
								'button_value' => "Modify",
								'extra_style' => 'style="width:41px;"' ),
								
							'right' => array(
								'pk_name' => "item_id",
								'pk_value' => $s->m_setting_id,
								'process' => "view",
								'id' => "list_item",
								'button_value' => "Cancel",
								'extra_style' => 'style="width:41px;"' ),
	
							'table_style' => 'style="position:relative;width:100px;"'
							)
						) . '
					</form>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-delete-form":
			
				$s = $vars['active_record'];
				
				$html = '
				<div class="padder_10 header color_accent">
					Really delete this setting?
				</div>
				
				<div> ' . 
					Common::getHtml( "get-form-buttons", array( 
						'left' => array( 
							'pk_name' => "setting_id",
							'pk_value' => $s->m_setting_id,
							'process' => "delete",
							'id' => "setting",
							'button_value' => "Delete",
							'extra_style' => 'style="width:41px;"' ),
							
						'right' => array(
							'pk_name' => "item_id",
							'pk_value' => $s->m_setting_id,
							'process' => "view",
							'id' => "list_item",
							'button_value' => "Cancel",
							'extra_style' => 'style="width:41px;"' ),

						'table_style' => 'style="position:relative;width:100px;padding-left:12px;"'
						)
					) . '
				</div>
				';

				$return = array( 'html' => $html );
				break;
				
			case "get-admin-list-item":
				
				$item = $vars['active_record'];
				$form_vars = array( 'active_record' => $item );
				$view_form = Setting::getHtml( "get-view-form", $form_vars );
				$edit_form = Setting::getHtml( "get-edit-form", $form_vars );
				$delete_form = Setting::getHtml( "get-delete-form", $form_vars );
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$buttons = Common::getHtml( "get-admin-item-buttons", array( 'item_id' => $item->m_setting_id ) );
				
				$html = '
				<div class="item_container bg_color_light_tan border_dark_tan" style="margin-top:0px;" hover_enabled="1">		
					<div id="item_view_' . $item->m_setting_id . '">
						' . $view_form['html'] . '
					</div>
					
					<div id="item_mod_' . $item->m_setting_id . '" style="display:none;">
						' . $edit_form['html'] . '
					</div>
					
					<div id="item_delete_' . $item->m_setting_id . '" style="display:none;">
						' . $delete_form['html'] . '
					</div>
					
					<div class="title_button_container" id="item_control" style="display:none;width:100px;height:40px;">
						' . $buttons['html'] . '
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
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