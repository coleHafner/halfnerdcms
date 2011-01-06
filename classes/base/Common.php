 <?

/**
 * A class to handle common functions.
 * @since 20100508, hafner
 */

require_once( "base/Database.php" );

class Common {
	
	/**
	 * Instance of the Database class.
	 * @var Database
	 */
	protected $m_db;
	
	/**
	 * Sets the environment( local, dev, live )
	 * @return string
	 */
	protected $m_env;
	
	public function __construct() {
		
		//set relative path
		$this->m_env = $this->determineEnv();
		
		$all_paths = $this->getPathInfo();
		$cur_paths = $all_paths[$this->m_env];
		
		//setup db connection
		$this->m_db = new Database( $cur_paths );
		
	}//constructor()
	
	/**
	 * Generates a link.
	 * @since	20100620, hafner
	 * @return	string
	 * @param 	array		$get		variables collected by the current controller + additional variables
	 */
	public function makeLink( $get )
	{
		$return = "";
		$counter = 0;
		$valid_fields = array( "v", "sub", "id1", "id2" );
			
		foreach( $get as $field => $val )
		{
			$field = strtolower( $field );
			
			if( in_array( $field, $valid_fields ) )
			{
				
				//$delim = ( $counter == 0 ) ? "/?" : "&"; 
				//$return .= $delim . $field . "=" . strtolower( $val );
				//$counter++;
				$val = ( strtolower( $field ) == "v" ) ? "_" . $val : $val;
				$return .= "/" . strtolower( $val );
			}
		}//loop through controller vars
		
		$paths = $this->getPathInfo();
		$return = ( strtolower( trim( $this->m_env ) ) == "live" ) ? str_replace( "/?", "/" . $paths[$this->m_env]['web'] . "?", $return ) : $return;
				
		return $return;
		 	
	}//makeLink()
	
	public function makeFilePath( $type )
	{
		
	}//makeFilePath()
	
	public function getPathInfo() 
	{
		return array(
			'local' => array(
				'absolute' => "/usr/local/www/halfnerdcms",
				'web' => "www",
				'css' => "/css",
				'css_ex' => "/css/extensions",
				'images' => "/images",
				'images_ex' => "/images/extensions",
				'user_images' => "/images/users",
				'js' => "/js",
				'js_ex' => "/js/extensions",
				'classes' => "/classes",
				'classes_ex' => "/classes/ex",
				'db_host' => "localhost",
				'db_name' => "cms",
				'db_user' => "cms_user",
				'db_pass' => "passwd1000!",
			),
			
			//dev server
			'dev' => array(
				'absolute' => "/home/users/web/b937/moo.halfnerdcom/cms",
				'web' => "www",
				'css' => "/css",
				'css_ex' => "/css/extensions",
				'images' => "/images",
				'images_ex' => "/images/extensions",
				'user_images' => "/images/users",
				'js' => "/js",
				'js_ex' => "/js/extensions",
				'classes' => "/classes",
				'classes_ex' => "/classes/ex",
				'db_host' => "halfnerdcom.fatcowmysql.com",
				'db_name' => "halfnerd_cms",
				'db_user' => "cms_user",
				'db_pass' => "passwd1000!"
			),
			
			//live server
			'live' => array(
				'absolute' => "",
				'web' => "pbr/www",
				'css' => "/css",
				'css_ex' => "/css/extensions",
				'images' => "/images",
				'images_ex' => "/images/extensions",
				'user_images' => "/images/users",
				'js' => "/js",
				'js_ex' => "/js/extensions",
				'classes' => "/classes",
				'classes_ex' => "/classes/ex",
				'db_host' => "",
				'db_name' => "",
				'db_user' => "",
				'db_pass' => ""
			)
		);
		
	}//getPathInfo()
	
