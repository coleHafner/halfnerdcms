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
		
		
		if( in_array( $this->m_common->m_env, Common::constructionEnvironments() ) )
		{
			$content = Common::getHtml( "construction-message", array() ); 
		}
		else
		{
			$content = $this->getHtml( $this->m_controller_vars['sub'], array() );
		}
		
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
				$slides = self::getNavItems();
				
				$html = '
				<div class="widget_holder">
					
					<div class="widget_content_holder">
						<div id="slide_holder">
						';
			
				foreach( $slides as $i => $slide )
				{
					$slide_num = $i + 1;
					$content = $this->getHtml( $slide['cmd'], array() );
					
					$html .= '
							<div class="slide">
								<div class="padder_10_top">
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
				
				<div class="port_controls" style="display:none;">
					
					<!--
					<div class="port_control_grid">
						<a href="#" class="overlay show_slide_p" slide_num="1"></a>
					</div>
					-->
					
					<div class="port_control_left">
						<a href="#" class="overlay show_slide_p" direction="back"></a>
					</div>
					
					<div class="port_control_right">
						<a href="#" class="overlay show_slide_p" direction="forward"></a>
					</div>
					
					<div class="clear"></div>
				</div>
				
				
				<input type="hidden" id="max_slides_v" value="' . count( $slides ) . '" />
				<input type="hidden" id="current_slide_v" value="1" />
				<input type="hidden" id="current_slide_v_name" value="' . $slides[0]['cmd'] . '" />
				
				<input type="hidden" id="max_slides_h" value="' . ( count( $p_entries ) + 1 ) . '" />
				<input type="hidden" id="current_slide_h" value="1" />
				
				<input type="hidden" id="feature_current" value="2" />
				<input type="hidden" id="feature_state" value="playing" />';
				
				//slide key
				foreach( $slides as $i => $slide )
				{
					$slide_num = $i + 1;
					
					$html .= '
				<input type="hidden" id="slide_v_key_' . $slide_num . '" name="' . $slide['cmd'] . '" />';
				}
				
				$return = array( 'html' => $html );
				break;
				
			case 'about':
				
				$counter = 1;
				$sites = $this->getPortfolioEntries();
				
				$html = '
				<div>
					<div class="featured_container box_shadow bg_white">
						<div class="padder_10">
						
							<div class="featured_nav_container">
								<div class="featured_nav">
								';
				foreach( $sites as $site )
				{
					if( $site['featured'] === TRUE )
					{ 
						$spacer = ( $counter == 3 ) ? '' : '<div class="item spacer"></div>';
						$selector = ( $counter != 1 ) ? ' featured_selector_inactive' : ' featured_selector_active'; 
					
						$html .= '
									<div class="item bg_dark">
										<div class="featured_thumb_tiny bg_white"><img src="/images/site_' . $site['img'] . '_thumb.jpg" /></div>
										<div class="featured_selector' . $selector . '"></div>
										<a href="#" class="overlay" feature_num="' . $counter . '" id="feature_slide_' . $counter . '"></a>
									</div>
									' . $spacer;
						
						$counter++;
						
					}//if featured
					
				}//loop through sites
				
				$html .= '
									
								</div>
							</div>
							
							<div class="featured_photo_container">
								<div class="padder_10_left">
									<div class="featured_photo_bg bg_dark">
									';
					
				//reset counter
				$counter = 1;
				
				foreach( $sites as $site )
				{
					if( $site['featured'] === TRUE )
					{ 
						$display = ( $counter == 1 ) ? '' : ' style="display:none;"';
					
						$html .= '
										<div class="featured_photo" id="photo_' . $counter . '"' . $display . '><img src="/images/site_' . $site['img'] . '_mid.jpg" /></div>
										';
						$counter++;
						
					}//if featured
					
				}//loop through sites
				
				$html .= '
									</div>
								</div>
							</div>
							
							<div class="featured_blurb_container">
								<div class="padder_10_left">
									<div class="featured_blurb_bg">
									';
				
				//reset counter
				$counter = 1;
				
				foreach( $sites as $site )
				{
					if( $site['featured'] === TRUE )
					{ 
						$display = ( $counter == 1 ) ? '' : ' style="display:none;"';
					
						$html .= '
										<div class="featured_blurb" id="blurb_' . $counter . '" ' . $display . '>
											<div class="padder_10_bottom">
												<b>' . $site['client'] . '</b>
											</div>
											
											<div class="default_line_height">
												' . stripslashes( $site['desc'] ) . '
											</div>
											<div class="featured_blurb_link">
											';
											
						if( $site['link'] !== FALSE )
						{
							$html .= '
												<a href="' . $site['link'] . '" target="_blank">Launch Site&nbsp;&gt;&nbsp;&gt;</a>
												';
						}
						
						$html .= '
											</div> 
										</div>
										';
						
						$counter++;
						
					}//if featured
					
				}//loop through sites
				
				$html .= '
									</div>
								</div>
							</div>
							
							<div class="clear"></div>
							
						</div>
					</div>
				</div>
			
				<div>
					
					<div class="about_skills">
						<div class="padder_15_top">
							<div class="padder_10_bottom">
								<img src="/images/header_skills.png" />
							</div>
							<img src="/images/about_skills.png" />
						</div>
					</div>
				
					<div class="about_me">
						<div class="padder_15_top">
							<div class="padder_10_bottom">
								<img src="/images/header_about.png" />
							</div>
							';
				
				//grab content
				$ab_art = Article::getArticleFromTags( "index", "about_me_blurb" );
				
				if( $ab_art !== FALSE )
				{
					$a_vars = array( 
						'paragraphs' => $ab_art[0]->splitBody(), 
						'open_tag' => '<div class="padder_10_bottom default_line_height">',
						'close_tag' => '</div>' 
					);
					
					$p_text = Article::getHtml( "pretty-article", $a_vars );
					$html .= $p_text['html'];
				}
				
				$html .= '
						</div>
					</div>
					
					<div class="clear"></div>
					
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case 'portfolio':
				
				$slide_num = 2;
				$sites = $this->getPortfolioEntries();
				
				$grid_vars = array( 
					'records' => $sites,
					'records_per_row' => 3,
					'html_cmd' => "portfolio-grid-item",
					'active_controller' => $this,
					'is_static' => FALSE,
					'extra_classes' => 'class="port_grid padder_15_top"'
				);
				
				$port_grid = $this->m_common->getHtml( "display-grid", $grid_vars );
				
				$html = '
				<div id="p_holder">
					<table class="blog_slide_table">
						<tr>
							<td>
								<div class="p_slide" id="p_slide_1">
									<div class="port_spacer">
										<div class="padder_10_bottom">
											<img src="/images/header_work.png" />
										</div>
										
										<div>
										';
				$p_art = Article::getArticleFromTags( "index", "portfolio_blurb" );
				
				if( $p_art !== FALSE )
				{
					$html .= $p_art[0]->m_body;
				}
				
				$html .= '
											 
										</div>
									</div>
									
									<div class="port_container bg_dark box_shadow">
										<div class="padder_10">
											<div class="port_inner bg_white">
												' . $port_grid['html'] . '
											</div>
										</div>
									</div>
									
								</div>
							</td>
						';
				
				foreach( $sites as $i => $site )
				{
					$html .= '	<td>
								<div class="p_slide" id="p_slide_' . ( $i + 1 ) . '">
								
									<div class="port_spacer">&nbsp;</div>
									
									<div class="port_container bg_dark box_shadow">
									
										<div class="padder_10">
										
											<div class="port_inner bg_white">
											
												<div class="padder_10">
												
													<table class="port_content_inner">
														<tr>
														
															<td class="photo bg_tan">
																<img src="/images/site_' . $site['img'] . '_large.jpg" />
															</td>
															
															<td class="summary">
																<div class="port_summary_container">
																	<div class="padder_10">
																	
																		<table class="port_skills">
																					';
					foreach( $site['features'] as $feature )
					{
						$html .= '
																			<tr>
																				<td>
																					' . $feature . '
																				</td>
																			</tr>
																			';
					}						
					
					$html .= '
																		</table>
																		
																		<div class="port_blurb_container bg_tan box_shadow margin_10_top">
																			<div class="padder_10 default_line_height">
																				' . stripslashes( $site['desc'] ) . '
																			</div>
																		</div>
																		
																		<div class="port_launch_site">
																		';
					if( $site['link'] !== FALSE )
					{
						$html .= '
																			<a href="' . $site['link'] . '" target="_blank">Launch Site&nbsp;&gt;&nbsp;&gt;</a>
																			';
					}
					
					$html .= '
																		</div>
																		
																	</div>
																</div>
															</td>
															
														</tr>
													</table>
													
													<div class="port_title bg_red box_shadow">
														<div class="padder_10 port_title_text">
															' . $site['client'] . '
															
														</div>
														<div class="logo_ne"></div>
													</div>
													
												</div>
												<!--padder-->
												
											</div>
											<!--inner-->
											
										</div>
										<!--padder-->
										
									</div>
									<!--container-->
									
								</div>
								<!--slide-->
								
							</td>';
				}
				
				$html .= '
						</tr>
					</table>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "portfolio-grid-item":
				
				$r = $vars['active_record'];
				
				$html = '
				<div class="port_grid_item_container bg_tan box_shadow">
					<img src="/images/site_' . $r['img'] . '_thumb_mid.jpg" />
					<a href="#" class="overlay bg_white opacity_70 show_slide_p port_magnify" slide_num="' . ( $vars['item_num'] + 1 ) . '" style="display:none;"></a>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case 'contact':
				
				$html = '
				<div class="con_container">
				
					<div class="padder_10_bottom">
						<img src="/images/header_contact.png" />
					</div>
					';
				
				$c_art = Article::getArticleFromTags( "index", "contact_blurb" );
				
				if( $c_art !== FALSE )
				{
					$a_vars = array( 
						'paragraphs' => $c_art[0]->splitBody(), 
						'open_tag' => '<div class="padder_10_bottom default_line_height">',
						'close_tag' => '</div>' 
					);
					
					$p_text = Article::getHtml( "pretty-article", $a_vars );
					$html .= $p_text['html'];
				}
				
				$html .= '
				</div>
				
				<div class="con_spacer">
					&nbsp;
				</div>
				
				<div class="con_container bg_tan box_shadow">
					<div class="padder_15">
						<div class="result_message"></div>
						<form name="email_form" id="email_form">
						
							<div>
								<span class="title_span con_label">
									Email:
								</span>
								<input type="text" class="text_input con_input" name="contact_email" />
							</div>
							
							<table class="con_selector_split padder_25_top">
								<tr>
								
									<td class="selector">
										<span class="title_span con_label">
											Inquiry:
										</span>
										<select style="width:100%;" name="contact_inquiry">
											<option value="General Question">General Question</option>
											<option value="Project Request">Project Request</option>
										</select>
									</td>
									
									<td class="spacer">&nbsp;</td>
									
									<td class="selector">
										<!--
										<span class="title_span con_label">
											Budget:
										</span>
										<select style="width:100%;" name="contact_budget">
											<option value="0">$1</option>
										</select>
										-->
									</td>
									
								</tr>
							</table>
							
							<div class="padder_25_top">
								<span class="title_span con_label">
									Message:
								</span>
								<textarea class="con_textarea" name="contact_message"></textarea>
							</div>
							
							<div class="padder_10_top padder_5_right">
								<div class="con_button bg_red center" id="mail">
									<div class="padder_10_top">
										Send
									</div>
									<a href="#" class="overlay_absolute"></a>
								</div>
							</div>
							
						</form>
					</div>
				</div>
				
				<div class="clear"></div>
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
		$return = array(
		
			array( 
				'img' => 'bts', 
				'client' => "Bottom Time Scuba", 
				'type' => "Business", 
				'link' => 'http://bottomtimescuba.org', 
				'features' => array( "Cross Browser Compliant", "Custom Framework", "Built in CMS" ), 
				'desc' => "This is my first site. It was a fun little project for a local scuba shop. It was all done in procedural PHP. I added a custom CMS for the client. ",
				'featured' => FALSE 
			),
			
			array( 
				'img' => 'mdp', 
				'client' => "Madness Entertainment", 
				'type' => "Portfolio", 
				'link' => FALSE, 
				'features' => array( "Youtube API Integration", "Custom Framework", "Built in CMS" ),
				'desc' => "This project was for a friend\'s production studio. It integrates with Google\'s YouTube API, so they can showcase their videos via their youTube account.<br/><br/>The client is in the process of switching hosts. It will be online soon.",
				'featured' => FALSE 
			),
			
			array( 
				'img' => 'pbr', 
				'client' => "Rebekah Hill Photography", 
				'type' => "Portfolio", 
				'link' => FALSE, 
				'features' => array( "Google Photo API Integration", "Custom Framework", "Built in CMS" ),
				'desc' => "This site is still in production. It is a photography site made for my friend. It integrates with Google\'s Picasa API and allows content management from Google\'s Picasa service.<div class=\"padder_5_top\">Since this site is still under development.</div>",
				'featured' => TRUE 
			),
			
			array( 
				'img' => 'sbc', 
				'client' => "Simple Bicycle Co.", 
				'type' => "Business", 
				'link' => 'http://simplebicycleco.com', 
				'features' => array( "Cross Browser Compliant", "Custom Framework", "Built in CMS" ),
				'desc' => "This site is for a custom frame maker in Washington. It was built on my framework and customized to give my client complete control of the site\'s content.",
				'featured' => FALSE 
			),
			
			array( 
				'img' => 'cah', 
				'client' => "Cole and Heather", 
				'type' => "Event", 
				'link' => 'http://coleandheather.com', 
				'features' => array( "RSVP Tracking System", "Cross Browser Compliant", "Google Maps Integration" ),
				'desc' => "This is a personal project for my upcoming wedding. It was built on my framework and has a RSVP guest system built in. It also integrates with Google Maps API for easy directions to the wedding.",
				'featured' => TRUE 
			),
			
			array( 
				'img' => 'hfn', 
				'client' => "Halfnerd Framework", 
				'type' => "Personal", 
				'link' => FALSE, 
				'features' => array( "Cross Browser Compliant", "Custom Framework", "Built in Permissions System" ),
				'desc' => "This is the UI for my custom PHP framework. It provides an administration interface for developers and clients alike.<div class=\"padder_5_top\">I have plans to release this framework under the GLP license. It will be soon be available.</div>",
				'featured' => TRUE
			)
		);
		
		return array_reverse( $return );
		
	}//getPortfolioEntries()
	
	public static function getNavItems()
	{
		return array(
			array( 'cmd' => "about" ),
			array( 'cmd' => "portfolio" ),
			array( 'cmd' => "contact" )
		);
		
	}//getNavItems()
		
}//class Index
?>