<?
/**
 * Controls the home page content.
 * @since	20100425, halfNerd
 * @see parent class @ 'classes/base/Controller'
 * To Create new controller: 'save as'
 */

require_once( "base/Controller.php" );
require_once( "base/Article.php" );
require_once( "base/File.php" );

class /*Controller Class Name Here*/ extends Controller{
	
	/**
	 * Constructs the controller object.
	 * @return 	Index
	 * @param	array			$controller_vars		$_GET array.				
	 */
	public function __construct( $controller_vars )
	{
		parent::setControllerVars( $controller_vars );
	}//constructor
	
	/**
	 * This sets the content for this controller. 
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() 
	{
		$valid_subs = array( "manage-articles", "manage-views", "manage-users", "manage-account" );
		$sub_option = ( in_array( $this->m_controller_vars['sub'], $valid_subs ) ) ? $this->m_controller_vars['sub'] : "greeting";
		
		$nav = $this->getHtml( "main-nav" );
		$content = $this->getHtml( $sub_option );
		
		$this->m_content = '
		<div class="grid_3">
			<div class="padder_15">
				' . $nav['html'] . '
			</div>
		</div>
		<div class="grid_9">				
			<div class="title_bar header center padder_15 bg_gradient_linear color_orange rounded_corners">
				' . $content['title'] . '
				' . $content['title_button'] . '
			</div>
			' . $content['html'] . '
		</div>
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
			case "example-cmd":
				$return = array( 'html' =>
					'<div class="example_class">
						this is an example of HTML returned by this function
					</div>' 
				);
				break;
				
			default:
				throw new Exception( "Error: Invalid HTML command." );
				break;
		}
	}//getHtml()
		
}//class Index
?>
