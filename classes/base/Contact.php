<?
/**
 * A class to handle a JobTitle record.
 * @since	20100618, hafner
 */


require_once( "base/Authentication.php" );
require_once( "base/Common.php" );
require_once( "base/ContactType.php" );
require_once( "base/File.php" );
require_once( "base/FormHandler.php" );


class Contact
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
	protected $m_contact_id;
	
	/**
	 * Contact Type from common_ContactTypes
	 * @var	int
	 */
	protected $m_contact_type_id;
	
	/**
	 * Id of the authentication record for this contact.
	 * @var	int
	 */
	protected $m_authentication_id;
	
	/**
	 * Id of the thumbnail file for this contact.
	 * @var	int
	 */
	protected $m_thumb_id;
	
	/**
	 * Id of the full image for this contact.
	 * @var	int
	 */
	protected $m_full_img_id;
	
	/**
	 * first name of the contact record.
	 * @var	string
	 */
	protected $m_first_name;
	
	/**
	 * last name of the contact record.
	 * @var	string
	 */
	protected $m_last_name;
	
	/**
	 * Description of the current contact.
	 * @var	string
	 */
	protected $m_description;
	
	/**
	 * Use Gravatar flag.
	 * @var	boolean
	 */
	protected $m_use_gravatar;
	
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
	 * @param	int				$contact_id		id of the current record
	 * @param	boolean			$objects			whether or not to populate the objects collection
	 */
	public function __construct( $contact_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->setMemberVars( $contact_id, $objects );
		
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20100618, hafner
	 * @return	boolean
	 
	 */
	public function setMemberVars( $contact_id, $objects )
	{
		$chosen_id = ( is_numeric( $contact_id ) ) ? $contact_id : 0;
		
		//get member vars
		$sql = "
		SELECT 
			contact_id,
			contact_type_id,
			authentication_id,
			contact_type_id,
			thumb_id,
			full_img_id,
			first_name,
			last_name,
			description, 
			use_gravatar,
			active
		FROM 
			common_Contacts
		WHERE 
			contact_id = " . $chosen_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//assign member vars
		$this->m_contact_id = $row['contact_id'];
		$this->m_contact_type_id = $row['contact_type_id'];
		$this->m_authentication_id = $row['authentication_id'];
		$this->m_thumb_id = $row['thumb_id'];
		$this->m_full_img_id = $row['full_img_id'];
		$this->m_first_name = $row['first_name'];
		$this->m_last_name = $row['last_name'];
		$this->m_description = stripslashes( $row['description'] ); 
		$this->m_use_gravatar = $this->m_common->m_db->fixBoolean( $row['use_gravatar'] );
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
			'contact_id' => $this->m_contact_id,
			'contact_type_id' => $this->m_contact_type_id,
			'authentication_id' => $this->m_authentication_id,
			'thumb_id' => $this->m_thumb_id,
			'full_img_id' => $this->m_full_img_id,
			'first_name' => $this->m_first_name,
			'last_name' => $this->m_last_name,
			'description' => $this->m_description,
			'use_gravatar' => $this->m_gravatar,
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
			$req_fields = array( 'contact_type_id' => 0, 'authentication_id' => 0, 'thumb_id' => 0, 'full_img_id' => 0 );
			$input['contact_id'] = $this->m_common->m_db->insertBlank( 'common_Contacts', 'contact_id', $req_fields );
			$this->m_contact_id = (int) $input['contact_id'];
			$return = $this->m_contact_id;
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
				common_Contacts
			SET 
				contact_type_id = " . $input['contact_type_id'] . ",
				first_name = '" . $this->m_common->m_db->escapeString( ucfirst( strtolower( $input['first_name'] ) ) ) . "',
				last_name = '" . $this->m_common->m_db->escapeString( ucfirst( strtolower( $input['last_name'] ) ) ) . "',
				description = '" . $this->m_common->m_db->escapeString( strtolower( $input['description'] ) ) . "',
				use_gravatar = " . $input['use_gravatar'] . "
			WHERE 
				contact_id = " . $this->m_contact_id;
			
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$return = $this->m_contact_id;
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
			//deactivate authentication record and all articles posted by this auth
			$this->m_objects_collection['authentication']->delete( TRUE );
			
			$sql = "
			UPDATE common_Contacts
			SET active = 0
			WHERE contact_id = " . $this->m_contact_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			//delete authentication
			$this->m_objects_collection['authentication']->delete();
			
			$sql = "
			DELETE
			FROM common_Contacts
			WHERE contact_id = " . $this->m_contact_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM common_Contacts
			WHERE contact_id = " . $this->m_contact_id;
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
		//check blank first name
		if( strlen( $input['first_name'] ) == 0 )
		{
			$this->m_form->m_error = "You must fill in the first name.";
		}
		
		//check blank last name
		if( !$this->m_form->m_error )
		{
			if( strlen( $input['last_name'] ) == 0 )
			{
				$this->m_form->m_error = "You must fill in the last name.";
			}
		}
		
		//check duplicate record
		if( $is_addition )
		{
			if( !$this->m_form->m_error )
			{
				$dup_check = array( 
					'table_name' => "common_Contacts",
					'pk_name' => "contact_id",
					'check_values' => array( 'first_name' => $input['first_name'], 'last_name' => $input['last_name'] )
				);
				
				if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
				{
					$this->m_form->m_error = "That contact already exists";
				}
			}
		}
		
		if( !$this->m_form->m_error )
		{
			if( strlen( $input['description'] ) == 0 )
			{
				$this->m_form->m_error = "You must choose a bio.";
			}
		}
		
		//check blank contact type
		if( !$this->m_form->m_error )
		{
			if( $input['contact_type_id'] == 0 )
			{
				$this->m_form->m_error = "You must choose a contact type.";
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
		return array(
			'contact_type' => new ContactType( $this->m_contact_type_id, TRUE ),
			'authentication' => new Authentication( $this->m_authentication_id ),
			'thumb_file' => new File( $this->m_thumb_id, TRUE ),
			'full_img_file' => new File( $this->m_full_img_id, TRUE )
		);
	}//populateObjectsCollection()
	
	/**
	* Adds auth id to this contact record.
	* @author	Version 20101018, hafner
	* @return	mixed
	* @param	int			$auth_id		pk of common_Authentication record
	*/
	public function addAuth( $auth_id )
	{
		$sql = "
		UPDATE common_Contacts
		SET authentication_id = " . $auth_id . "
		WHERE contact_id = " . $this->m_contact_id;
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//addAuth()
	
	/**
	* Adds file_id to this contact record.
	* @author	Version 20101018, hafner
	* @return	mixed
	* @param	int			$file_id		pk of common_Files record
	*/
	public function addThumb( $thumb_id )
	{
		$sql = "
		UPDATE common_Contacts
		SET thumb_id = " . $thumb_id . "
		WHERE contact_id = " . $this->m_contact_id;
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
	
	}//addThumb()
	
	/**
	* Adds full image id to this contact record.
	* @author	Version 20101018, hafner
	* @return	mixed
	* @param	int			$full_img_id		pk of common_Files record
	*/
	public function addFullImg( $full_img_id )
	{
		$sql = "
		UPDATE common_Contacts
		SET full_img_id = " . $full_img_id . "
		WHERE contact_id = " . $this->m_contact_id;
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//addFullImg()
	
	/**
	 * Gets all records.
	 * If records found, returns array of product category objects, FALSE otherwise.
	 * @since	20100923, Hafner
	 * @return	mixed
	 */
	public function getAllRecords( $constraints = array() )
	{
		$class = get_class( $this );
		$return = FALSE;
		
		$sql = "
		SELECT contact_id
		FROM common_Contacts
		WHERE active = 1
		ORDER BY last_name ASC";
		
		$records = $this->m_common->m_db->doQuery( $sql );
		
		if( is_array( $records ) )
		{
			foreach( $records as $i => $info )
			{
				$return[$i + 1] = new $class( $info['contact_id'], TRUE );
				
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
		$steps = array( 1 => "Set Details", 2 => "Set Thumbnail" );
		
		switch( strtolower( $cmd ) )
		{
			case "get-step-meter":
				$step_vars = array( 'steps' => $steps );
				$return = $this->m_common->getHtml( "get-step-meter", $step_vars );
				break;
				
			case "get-step-nav":
				$step_nav_vars = array( 
					'total_steps' => count( $steps ), 
					'current_step' => $vars['current_step'], 
					'link_trigger_id' => "contact-step" 
				);
				
				$return = $this->m_common->getHtml( "get-step-nav", $step_nav_vars );
				break;
				
			case "get-contact-details-form":
				
				$ct = new ContactType( 0 );
				$ct_dd = $ct->getHtml( 'contact-type-drop-down', array( 'contact_type_id' => $vars['active_contact']->m_contact_type_id ) );
				$link_vars = array( 'v' => "admin", 'sub' => "contacts", 'id1' => "manage-job-titles" );
				$url = $this->m_common->makeLink( $link_vars );
				$ct_add_link = '<a href="' . $url . '"> Manage </a>';
				
				$first_val = "First Name";
				$last_val = "Last Name";
				$bio_val = "Bio";
				
				if( $vars['active_contact']->m_contact_id > 0 )
				{
					$first_val =  $vars['active_contact']->m_first_name;
					$last_val =  $vars['active_contact']->m_last_name;
					$bio_val =  $vars['active_contact']->m_description;
				}
				
				$return = array(
					'body' => '
						<form id="contact_details_form">
							<table class="contact_table">
								<tr>
									<td colspan="2" id="result" class="result">
									
									</td>
								</tr>
								<tr>
									<td>
										<input type="text" name="first_name" id="first_name" value="' . $first_val . '" class="text_input" clear_if="First Name">
									</td>
									
									<td>
										<input type="text" name="last_name" id="last_name" value="' . $last_val . '" class="text_input" clear_if="Last Name">
									</td>
								</tr>
								<tr>
									<td>
										<br/>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<textarea name="description" id="description" class="text_input" style="height:200px;width:510px;" clear_if="Bio">' . $bio_val .'</textarea>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="text-align:center;padding-top:10px;">
										' . $ct_dd['body'] . ' ' . $ct_add_link . '
									</td>
								</tr>
								<tr>
									<td colspan="2" style="text-align:center;">
										<br/>
										<a href="#" id="alter_contact" action="' . $vars['action'] . '" contact_id="' . $vars['active_contact']->m_contact_id . '">
											<img src="/images/btn_save.gif"/>
										</a>
									</td>
								</tr>
							</table>
						</form>'
				);
				break;
				
			case "thumb-form":
				$file = new File( $vars['active_contact']->m_objects_collection['thumb_file']->m_file_id );
				$vars['active_file'] = $file;
				$vars['file_type_title'] = "contact_image";
				$vars['link_to'] = "contact_thumb";
				$vars['button_id'] = "upload_image";
				$vars['active_id'] = $vars['active_contact']->m_contact_id;
				
				$file_html = $file->getHtml( "get_file_upload_form", $vars );
				
				$warning = '
				This crew member has no <br/>
				current thumbnail image.';
				
				if( $file->m_file_id > 0 )
				{
					
					$warning = '
					<img src="' . $file->m_relative_path . '/' . $file->m_file_name . '" class="contact_thumb"/>
					';
				}
				
				$body = '
				<p class="file_upload_warning">
					<span style="color:#FF0000;">
						' . $warning . '
					</span>
				</p>
				
				<div id="result" class="result">
				</div>
				
				<div class="file_upload">
					' . $file_html['body'] . '
				</div>';
				
				$return = array(
					'title' => 'Upload Thumbnail Image',
					'body' => $body 
				);
				break;
				
			case "full-img-form":
				$file = new File( $vars['active_contact']->m_objects_collection['full_img_file']->m_file_id );
				$vars['active_file'] = $file;
				$vars['file_type_title'] = "contact_image";
				$vars['link_to'] = "contact_full_img";
				$vars['button_id'] = "upload_image";
				$vars['active_id'] = $vars['active_contact']->m_contact_id;
				
				$file_html = $file->getHtml( "get_file_upload_form", $vars );
				
				$warning = '
				This crew member has no <br/>
				current bio image.';
				
				if( $file->m_file_id > 0 )
				{
					
					$warning = '
					<img src="' . $file->m_relative_path . '/' . $file->m_file_name . '" class="contact_thumb"/>
					';
				}
				
				$body = '
				<p class="file_upload_warning">
					<span style="color:#FF0000;">
						' . $warning . '
					</span>
				</p>
				
				<div id="result" class="result">
				</div>
				
				<div class="file_upload">
					' . $file_html['body'] . '
				</div>';
				
				$return = array(
					'title' => 'Upload Bio Image',
					'body' => $body 
				);
				break;
				
			case "show-delete":
				$body = '
				<div style="position:relative;width:300px;padding:10px;">
					
					<table style="margin:auto;background-color:#111;border:2px solid #FF0000;padding:10px;width:250px;text-align:center;">
						
						<tr>
							<td>
								<img class="contact_thumb" src="/' . $vars['active_contact']->m_objects_collection['thumb_file']->m_relative_path . "/" . $vars['active_contact']->m_objects_collection['thumb_file']->m_file_name . '"/>
							</td>
						</tr>
						
						<tr>
							<td style="font-size:16px;vertical-align:top;color:#FF0000;text-align:center;">
								' . $vars['active_contact']->m_first_name . ' ' . $vars['active_contact']->m_last_name . '
							</td>
						</tr>
						
					</table>
					
					<div style="text-align:center;margin-top:10px;">
						<a href="#" id="alter_contact" contact_id="' . $vars['active_contact']->m_contact_id . '" action="delete">
							<img src="images/btn_delete.gif"/>
						</a>
					</div>
					
				</div>
				';
				
				$return = array(
					'title' => "Delete Crew Member", 
					'body' => $body 
				);
				break;
				
			case "show-crew-member":
				
				$img_link = "No image...";
				$c = $vars['active_contact'];
				
				if( is_object( $c->m_objects_collection['thumb_file'] ) &&
					$c->m_objects_collection['thumb_file']->m_file_id > 0 )
				{
					$img_link = '<img style="width:250px;margin:auto;" src="' . $c->m_objects_collection['thumb_file']->m_relative_path . '/' . $c->m_objects_collection['thumb_file']->m_file_name . '"/>';
				}
				
				$body = '
				<div style="position:relative;width:650px;height:400px;padding:10px;">
					
					<div style="position:relative;float:left;width:270px;border:2px solid #FF0000;background-color:#111;text-align:center;padding:10px;">
						' . $img_link . '
					</div>
					
					<div style="position:relative;float:left;width:300px;padding:10px;">
						' . $c->m_description . '
					</div>
					
						
				</div>
				';
				
				$return = array(
					'title' => $c->m_first_name . ' ' . $c->m_last_name . ' - ' . $c->m_objects_collection['contact_type']->m_title,
					'body' => $body
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
}//class JobTitle
?>