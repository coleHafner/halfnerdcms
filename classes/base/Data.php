<?
/**
 * A class for importing data into the simple bike co database.
 * @since	20100618, hafner
 */

require_once( "base/Article.php" );
require_once( "base/Captcha.php" );
require_once( "base/Common.php" );
require_once( "base/File.php" );
require_once( "base/FileType.php" );
require_once( "base/Section.php" );
require_once( "base/State.php" );
require_once( "base/View.php" );

class Data
{
	/**
	 * Instance of the Common class.
	 * @var Common
	 */
	protected $m_common;
	
	/**
	 * Path to the import file.
	 * @var string
	 */
	protected $m_path;
	
	/**
	 * Name of the current import file.
	 * @var string
	 */
	protected $m_file_name;
	
	/**
	 * Constructs the SsrData Object.
	 * @return 	SsrData
	 * @since 	20100618, hafner
	 * @param	string 		$file_name		name of the current import file
	 */
	public function __construct( $file_name )
	{
		//set the member vars
		$this->setMemberVars( $file_name );
		
	}//constructor()
	
	/**
	 * Sets the member variables for this class.
	 * Returns TRUE if file name is valid and exists, throws exception otherwise.
	 * @since 	20100618, hafner
	 * @return 	boolean
	 * @param 	string			$file_name		name of file to import
	 */
	public function setMemberVars( $file_name )
	{
		//create common object
		$this->m_common = new Common();
		
		//get path info
		$paths = $this->m_common->getPathInfo();
		
		//set path
		$this->m_path = $paths[$this->m_common->m_env]['absolute'] . "/data";
		
		//set file name
		$this->m_file_name = $file_name;
		
		//validate file existence
		if( !file_exists( $this->m_path . "/" . $this->m_file_name  ) )
		{
			throw new Exception( "File " . $this->m_path . "/" . $this->m_file_name . " does not exist ! Please provide a valid file name." );
		}
		
		return TRUE;
		
	}//setMemberVars()
	
	/**
	 * Converts a file into an array.
	 * @since 	20100618, hafner
	 * @return 	array of file lines broken into arrays
	 */
	public function convertFile()
	{
		$return = array();
		$fp = @fopen( $this->m_path . "/" . $this->m_file_name, "r" );
		switch( $this->m_file_name )
		{
			case "states.csv":
				$indexes = array( 
					"state_full_name", 
					"state" 
				);
				$return = $this->pairLines( $indexes, $fp );
				break;
				
			case "files.csv":
				$indexes = array( 
					"file_name", 
					"relative_path", 
					"file_type_title", 
					"file_title" 
				);
				$return = $this->pairLines( $indexes, $fp );
				break;
				
			case "products.csv":
				$indexes = array(
					"title", 
					"product_category_title",
					"img_thumb_name",
					"img_full_name",
					"price",
					"description"
				);
				$return = $this->pairLines( $indexes, $fp );
				break;
				
			case "prodCatInfo.csv":
				$indexes = array( 
					"product_category_title",
					"description",
					"img_logo_name"
				);  
				$return = $this->pairLines( $indexes, $fp );
				break;
				
			case "alerts.csv":
				$indexes = array(
					"alert_title",
					"start_date",
					"end_date",
					"body"
				);
				$return = $this->pairLines( $indexes, $fp );
				break;
				
			case "articles.csv":
				$indexes = array( 
					"view_title", 
					"article_title",
					"section_title",
					"body" 
				);
				$return = $this->pairLines( $indexes, $fp );
				break;
				
			case "captcha.csv":
					$indexes = array(
						"captcha_string",
						"file_name"
					);
					$return = $this->pairLines( $indexes, $fp );
				break;
			
			//used for data with only 1 field per line ( title field )
			default:
				$indexes = array( "title" );
				$return = $this->pairLines( $indexes, $fp );
				break;
				
		}//switch
		
		return $return;
		
	}//convertFile()
	
	/**
	 * Pairs lines from file with specified indexes
	 * @since	20100620, hafner
	 * @return	array	
	 * @param	file resource		$fp			pointer to the current file.
	 * @param	array				$indexes	array of keys
	 */
	public function pairLines( $indexes, $fp )
	{
		while( !feof( $fp ) )
		{
			$line = trim( fgets( $fp ) );
			if( strlen( $line ) > 0 )
			{
				$line_array = explode( "^", $line );
				$return[] = $this->convertLine( $indexes, $line_array );
			}
		}
		
		return $return;
		
	}//pairLines()
	
