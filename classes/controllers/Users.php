<?
/**
 * Controls the home page content.
 * @since	20100425, halfNerd
 * @see parent class @ 'classes/base/Controller'
 * To Create new controller: 'save as'
 */

require_once( "base/Controller.php" );
require_once( "base/User.php" );

class Users extends Controller{
	
	/**
	 * Constructs the controller object.
	 * @return 	Index
	 * @param	array			$controller_vars		$_GET array.				
	 */
	public function __construct( $controller_vars )
	{
		parent::setControllerVars( $controller_vars );
		
		$this->m_valid_views = array(
			'all' => "", 
			'search' => "" 
		);
		
	}//constructor
	
	/**
	 * This sets the content for this controller. 
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() 
	{
		//check if user edit has been requested
		if( !in_array( $this->m_controller_vars['sub'], array_keys( $this->m_valid_views ) ) )
		{
			if( !User::usernameExists( $this->m_controller_vars['sub'] ) )
			{
				//if no user with that name exists, default to default view
				$this->m_controller_vars['sub'] = $this->validateCurrentView();
			}
		}
		
		$content = $this->getHtml( $this->m_controller_vars['sub'] );
		$side_bar = Common::getHtml( "get-side-bar", array( 'view' => $this->m_linked_objects['view'] ) );
		
		$this->m_content = '
		<div class="grid_9">
			' . $content['html'] . '
		</div>
		<div class="grid_3">
			' . $side_bar['html'] . '
		</div>
		<div class="clear"></div>
		';
				
	}//setContent()
	
	/**
	 * Wrapper function that provides access to this conroller's "m_content" member variable.
	 * @see classes/base/Controller#getContent()
	 */
	public function getContent() 
	{
		return $this->m_content;
	}//getContent()
	
	/**
	 * This is a place to setup all the HTML code you're likely to use more than once.
	 * @return	array
	 * @var		string			$cmd		string to tell which html to grab
	 * @var		array			$vars		optional - array of options for the current command		 
	 */
	public function getHtml( $cmd, $vars = array() ) 
	{
		switch( strtolower( trim( $cmd ) ) )
		{
			case "all":
				
				$users = User::getUsers( "active", "1" );
				$search_bar = self::getHtml( "get-search-bar" );
				
				$headline = self::getHtml( "get-headline", array(
					'title' => "View All Users",
					'extra_content' => $search_bar['html'] ) 
				);
				
				
				
				$grid_vars = array(
					'records' => $users,
					'is_static' => TRUE,
					'records_per_row' => 2,
					'id' => 'id="user_grid"',
					'extra_classes' => 'class="user_grid"',
					'active_controller' => "User",
					'html_cmd' => 'get-view-form-badge',
					'html_vars' => array( 'show_controls' => FALSE, 'hover_enabled' => FALSE ),
					'empty_message' => "There are 0 users ( how are you logged in? ). Click the '+' button to add one."
				);
				
				$grid = Common::getHtml( "display-grid", $grid_vars );
				
				$html = 
				$headline['html'] . '
				
				<div class="border_dark_grey rounded_corners">
				
					<div class="padder">
						' . $grid['html'] . '
					</div>
				</div>
				';
									
				$return = array( 'html' => $html );
				break;
					
			case "search":
				
				$search_bar = self::getHtml( "get-search-bar", array( 'search_term' => $this->m_controller_vars['id1'] ) );
				
				$headline = self::getHtml( "get-headline", array(
					'title' => 'Search Results For "' . $this->m_controller_vars['id1'] . '"',
					'extra_content' => $search_bar['html'] ) 
				);
				
				$empty_message = ' 
				<div class="center">
					Your Search Returned 0 Results.
					<br/><br/> 
					<a href="' . $this->m_common->makeLink( array( 
						'v' => "users",
						'sub' => "all" ) ) . '"> 
						&lt;&lt; View All Users
					</a>
				</div>
				';
				
				$grid_html = User::getHtml( "get-user-grid", array(
					'records' => User::getUserSearchResults( $this->m_controller_vars['id1'] ), 
					'container_class' => "user_badge",
					'show_controls' => FALSE,
					'hover_enabled' => "0",
					'empty_message' => $empty_message ) 
				);
				
				$html = 
				$headline['html'] . '
				
				<div class="border_dark_grey rounded_corners">
				
					<div class="padder">
						' . $grid_html['html'] . '
					</div>
				</div>
				';
									
				$return = array( 'html' => $html );
				break;
				
			case "get-search-bar":
				
				$search_term = ( array_key_exists( "search_term", $vars ) ) ? $vars['search_term'] : "Search...";
				
				$html = '		
				<table>
					<tr>
						<td>
							<input id="user_search_term" type="text" class="user_auto_search text_input_button_height text_med input_clear color_accent" clear_if="Search..." value="' . $search_term . '" />
							<input id="user_base_url" type="hidden" value="' . $_SERVER['SERVER_NAME'] . '" />
						</td>
						<td>' .
						 
							Common::getHtml( "get-button", array(
								'id' => "user",
								'process' => "search",
								'pk_name' => "user_id",
								'pk_value' => "0",
								'button_value' => "Search",
								'extra_style' => 'style="position:relative;width:41px;"' ) 
							) . '
						</td>
					</tr>
				</table>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "get-headline":
				
				$extra_content = ( array_key_exists( "extra_content", $vars ) ) ? $vars['extra_content'] : "";
				
				$html = '
				<div class="padder bg_gradient_linear bg_color_tan margin_20_bottom">
				
					<div class="header color_orange" style="position:relative;float:left;padding-top:8px;">
						' . $vars['title'] . '
					</div>
					
					<div style="position:relative;float:right;">
						' . $extra_content . '
					</div>

					<div class="clear"></div>
					
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			default:
				
				//view user profile
				$user_id = $this->m_common->m_db->getIdFromTitle( $cmd, array(
					'pk_name' => "user_id", 
					'table' => "common_Users",
					'title_field' => "username" )
				);
				
				//new user
				$u = new User( $user_id );
				$user_html = User::getHtml( "get-view-form-full", array( 'active_record' => &$u ) );
				
				//show user profile
				$html = '
				<div class="rounded_corners bg_color_light_tan">
					<div class="padder_10">
						' . $user_html['html'] . '
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
		}
		
		return $return;
		
	}//getHtml()
		
}//class Index
?>
