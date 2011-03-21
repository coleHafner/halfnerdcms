<?
/**
 * A class to handle a File record.
 * @since	20100618, hafner
 */

require_once( "base/Common.php" );
require_once( "base/FormHandler.php" );
require_once( "base/FileType.php" );
require_once( "base/FileHandler.php" );

class File
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
	protected $m_file_id;
	
	/**
	 * Id of the file_type related to this record.
	 * @var	int
	 */
	protected $m_file_type_id;
	
	/**
	 * Relative path to the file
	 * @var	string
	 */
	protected $m_relative_path;
	
	/**
	 * Name of the file.
	 * @var	string
	 */
	protected $m_file_name;
	
	/**
	 * Upload timestamp of the initial file upload.
	 * @var	string
	 */
	protected $m_upload_timestamp;
	
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
	 * @param	int				$file_id			id of the current record
	 */
	public function __construct( $file_id, $objects = FALSE )
	{
		$this->m_common = new Common();
		$this->m_form = new FormHandler( 1 );
		$this->m_file_id = ( is_numeric( $file_id ) && $file_id > 0 ) ? $file_id : 0;
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
			file_id,
			file_type_id,
			file_name,
			upload_timestamp,
			active
		FROM 
			common_Files
		WHERE 
			file_id = " . $this->m_file_id;
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = ( $this->m_common->m_db->numRows( $result ) > 0 ) ? $this->m_common->m_db->fetchAssoc( $result ) : array();
		
		//set member vars
		$this->m_file_id = $row['file_id'];
		$this->m_file_type_id = $row['file_type_id'];
		$this->m_file_name = $row['file_name'];
		$this->m_upload_timestamp = $this->m_common->convertTimestamp( $row['upload_timestamp'] );
		$this->m_active = $this->m_common->m_db->fixBoolean( $row['active'] );
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects() : array();
		
		//set relative path
		$file_type = new FileType( $this->m_file_type_id );
		$this->m_relative_path = $file_type->m_directory;
		
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
			'file_id' => $this->m_file_id,
			'file_type_id' => $this->m_file_type_id,
			'relative_path' => $this->m_relative_path,
			'file_name' => $this->m_file_name,
			'upload_timestamp' => $this->m_upload_timestamp,
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
			$req_fields = array( 'upload_timestamp' => strtotime( date( "Y-m-d h:i:s" ) ) );
			$input['file_id'] = $this->m_common->m_db->insertBlank( 'common_Files', 'file_id', $req_fields );
			$this->m_file_id = (int) $input['file_id'];
			$return = $this->m_file_id;
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
				common_Files
			SET 
				file_type_id = " . $input['file_type_id'] . ",
				file_name = '" . $input['file_name'] . "' 
			WHERE 
				file_id = " . $this->m_file_id;
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
			UPDATE common_Files
			SET active = 0
			WHERE file_id = " . $this->m_file_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		}
		else
		{
			$sql = "
			DELETE
			FROM mdp_Events
			WHERE file_id = " . $this->m_file_id;
			$this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			
			$sql = "
			DELETE
			FROM common_Files
			WHERE file_id = " . $this->m_file_id;
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
		//check file_type_id
		if( !array_key_exists( "file_type_id", $input ) ||
			$input['file_type_id'] == 0 )
		{
			$this->m_form->m_error = "You must select a file type.";
		}
		
		//check blank file_name
		if( !$this->m_form->m_error )
		{
			$this->m_form->checkBlank( $input['file_name'], "File Name" );
		}
		
		//check duplicate file_name
		if( !$this->m_form->m_error )
		{
			$dup_file_result = $this->checkDuplicateFile( $input['file_name'] );
			
			if( is_numeric( $dup_file_result ) )
			{
				$this->m_form->m_error = "That File already exists";
			}
		}
		
		return $this->m_form->m_error;
		
	}//checkInput()
	
	public function checkDuplicateFile( $file_name )
	{
		$dup_check = array( 
			'table_name' => "common_Files",
			'pk_name' => "file_id",
			'check_values' => array( 'file_name' => $file_name )
		);
			
		return $this->m_common->m_db->checkDuplicate( $dup_check );
		
	}//checkDuplicateFile()
	
	public function sendFile()
	{
		$paths = $this->m_common->getPathInfo();
		$full_path = $paths[$this->m_common->m_env]['absolute'] . "/" . $paths[$this->m_common->m_env]['web'];
		$file_ref = $full_path . "/" . $this->m_relative_path . "/" . $this->m_file_name;
		 
		if( file_exists( $file_ref ) )
		{
			//output file
			//get file type
			$file_split = explode( ".", $this->m_file_name );
			$file_type = $file_split[ count( $file_split ) - 1 ];
			
			$extensions_reference = array(
			//image
				'png'	=> "image/png",
				'gif'	=> "image/gif",
				'jpg' 	=> "image/jpeg",
				'jpeg'	=> "image/jpeg",
			//ms office
				'doc'	=> "application/msword",
				'docx'	=> "application/msword",
				'xls'	=> "application/vnd.ms-excel",
				'xlsx'	=> "application/vnd.ms-excel",
				'ppt'	=> "application/vnd.ms-powerpoint",
			//compression
				'zip'	=> "application/zip",
				'tar'	=> "application/x-tar",
				'bz2'	=> "application/x-bzip2",
				'gz'	=> "application/x-gzip",
			//other
				'pdf'	=> "application/pdf"
			);
			
			// send the file to the browser
			Header( "Content-type: " . $extensions_reference[$file_type]  );
			Header( "Content-Disposition: attachment; filename=\"" . $this->m_file_name . "\"" );
			Header( "Content-length: " . filesize( $file_ref ) );
			
			//send file contents
			$fp = fopen( $file_ref, "r" );
			assert( 'FALSE != $fp' );
	
			fpassthru( $fp );
			fclose( $fp );	
		}
		else
		{
			throw new exception( "Error: File '" . $file_ref . "' does not exist" );
		}
		
		return TRUE;
		
	}//sendFile()
	
	/**
	 * Sets linked objects.
	 * @since	20100718, hafner
	 * @return	array
	 */
	public function setLinkedObjects()
	{
		return array(
			'file_type' => new FileType( $this->m_file_type_id )
		);
	}//setLinkedObjects()
	
	/**
	* Returns HTML
	* @author	20100923, Hafner
	* @return	array
	* @param	string		$cmd		determines which HTML snippet to return
	* @param	array		$vars		array of variables for the html
	*/
	public function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( $cmd ) )
		{
			case "get-file-upload-form":
				
				$paths = $this->m_common->getPathInfo();
				
				$return = array( 'body' => '
					<div id="file_upload_container" style="display:none;">
						<div class="header center">
							Upload New File 
							<br/>
							<span class="normal_font">( or</span> 
							<a href="#" id="show_image_library" class="selected normal_font">
								choose image from library
							</a>
							<span class="normal_font">)</span>
						</div>
						
						<div id="result" class="result">
						</div>
						
						<p class="dashed_top dashed_bottom color_accent bottom_spacer center" style="padding:10px;">
							Uploading a new file will override <br/> the current file.
						</p>
						
						<div class="home_pic bottom_spacer center" style="margin-right:auto;">
							<img src="' . $paths[$this->m_common->m_env]['images'] . '/' . $this->m_file_name . '" />
						</div>
						
						<form id="file_upload_form" enctype="multipart/form-data" action="/ajax/halfnerd_helper.php?task=file&process=upload_file" target="hidden_frame" method="POST" >
						
							<div class="bottom_spacer">
								<input name="file_1" id="file_to_upload" type="file" style="width:200px;"/>
							</div>
							
							<input type="hidden" name="file_type" value="' . $vars['file_type_title'] . '" />
							<input type="hidden" name="active_id" value="' . $vars['active_id'] . '" />
							<input type="hidden" name="link_to" value="' . $vars['link_to'] . '" />
							<input type="hidden" id="file_is_unique" value="0" />
							
							<div class="bottom_spacer" style="position:relative;margin-top:20px;">
								<a href="#" id="upload_image">
									<div class="button center">
										Save
									</div>
								</a>
							</div>
							
						</form>
					</div>
					' 
				);
				break;
			
			case "get-image-library":
				$body = '
				<div id="image_library_container">
				
					<div class="header center">
						Select Image From Library 
						<br/>
						<span class="normal_font">( or</span> 
						<a href="#" id="show_file_upload" class="selected normal_font">
							upload new image
						</a>
						<span class="normal_font">)</span>
					</div>
					
					<div id="result" class="result">
					</div>
					
					<p class="dashed_top dashed_bottom color_accent bottom_spacer center" style="padding:10px;">
						Click on a file to select.
					</p>
					
					<div class="img_lib_scroller">
					';
					
				$files = $this->getFileByType( "image" );
				$paths = $this->m_common->getPathInfo();

				foreach( $files as $i => $info )
				{
					$body .= '
						<div class="home_pic bottom_spacer center img_library_image" style="margin-right:auto;" article_id="' . $vars['active_id'] .'" file_id="' . $info['file_id'] . '" >
							<img src="' . $paths[$this->m_common->m_env]['images'] . '/' . $info['file_name'] . '" />
						</div>
					';
				}
				
				$body .= '
					</div>
				</div>
				';
				
				$return = array( 'body' => $body );
				break;
				
			default:
				throw new Exception( "Error: Command '" . $cmd . "' is invalid." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	public function getFileByType( $file_type_title )
	{
		$return = array();
		
		$sql = "
		SELECT file_name, file_id
		FROM common_Files
		WHERE file_type_id = (
			SELECT file_type_id
			FROM common_FileTypes
			WHERE LOWER( title ) = '" . strtolower( trim( $file_type_title ) ) . "' )";
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $this->m_common->m_db->fetchAssoc( $result ) )
		{
			$return[] = $row;
		}
		
		return $return;
		
	}//getFileByType()
	
	/**
	 * Gets all records.
	 * If records found, returns array of product category objects, FALSE otherwise.
	 * @since	20100923, Hafner
	 * @return	mixed
	 */
	public function getAllRecords( $constraints = array() )
	{
		$class = get_class( $this );
		$constraint = "";
		$return = FALSE;
		
		//compile constraints
		if( array_key_exists( 'file_type_title', $constraints ) )
		{
			$table_info = array( 'pk_name' => "file_type_id", 'table' => "common_FileTypes", 'title_field' => "title" ); 
			$file_type_id = $this->m_common->m_db->getIdFromTitle( $constraints['file_type_title'], $table_info );
			
			if( $file_type_id > 0 )
			{
				$constraint = " AND 
				file_type_id = " . $file_type_id;
			}
		}
		
		$sql = "
		SELECT 
			product_category_id AS product_category_id
		FROM 
			sbc_ProductCategories
		WHERE
			active = 1" . $constraint . "
		ORDER BY
			title ASC";
		
		$records = $this->m_common->m_db->doQuery( $sql );
		
		if( is_array( $records ) )
		{
			foreach( $records as $info )
			{
				$return[] = new $class( $info['product_category_id'], TRUE );
			}
		}
		
		return $return;
		
	}//getAllRecords()
	
	/**
	 * Uploads a file.
	 * Always returns TRUE.
	 * @since	20100928, Hafner
	 * @return	boolean
	 * @param	array			$post		$_POST array
	 * @param	array			$files		$_FILES array
	 */
	public function doFileUpload( $post, $files )
	{
		$return = 0;
		$fh = new FileHandler( $files['file_to_upload'] );
		$file_type = new FileType( $post['file_type_id'] );
		
		if( $fh->m_file_error == 0 &&
			$fh->m_file_size > 0 )
		{
			$paths = $this->m_common->getPathInfo();
			$upload_path = $paths[$this->m_common->m_env]['absolute'] . "/" . $paths[$this->m_common->m_env]['web'] . $file_type->m_directory;
			$file_name = ( array_key_exists( "unique_file_name", $post ) ) ? $post['unique_file_name'] : self::getUniqueFileName(); 
			$file_name .= "." . self::extractFileExtension( $fh->m_file_name );
			
			//do upload
			$file_upload_result = $fh->uploadFile( $upload_path, $file_name );
			
			if( $file_upload_result )
			{
				//make new file
				$input = array(
					'file_name' => $file_name,
					'upload_timestamp' => strtotime( "now" ),
					'file_type_id' => $file_type->m_file_type_id
				);
				
				$file = new File( 0 );
				$file_add_result = $file->add( $input );
				
				//link file to slideshow
				if( is_numeric( $file_add_result ) ) 
				{
					$return = $file->m_file_id;
				}
				else
				{
					throw new Exception( "Error: File add error. File class said '" . $file_add_result . "'" );
				}
			}
			else 
			{
				throw new Exception( "Error: File upload error. FileHandler class said '" . $file_upload_result . "'" );
			}	
		}
		else 
		{
			throw new exception( "File upload error. PHP said: " . $fh->m_file_error );
		}
		
		return $return;
		
	}//doFileUpload()
	
	public static function getUniqueFileName()
	{
		//vars
		$is_unique = FALSE;
		$common = new Common();
		
		//get unique name
		while( !$is_unique )
		{
			$return = strtolower( substr( md5( strrev( microtime() ) ), 0, 10 ) );
			
			$sql = "
			SELECT count(*)
			FROM common_Files
			WHERE LOWER( file_name ) LIKE '" . $return . "%'";
			
			$result = $common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $common->m_db->fetchRow( $result );
			$is_unique = ( $row[0] == 0 ) ? TRUE : FALSE;
		}
		
		return $return;
		
	}//getUniqueFileName()
	
	public static function extractFileExtension( $file_name ) 
	{
		$name_split = explode( ".", $file_name );
		$index = count( $name_split ) - 1;
		return $name_split[$index];
	}//extractFileExtension()
	
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
		$exclusions = array( 'm_file_id' );

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
}//class File
?>