	/**
	 * Turns an array of sql constraints into a string.
	 * @since	20100620, hafner
	 * @return string
	 * @param	array			$constraints		array( '[field_name1]' => '[value1]', '[field_name2]' => '[value2]' etc. . . ) )
	 * @param	array			$operators			if TRUE $constraints = array( [0] => array( '[field_name1]' => '[value1]', ['operator'] => "<= || >= || =" ), [1]  => array( '[field_name2]' => '[value2]', ['operator'] => "<= || >= || =" ) etc. . . ) )
	 */
	public function compileSqlConstraints( $constraints )
	{
		if( is_array( $constraints ) && count( $constraints ) > 0 )
		{	
			$counter = 1;
			$return = " WHERE ";
			$total_vals = count( $constraints );
			
			foreach( $constraints as $field => $val )
			{
				$joiner = ( $counter != $total_vals ) ? " AND" : "";
				$l = ( !is_numeric( $val ) ) ? "'" : "";
				$r = ( !is_numeric( $val ) ) ? "'" : "";
				
				$return .= "
				LOWER( TRIM( " . $field . " ) ) = " . $l .  strtolower( trim( $val ) ) . $r . $joiner;  
				$counter++;
			}
		}
		else
		{
			print_r( $dup_check );
			throw new exception( "Error: Invalid input for Common->compileSqlConstraints()" );
		}
		
		return $return;
	
	}//compileSqlConstraints()
	
	/**
	 * Determines the environment.
	 * @since	20100621, hafner
	 * @return	string
	 */
	public function determineEnv()
	{
		$return = "local";
		$paths = $this->getPathInfo();
		
		$dev_path = $paths['dev']['absolute'] . "/" . $paths['dev']['web'];
		$live_path = $paths['live']['absolute'] . "/" . $paths['live']['web'];
		
		if( file_exists( $dev_path . "/is_dev.txt" ) )
		{
			$return = "dev";	
		}
		else if( file_exists( $live_path . "/is_live.txt" ) )
		{
			$return = "live";	
		}
		
		return $return;
		
	}//determineEnv()
	
	/**
	 * Used primarily in the mdp_helper.php file.
	 * @since	20100628, hafner
	 * @return	mixed
	 * @param	boolean			$return			whether or not the action was a success
	 * @param	string			$message		success/failure message
	 */
	function sendJsonResponse( $return, $message )
	{
		//send JSON header and response
		header( 'Content-type: application/x-json' );
		echo json_encode( $return );
		
	}//sendJsonResponse()
	
	public function convertTimestamp( $ts, $include_time = TRUE )
	{
		$format = ( $include_time ) ? "F j \@ g:i a" : "F d";
		return date( $format, $ts );
	}//convertTimestamp()
	
	public function compileHiddenFields( $array )
	{
		$return = '';
		
		foreach( $array as $k => $v )
		{
			if( $k != "task" && 
				$k != "process" )
			{
				$return .= '
				<input type="hidden" name="' . $k . '" id="' . $k . '" value="' . $v . '"/>
				';
			}
		}
		
		return $return;
		
	}//compileHiddenFields()
	
	public function formatText( $text, $class = "" )
	{
		if( strlen( $class ) > 0 )
		{
			$class = 'class="' . $class . '"';
		}
		
		$text = str_replace( "\\n", "\n", $text );
		
		$return ='<p ' . $class . '>' . $this->convertLinks( $text ) . '</p>';
		
		if( strlen( $text ) > 0 &&
			strpos( $text, "\n" ) )
		{
			$return = '';
			$body_split = explode( "\n", $text );
			
			foreach( $body_split as $p )
			{
				$return .= '<p ' . $class . '>' . $this->convertLinks( $p ) . '</p>'; 
			}
		}
		
		return $return;
		
	}//formatText()
	
