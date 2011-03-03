<?
/**
 * A class to handle the layout common to every page on the site.
 * @since	20100425, hafner
 */

require_once( 'base/Common.php' );
require_once( 'base/View.php' );

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
	protected $m_active_controller_name;
	
	/**
	 * Constructs the Layout object.
	 * @return Layout
	 * @since	20100307, hafner
	 * @mod		20100502, hafner
	 * @param	string			$view			name of the controller	
	 */
	public function __construct( $get )
	{
		$this->m_common = new Common();
		$this->m_active_controller_name = ucfirst( strtolower( $get['v'] ) );
		
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
		
		$paths = $this->m_common->getPathInfo();
		$file_paths = $paths[$this->m_common->m_env];
		
		$sql = "SELECT alias FROM common_Views WHERE LOWER( controller_name ) = '" . strtolower( $this->m_active_controller_name ) . "'";
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $this->m_common->m_db->fetchRow( $result );
		$alias = $row[0];
		
		$sql = "SELECT value FROM common_Settings WHERE LOWER( title ) = 'site-name'";
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $this->m_common->m_db->fetchRow( $result );
		$site = $row[0];
		
		return '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" >
		
		<head>
		
			<meta name="viewport" content="width=device-width; user-scalable=1;" >
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
			
			<title>' . $alias . ' - ' . $site . '</title>

			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/960_grid.css" type="text/css" />
			<link rel="stylesheet" href="' . $file_paths['css_ex'] . '/jquery-ui-1.8.1.custom.css" type="text/css" />
			<link rel="stylesheet" href="' . $file_paths['css'] . '/common.css" type="text/css" />
			
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery-1.4.2.js"></script>
			<script type="text/javascript" src="' . $file_paths['js_ex'] . '/jquery-ui-1.8.1.custom.min.js"></script>
			<script type="text/javascript" src="' . $file_paths['js'] . '/jquery.common.js"></script>
			
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
		<body>
		
		<!--header section-->
		<div class="header_section">
			<div class="container_12">
				<div class="grid_12">
					&nbsp;
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<!--/header section-->
		
		<!--content section-->
		<div class="content_section">
			<div class="container_12">
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
		</div>
		<!--/content section-->
		
		<!--footer section-->
		<div class="footer_section">
			<div class="container_12">
				<div class="grid_12">
					<div class="center padder_5_top">
						<a href="' . $this->m_common->makeLink( array( 'v' => "admin" ) ) . '">
							CMS Login
						</a>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<!--/footer section-->
		
		<iframe class="input text_input" style="height:200px;width:600px;margin:20px auto 20px auto;display:none;" id="hidden_frame" name="hidden_frame" ></iframe>
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
		$views = array();
		
		foreach( $views as $c_name => $view ) 
		{
			
			if( $view->m_show_in_nav )
			{
				//determine active selector
				$sub = "";
				$link = ( strlen( $view->m_external_link ) > 0 ) ? $view->m_external_link : $this->m_common->makeLink( array( 'v' => $c_name ) );
				$link_style = ( strlen( $view->m_external_link ) > 0 ) ? 'target="_blank"' : "";
				$selected = ( $c_name == $this->m_active_controller_name ) ? 'class="selected"': "";
				
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
	* Get a member variable's value
	* @return	mixed
	* @param	string		$var_name		Variable name to get
	* @since 	20100403, hafner
	* @mod		20100403, hafner
	*/
	public function __get( $var_name )
	{
		$exclusions = array( 'm_common' );
		
		if( !in_array( $var_name, $exclusions ) ) 
		{
			$return = $this->$var_name;
		}
		
		return $return;
		
	}//__get()
	
}//class Layout
?>