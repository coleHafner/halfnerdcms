<?
/**
 * A class to handle the layout common to every page on the site.
 * @since	20100425, hafner
 */

require_once( 'base/Common.php' );
require_once( 'base/View.php' );
require_once( "base/EnvVar.php" );
require_once( "services/PicasaAlbum.php" );

class Layout
{
	/**
	 * Instance of the Common class.
	 * @var	Common
	 */
	protected $m_common;
	
	/**
	 * Name of the current view.
	 * @var	int
	 */
	protected $m_active_view;
	
	/**
	 * Array of possible views.
	 * @var	array
	 */
	protected $m_views;
	
	/**
	 * Details of the current page. Includes title and other details.
	 * @var	string
	 */
	protected $m_page_details;
	
	/**
	 * Constructs the Layout object.
	 * @return Layout
	 * @since	20100307, hafner
	 * @mod		20100502, hafner
	 * @param	string			$view			name of the controller	
	 */
	public function __construct( $get )
	{
		$v = ( array_key_exists( "v", $get ) ) ? $get['v'] : "";
		$view = ucfirst( $v );
		
		$this->m_common = new Common();
		$this->m_active_view = $this->validateView( $view );
		$this->m_page_details = $this->getPageDetails();
		
	}//Layout()
	
	/**
	 * Gets the details for this page. 
	 * @return	array
	 * @since	20100307, hafner
	 * @mod		20100307, hafner
	 */
	public function getPageDetails()
	{
		$v = new View(0);
		
		return $v->getAllRecords( FALSE );
		
	}//getPageDetails()
	
	/**
	 * Outputs the 'head' section of the HTML document.
	 * @return	string
	 * @since	20100323, hafner
	 * @mod		20100323, hafner
	 */
	public function getHtmlHeadSection()
	{
		$paths = $this->m_common->getPathInfo();
		$file_paths = $paths[$this->m_common->m_env];
		
		return '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" >
		
		<head>
		
			<title>' . $this->m_page_details[$this->m_active_view]->m_alias . '</title>

			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/960_grid.css" type="text/css" />
			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/jquery-ui-1.8.1.custom.css" type="text/css" />
			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/colorbox.css" type="text/css" />
			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/imgbox.css" type="text/css" /> 
			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/nivo-slider.css" type="text/css" />
			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/timepicker.css" type="text/css" />
			<link rel="stylesheet" href="' . $file_paths['css'] . '/common.css" type="text/css" />
			
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery-1.4.2.js"></script>
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery-ui-1.8.1.custom.min.js"></script>
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery.colorbox.js"></script>
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery.imgbox.js"></script>
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery.nivo.slider.js"></script>
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery.timepicker.js"></script>
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery.tools.min.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.common.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.auth.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.article.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.view.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.section.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.user.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.file.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.mail.js"></script>
			
			<!--			
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.halfnerd.values.js"></script>
			-->
						
		</head>
		';
		
	}//getHtmlHeadSection()
	
	/**
	 * Outputs the section directly above the unique content for each page.
	 * @return	string
	 * @since	20100323, hafner
	 * @mod		20100323, hafner
	 */
	public function getHtmlBodySection( $login_string )
	{  
		$login = ( strlen( $login_string ) > 0 ) ? $login_string : '';
		
		$return .= '
		<body class="font_normal bg_color_white">
		
		
		<!--nav section-->
		<div class="nav_section bg_color_light_tan">
			
			<!--nav container-->
			<div class="container_12 nav_container">
			
				<!--nav-->
				<div class="grid_12">
					
					<div class="padder_10">
					
						<div class="logo_container rounded_corners bg_color_tan center border_color_accent">
							<img src="/images/logo.png"/>
						</div>
						
						<div class="logo_words_container header_mega color_accent">
							Halfnerd CMS
						</div>
						
						' . $login . '
					</div>
					
				</div>
				<!--/nav-->
				
			</div>
			<!--/nav container--> 
			
		</div>
		<!--/nav section-->
		
		<!--main content section-->
		<div class="main_section">
			
			<!--main content container-->
			<div class="container_12 main_container">
				';
				
		return $return;
		
	}//getHtmlBodySection()
	
