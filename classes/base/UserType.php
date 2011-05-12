<?
/**
 * A class to handle a File record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );
require_once( "base/File.php" );

class UserType
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
	protected $m_user_type_id;
	
	/**
	 * Alias of this view.
	 * @var	string
	 */
	protected $m_title;
	
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
	 * @param	int				$user_type_id			id of the current record
	 */
	public function __construct( $user_type_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_user_type_id = ( is_numeric( $user_type_id ) ) ? $user_type_id : 0; 
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
			user_type_id,
			title,
			active
		FROM 
			common_UserTypes
		WHERE 
			user_type_id = " . $this->m_user_type_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		$row = array_change_key_case( $row, CASE_LOWER );

		//set member vars
		$this->m_user_type_id = $row['user_type_id'];
		$this->m_title = stripslashes( $row['title'] );
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
			'user_type_id' => $this->m_user_type_id,
			'title' => $this->m_title,
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
			$input['user_type_id'] = $this->m_common->m_db->insertBlank( 'common_UserTypes', 'user_type_id', array() );
			$this->m_user_type_id = (int) $input['user_type_id'];
			$this->modify( $input, TRUE );
			$return = $this->m_user_type_id;
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
				common_UserTypes
			SET 
				title = '" . addslashes( $input['title'] ) . "'
			WHERE 
				user_type_id = " . $this->m_user_type_id;
				
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_user_type_id;
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
			UPDATE common_UserTypes
			SET active = 0
			WHERE user_type_id = " . $this->m_user_type_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM common_UserTypes
			WHERE user_type_id = " . $this->m_user_type_id;
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
			strtolower( $input['title'] ) == "new user type" )
		{
			$this->m_form->m_error = "You must specify a title.";
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	/**
	 * Sets the linked objects for this View object.
	 * Returns array of linked objects.
	 * @since	20100907, Hafner
	 * @return	array
	 */
	public function setLinkedObjects()
	{
		return array();
		
	}//setLinkedObjects()
	
	public static function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( trim( $cmd ) ) )
		{				
			case "get-manager":
								
				$records = self::getUserTypes( "active", "1" );
				$list = self::getHtml( 'get-type-list', array( 'records' => $records ) );
				
				$html = '
				' . Common::getHtml( "title-bar", array( 'title' => "Manage User Types", 'classes' => '' ) ) . '
				<div class="padder_20_bottom padder_10_top">
					
					<div class="padder" id="user_type_items_container">			
						' . $list['html'] . '
					</div>
									
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-type-list":
				
				$records = $vars['records'];
				
				$html = '
				<table class="manager_items">
					<tr id="section_item_add" class="bg_color_tan" >
						<td style="width:33%">
						
							<div class="padder_10_left" >
								<form id="user_type_form_0">
									<input type="text" name="title" class="text_input text_long center color_black input_clear" value="New User Type" clear_if="New User Type" />
									<input type="hidden" name="from_add" value="1" />
								</form>
							</div>
							
							
						</td>
						
						<td class="center" style="width:33%">
							
							' . Common::getHtml( "get-button", array( 
								'pk_name' => "user_type_id",
								'pk_value' => "0",
								'process' => "add",
								'id' => "user_type",
								'button_value' => "Add",
								'extra_style' => 'style="width:41px;"' ) 
							) . '
						</td>
						
						<td class="center" style="width:34%">
								' . Common::getHtml( "get-button", array( 
								'pk_name' => "manager_id",
								'pk_value' => "0",
								'process' => "hide",
								'id' => "manager",
								'button_value' => "Hide Manager",
								'extra_style' => 'style="width:110px;"' ) 
							) . '
						</td>
						
					</tr>
				';
				
				foreach( $records as $i => $ut )
				{						
					$bg_color = ( $i%2 ) ? "bg_color_light_tan" : "bg_color_tan";
					
					$html .= '
					<tr id="user_type_item_' . $ut->m_user_type_id . '" class="' . $bg_color . '" >
						<td style="width:33%">
							<div id="user_type_title_' . $ut->m_user_type_id . '" class="header_sub color_black padder_10_left">
								' . $ut->m_title . '
							</div>
							
							<div id="user_type_title_box_' . $ut->m_user_type_id . '" class="padder_10_left" style="display:none;" >
								<form id="user_type_form_' . $ut->m_user_type_id . '">
									<input type="text" name="title" class="text_input text_long center color_black" value="' . $ut->m_title . '" />
									<input type="hidden" name="from_add" value="0" />
								</form>
							</div>

						</td>
						
						<td class="center" style="width:33%">
							
							<div id="user_type_mod_init_' . $ut->m_user_type_id . '">
								' . Common::getHtml( "get-button-round", array(
									'id' => "user_type",
									'process' => "show_modify",
									'pk_name' => "user_type_id",
									'pk_value' => $ut->m_user_type_id,
									'button_value' => "m",
									'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
									'link_style' => 'style="margin:auto;"' ) 
								) . '
							</div>
							
							<div id="user_type_mod_confirm_' . $ut->m_user_type_id . '" style="display:none;">
								' . Common::getHtml( "get-form-buttons", array( 
									'left' => array(
										'pk_name' => "user_type_id",
										'pk_value' => $ut->m_user_type_id,
										'process' => "modify",
										'id' => "user_type",
										'button_value' => "Modify",
										'extra_style' => 'style="width:41px;"' ),
									
									'right' => array(
										'pk_name' => "user_type_id",
										'pk_value' => $ut->m_user_type_id,
										'process' => "cancel_modify",
										'id' => "user_type",
										'button_value' => "Cancel",
										'extra_style' => 'style="width:41px;"' ) )
									) . '
							</div>
							
						</td>
						
						<td class="center" style="width:34%">
							
							<div id="user_type_delete_init_' . $ut->m_user_type_id . '">
								' . Common::getHtml( "get-button-round", array(
										'id' => "user_type",
										'process' => "show_delete",
										'pk_name' => "user_type_id",
										'pk_value' => $ut->m_user_type_id,
										'button_value' => "x",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="margin:auto;"' ) 
									) . '
							</div>
							
							<div id="user_type_delete_confirm_' . $ut->m_user_type_id . '" style="display:none;">
								' . Common::getHtml( "get-form-buttons", array( 
									'left' => array(
										'pk_name' => "user_type_id",
										'pk_value' => $ut->m_user_type_id,
										'process' => "delete",
										'id' => "user_type",
										'button_value' => "Delete",
										'extra_style' => 'style="width:41px;"' ),
									
									'right' => array(
										'pk_name' => "user_type_id",
										'pk_value' => $ut->m_user_type_id,
										'process' => "cancel_delete",
										'id' => "user_type",
										'button_value' => "Cancel",
										'extra_style' => 'style="width:41px;"' ) )
									) . '
							</div>
						
						</td>
						
					</tr>
					';
				}
				
				$html .= '
				</table>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-radio-selectors":

				$active_ut = $vars['active_record'];
				$active_user = $vars['active_user'];
				$all_ut = self::getUserTypes( "active", "1" );
				
				$html = '
				<div class="padder spacer_bottom padder_10_left">
					<div style="float:right;">
						<a href="#" id="user" process="refresh_user_type_selector" user_id="' . $active_user->m_user_id . '">
							Refresh
						</a>
					</div>
				</div>
				';
				
				foreach( $all_ut as $i => $ut )
				{
					$selected = ( $ut->m_user_type_id == $active_ut->m_user_type_id ) ? 'checked="checked"' : "";
					$row_class = ( $i%2 ) ? "bg_color_tan" : "bg_color_light_tan";
					
					$html .= '
					<div class="padder ' . $row_class . '">
						<table>
							<tr class="' . $row_class . '">	
								<td style="vertical-align:top;">
									<input type="radio" name="user_type_id" ' . $selected . ' value="' . $ut->m_user_type_id . '"/>&nbsp;
								</td>
														
								<td>
									<span class="header_sub color_accent">' . $ut->m_title . '</span>
								</td>
							</tr>
						</table>
					</div>
					';
				}
				
				$return = array( 'html' => $html );
				break;
				
			default:
				throw new Exception( "Error: Invalid HTML command." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	public static function getUserTypes( $field, $value )
	{
		$i = 1;
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT user_type_id
		FROM common_UserTypes
		WHERE user_type_id > 0 AND
		" . $field . " = " . $value . "
		ORDER BY title ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[$i] = new UserType( $row[0], FALSE );
			$i++;
		}
		
		return $return;
		
	}//getUserTypes()
		
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
		$exclusions = array( 'm_user_type_id' );

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
	
}//class View
?>