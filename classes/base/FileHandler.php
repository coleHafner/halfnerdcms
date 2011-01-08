<?

require_once( "base/Common.php" );

/**
* A class to manage files.
* @since	20100928, Hafner
*/
class FileHandler
{
	/**
	 * Instance of the Common class.
	 * @var	Common
	 */
	protected $m_common;
	
	/**
	* The file we are operating on or an associative array to the file
	* @var mixed
	*/
	protected $m_file;
	
	/**
	* The name of the file
	* @var string
	*/
	protected $m_file_name;
	
	/**
	* The file's MIME type
	* @var string
	*/
	protected $m_file_type;
	
	/**
	* File name while in the tmp directory
	* @var string
	*/
	protected $m_file_tmp_name;
	
	/**
	* Error related to uploads
	* @var string
	*/
	protected $m_file_error;
	
	/**
	* Size of the file
	* @var int
	*/
	protected $m_file_size;
	
	/** 
	* Sets this objects member variables.
	* @since 	20100928, Hafner
	* @return	FileHandler
	* @param	array			$file				Input array as the result of a PHP upload
	*/
	public function __construct( $file )
	{
		
		$this->m_common = new Common();
		
		// pass NULL for cases where an empty object is desired--no file was uploaded
		if( $file != NULL )
		{
			$this->m_file = $file;
			$this->m_file_name = $file['name'];
			$this->m_file_type = $file['type'];
			$this->m_file_tmp_name = $file['tmp_name'];
			$this->m_file_error = $file['error'];
			$this->m_file_size = $file['size'];
		}
		
	} // Constructor

	/**
	* Uploads a file to the upload location
	* @since 	Version 20100928, Hafner
	* @return	boolean
	* @param	string			$to_file		The name of the file
	* @param	string			$file_path		Path to file 
	* @param	string			$file_name		Unique file name ( determined @ File::getUniqueFileName() )
	*/
	public function uploadFile( $file_path, $file_name )
	{
		$return = FALSE; 
		$to_file = $file_path . "/" . $file_name;
		
		$cmd = "cp " . $this->m_file_tmp_name . " '" . $to_file . "'";
		
		//upload file
		exec( $cmd );
		
		//change permissions
		exec( "chmod 755 " . $to_file );
		
		//verify file exists
		if( file_exists( $file_path . "/" . $file_name ) )
		{
			$return = TRUE;
		}

		return $return;
		
	}//uploadFile()
	
	/**
	* Resize an image dynaimcally if its larger than the given dimensions
	* This only works with ImageMagick installed on the server
	* Always returns TRUE
	* @return	boolean
	* @since 	Version 20100928, Hafner
	* @param	string		$image					The image to resize
	* @param	string		$dimensions				Th maximum length and width in the format "LxW"
	*/
	public function resizeImage( $image, $dimensions )
	{
		require_once( "base/Common.php" );
		$common = new Common();
		$defaults = $common->defaults();
		$paths = $common->pathInfo();
		
		$split = explode( "x", $dimensions );
		$max_width = $split[0];
		$max_height = $split[1];
		
		$image_size = getimagesize( $image );
		// if any image dimension is larger than the desired size, resize
		if( $image_size[0] > $max_width || $image_size[1] > $max_height )
		{
			exec( $paths['full_mogrify'] . " -resize " . $max_width . "x" . $max_height . " '" . $image . "'" );
			exec( $paths['full_mogrify'] . " -colorspace rgb '" . $image . "'" );
		}
		
		return TRUE;
		
	}//resizeImage()
	
	/**
	* Attempts to delete files from a web interface using two different methods.
	* @return	bool
	* @since 	Version 20100928, Hafner
	* @param	string		$target_file			The file to delete
	*/
	public function deleteFile( $target_file )
	{
		$return = FALSE;
		exec( "rm " . $target_file );
		
		// check to see if the file is gone
		if( !file_exists( $target_file ) )
		{
			$return = TRUE;
		}
		
		return $return;
		
	} // end deleteFile()
	
	/**
	* Replaces non-alphanumeric characters with _ in the input file name.
	* @since 	20100928, Hafner
	* @return	boolean
	*/
	public function fixFileName()
	{
		$this->m_file_name = ereg_replace( '[^0-9a-zA-Z\.]', '_', $this->m_file_name );
		
		return TRUE;
		
	}//requiredFieldsReceived()
	
	public function validateUserPhoto()
	{
			
	}//validateUserPhoto()
	
	/*
	* Returns the variable name if access is allowed.  Returns FALSE otherwise.
	* @return	mixed
	* @since 	Version 20100928, Hafner
	* @param	string			$var_name			The variable of operation
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
			echo "<h3>Access to get member " . get_class( $this ) . "::" . $var_name . " denied.</h3><br/>\n";
			return FALSE;
		}
		
	} // __get()
	
	/**
	* @since 	20100928, Hafner
	* @return	boolean
	* @param	string			$var_name			The variable of operation
	* @param	mixed			$var_value			The value to which the variable is to be set
	*/
	public function __set( $var_name, $var_value )
	{
		$exclusions = array( "m_file", "m_file_type", "m_file_tmp_name", "m_file_size" );
		
		if( !in_array( $var_name, $exclusions ) )
		{
			$this->$var_name = $var_value;
			return TRUE;
		}
		else
		{
			echo "<b>Access to set member " . get_class( $this ) . "::" . $var_name . " denied.</b><br/>\n";
			return FALSE;
		}
	} // __set()

}//FileHandler class
?>