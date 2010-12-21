<?
/**
 * Controls the home page content.
 * @since	20100425, halfNerd
 */

require_once( "base/Controller.php" );
require_once( "base/Article.php" );
require_once( "base/File.php" );

class Index extends Controller{
	
	/**
	 * Constructs the Index controller object.
	 * @return 	Index
	 * @param	array			$controller_vars		array of variables for current layout.				
	 */
	public function __construct( $controller_vars )
	{
		parent::setControllerVars( $controller_vars );
	}//constructor
	
	/**
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() 
	{
		
		//grab home article
		$this->m_content = '
		<div class="grid_12 center">
			Hello world
		</div>
		<div class="clear"></div>
		';
		
	}//setContent()
	
	/**
	 * @see classes/base/Controller#getContent()
	 */
	public function getContent() 
	{
		return $this->m_content;
	}//getContent()
	
	public function getHtml( $cmd, $vars = array() ) 
	{
		switch( strtolower( trim( $cmd ) ) )
		{
			default:
				throw new Exception( "Error: Invalid HTML command." );
				break;
		}
	}//getHtml()
		
}//class Index
?>