	/**
	 * Closes the main HTML tags.
	 * @return	string
	 * @since	20100323, hafner
	 * @mod		20100323, hafner
	 */
	public function getHtmlFooterSection()
	{
		$return = '
			</div>
			<!--/main content container-->
				
		</div>
		<!--/main content section-->
		
		<!--footer section-->
		<div class="footer_section bg_color_white">
		
			<div class="container_12">
				
				<div class="grid_12 center font_small">
					&copy; [Year] Client Name 
					<span style="color:#FF0000;">|</span> 
					Designed by HalfNerd 
					<span style="color:#FF0000;">|</span>
					<a href="' . $this->m_common->makeLink( array( 'v' => "admin" ) ) . '">CMS Login</a>
				</div>
				
			</div> 
		</div>
		<!--/footer section-->
		
		<iframe class="input text_input" style="height:200px;width:300px;display:none;" id="hidden_frame" name="hidden_frame" ></iframe>
		';
		
		return $return;
		
	}//getHtmlFooterSection()
	
	public function getClosingTags()
	{
		return '
		</body>
		
		</html>
		';
	}//getClosingTags()
	
	/**
	 * Outputs the primary navigation.
	 * @return	string
	 * @since	20100323, hafner
	 * @mod		20100403, hafner
	 */
	public function getHtmlNav()
	{
		$return ='
		<div class="nav_item social_icons">
			<a href="http://photos.google.com/photonorthwest" target="_blank">
				<img src="/images/icon_picasa.png" />
			</a>
			
			&nbsp;&nbsp;
			
			<a href="http://facebook.com/photonorthwest" target="_blank">
				<img src="/images/icon_facebook.png" />
			</a>
		</div>
		';
		
		foreach( $this->m_page_details as $c_name => $view ) 
		{
			
			if( $view->m_show_in_nav )
			{
				//determine active selector
				$sub = "";
				$link = ( strlen( $view->m_external_link ) > 0 ) ? $view->m_external_link : $this->m_common->makeLink( array( 'v' => $c_name ) );
				$link_style = ( strlen( $view->m_external_link ) > 0 ) ? 'target="_blank"' : "";
				$selected = ( $c_name == $this->m_active_view ) ? 'class="selected"': "";
				
				//compile html
				$return .= '
				<div class="nav_item" ' . $fixed_width . '>
					<a href="' . $link . '" ' . $selected . ' ' . $link_style . '>
						' . $view->m_alias . '
					</a>
					' . $sub . '
				</div>
				';
			}		
			
		}//loop through views
		
		return $return;
		
	}//getHtmlNav()
	
	/**
	 * Returns an HTML string that contains the elements referenced in a table. 
	 * @since	20100603, hafner
	 * @return	string
	 * @param 	array			$data			array of data
	 * @param 	array 			$style			array( 'table_style'  => "id or class of table", 'elements_per_row'  => integer )
	 */
	public function getTableGrid( $data, $style )
	{
		//calc vars
		$num_rows = ceil( count( $data )/$style['elements_per_row'] );
		$return = '
		<table ' . $style['table_style'] . '>
		';
		
		for( $i = 0; $i < $num_rows; $i++ ) 
		{
			$return .= '
			<tr>';
			
			$start = ( $i == 0 ) ? 0 : ( $style['elements_per_row'] * $i );
			$stop = ( $i == 0 ) ? ( $style['elements_per_row'] - 1 ) : ( $style['elements_per_row'] * $i ) + ( $style['elements_per_row'] - 1 );
			
			for( $j = $start; $j < $stop; $j++ )
			{
				$return .= '
				<td>
					' . $data[$j] . '
				</td>
				';
				
				if( $j == count( $data ) - 1 ) break;
			}
			
			$return .= '
			</tr>';
		}
		
		$return .= '
		</table>';
		
		return $return;
		 
	}//getTableGrid()
	
	/**
	 * Validates the current view.
	 * Returns the name of the view.
	 * @since	20100323, hafner
	 * @return	string
	 * @param	string		$view		view from the url
	 */
	public function validateView( $view )
	{
		$sql = "
		SELECT count(*)
		FROM common_Views
		WHERE LOWER( TRIM( controller_name ) ) = '" . strtolower( trim( $view ) ) . "'
		AND parent_view_id = 0";
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $this->m_common->m_db->fetchRow( $result );
		
		return ( $row[0] == 1 ) ? ucfirst( strtolower( $view ) ) : "Index";
		
	}//validateView()
	
	/**
	* Get a member variable's value
	* @return	mixed
	* @param	string		$var_name		Variable name to get
	* @since 	20100403, hafner
	* @mod		20100403, hafner
	*/
	public function __get( $var_name )
	{
		$exclusions = array( 'm_common' );
		if( !in_array( $var_name, $exclusions ) ) {
			$return = $this->$var_name;
		}else {
			if( $this->m_common->m_in_production ) {
				echo "<h3>Access to get member " . get_class( $this ) . "::" . $var_name . " denied.</h3><br/>\n";
				$return = FALSE;
			}else {
				$return = FALSE;
			}
		}
		return $return;
	}//__get()
	
}//class Layout
?>