	/**
	 * Formats a line part. Trims spaces, lowercases all
	 * @since 	20100618, hafner
	 * @return 	string
	 * @param 	string		$line_part		part of a line
	 */
	public function formatLinePart( $line_part )
	{
		return strtolower( $this->m_common->m_db->escapeString( trim( $line_part ) ) );
	}//formatLinePart()
	
	/**
	 * Converts a line into an associative array.
	 * @since 	20100618, hafner
	 * @return 	array
	 * @param 	array		$keys		array keys
	 * @param 	array		$values		array values
	 */
	public function convertLine( $keys, $values )
	{
		$return = array();
		foreach( $keys as $index => $key_name )
		{
			$value = ( $key_name != "description" ) ? $this->formatLinePart( $values[$index] ) : addslashes( $values[$index] ); 
			$return[$key_name] = $value;	
		}
		
		return $return;
	}//convertLine()
	
	/**
	 * Imports array of file info into the database.
	 * Returns TRUE if import suceeded, FALSE otherwise.
	 * @since 	20100618, hafner
	 * @return 	boolean
	 * @param	array			$data		array of data from the current file
	 */
	public function importFile( $data ) 
	{
		switch( $this->m_file_name )
		{
			case "files.csv":
				$return = $this->importFiles( $data );
				break;
				
			case "fileTypes.csv":
				$return = $this->importFileTypes( $data );
				break;
				
			case "productCategories.csv":
				$return = $this->importProductCategories( $data );
				break;
				
			case "products.csv":
				$return = $this->importProducts( $data );
				break; 
				
			case "states.csv":
				$return = $this->importStates( $data );
				break;
				
			case "prodCatInfo.csv":
				$return = $this->importProdCatInfo( $data );
				break;
				
			case "alerts.csv":
				$return = $this->importAlerts( $data );
				break;
				
			case "views.csv":
				$return = $this->importViews( $data );
				break;
				
			case "articles.csv":
				$return = $this->importArticles( $data );
				break;
				
			case "sections.csv":
				$return = $this->importSections( $data );
				break;
				
			case "captcha.csv":
				$return = $this->importCaptcha( $data );
				break;
				
			default:
				throw new Exception( "Error Invalid File Name" );
		}//switch
		
		return $return;
	}//importFile()
	
	/**
	 * Imports the data into the mdp_Events table.
	 * Returns TRUE on success, throws exception otherwise.
	 * @since	20100620, hafner
	 * @return	boolean
	 * @param	array			$data			array of import data from .csv
	 */
	public function importCaptcha( $data )
	{
		foreach( $data as $info )
		{
			$f_input = array( "file_name" => $info['file_name'] );
			
			//handle file record
			$file = $this->handleRecord( $f_input, "file" );
			
			//record file id
			$info['file_id'] = $file->m_file_id;
			
			//handle captcha record
			$this->handleRecord( $info, "captcha" );
			
		}	
		
		return TRUE;
		
	}//importCaptcha()
	
	/**
	 * Imports the data into the mdp_Events table.
	 * Returns TRUE on success, throws exception otherwise.
	 * @since	20100620, hafner
	 * @return	boolean
	 * @param	array			$data			array of import data from .csv
	 */
	public function importViews( $data )
	{
		foreach( $data as $info )
		{
			//add record
			$this->handleRecord( $info, "view" );
		}
		
		return TRUE;
		
	}//importViews()
	
	/**
	 * Imports the data into the mdp_Events table.
	 * Returns TRUE on success, throws exception otherwise.
	 * @since	20100620, hafner
	 * @return	boolean
	 * @param	array			$data			array of import data from .csv
	 */
	public function importArticles( $data )
	{
		foreach( $data as $info )
		{
			//handle view record
			$view = $this->handleRecord( $info, "view" );
			
			//handle article record
			$a = $this->handleRecord( $info, "article" );
			
			//add view to article
			$a2v_id = $a->addView( $view->m_view_id );
			
			//handle section
			$section = $this->handleRecord( $info, "section" );
			
			//add section to article
			$a->addSection( $a2v_id, $section->m_section_id );
		}
		
		return TRUE;
		
	}//importArticles()
	
