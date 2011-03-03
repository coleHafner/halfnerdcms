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
		
		$this->m_valid_views = array(
			'slider' => "Home"
		);
		
	}//constructor
	
	/**
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() 
	{
		$this->m_controller_vars['sub'] = $this->validateCurrentView();
		$content = $this->getHtml( $this->m_controller_vars['sub'], array() );
		
		//grab home article
		$this->m_content = '
		<div class="grid_12">
			' . $content['html'] . '
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
			case "slider":
				
				$slides = array(
					array( 'title' => "About", 'desc' => "Learn About Me", 'cmd' => 'about'  ),
					array( 'title' => "Portfolio", 'desc' => "See My Work", 'cmd' => 'portfolio'  ),
					array( 'title' => "Contact", 'desc' => "Get Ahold of Me", 'cmd' => 'contact'  ),
					array( 'title' => "Blog", 'desc' => "Read my Rants", 'cmd' => 'blog'  )
				);
				
				$html = '
				<div class="widget_holder">
					<div class="widget_nav_holder">
						<ul class="slide_controls">
						';
					
					foreach( $slides as $i => $slide )
					{
						$slide_num = $i + 1;
						$selected = ( $slide_num == 1 ) ? 'class="selected"' : '';
						
						$html .= '
						<li id="nav_item' . $slide_num . '" ' . $selected . '>
							<div class="nav_item_icon_holder">
								<div class="nav_item_icon rounded_corners" id="icon' . $slide_num . '">&nbsp;</div>
							</div>
							
							<div class="nav_item_desc_holder">
								<span class="font_title">' . $slide['title'] . '</span>
								<div class="padder_5_top font_sub">
									' . $slide['desc'] . '
								</div>
							</div>
							
							<div class="clear"></div>
							<a href="#" class="show_slide" slide_num="' . $slide_num . '"></a>
							
						</li>
						';
					} 
					
					$html .= '
						</ul>
					</div>	
					
					<div class="widget_content_holder">
						<div id="slide_holder">
					';
					
					foreach( $slides as $i => $slide )
					{
						$slide_num = $i + 1;
						$content = $this->getHtml( $slide['cmd'], array() );
						
						$html .= '
							<div class="slide">
								<div class="padder_10 padder_15_top padder_no_bottom">
									' . $content['html'] . '
								</div>
							</div>
							';
					}
					
					$html .= '
						</div>
					</div>
					
					<div class="clear"></div>
				</div>
				
				<input type="hidden" id="current_slide" value="1" />
				<input type="hidden" id="current_slide_blog" value="1" />
				';
				
				$return = array( 'html' => $html );
				break;
				
			case 'about':
				$return = array(
					'html' => '
					<div class="font_title">
						About
					</div>
					'
				);
				break;
				
			case 'portfolio':
				$return = array(
					'html' => '
					<div class="font_title">
						Portfolio
					</div>'
				);
				break;
				
			case 'contact':
				$return = array(
					'html' => '
					<div class="font_title">
						Contact Me
					</div>'
				);
				break;
				
			case 'blog':
				
				$blog_entries = array( 
					array( 'title' => 'Welcome to Blogville. Population me.', 'content' => "this is test content" ),
					array( 'title' => 'Blog1.', 'content' => "this is test content1" ),
					array( 'title' => 'Blog2.', 'content' => "this is test content2" ),
					array( 'title' => 'Blog3.', 'content' => "this is test content3" ) 
				);
				
				$html = '
				<div id="blog_holder">
					<table class="blog_slide_table">
						<tr>
						';
				
				foreach( $blog_entries as $i => $blog )
				{
					$slide_num = $i + 1;
					
					$html .= '
							<td>
								<div class="blog_slide">
									<div class="padder_5 font_title">
										' . $blog['title'] . '
									</div>
									<div class="padder_5">
										' . $blog['content'] . '
									</div>
								</div>
							</td>
					';
				}
				
				$html .= '
						</tr>
					</table>
				</div>
				
				<div class="blog_controls">
					<div class="padder_5">
				';
				
				foreach( $blog_entries as $i => $blog )
				{
					$slide_num = $i + 1;
					
					$html .= '
						<a class="show_slide_blog" href="#" slide_num="' . $slide_num . '">' . $slide_num . '</a> &nbsp;
						';
				}
				
				$html .= '
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			default:
				throw new Exception( "Error: Invalid HTML command." );
				break;
		}
		
		return $return;
		
	}//getHtml()
		
}//class Index
?>
