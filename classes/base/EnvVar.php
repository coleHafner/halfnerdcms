<?
/**
 * A class to handle a EnvVar record.
 * @since	20101004, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );

class EnvVar
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
	 * PK of the Record.
	 * @var	int
	 */
	protected $m_env_var_id;
	
	/**
	 * Tile of the record.
	 * @var	string
	 */
	protected $m_title;
	
	/**
	 * Body of the record.
	 * @var	string
	 */
	protected $m_content;
	
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
	 * @since	20101004, hafner
	 * @return	State
	 * @param	int				$env_var_id			id of the current record
	 */
	public function __construct( $env_var_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->setMemberVars( $env_var_id, $objects );
	}//constructor
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE, always.
	 * @since	20101004, hafner
	 * @return	boolean
	 */
	public function setMemberVars( $env_var_id, $objects )
	{
		$chosen_id = ( is_numeric( $env_var_id ) ) ? $env_var_id : 0;
		
		//get member vars
		$sql = "
		SELECT 
			env_var_id,
			title,
			content,
			active
		FROM 
			common_EnvVars
		WHERE 
			env_var_id = " . $chosen_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_env_var_id = $row['env_var_id'];
		$this->m_title = stripslashes( $row['title'] );
		$this->m_content = stripslashes( $row['content'] );
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects() : array(); 
		
		return TRUE;
		
	}//setMemberVars()
	
	/**
	* Get an array of data suitable to use in modify
	* @since 	20101004, hafner
	* @return 	array
	* @param 	boolean 		$fix_clob		whether or not to file member variables of CLOB type
	*/
	public function getDataArray( $fix_clob = TRUE ) 
	{
		return array(
			'env_var_id' => $this->m_env_var_id,
			'title' => $this->m_title,
			'content' => $this->m_content,
			'active' => $this->m_active
		);
		
	}//getDataArray()
	
	/**
	* Save with the current values of the instance variables
	* This is a wrapper to modify() to ease some methods of coding
	* @since 	20101004, hafner
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
	 * @since	20101004,hafner
	 * @return	mixed
	 * @param	array				$input				array of input data
	 */
	public function add( $input )
	{
		$this->checkInput( $input, TRUE );
		
		if( !$this->m_form->m_error )
		{
			//only set upload_timestamp on add
			$input['env_var_id'] = $this->m_common->m_db->insertBlank( 'common_EnvVars', 'env_var_id' );
			
			$this->m_env_var_id = (int) $input['env_var_id'];
			$return = $this->m_env_var_id;
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
	 * @since	20101004, hafner
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
				common_EnvVars
			SET 
				title = '" . $this->m_common->m_db->escapeString( $input['title'] ) . "',
				content = '" .  $this->m_common->m_db->escapeString( $input['content'] ) . "'
			WHERE 
				env_var_id = " . $this->m_env_var_id;
			
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
	 * @since	20101004, hafner
	 * @return	mixed
	 * @param	array				$input				array of input data 
	 */
	public function delete( $deactivate = TRUE )
	{
		if( $deactivate )
		{
			$sql = "
			UPDATE common_EnvVars
			SET active = 0
			WHERE env_var_id = " . $this->m_env_var_id;
		}
		else
		{
			$sql = "
			DELETE
			FROM common_EnvVars
			WHERE env_var_id = " . $this->m_env_var_id;
		}
		
		$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		return TRUE;
		
	}//delete()
	
	/**
	 * Validates the form input for creating/modifying a new File record.
	 * Returns FALSE on success, error message string otherwise.
	 * @since	20101004, hafner
	 * @return	mixed
	 * @param	array			$input			array of data
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public function checkInput( $input, $is_addition )
	{
		//check missing title
		if( !array_key_exists( "title", $input ) || 
			strlen( trim( $input['title'] ) ) == 0 )
		{
			$this->m_form->m_error = "You must select a title.";
		}

		//check duplicate title
		if( $is_addition )
		{
			if( !$this->m_form->m_error )
			{
				$dup_check = array( 
					'table_name' => "common_EnvVars",
					'pk_name' => "env_var_id",
					'check_values' => array( 'title' => strtolower( $input['title'] ) )
				);
				
				if( is_numeric( $this->m_common->m_db->checkDuplicate( $dup_check ) ) )
				{
					$this->m_form->m_error = "That title already exists";
				}
			}
			
		}//check duplicate title
		
		//check missing body
		if( !$this->m_form->m_error )
		{
			if( !array_key_exists( "content", $input ) || strlen( trim( $input['content'] ) ) == 0 )
			{
				$this->m_form->m_error = "You must fill in the content.";
			}
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	public function setLinkedObjects()
	{
		return array();
	}//setLinkedObjects()
	
	/**
	* Returns HTML
	* @author	20100908, Hafner
	* @return	array
	* @param	string		$cmd		determines which HTML snippet to return
	* @param	array		$vars		array of variables for the html
	*/
	public function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( $cmd ) )
		{
			case "get-add-mod-form":
				$return = array(
					'title' => "",
					'body' => '
					<div class="envvar_holder">
					
						<div id="result" class="result" style="height:15px;">
						</div>
						
						<form id="env_var_form">
							<input type="text" name="content" value="' . $vars['active_env_var']->m_content . '" class="text_input" style="width:400px;text-align:center;" clear_if="">
							<input type="hidden" name="title" value="' . $vars['active_env_var']->m_title . '"/>
						</form>
						 
						<br/>
						<a href="#" id="alter_env_var" action="' . $vars['action'] . '" env_var_id="' . $vars['active_env_var']->m_env_var_id . '">
							<img src="/images/btn_save.gif"/>
						</a>
						
					</div>'
				);
				break;
				
			default:
				throw new Exception( "Error: Command '" . $cmd . "' is invalid." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	/**
	* Get a member variable's value
	* @author	Version 20101004, hafner
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
	* @since	20101004, hafner
	* @return	mixed
	* @param	string		$var_name		Variable name to set
	* @param	string		$var_value		Value to set
	*/
	public function __set( $var_name, $var_value )
	{
		$exclusions = array( 'm_article_id' );

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