	/**
	 * Imports the data into the mdp_Events table.
	 * Returns TRUE on success, throws exception otherwise.
	 * @since	20100620, hafner
	 * @return	boolean
	 * @param	array			$data			array of import data from .csv
	 */
	public function importAlerts( $data )
	{
		foreach( $data as $info )
		{
			$this->handleRecord( $info, "alert" );
		}

		return TRUE;
		
	}//importAlerts()
	/**
	 * Imports the data into the mdp_Events table.
	 * Returns TRUE on success, throws exception otherwise.
	 * @since	20100620, hafner
	 * @return	boolean
	 * @param	array			$data			array of import data from .csv
	 */
	public function importProducts( $data )
	{ 
		foreach( $data as $info )
		{
			//add product category
			$pg = $this->handleRecord( $info, "product_category" );
			
			//add img_thumb
			$info['file_name'] = $info['img_thumb_name'];
			$thumb = $this->handleRecord( $info, "file" );
			
			//add img_full
			$info['file_name'] = $info['img_full_name'];
			$full = $this->handleRecord( $info, "file" );
			
			//add product
			$info['product_category_id'] = $pg->m_product_category_id;
			$info['img_thumb_id'] = $thumb->m_file_id;
			$info['img_full_id'] = $full->m_file_id;
			$product = $this->handleRecord( $info, "product" );
			
		}//loop through event data
		
		return TRUE;
		
	}//importEvents()
	
	public function importProductCategories( $data )
	{  
		foreach( $data as $i => $info )
		{
			//handle pg record
			$info['product_category_title'] = stripslashes( $info['title'] );
			$pg = $this->handleRecord( $info, "product_category" );
		}
		
		return TRUE;
		
	}//importProductCategories()
	
	/**
	 * Imports the data into the common_States table.
	 * Returns TRUE on success, throws exception otherwise.
	 * @since	20100620, hafner
	 * @return	boolean
	 * @param	array			$data			array of import data from .csv
	 */
	public function importStates( $data )
	{
		foreach( $data as $info )
		{
			$state = $this->handleRecord( $info, "state" );
		}
		
		return TRUE;
		
	}//importStates()
	
	/**
	 * Imports file data.
	 * @since	20100621, hafner
	 * @return	boolean
	 * @param	array			$data			array of file data
	 */
	public function importFiles( $data )
	{
		foreach( $data as $info )
		{
			//handle file type record
			$ft = $this->handleRecord( $info, "file_type" );
			
			//handle file record
			$info['file_type_id'] = $ft->m_file_type_id;
			$file = $this->handleRecord( $info, "file" );
		}
		
		return TRUE;
		
	}//importFiles()
	
	/**
	 * Imports fileType data.
	 * @since	20100621, hafner
	 * @return	boolean
	 * @param	array			$data			array of file data
	 */
	public function importFileTypes( $data )
	{
		
		foreach( $data as $info )
		{
			$info['file_type_title'] = $info['title'];
			$this->handleRecord( $info, "file_type" );
		}
		
		return TRUE;
		
	}//importFileTypes()
	
	/**
	 * Import product category info.
	 * @since	20100713, hafner
	 * @return	true
	 * @param	array			$data			array of file data
	 */
	public function importProdCatInfo( $data )
	{
		foreach( $data as $info )
		{
			//handle pg record
			$pg = $this->handleRecord( $info, "product_category" );
			
			//handle file record
			$info['file_name'] = $info['img_logo_name'];
			$file = $this->handleRecord( $info, "file" );
			
			//handle prodCatInfo
			$pg->addInfoRecord();
			$pg->addDescription( $info['description'] );
			$pg->addFile( $file->m_file_id );
			
		}
		return TRUE;
	}//importProdCatInfo()
	