	public function convertLinks( $str )
	{
		return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a target='_blank' href=\"\\0\">\\0</a>", $str );
		
	}//convertLinks()
	
	/**
	 * Validates the authenticity of an email address.
	 * Returns TRUE if valid, FALSE otherwise.
	 * @since	20100909, Hafner
	 * @return	boolean
	 * @param 	string			$email			email address to validate
	 */
	public function validateEmailAddress( $email )
	{
		$return = FALSE;
		$email = strtolower( trim( $email ) );
		
		if( !$return )
		{
			if( strlen( $email ) == 0 )
			{
				$return ="You must provide an email address.";
			}
		}
		
		if( !$return )
		{
			if( strpos( $email, "@" ) === FALSE || 
				strpos( $email, "." ) === FALSE ||
				strpos( $email, " " ) !== FALSE )
			{
				$return = "You must provide a valid email address";
			}
		}
		
		return $return;
		
	}//validateEmailAddress()
	
	public function convertViewAlias( $alias, $type )
	{
		switch( strtolower( $type ) )
		{
			case "url":
				$return = strtolower( str_replace( " ", "-", $alias ) );
				break;
				
			case "interface":
				$return = ucfirst( strtolower( str_replace( "-", " ", $alias ) ) );
				break;
				
			default:
				throw new Exception( "Error: Type '" . $type . "' is invalid." );
				break;
		}
		
		return $return;
		
	}//convertViewAlias()
	
	public function getListRecords( $table )
	{
		$return = FALSE;
		
		switch( strtolower( trim( $table ) ) )
		{
			case "common_views":
				$pk = "view_id";
				$title = "alias";
				break;
				
			case "common_sections":
				$pk = "section_id";
				$title = "title";
				break;
				
			default:
				throw new Exception( "Error: Invalid Table Name." );
				break;
		}
		
		$sql = "
		SELECT " . $pk . " AS id," . $title . " AS title
		FROM " . $table . "
		WHERE " . $pk . " > 0 AND active = 1
		";
		
		$result = $this->m_db->query( $sql, __FILE__, __LINE__ );
		
		while( $row = $this->m_db->fetchAssoc( $result ) )
		{
			$return[] = $row;
		}
		
		return $return;
		
	}//getListRecords()
	
	/**
	 * Gets HTML
	 * @since	20101007
	 * @author	20101007, hafner
	 * @param	string			$cmd			command html to get
	 * @param	boolean			$is_addition	if we are adding a new record, is_addition = TRUE, FALSE otherwise.			 
	 */
	public static function getHtml( $cmd, $vars = array() )
	{
		switch( strtolower( $cmd ) )
		{
			case "under-construction":
				$return = array( 
					'body' => '
					<div class="under_construction_container">
						This section is  under construction...
					</div>
					' 
				);
				break;
				
			case "full-div":
				$return = array(
					'out' => '<div class="grid_12 rounded_corners"> ',
					'in' => '<div class="padder">' 
				);
				break;
				
			case "title-bar":
				$return = '
				<div class="center header color_accent ' . $vars['classes'] . '">
					' .  $vars['title'] . '
				</div>
				';
				break;
				
			case "selector-module":
			
				$container_style = ( array_key_exists( "container_style", $vars ) ) ? $vars['container_style'] : "";
				$content_style = ( array_key_exists( "content_style", $vars ) ) ? $vars['content_style'] : "";
			
				$return = '
				<div class="rounded_corners bg_color_tan selector_module border_dark_grey" ' . $container_style . '>
				
					<div class="padder">
						<div class="bg_color_white rounded_corners padder center color_orange">
							<b>' . $vars['title'] . '</b>
						</div>
						<div class="center ' . $vars['content_class'] . '" ' . $content_style . '>
							' .  $vars['content'] . '
						</div>
					</div>
					
				</div>
				';
				break;
				
			case "get-form-buttons":
			
				$table_style = ( array_key_exists( "table_style", $vars ) ) ? $vars['table_style'] : 'style="postion:relative;margin:auto;"';
			
				$return = '
				 <table ' . $table_style . '>
					<tr>
						<td class="center">
							' . self::getHtml( "get-button", $vars['left'] ) . '
						</td>
						<td>
							&nbsp;
						</td>
						<td class="center">
							' . self::getHtml( "get-button", $vars['right'] ) . '
						</td>
					</tr>
				</table>
				';
				break;
				
			case "get-button":
			
				$style = ( array_key_exists( "extra_style", $vars ) ) ? $vars['extra_style'] : "";
				
				if( !array_key_exists( "href", $vars) )
				{
					$link_guts = 'id="' . $vars['id'] . '" process="' . $vars['process'] . '" ' . $vars['pk_name'] . '="' . $vars['pk_value'] . '"';
				}
				else
				{
					$link_guts = 'href="' . $vars['href'] . '"';
				}
				
				$return = '
				<a ' . $link_guts . ' class="button rounded_corners color_accent center no_hover bg_color_white" ' . $style . '>
					' . $vars['button_value'] . '
				</a>
				';
				break;
				
			case "get-button-round":
			
				$active = '';
				$border_class = 'border_color_white';
				$link_style = $style = ( array_key_exists( "link_style", $vars ) ) ? $vars['link_style'] : "";
				$inner_div_style = ( array_key_exists( "inner_div_style", $vars ) ) ? $vars['inner_div_style'] : "";
				
				//determine selected
				if( array_key_exists( "selected", $vars ) &&
					$vars['selected'] == 1 )
				{
					$active = 'active="1"';
					$border_class = 'border_color_orange';
				}
				
				//determine link guts
				if( !array_key_exists( "href", $vars) )
				{
					$link_guts = 'href="#" id="' . $vars['id'] . '" process="' . $vars['process'] . '" ' . $vars['pk_name'] . '="' . $vars['pk_value'] . '"';
				}
				else
				{
					$link_guts = 'href="' . $vars['href'] . '"';
				}
				
				$return = '
				<a ' . $link_guts . ' class="no_hover admin_button bg_color_white center ' . $border_class . '" ' . $link_style . ' ' . $active . '>
					<div ' . $inner_div_style . '>
						' . $vars['button_value'] . '
					</div>
				</a>
				';

				break;
				
			case "get-button-square":
				$link_style = $style = ( array_key_exists( "link_style", $vars ) ) ? $vars['link_style'] : "";
				$inner_div_style = ( array_key_exists( "inner_div_style", $vars ) ) ? $vars['inner_div_style'] : "";
				
				if( !array_key_exists( "href", $vars) )
				{
					$link_guts = 'href="#" id="' . $vars['id'] . '" process="' . $vars['process'] . '" ' . $vars['pk_name'] . '="' . $vars['pk_value'] . '"';
				}
				else
				{
					$link_guts = 'href="' . $vars['href'] . '"';
				}
				
				$return = '
				<a ' . $link_guts . ' class="orange_hover square_button bg_color_white center border_color_white rounded_corners" ' . $link_style . '>
					<div ' . $inner_div_style . '>
						' . $vars['button_value'] . '
					</div>
				</a>
				';

				break;
				
			case "selector-module-spacer":
				$return = '	
				<div class="selector_module_spacer"></div>
				';
				break;
				
			case "select-list":
				$return = '
				<select name="' . $vars['name'] . '" class="select_list ' . $vars['class'] . '">
					<option value="0">
						' . $vars['default_option'] . '
					</option>
					';
				
				foreach( $vars['options'] as $i => $info )	
				{
					$selected = ( $vars['selected_option'] == $info['id'] ) ? 'selected="selected"' : '';
					
					$return .= '
					<option value="' . $info['id'] . '" ' . $selected . '>
						' . $info['title'] . '
					</option>
					';
				}
					
				$return .= '
				</select>
				';
					
				break;
				
			case "show-missing-controller-message":
				$return = '
				<div class="missing_controller rounded_corners bg_color_light_tan border_dark_grey center font_normal header bold">
					
					<p>
						Error: Controller File <span class="color_accent">"' . $vars['requested_controller'] . '"</span> does not exist.
						<br/>						
						It is expected @ <span class="color_accent">"' . $vars['controller_path'] . '"</span>.
						<br/>
						Please contact your site administrator to remedy this problem.
						<br/>
						&lt;&nbsp;&lt; <a href="#" onClick="history.back();return false;">Go Back</a>
					</p>
					
				</div>
				';
				break;
				
			case "get-reorder-tab":
				$return = '
				<div class="item_reorder_container">
					<div class="item_reorder_bar bg_color_accent"></div>
					<div class="item_reorder_bar bg_color_accent"></div>
					<div class="item_reorder_bar bg_color_accent"></div>
				</div>
				';
				break;
				
			default:
				throw new exception( "Error: Invalid HTML command." );
				break;
		}
		
		return $return;
		
	}//getHtml()
	
	public function truncateString( $title )
	{
		return ( strlen( $title ) > 48 ) ? substr( $title, 0, 46 ) . "..." : $title; 
	}//truncateString()
	
	public function controllerFileExists( $controller_file_name )
	{
		return file_exists( $this->compileControllerLocationBasePath() . $controller_file_name );
		
	}//controllerFileExists()
	
	public function compileControllerLocationBasePath()
	{
		$paths = $this->getPathInfo();
		$env_paths = $paths[ $this->m_env ];
		
		return $env_paths['absolute'] . $env_paths['classes'] . "/controllers/";
		
	}//compileControllerLocationBasePath()
	
	/**
	 * Allows access to this classes member variables.
	 * Returns the requested member variable if not in exceptions array.
	 * @return 	mixed
	 */
	public function __get( $var_name )
	{
		$exclusions = array();
		
		if( !in_array( $var_name, $exclusions ) ) {
			$return = $this->$var_name;
		} else {
			throw new Exception( "Error: Access to member variable '" . $var_name . "' denied." );
		}
		
		return $return;
	}//get()
	
}//class Common
?>