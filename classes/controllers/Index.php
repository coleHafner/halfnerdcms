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
					array( 'title' => "The Lab", 'desc' => "Works in Progress", 'cmd' => 'lab'  ),
					array( 'title' => "Contact", 'desc' => "Get Ahold of Me", 'cmd' => 'contact'  ),
					array( 'title' => "Blog", 'desc' => "Read my Rants", 'cmd' => 'blog'  )
				);
				
				$html = '
				<div class="widget_holder bg_color_blue">
					<div class="widget_nav_holder">
					
						<div class="logo_holder"></div>
						
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
									<div class="padder_5_top font_med">
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
						
						<div class="social_icons">
							<div class="padder_5 center">
								<a href="http://www.facebook.com/colehafner" target="_blank">
									<img src="/images/icon_facebook_drawn.png" />
								</a>
								<a href="http://www.linkedin.com/in/colehafner" target="_blank">
									<img src="/images/icon_linkedin_drawn.png" />
								</a>
							</div>
						</div>
						
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
								<div class="padder_10 padder_15_top">
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
				<input type="hidden" id="current_slide_p" value="1" />
				<input type="hidden" id="current_slide_blog" value="1" />
				';
				
				$return = array( 'html' => $html );
				break;
				
			case 'about':
				$return = array(
					'html' => '
					<div class="font_title">
						About Me
					</div>
					
					'
				);
				break;
				
			case 'portfolio':
				
				$sites = array(
					array( 'img' => 'bts', 'client' => "Bottom Time Scuba", 'type' => "Business", 'link' => 'http://bottomtimescuba.org', 'skills' => "HTML, CSS, MYSQL, PHP, JS" ),
					array( 'img' => 'mdp', 'client' => "Madness Entertainment", 'type' => "Portfolio", 'link' => 'http://madnessentertainment.com', 'skills' => "HTML, CSS, MYSQL, PHP, JS" ),
					array( 'img' => 'pbr', 'client' => "Rebekah Hill Photography", 'type' => "Portfolio", 'link' => 'http://pbr.halfnerddesigns.com', 'skills' => "HTML, CSS, MYSQL, PHP, JS" ),
					array( 'img' => 'sbc', 'client' => "Simple Bicycle Co.", 'type' => "Business", 'link' => 'http://simplebicycleco.com', 'skills' => "HTML, CSS, MYSQL, PHP, JS" ),
					array( 'img' => 'cah', 'client' => "Cole and Heather", 'type' => "Event", 'link' => 'http://coleandheather.com', 'skills' => "HTML, CSS, MYSQL, PHP, JS" )
				);
				
				$html = '
				<div id="p_holder">
					<table class="blog_slide_table">
						<tr>
						';
				
				foreach( $sites as $i => $site )
				{
					$slide_num = $i + 1;
					
					$html .= '
							<td>
								<div class="p_slide" id="p_slide_' . $slide_num . '">
									<div style="position:relative;margin-top:12px;">
										' . $site['client'] . '
									</div>
									<div class="p_img_holder">
										<img src="/images/site_' . $site['img'] . '_big.jpg" />
									</div>
								</div>
							</td>
					';
				}
				
				$html .= '
						</tr>
					</table>
				</div>
				
				<div class="p_controls">
					<div class="padder_5" style="position:relative;">
						<a href="#" class="show_slide_p" direction="back">&lt;&lt;</a>
						&nbsp;||&nbsp;
						<a href="#" class="show_slide_p" direction="forward">&gt;&gt;</a>
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
				
				/*
				$grid_vars = array(
					'records' => $sites,
					'is_static' => FALSE,
					'records_per_row' => 2,
					'id' => 'id="portfolio_grid"',
					'extra_classes' => 'class="portfolio_grid"',
					'active_controller' => &$this,
					'html_cmd' => 'get-portfolio-grid-item',
					'empty_message' => "There are 0 portfolio entries at this time. Please check back later."
				);
				
				$grid = Common::getHtml( "display-grid", $grid_vars );
				
				$html = '
				<div class="font_title">
					Portfolio
				</div>
				<div>
					' . $grid['html'] . '
				</div>
				';
				
				$return = array( 'html' => $html );
				*/
				break;
				
			case "get-portfolio-grid-item":
				
				$site = $vars['active_record'];
				
				$html = '
				<div style="position:relative;margin-top:12px;">
					' . $site['client'] . '
				</div>
				<div style="position:relative;padding:10px 3px 10px 3px;margin-top:5px;background-color:#EAEAEA;border:1px solid #999;">
					<img src="/images/site_' . $site['img'] . '_big.jpg" style="width:350px;"/>
				</div>
				';
				
				$return = array( "html" => $html );
				break;

			case 'lab':
				$return = array(
					'html' => '
					<div class="font_title">
						Labs
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
								<div class="blog_slide" id="blog_slide_' . $slide_num . '">
									<div class="padder_5 font_title" id="blog_title_' . $slide_num . '">
										' . $blog['title'] . '
									</div>
									<div class="padder_5" id="blog_content_' . $slide_num . '">
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
					<div class="padder_5" style="position:relative;padding-top:1px;padding-left:1px;">
						<table class="blog_nav">
							<tr>
								<td>
									<div class="rounded_corners border_solid_grey blog_search_holder">
										<div style="position:relative;float:left;">
											<input type="text" id="blog_search_term" style="margin-right:5px;" class="color_blue"/>
										</div>
										<div id="blog_search_button">
											Search
										</div>
										<div class="clear"></div>
									</div>
								</td>
								<!--
								<td>
									<a href="#" class="rounded_corners border_solid_grey" id="blog_search_button" style="margin-right:10px;">Search</a>
								</td>
								-->
								';
				
				foreach( $blog_entries as $i => $blog )
				{
					$slide_num = $i + 1;
					$title = ( $slide_num == 1 ) ? "Home" : $slide_num;
					$selected = ( $slide_num == 1 ) ? 'selected_blog' : '';
					
					$html .= '
								<td>
									<a id="blog_control_' . $slide_num . '" class="show_slide_blog rounded_corners border_solid_grey ' . $selected . '" href="#" slide_num="' . $slide_num . '">' . $title . '</a>
								</td>
							';
				}
				
				$html .= '
							</tr>
						</table>
						
						<!--
						<div class="blog_controls_tab padder_5_top center">
							<a href="#" class="font_small">X</a>
						</div>
						-->
						
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
