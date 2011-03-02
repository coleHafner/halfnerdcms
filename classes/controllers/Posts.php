<?
/**
 * Controls the home page content.
 * @since	20100425, halfNerd
 * @see parent class @ 'classes/base/Controller'
 * To Create new controller: 'save as'
 */

require_once( "base/Authentication.php" );
require_once( "base/Controller.php" );
require_once( "base/User.php" );

class Posts extends Controller{
	
	/**
	 * Constructs the controller object.
	 * @return 	Index
	 * @param	array			$controller_vars		$_GET array.				
	 */
	public function __construct( $controller_vars )
	{
		parent::setControllerVars( $controller_vars );
		
		$this->m_valid_views = array(
			'all' => ""
		);
		
	}//constructor
	
	/**
	 * This sets the content for this controller. 
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() 
	{
		//validate sub-option
		$this->m_controller_vars['sub'] = $this->validateCurrentView();
		
		//grab html
		$nav = $this->getHtml( "posts-nav-options" );
		$content = $this->getHtml( $this->m_controller_vars['sub'] );
		$side_bar = Common::getHtml( 'get-side-bar', array( 'view' => $this->m_linked_objects['view'] ) );
		
		$this->m_content = '
		<div class="grid_9">
			<div class="margin_20_bottom">
				'.  $nav['html'] . '
			</div>
			
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
			case "posts-nav-options":
				break;
				
			case "all":
				break;
				
			default:
				throw new exception( "Error: Invalid HTML command '" . $cmd . "'" );
				break;
		}
		
		return $return;
		
	}//getHtml()
		
}//class Account
?>