	/**
	 * Handles a certain type of record.
	 * @since	20100626, hafner
	 * @return	object
	 * @param 	array			$info			input for a single record
	 * @param	string			$type			type of record to input
	 */
	public function handleRecord( $info, $type )
	{
		
		switch( $type ) 
		{
			case "state":
				$state_input = array(
					'full_name' => $info['state_full_name'],
					'abbrv' => $info['state']
				);
				
				$dup_check = array(
					'table_name' => "common_States",
					'pk_name' => "state_id",
					'check_values' => $state_input
				);
				
				$state = new State( 0 );
				$return = $this->m_common->m_db->addRecord( $state, $state_input, $dup_check );
				break;
				
			case "file":
				//gather file data
				$file_input = array( 
					'file_name' => $info['file_name'],
					'relative_path' => $info['relative_path'],
					'file_type_id' => $info['file_type_id']
				);
				
				$dup_check = array(
					'table_name' => 'common_Files',
					'pk_name' => 'file_id',
					'check_values' => array( 'file_name' => $info['file_name'] )
				);
				
				$file = new File( 0 );
				$return = $this->m_common->m_db->addRecord( $file, $file_input, $dup_check );
				break;
				
			case "file_type":
				$input = array( 'title' => $info['file_type_title'] );
				
				$dup_check = array(
					'table_name' => "common_FileTypes",
					'pk_name' => "file_type_id",
					'check_values' => $input
				);
				
				$ft = new FileType( 0 );
				$return = $this->m_common->m_db->addRecord( $ft, $input, $dup_check );
				break;
				
			case "product":
				$p_input = array(
					'title' => $info['title'],
					'img_thumb_id' => $info['img_thumb_id'],
					'img_full_id' => $info['img_full_id'],
					'product_category_id' => $info['product_category_id'],
					'description' => $info['description'],
					'price' => $info['price']
				);
				
				$dup_check = array(
					'table_name' => 'sbc_Products',
					'pk_name' => 'product_id',
					'check_values' => array( 'title' => $info['title'] )
				);

				$p = new Product( 0 );
				$return = $this->m_common->m_db->addRecord( $p, $p_input, $dup_check );
				break;
				
			case "product_category":
				$pc_input = array(
					'title' => $info['product_category_title']
				);
				
				$dup_check = array(
					'table_name' => "sbc_ProductCategories",
					'pk_name' => "product_category_id",
					'check_values' => array(
						'title' => $pc_input['title'], 
					)
				);
				
				$pc = new ProductCategory( 0 );
				$return = $this->m_common->m_db->addRecord( $pc, $pc_input, $dup_check );
				break;
				
			case "alert":
				$alert_input = array( 
					'start_timestamp' => $info['start_date'],
				 	'end_timestamp' => $info['end_date'],
					'title' => $info['alert_title'],
					'body' => $info['body'] 
				);
				
				$dup_check = array(
					'table_name' => "sbc_Alerts",
					'pk_name' => "alert_id",
					'check_values' => array( 'title' => $info['alert_title'] )
				);
				
				$alert = new Alert( 0 );
				$return = $this->m_common->m_db->addRecord( $alert, $alert_input, $dup_check );
				break;
				
			case "view":
				$title = ( array_key_exists( "view_title", $info ) ) ? $info['view_title'] : $info['title'];
				
				$view_input = array(
					'title' => ucWords( $title )
				);
				
				$dup_check = array(
					'table_name' => "common_Views",
					'pk_name' => "view_id",
					'check_values' => array( 'title' => $view_input['title'] ) 
				);
				
				$view = new View( 0 );
				$return = $this->m_common->m_db->addRecord( $view, $view_input, $dup_check );
				break;
				
			case "article":
				$a_input = array(
					'title' => $info['article_title'],
					'body' => $info['body']
				);
				
				$dup_check = array(
					'table_name' => "common_Articles",
					'pk_name' => "article_id",
					'check_values' => array(
						'title' => $info['article_title']
					)
				);
				
				$a = new Article( 0 );
				$return = $this->m_common->m_db->addRecord( $a, $a_input, $dup_check );
				break;
				
			case "section":
				$s_input = array( 'title' => ucwords( $info['section_title'] ) );
				
				$dup_check = array( 
					'table_name' => "common_Sections",
					'pk_name' => "section_id",
					'check_values' => array( 'title' => $s_input['title'] ) 
				);
				
				$s = new Section( 0 );
				$return = $this->m_common->m_db->addRecord( $s, $s_input, $dup_check );
				break;
				
			case "captcha":
				$c_input = array( 'captcha_string' => $info['captcha_string'], 'file_id' => $info['file_id'] );
				
				$dup_check = array(
					'table_name' => "common_Captcha",
					'pk_name' => "captcha_id",
					'check_values' => array( 'captcha_string' => $c_input['captcha_string'] )
				);
				
				$c = new Captcha( 0 );
				$return = $this->m_common->m_db->addRecord( $c, $c_input, $dup_check );
				break;
				
			default:
				throw new exception( "Error: invalid record type." );
				break;
		}
		
		return $return;
		
	}//handleRecord()
	
}//class Data
?>