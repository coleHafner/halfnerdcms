<?
/**
 * A class to handle a File record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );
require_once( "base/File.php" );

class View
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
	protected $m_view_id;
	
	/**
	 * ID of the parent view of the current view.
	 * @var	int
	 */
	protected $m_parent_view_id;
	
	/**
	 * Array of Sub View Ids.
	 * @var	array
	 */
	protected $m_sub_view_ids;
	
	/**
	 * Id of the file_type related to this record.
	 * @var	int
	 */
	protected $m_controller_name;
	
	/**
	 * Id of the image for the nav.
	 * @references	common_Files( file_id )
	 * @var	int
	 */
	protected $m_nav_image_id;
	
	/**
	 * Whether or not to show this view in the master nav.
	 * @var	boolean
	 */
	protected $m_show_in_nav;
	
	/**
	 * Alias of this view.
	 * @var	string
	 */
	protected $m_alias;
	
	/**
	 * External link.
	 * @var	string
	 */
	protected $m_external_link;
	
	/**
	 * Priority of this view in the master nav.
	 * @var	int
	 */
	protected $m_nav_priority;
	
	/**
	 * Whether or not this view need to be authenticated.
	 * @var	boolean
	 */
	protected $m_requires_auth;
	
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
	 * @param	int				$view_id			id of the current record
	 */
	public function __construct( $view_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_view_id = ( is_numeric( $view_id ) ) ? $view_id : 0; 
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
			view_id,
			parent_view_id,
			controller_name,
			alias,
			external_link,
			nav_image_id,
			show_in_nav,
			requires_auth,
			nav_priority,
			active
		FROM 
			common_Views
		WHERE 
			view_id = " . $this->m_view_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		$row = array_change_key_case( $row, CASE_LOWER );

		//set member vars
		$this->m_view_id = $row['view_id'];
		$this->m_parent_view_id = $row['parent_view_id'];
		$this->m_controller_name = stripslashes( $row['controller_name'] );
		$this->m_alias = stripslashes( $row['alias'] );
		$this->m_external_link = strtolower( $row['external_link'] );
		$this->m_nav_priority = ( !is_null( $row['nav_priority'] ) ) ? $row['nav_priority'] : 0;
		$this->m_nav_image_id = ( !is_null( $row['nav_image_id'] ) ) ? $row['nav_image_id'] : 0;
		$this->m_show_in_nav = $this->m_common->m_db->fixBoolean( $row['show_in_nav'] );
		$this->m_requires_auth = $this->m_common->m_db->fixBoolean( $row['requires_auth'] );
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_sub_view_ids = $this->setSubViewIds();
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
			'view_id' => $this->m_view_id,
			'parent_view_id' => $this->m_parent_view_id,
			'controller_name' => $this->m_controller_name,
			'alias' => $this->m_alias,
			'external_link' => $this->m_external_link,
			'nav_priority' => $this->m_nav_priority,
			'nav_image_id' => $this->m_nav_image_id,
			'show_in_nav' => $this->m_show_in_nav,
			'requires_auth' => $this->m_requires_auth,
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
			$req_fields = array( 'parent_view_id' => 0 );
			$input['view_id'] = $this->m_common->m_db->insertBlank( 'common_Views', 'view_id', $req_fields );
			$this->m_view_id = (int) $input['view_id'];
			$this->modify( $input, TRUE );
			$return = $this->m_view_id;
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
			$priority = $this->getAutoPriority();
			$show_in_nav = ( array_key_exists( "show_in_nav", $input ) ) ? "1" : "0";
			$requires_auth = ( array_key_exists( "requires_auth", $input ) ) ? "1" : "0";
			
			//external link
			$link = "NULL";
			
			//controller name
			$controller_name = "NULL";
			
			if( array_key_exists( "external_link", $input ) && 
				strlen( $input['external_link'] ) > 0 && 
				strtolower( trim( $input['external_link'] ) ) != "external link" )
			{
				$link = "'" . $input['external_link'] . "'";
			}
			else
			{
				if( array_key_exists( "controller_name", $input ) && 
					strlen( $input['controller_name'] ) > 0 && 
					strtolower( trim( $input['controller_name'] ) ) != "controller class name" )
				{
					$controller_name = "'" .  $this->m_common->m_db->escapeString( $input['controller_name'] ) . "'";
				}
			}
			
			$sql = "
			UPDATE 
				common_Views
			SET 
				controller_name = " .  $controller_name . ",
				alias = '" . $this->m_common->m_db->escapeString( $input['alias'] ) . "',
				nav_priority = " . $priority . ",
				show_in_nav = " . $show_in_nav . ",
				requires_auth = " . $requires_auth . ",
				external_link = " . $link . "
			WHERE 
				view_id = " . $this->m_view_id;
				
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_view_id;
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
			UPDATE common_Views
			SET active = 0
			WHERE view_id = " . $this->m_view_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM common_Articles
			WHERE view_id = " . $this->m_view_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM common_Views
			WHERE view_id = " . $this->m_view_id;
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
		if( !array_key_exists( "alias", $input ) || 
			strlen( trim( $input['alias'] ) ) == 0 ||
			strtolower( trim( $input['alias'] ) ) == "nav display name" )
		{
			$this->m_form->m_error = "You must select a Nav Display Name.";
		} 
		
		//check duplicate alias
		if( !$this->m_form->m_error )
		{
			if( $is_addition )
			{
				$dup_check = array( 
					'table_name' => "common_Views",
					'pk_name' => "view_id",
					'check_values' => array( 'alias' => strtolower( $input['alias'] ) )
				);
				
				if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
				{
					$this->m_form->m_error = "That Nav Display Name already exists.";
				}
			}
		}
		
		if( !array_key_exists( "external_link", $input ) ||
			strlen( trim( $input['external_link'] ) ) == 0 ||
			strtolower( trim( $input['external_link'] ) ) == "external link" )
		{
			//check missing controller name
			if( !$this->m_form->m_error )
			{
				if( !array_key_exists( "controller_name", $input ) || 
					strlen( trim( $input['controller_name'] ) ) == 0 ||
					strtolower( trim( $input['controller_name'] ) ) == "controller class name" )
				{
					$this->m_form->m_error = "You must select a Controller Class Name.";
				} 
			}
			
			//check valid controller name
			if( !$this->m_form->m_error )
			{
				if( strpos( $input['controller_name'], " " ) !== FALSE ||
					strpos( $input['controller_name'], "," ) !== FALSE ||
					strpos( $input['controller_name'], "." ) !== FALSE )
				{
					$this->m_form->m_error = "Controller Class Name must contain only letters.";
				}
			}
			
			//check duplicate controller name
			if( !$this->m_form->m_error )
			{
				if( $is_addition )
				{
					$dup_check = array( 
						'table_name' => "common_Views",
						'pk_name' => "view_id",
						'check_values' => array( 'controller_name' => strtolower( $input['controller_name'] ) )
					);
					
					if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
					{
						$this->m_form->m_error = "That Controller Class Name already exists.";
					}
				}
			}
			
			//check reserved controller names
			if( !$this->m_form->m_error )
			{
				if( strtolower( trim( $input['controller_name'] ) ) == "controllertemplate" )
				{
					$this->m_form->m_error = "'" . $input['controller_name'] . "' is a reserved name. Please choose a different name.";
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
		$class = get_class( $this ); 
		$return = array( 'file' => new File( $this->m_nav_image_id ), 'sub_views' => array() );
		
		foreach( $this->m_sub_view_ids as $i => $info )
		{
			$return['sub_views'][] = new $class( $info['view_id'] );
		}
		
		return $return;
		
	}//setLinkedObjects()
	
	public function setSubViewIds()
	{
		$sql = "
		SELECT view_id
		FROM common_Views
		WHERE parent_view_id = " . $this->m_view_id . " AND
		active = 1";
		
		$return = $this->m_common->m_db->doQuery( $sql, TRUE );
		
		if( !is_array( $return ) )
		{
			$return = array();
		}
		
		return $return;
		
	}//getSubViewIds()
	
	/**
	 * Adds an file to this view record. 
	 * Returns TRUE if nav_image_id is valid, FALSE if not.
	 * @since	20100915, Hafner
	 * @return	boolean
	 * @param 	int 		$nav_image_id		image id to be added ( should be an existing file_id @ 'common_Files' )
	 */
	public function addNavImageId( $nav_image_id )
	{
		$return = TRUE;
		$constraints = array( 
			'table_name' => "common_Files",
			'pk_name' => "file_id",
			'pk_value' => $nav_image_id 
		);
		
		if( !is_numeric( $nav_image_id ) || 
			!$this->m_common->m_db->keyExists( $constraints ) )
		{
			$return = FALSE;
		}
		else
		{
			$sql = "
			UPDATE common_Views
			SET nav_image_id = " . $nav_image_id . "
			WHERE view_id = " . $this->m_view_id;
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		
		return $return;
		
	}//addNavImageId()
	
	public function addParentViewId( $parent_view_id )
	{
		//validate parent view id exists
		$constraints = array( 
			'table_name' => "common_Views", 
			'pk_name' => "view_id", 
			'pk_value' => $parent_view_id );
		
		if( $parent_view_id > 0 &&
			$this->m_common->m_db->keyExists( $constraints ) )
		{
			$sql = "
			UPDATE common_Views
			SET parent_view_id = " . $parent_view_id . "
			WHERE view_id = " . $this->m_view_id;
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			throw new exception( "Error: Parent View ID '" . $parent_view_id . "' is invalid. ( is zero or does not exist @ table '" . $constraints['table_name'] . " )." );
		}
		
		return TRUE;
		
	}//addParentViewId()
	
	/**
	 * Gets all views.
	 * Returns an array of view objects.
	 * @since	20100907, Hafner
	 * @return	array
	 * @param	boolean			$nav_only		if TRUE, constrains query to only views that 'show_in_nav' == 1
	 */
	public function getAllRecords( $nav_only = FALSE )
	{
		$class = get_class( $this );
		$constraint = '';
		$return = FALSE;
		
		if( $nav_only )
		{
			$constraint = "AND show_in_nav = 1";
		}
		
		$sql = "
		SELECT 
			view_id AS view_id,
			controller_name AS controller_name
		FROM 
			common_Views
		WHERE
			active =1 AND
			view_id != 0 AND 
			parent_view_id = 0
			" . $constraint . "
		ORDER BY 
			nav_priority ASC";
		
		
		$records = $this->m_common->m_db->doQuery( $sql );
		
		if( is_array( $records ) )
		{
			foreach( $records as $i => $info )
			{
				$return[$info['controller_name']] = new $class( $info['view_id'], TRUE );
			}
		}
				
		return $return;
		
	}//getAllRecords()
	
	/**
	 * Updates this view's external link.
	 * @author	20101129, Hafner
	 * @return	boolean
	 * @param	string		$external_link		external link for this view
	 */
	public function updateExternalLink( $external_link )
	{
		if( $this->m_view_id == 0 )
		{
			throw new Exception( "Error: View id must be > 0." );
		}
		
		$sql = "
		UPDATE common_Views
		SET external_link = '" . $external_link . "'
		WHERE view_id = " . $this->m_view_id;
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//updateExternalLink()
	
	public function getAutoPriority()
	{
		$sql = "
		SELECT count(*)
		FROM common_Views
		WHERE show_in_nav = 1";
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $this->m_common->m_db->fetchRow( $result );
		
		return (int)$row[0] + 1;
		
	}//getAutoPriority()
	
	public static function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( trim( $cmd ) ) )
		{
			case "get-view-form":
			
				$v = $vars['active_record'];
				
				//external link
				$link = "No";
				$link_class = "font_no";
				
				if( strlen( $v->m_external_link ) > 0 ) 
				{
					$link = $v->m_external_link;
					$link_class = "bold";
				}
				
				//file info
				if( $link == "No" )
				{
					//file location
					$file_name = $v->m_controller_name . ".php";
					$controller_location = $v->m_common->compileControllerLocationBasePath();
					
					//file exists
					$file_exists = "No";
					$file_class = "font_no";
					
					//require_once( "controllers/" . $file_name );
					//class_exists( ucfirst( strtolower( $v->m_controller_name ) ) ) 
					
					if( $v->m_common->controllerFileExists( $file_name ) )
					{
						$file_exists = "Yes";
						$file_class = "font_yes";
					}
				}
				
				//requires login
				$login = "No";
				$login_class = "font_no";
				
				if( $v->m_requires_auth )
				{
					$login = "Yes";
					$login_class = "font_yes";
				}
				
				//show in nav
				$nav_show = "No";
				$nav_class = "font_no";
				
				if( $v->m_show_in_nav )
				{
					$nav_show = "Yes";
					$nav_class = "font_yes";
				}
				
				$html = '
				<div class="header color_accent">
					' . $v->m_alias . '
				</div>
				
				<div>
					<table>
					';
					
				if( $link == "No" )
				{
						$html .= '
						<tr>
							<td class="padder"> 
								File Exists: <span class="' . $file_class . '">' . $file_exists . '</span>
							</td>
							<td class="padder" colspan="2">
								File Name: <span class="bold">' . $file_name . '</span>
							</td>
						</tr>
						';
				}
				
				$html .= '
						<tr>
							<td class="padder"> 
								Show In Nav: <span class="' . $nav_class . '">' . $nav_show . '</span>
							</td>
							<td class="padder">
								Requires Login: <span class="' . $login_class . '">' . $login . '</span>
							</td>
							';
				if( $link == "No" )	
				{
					$html .= '
							<td>
								External Link: <span class="' . $link_class . '">' . $link . '</span>
							</td>
							';
				}
							
				$html .= '
						</tr>
						';
						
				if( $link == "No" )
				{
					$html .= '
						<tr>
							<td class="padder" colspan="3">
								Location: <span class="bold">' . $controller_location . '</span>
							</td>
						</tr>
						';
				}
				else
				{
					$html .= '
						<tr>
							<td colspan="3" class="padder">
								External Link: <span class="' . $link_class . '">' . $link . '</span>
							</td>
						</tr>
						';				
				}
					
				$html .= '
					</table>
					
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
			
			case "get-edit-form":
			
				$v = $vars['active_record'];
				
				if( $v->m_view_id > 0 )
				{
					$process = "modify";
					$alias = ( strlen( $v->m_alias ) > 0 ) ? $v->m_alias : "Nav Display Name";
					$external_link = ( strlen( $v->m_external_link ) > 0 ) ? $v->m_external_link : "External Link";
					$show_in_nav = ( $v->m_show_in_nav ) ? 'checked="checked"' : "";
					$requires_auth = ( $v->m_requires_auth ) ? 'checked="checked"' : "";
					$from_add = "0";				
					
					if( strlen( $v->m_external_link ) == 0 || 
						is_null( $v->m_external_link ) )
					{
						
						$controller_name = $v->m_controller_name;
						
						if( $v->m_common->controllerFileExists( $v->m_controller_name . ".php" ) )
						{
							$controller_disabled = 'disabled="disabled"';
							$disabled_class = "font_example";
							$hidden_controller_name_input = '<input type="hidden" name="controller_name" value="' . $v->m_controller_name . '" />';
							
						}
						else
						{
							$disabled_class = "";
							$controller_disabled = "";
							$hidden_controller_name_input = "";
						}
					}
					else
					{
						$controller_name = "No Controller Required";
						$controller_disabled = 'disabled="disabled"';
						$disabled_class = "font_example";
						$hidden_controller_name_input = '<input type="hidden" name="controller_name" value="' . $controller_name . '" />';
					}
					
				}
				else
				{
					$process = "add";
					$alias = "";
					$controller_name = "";
					$external_link = "";
					$show_in_nav = "";
					$requires_auth = "";
					$controller_disabled = '';
					$disabled_class = '';
					$hidden_controller_name_input = '';
					$from_add = "1";
				}
				
				//show in nav
				$show_in_nav_html = '
				<div>
					<input type="checkbox" name="show_in_nav" class="" ' . $show_in_nav . ' /> Show in Nav
				</div>
				';
				
				//requires auth
				$requires_auth_html = '
				<div>
					<input type="checkbox" name="requires_auth" class="" ' . $requires_auth . ' />Requires Login
				</div>
				';
				
				$return = array( 'html' => '
				' . Common::getHtml( "title-bar", array( 'title' => ucWords( $process ) . " View", 'classes' => '' ) ) . '
				<form id="view_form_' . $v->m_view_id . '">
					<div>
						<div style="position:relative;float:left;width:35%;">
							<div class="padder_10">
								
								<span class="title_span">
									Nav Display Name:
								</span>
								
								<input type="text" name="alias" class="text_input text_long" value="' . $alias . '" />
							</div>
							
							<div class="padder_10 padder_no_top">
								
								<span class="title_span">
									Controller File Name:
								</span>
								
								<input type="text" name="controller_name" class="text_input text_long ' . $disabled_class . '" value="' . $controller_name . '" ' . $controller_disabled . ' />
							</div>
							
							<div class="padder_10 padder_no_top">
								
								<span class="title_span">
									External Link:
								</span>
								
								<input type="text" name="external_link" class="text_input text_long" value="' . $external_link . '" />
							</div>
						</div>
						
						<div style="position:relative;float:left;width:65%;padding-top:50px;">
						
							<div class="padder_10">
								' . Common::getHtml( "selector-module", array( 
									'title' => "Show Page In Main Nav?", 
									'content' => $show_in_nav_html,
									'content_class' => "padder_10_top" ) ) . '
									
								' . Common::getHtml( "selector-module-spacer", array() ) . '
								
								' . Common::getHtml( "selector-module", array( 
									'title' => "Does Page Require Login?", 
									'content' => $requires_auth_html,
									'content_class' => "padder_10_top" ) ) . '
									
								<div class="clear"></div>
								
							</div>
							
						</div>
						
						<div class="clear"></div>
						
					</div>
					
					<input type="hidden" name="from_add" value="' . $from_add . '" />
					' . $hidden_controller_name_input . '
				
					' . Common::getHtml( "get-form-buttons", array( 
					
						'left' => array( 
							'pk_name' => "view_id",
							'pk_value' => $v->m_view_id,
							'process' => $process,
							'id' => "view",
							'button_value' => ucwords( $process ),
							'extra_style' => 'style="width:41px;"' ),
							
						'right' => array(
							'pk_name' => "item_id",
							'pk_value' => $v->m_view_id,
							'process' => "view",
							'id' => "list_item",
							'button_value' => "Cancel" ) 
						) 
					) . '
				</form>'
				
				);
				break;
				
			case "get-delete-form":
			
				$v = $vars['active_record'];
				
				
				$return = array( 'html' => '
					<div class="padder_10">
						' . Common::getHtml( "title-bar", array( 'title' => "Really Delete View?", 'classes' => '' ) ) . '
						
						<div class="button_container">
							' . Common::getHtml( "get-form-buttons", array( 
						
								'left' => array( 
									'pk_name' => "view_id",
									'pk_value' => $v->m_view_id,
									'process' => "delete",
									'id' => "view",
									'button_value' => "Delete" ),
									
								'right' => array(
									'pk_name' => "item_id",
									'pk_value' => $v->m_view_id,
									'process' => "view",
									'id' => "list_item",
									'button_value' => "Cancel" ) 
								) 
							) . '
						</div>
					</div>
					'
				);
				break;
				
			case "get-reorder-form":
				
				$return = array( 'html' => '
					' . Common::getHtml( "title-bar", array( 'title' => "Reorder Navigation", 'classes' => '' ) ) . '	
					<div class="center">
						Drag and drop the items below to reorder the pages in the main navigation.
					</div>
					
					<div class="button_container" style="margin-top:10px;">
						' . Common::getHtml( "get-form-buttons", array( 
					
							'left' => array( 
								'pk_name' => "view_id",
								'pk_value' => "0",
								'process' => "reorder",
								'id' => "view",
								'button_value' => "Save Order" ),
								
							'right' => array(
								'pk_name' => "view_id",
								'pk_value' => $v->m_view_id,
								'process' => "cancel_reorder",
								'id' => "view",
								'button_value' => "Cancel",
								'extra_style' => 'style="width:61px;"' ) 
							) 
						) . '
					</div>
					'
				);
				break;
				
			case "get-admin-list-item":
				
				$v = $vars['active_record'];
				$view_form = View::getHtml( "get-view-form", array( 'active_record' => $v ) );
				$mod_form = View::getHtml( "get-edit-form", array( 'active_record' => $v ) );
				$delete_form = View::getHtml( "get-delete-form", array( 'active_record' => $v ) );
				$hover_enabled = ( array_key_exists( "hover_enabled", $vars['options'] ) && $vars['options']['hover_enabled'] == TRUE ) ? "1" : "0";
				$li_classes = Common::getHtml( "get-admin-list-item-classes", array( 'type' => "page" ) );
				
				$html = '
				<div class="' . $li_classes['html'] . '" hover_enabled="' . $hover_enabled . '">
							
					<div id="item_view_' . $v->m_view_id . '">						
						' . $view_form['html'] . '
					</div>
										
					<div id="item_mod_' . $v->m_view_id . '" style="display:none;">
						' . $mod_form['html'] . '
					</div>
					
					<div id="item_delete_' . $v->m_view_id . '" style="display:none;">
						' . $delete_form['html'] . '
					</div>
					
					<div class="title_button_container" id="item_control" style="display:none;">
					';
					
					if( strtolower( $v->m_controller_name ) != "admin" )
					{
						$buttons = Common::getHtml( "get-admin-item-buttons", array( 'item_id' => $v->m_view_id ) );
						
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
					';
					
					if( $vars['options']['list_type'] == "reorder" )
					{
						$html .= Common::getHtml( "get-reorder-tab", array() );
					}
					
				$html .= '
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
	
	public static function getViews( $field, $value )
	{
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT view_id
		FROM common_Views
		WHERE view_id > 0 AND
		" . $field . " = " . $value . "
		ORDER BY nav_priority ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[] = new View( $row[0], FALSE );
		}
		
		return $return;
		
	}//getViews()
	
	public static function getNavViews()
	{
		$return = array();
		$common = new Common();
		
		$sql = "
		SELECT view_id
		FROM common_Views
		WHERE show_in_nav = 1 AND 
		active = 1 
		ORDER BY nav_priority ASC";
		
		$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $common->m_db->fetchRow( $result ) )
		{
			$return[] = new View( $row[0], FALSE );
		}
		
		return $return;
		
	}//getNavViews()
	
	public function updateViewPriorities( $id_priority_string )
	{
		
		//reset all nav_priorities
		$sql = "
		UPDATE common_Views
		SET nav_priority = 0";
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		//get array from string
		$id_priority_array = explode( "^", $id_priority_string );
		
		foreach( $id_priority_array as $i => $view_id )
		{
			$sql = "
			UPDATE common_Views 
			SET nav_priority = " . ( $i + 1 ) . " 
			WHERE view_id = " . $view_id;
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		
		return TRUE;
		
	}//updateViewPriorities()
	
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
		$exclusions = array( 'm_view_id' );

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