<?
/**
 * A class to handle a File record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );

class Permission
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
	 * PK of the current record.
	 * @var	int
	 */
	protected $m_permission_id;
	
	/**
	 * Three character abbrv for the current permission.
	 * @var	string
	 */
	protected $m_alias;
	
	/**
	 * Title for the current permission.
	 * @var	string
	 */
	protected $m_title;
	
	/**
	 * Summary for this permission.
	 * @var	string
	 */
	protected $m_summary;
	
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
	 * @param	int				$permission_id			id of the current record
	 */
	public function __construct( $permission_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_permission_id = ( is_numeric( $permission_id ) ) ? $permission_id : 0; 
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
			permission_id,
			alias,
			title,
			summary,
			active
		FROM 
			common_Permissions
		WHERE 
			permission_id = " . $this->m_permission_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		$row = array_change_key_case( $row, CASE_LOWER );

		//set member vars
		$this->m_permission_id = $row['permission_id'];
		$this->m_alias = $row['alias'];
		$this->m_title = stripslashes( $row['title'] );
		$this->m_summary = stripslashes( $row['summary'] );
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
			'permission_id' => $this->m_permission_id,
			'alias' => $this->m_alias,
			'title' => $this->m_title,
			'summary' => $this->m_alias,
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
			$input['permission_id'] = $this->m_common->m_db->insertBlank( 'common_Permissions', 'permission_id', array() );
			$this->m_permission_id = (int) $input['permission_id'];
			$this->modify( $input, TRUE );
			$return = $this->m_permission_id;
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
				common_Permissions
			SET 
				alias = '" . strtoupper( $input['alias'] ) . "',
				title = '" . addslashes( $input['title'] ) . "',
				summary = '" . addslashes( $input['summary'] ) . "'
			WHERE 
				permission_id = " . $this->m_permission_id;
				
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_permission_id;
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
			UPDATE common_Permissions
			SET active = 0
			WHERE permission_id = " . $this->m_permission_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM common_UserToPermission
			WHERE permission_id = " . $this->m_permission_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM common_Permissions
			WHERE permission_id = " . $this->m_permission_id;
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
		
		if( !array_key_exists( "title", $input ) || 
			strlen( trim( $input['title'] ) ) == 0 )
		{
			$this->m_form->m_error = "You must define a title.";
		}
		
		//check missing summary
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "summary", $input ) || 
				strlen( trim( $input['summary'] ) ) == 0 )
			{
				$this->m_form->m_error = "You must define a permission summary.";
			}
		}
		
		//check missing alias
		if( !array_key_exists( "alias", $input ) ||
			strlen( $input['alias'] ) == 0 )
		{
			$this->m_form->m_error = "You must define a permission ID.";
		}
		
		//check valid alias
		if( !$this->m_form->m_error )
		{
			if( strlen( trim( $input['alias'] ) ) != 3 )
			{
				$this->m_form->m_error = "Permission ID must be 3 characters long.";
			}
		}
		
		if( $is_addition )
		{
			//check unique alias
			if( !$this->m_form->m_error )
			{
				$sql = "
				SELECT count(*)	
				FROM common_Permissions
				WHERE LOWER( TRIM( alias ) ) = '" . strtolower( trim( $input['alias'] ) ) . "'";
				
				$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
				$row = $this->m_common->m_db->fetchRow( $result );
				
				if( $row[0] > 0 )
				{
					$this->m_form->m_error = "That Permission ID already exists.";
				}
			}
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
			case "get-permissions-list-readonly":
				
				$selected_permissions = ( is_array( $vars['active_user_record']->m_permissions ) ) ? $vars['active_user_record']->m_permissions : array();
				$all_p = self::getPermissions( "active", "1" );
				
				$html = '
				<div class="padder_10_top">
				';
				
				foreach( $all_p as $i => $p )
				{
					$row_class = ( $i%2 ) ? "bg_color_tan" : "bg_color_light_tan";
					$checked = ( in_array( strtolower( $p->m_alias ), $selected_permissions ) ) ? 'checked="checked"' : "";
					
					$html .= '
					<div class="padder ' . $row_class . '">
						<table>
							<tr class="' . $row_class . '">	
								<td style="vertical-align:top;">
										<input type="checkbox" name="permission_' . $i . '" ' . $checked . ' value="' . $p->m_permission_id . '" />							
								</td>
														
								<td>
									<div class="header_sub color_accent">
										' . $p->m_alias . ' ( ' . $p->m_title . ' )
									</div>
									' . $p->m_summary . '
								</td>
							</tr>
						</table>
					</div>
					';
				}
				
				$html .= '
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-edit-form":
				
				$p = $vars['active_record'];
				
				if( $p->m_permission_id > 0 )
				{
					$title = $p->m_title;
					$alias = $p->m_alias;
					$summary =$p->m_summary;
					$process = "modify";
					$from_add = "0";
				}
				else 
				{
					$title = "";
					$alias = "";
					$summary = "";
					$process = "add";
					$from_add = "1";
				}
				
				$html = '
				' . Common::getHtml( "title-bar", array( 'title' => ucfirst( $process ) . " Permission", 'classes' => '' ) ) . '
				<form id="permission_form_' . $p->m_permission_id . '">
					<table style="width:100%;">
						<tr>
							<td style="width:160px;text-align:left;vertical-align:top;">
								<div class="padder">
									<span class="title_span">
										Title:
									</span>
									<input name="title" type="text" class="text_input text_long" value="' . $title . '" />
								</div>
								
								<div class="padder">
									<span class="title_span">
										Permission ID:
									</span>
									<input name="alias" type="text" class="text_input text_long" value="' . $alias . '" />
								</div>
							</td>
							<td style="width:10px;">
							</td>
							<td style="vertical-align:top;">
								<div style="position:relative;padding-top:5px;">
									<span class="title_span">
										Permission Summary:
									</span>
									<textarea name="summary" style="position:relative;height:93px;width:100%;">' . $summary . '</textarea>
								</div>
							</td>
						</tr>
					</table>
					
					<div class="padder_10_top">
					' . Common::getHtml( "get-form-buttons", array( 
					
						'left' => array( 
							'pk_name' => "permission_id",
							'pk_value' => $p->m_permission_id,
							'process' => $process,
							'id' => "permission",
							'button_value' => ucwords( $process ),
							'extra_style' => 'style="width:41px;"' ),
							
						'right' => array(
							'pk_name' => "item_id",
							'pk_value' => $p->m_permission_id,
							'process' => "view",
							'id' => "list_item",
							'button_value' => "Cancel" )
						) 
					) . '
					</div> 
					
					<input type="hidden" name="permission_id" value="' . $p->m_permission_id . '"/>
					<input type="hidden" name="from_add" value="' . $from_add . '"/>

				</form>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-view-form":
				
				$p = $vars['active_record'];
				
				$html = '
				<div>
					<div class="margin_10_bottom">
						<span class="header color_accent">
							' . $p->m_title . '
						</span>
						<span class="header_sub color_terciary">
							&nbsp;-&nbsp;
							' . $p->m_alias . '
						</span>
					</div>
					
					<div class="padder">
						' . $p->m_summary . '
					</div>
					
				</div>';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-delete-form":
				
				$p = $vars['active_record'];
				
				$html = '
				<div class="padder_10">
				
					' . Common::getHtml( "title-bar", array( 'title' => "Really Delete Permission?", 'classes' => '' ) ) . '
						
					<div class="padder_10_top">
						' . Common::getHtml( "get-form-buttons", array( 
						
							'left' => array( 
								'pk_name' => "permission_id",
								'pk_value' => $p->m_permission_id,
								'process' => "delete",
								'id' => "permission",
								'button_value' => "Delete",
								'extra_style' => 'style="width:41px;"' ),
								
							'right' => array(
								'pk_name' => "item_id",
								'pk_value' => $p->m_permission_id,
								'process' => "view",
								'id' => "list_item",
								'button_value' => "Cancel" ) 
							) 
						) . '
						</div> 
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-admin-list-item":
				
				$p = $vars['active_record'];
				$view_form = Permission::getHtml( "get-view-form", array( 'active_record' => &$p ) );
				$mod_form = Permission::getHtml( "get-edit-form", array( 'active_record' => &$p ) );
				$delete_form = Permission::getHtml( "get-delete-form", array( 'active_record' => &$p ) );
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				
				$html = '
				<div class="' . $item_classes['html'] . '" hover_enabled="1">
					
					<div id="item_view_' . $p->m_permission_id . '">						
						' . $view_form['html'] . '
					</div>
										
					<div id="item_mod_' . $p->m_permission_id . '" style="display:none;">
						' . $mod_form['html'] . '
					</div>
					
					<div id="item_delete_' . $p->m_permission_id . '" style="display:none;">
						' . $delete_form['html'] . '
					</div>
					
					<div class="title_button_container" id="item_control" style="display:none;">
					';
								
					if( strtolower( $p->m_alias ) != "spr" )
					{
						$buttons = Common::getHtml( "get-admin-item-buttons", array( 'item_id' => $p->m_permission_id ) );
						
						$html .= '
						' . $buttons['html'];
					}
					else 
					{
						$no_edit = Common::getHtml( 'get-item-no-edit' );
						
						$html .= '
							' . $no_edit['html'];
					}
					
					$html .= '
						<div class="clear"></div>
						
					</div>
					
				</div>
				';
					
				$return = array( 'html' => $html );
				break;
				
			default:
				throw new Exception( "Error: Invalid HTML command: '" . $cmd . "'" );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	public static function getPermissions( $field, $value )
	{
		$i = 1;
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT permission_id
		FROM common_Permissions
		WHERE permission_id > 0 AND
		" . $field . " = " . $value . "
		ORDER BY alias ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[$i] = new Permission( $row[0], FALSE );
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
		$exclusions = array( 'm_permission_id' );

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