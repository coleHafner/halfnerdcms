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
				
				$p_entries = $this->getPortfolioEntries();
				
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
				<input type="hidden" id="max_slides_p" value="' . count( $p_entries ) . '" />
				';
				
				$return = array( 'html' => $html );
				break;
				
			case 'about':
				$return = array(
					'html' => '
					
					<div class="about_img bg_color_blue">
						<img src="/images/about_img.jpg" />
					</div>
					
					<div class="font_title padder_5_bottom">
						About Me
					</div>
					
					<span class="default_line_height">
						
						Hi. My name is Cole. Technology is my friend. I am a developer by heart, but I am slowly making my way into design.
						More specifically, I am efficient in object oriented PHP, CSS, HTML, jQuery, and Javascript. I also design relational
						databases. Combine all these skills together and you get one very well rounded developer!
					</span>
					
					<div class="font_title padder_5_bottom" style="padding-top:10px;">
						Qualifications
					</div>
						
					<span class="default_line_height">
						I graduated from Southern Oregon University in 2008 with a bachelors degree in Computer Science. Since then I\'ve been
						working at a local advertising agency called Steelhead Advertising. My experience at Steelhead has allowed me to get a taste
						for every aspect of web development. I\'ve done server side scripting, data importing, interface enhancement, database 
						design, cross browser compatibility optimization, query building. . . You name it, I\'ve done it.
					</span>
						
					<table style="position:relative;margin-top:20px;width:100%;">
						<tr>
							<td style="width:47%;vertical-align:top;">
								<div class="font_title padder_5_bottom">
									Work Ethic
								</div>
								<span class="default_line_height">
									I work very hard to make my clients happy and the final product is always quality. I really try to get a sense of
									my client\'s vision of the site. My websites are feature rich and I value simple, intuitive interfaces. I am active
									in design communities and I have my fingers on the pulse of cutting edge techniques. Check out the portfolio section 
									for examples of my work.  
								</span>
							</td>
							
							<td style="width:5%;">
								&nbsp;
							</td>
							
							<td style="width:48%;vertical-align:top;">
								<div class="font_title padder_5_bottom">
									Just For Fun
								</div>
								<span class="default_line_height">
									I live in southern Oregon with my fiance and my dog. I live an active lifestyle and enjoy running. When I\'m not working 
									I enjoy scuba diving and taking my dog on hikes in the Ashland hills. I like most kinds of music, but usually listen to 
									classical piano when I write code.    
								</span>
							</td>
						</tr>
					</table>
					'
				);
				break;
				
			case 'portfolio':
				
				$sites = $this->getPortfolioEntries();
				
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
									<div class="p_header font_title">
										' . $site['client'] . '
									</div>
									<div class="p_img_holder">
										<img src="/images/site_' . $site['img'] . '_big.jpg" />
									</div>
									<div style="position:relative;margin-top:5px;">
										<table style="position:relative;width:100%;">
											<tr>
												<td style="vertical-align:top;" class="default_line_height">
													' . stripslashes( $site['desc'] ) . '
												</td>
												<td style="text-align:right;width:215px;vertical-align:middle;" class="default_line_height">
												
													<span style="position:relative;font-weight:bold;font-size:13px;" >
														' . $site['skills'] . '
													</span>
													<br/>
													
													<a href="' . $site['link'] . '" target="_blank">
														Launch Site&nbsp;&nbsp;
													</a>
													
												</td>
											</tr>
										</table>
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
					<div class="padder_5" style="position:relative;width:140px;">
						<div style="position:relative;float:left;">
							<a href="#" class="show_slide_p" direction="back">&lt;&lt; Prev</a>
						</div>
						<div style="position:relative;float:right;">		
							<a href="#" class="show_slide_p" direction="forward">Next &gt;&gt;</a>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case 'lab':
				$return = array(
					'html' => '
					<div class="font_title slide_header">
						Labs
					</div>'
				);
				break;
				
			case 'contact':
				$return = array(
					'html' => '
					<div class="font_title slide_header">
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
	
	public function getPortfolioEntries()
	{
		return array(
		
			array( 
				'img' => 'bts', 
				'client' => "Bottom Time Scuba", 
				'type' => "Business", 
				'link' => 'http://bottomtimescuba.org', 
				'skills' => "HTML, CSS, MYSQL, PHP", 
				'desc' => "This is my first site. It was a fun little project for a local scuba shop. It was all done in procedural PHP. I added a custom CMS for the client. " 
			),
			
			array( 
				'img' => 'mdp', 
				'client' => "Madness Entertainment", 
				'type' => "Portfolio", 
				'link' => 'http://madnessentertainment.com', 
				'skills' => "HTML, CSS, MYSQL, PHP, jQuery", 
				'desc' => "This project was for a friend\'s production studio. It integrates with Google\'s YouTube API, so they can showcase their videos via their youTube account." 
			),
			
			array( 
				'img' => 'pbr', 
				'client' => "Rebekah Hill Photography", 
				'type' => "Portfolio", 
				'link' => 'http://pbr.halfnerddesigns.com', 
				'skills' => "HTML, CSS, MYSQL, PHP, jQuery", 
				'desc' => "This site is still in production. It was made for my photographer friend and integrates with Google\'s Picasa API so the client can manage their photos via her Picasa account." 
			),
			
			array( 
				'img' => 'sbc', 
				'client' => "Simple Bicycle Co.", 
				'type' => "Business", 
				'link' => 'http://simplebicycleco.com', 
				'skills' => "HTML, CSS, MYSQL, PHP, jQuery", 
				'desc' => "This site is for a custom frame maker in Washington. It was built on my framework and customized to give my client complete control of the site\'s content." 
			),
			
			array( 
				'img' => 'cah', 
				'client' => "Cole and Heather", 
				'type' => "Event", 
				'link' => 'http://coleandheather.com', 
				'skills' => "HTML, CSS, MYSQL, PHP, JS", 
				'desc' => "This is a personal project for my upcoming wedding. It was built on my framework and has a RSVP guest system built in. It also integrates with Google Maps API for easy directions to the wedding." 
			)
		);
		
	}//getPortfolioEntries()
		
}//class Index
?>
