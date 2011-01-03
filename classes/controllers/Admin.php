<?
/**
 * Controls the content page.
 * @since	20100425, halfNerd
 */


require_once( "base/View.php" );
require_once( "base/User.php" );
require_once( "base/Article.php" );
require_once( "base/Section.php" );
require_once( "base/Controller.php" );
require_once( "base/Permission.php" );

class Admin extends Controller{
	
	/**
	 * Constructs the Contact controller object.
	 * @return 	Contact
	 * @param	array			$controller_vars		array of variables for current layout.				
	 */
	public function __construct( $controller_vars )
	{
		parent::setControllerVars( $controller_vars );
	}//constructor
	
	/**
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() {
	
		$valid_subs = array( "manage-articles", "manage-views", "manage-users", "manage-account", "manage-sections" );
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
			<div class="title_bar header center padder_15 bg_gradient_linear color_orange">
				' . $content['title'] . ' 
				' . $content['title_button'] . '
			</div>
			' . $content['html'] . '
		</div>
		
		<div class="clear"></div>
		';
	}//setContent()
	
	/**
	 * Outputs the html nav.
	 * @since	20100726, hafner
	 * @return	string
	 */
	public function getHtml( $cmd, $vars = array() )
	{
	
		switch( strtolower( trim( $cmd ) ) )
		{
			case "greeting":
				
				$title = "Welcome To The Halfnerd Admin Section";
				$title_button = '';
				
				$html = '
				<p>
					This is the administration section. This is where you can control the bits of text, images, navigation, and 
					even control the user access. It\'s the controller center for the entire site. In other words: This is where 
					all the magic happens.
				</p>
				
				<p>
					It was designed with simplicity in mind. Just click on an option in the left hand menu and you\'re on your way.
				</p>
				
				<p>
					Go ahead, give it a try. You\'ll love it!
				</p>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "main-nav":
			
				$title = "";
				$title_button = '';
				
				$basic_options = array( 
					'manage-articles' => "Manage Articles", 
					'manage-users' => "Manage Users",
					'manage-views' => "Manage Views"
				);
				
				$html = '
				<div class="admin_menu_container rounded_corners padder bg_color_light_tan">
				
					<div class="bg_color_white center header padder rounded_corners color_orange">
						Admin Options
					</div>
					
					<div class="padder_15 padder_no_top padder_no_bottom">
						<ul class="main_nav_list">
						';
						
			foreach( $basic_options as $sub => $display )
			{
				$selected = ( $this->m_controller_vars['sub'] == $sub ) ? 'class="selected"' : '';
				$pointer = ( $this->m_controller_vars['sub'] == $sub ) ? '&nbsp;&gt;&gt;' : '';
				
				$html .= '
							<li>
								<a ' . $selected . ' href="' . $this->m_common->makeLink( array( 'v' => "admin", 'sub' => $sub ) ) . '">
									' . $display . $pointer . '
								</a>
							</li>
							';
			}
						
			$html .= '
						</ul>
					</div>
						
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "manage-articles":
			
				$title = 'Add, Modify, and Delete Articles';
				
				$title_button = '
				<div class="title_button_container">
					
					' . Common::getHtml( "get-button-round", array(
						'id' => "article",
						'process' => "show_add",
						'pk_name' => "article_id",
						'pk_value' => "0",
						'button_value' => "+",
						'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
						'link_style' => 'style="float:right;"' ) ) . '
						
					' . Common::getHtml( "get-button-round", array(
						'id' => "section",
						'process' => "show_manager",
						'pk_name' => "section_id",
						'pk_value' => "0",
						'button_value' => "Manage Sections",
						'inner_div_style' => 'style="padding-top:4px;padding-left:1px;"',
						'link_style' => 'style="float:right;width:100px;font-size:10px;"'
					) ) . '

				</div>
				';
				
				$articles = Article::getArticles( "active", "1" );
				$article_list = $this->getHtml( "get-article-list", array( 'records' => $articles ) );
				
				$html = '
				<div class="item_list_container" id="article_list_container">
					' . $article_list['html'] . '
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "get-article-list":
			
				$articles = $vars['records'];
				$add_form = Article::getHtml( "get-edit-form", array( 'active_record' => new Article( 0 ) ) );
				$section_manager = Section::getHtml( "get-section-manager", array() );
				
				$html = '
				<div id="section_manager" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
					' . $section_manager['html'] . '
				</div>
				
				<div id="article_canvas_add" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
					' . $add_form['html'] . '
				</div>
				
				<ul id="article_list">
				';
				
				if( count( $articles ) > 0 )
				{
					foreach( $articles as $i => $a )
					{
						$view_form = Article::getHtml( "get-view-form", array( 'active_record' => $a ) );
						$mod_form = Article::getHtml( "get-edit-form", array( 'active_record' => $a ) );
						$delete_form = Article::getHtml( "get-delete-form", array( 'active_record' => $a ) );

						$html .= '
					<li id="item_' . $a->m_article_id . '">
						<div class="item_container padder_10 rounded_corners bg_color_light_tan" hover_enabled="1">
						
							<div id="article_info_' . $a->m_article_id . '">						
								' . $view_form['html'] . '
							</div>
												
							<div id="article_canvas_mod_' . $a->m_article_id . '" style="display:none;">
								' . $mod_form['html'] . '
							</div>
							
							<div id="article_canvas_delete_' . $a->m_article_id . '" style="display:none;">
								' . $delete_form['html'] . '
							</div>
							
							<div class="title_button_container" id="item_control" style="display:none;">
								
								' . Common::getHtml( "get-button-round", array(
									'id' => "article",
									'process' => "show_mod",
									'pk_name' => "article_id",
									'pk_value' => $a->m_article_id,
									'button_value' => "m",
									'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
									'link_style' => 'style="float:right;"' ) ) . '
									
								' . Common::getHtml( "get-button-round", array(
									'id' => "article",
									'process' => "show_delete",
									'pk_name' => "article_id",
									'pk_value' => $a->m_article_id,
									'button_value' => "x",
									'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
									'link_style' => 'style="float:right;"' ) ) . '
									
									<div class="clear"></div>
							</div>

						</div>
					</li>
					';
					
					}
				
				}
				else
				{
					$html .= '
					<li id="0">
						<div class="article_container center" style="border-style:hidden;">
							There are 0 Articles, sucka!!!
						</div>
					</li>
					';
				}
				
				$html .= '
				</ul>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "manage-views":
				$title = 'Add or Subtract Pages';
				
				$title_button = '
				<div class="title_button_container">
				
				' . Common::getHtml( "get-button-round", array(
					'id' => "view",
					'process' => "show_add",
					'pk_name' => "view_id",
					'pk_value' => "0",
					'button_value' => "+",
					'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
					'link_style' => 'style="float:right;"'
				 ) ) . '
				 
				' . Common::getHtml( "get-button-round", array(
					'id' => "view",
					'process' => "show_reorder",
					'pk_name' => "view_id",
					'pk_value' => "0",
					'button_value' => "Reorder Pages",
					'inner_div_style' => 'style="padding-top:4px;padding-left:1px;"',
					'link_style' => 'style="float:right;width:90px;font-size:10px;"'
				) ) . '

				</div>
				';
				
				$views = View::getViews( "active", "1" );
				$view_list = $this->getHtml( 'get-view-list', array( 'records' => $views, 'hover_enabled' => TRUE, 'list_type' => "normal" ) );
				
				$html = '
				<div class="item_list_container" id="view_list_container">			
					' . $view_list['html'] . '
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "get-view-list":
							
				$views = $vars['records'];
				$reorder_form = View::getHtml( "get-reorder-form", array() );
				$add_form = View::getHtml( "get-edit-form", array( 'active_record' => new View( 0 ) ) );
				$hover_enabled = ( array_key_exists( "hover_enabled", $vars ) && $vars['hover_enabled'] == TRUE ) ? "1" : "0";
				
				if( $vars['list_type'] == "normal" )
				{
					$html = '
				<div id="view_canvas_add" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
					' . $add_form['html'] . '
				</div>
				';
				}
				else if( $vars['list_type'] == "reorder" )
				{
					$html .= '
				<div id="view_canvas_reorder" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
					' . $reorder_form['html'] . '
				</div>
				';
				}
				
				$html .= '
				<ul id="view_list">
				';
				
				if( count( $views ) > 0 )
				{
					foreach( $views as $i => $v )
					{
						$view_form = View::getHtml( "get-view-form", array( 'active_record' => $v ) );
						$mod_form = View::getHtml( "get-edit-form", array( 'active_record' => $v ) );
						$delete_form = View::getHtml( "get-delete-form", array( 'active_record' => $v ) );
						
						$html .= '
						<li id="item_' . $v->m_view_id . '">
							<div class="item_container padder_10 rounded_corners bg_color_light_tan" hover_enabled="' . $hover_enabled . '">
							
								<div id="view_info_' . $v->m_view_id . '">						
									' . $view_form['html'] . '
								</div>
													
								<div id="view_canvas_mod_' . $v->m_view_id . '" style="display:none;">
									' . $mod_form['html'] . '
								</div>
								
								<div id="view_canvas_delete_' . $v->m_view_id . '" style="display:none;">
									' . $delete_form['html'] . '
								</div>
								
								<div class="title_button_container" id="item_control" style="display:none;">
								';
								
								$html .= '
									' . Common::getHtml( "get-button-round", array(
										'id' => "view",
										'process' => "show_mod",
										'pk_name' => "view_id",
										'pk_value' => $v->m_view_id,
										'button_value' => "m",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"' ) ) . '
										';
										
								if( strtolower( $v->m_controller_name ) != "admin" &&
									strtolower( $v->m_controller_name ) != "index" )
								{
									$html .= '
									' . Common::getHtml( "get-button-round", array(
										'id' => "view",
										'process' => "show_delete",
										'pk_name' => "view_id",
										'pk_value' => $v->m_view_id,
										'button_value' => "x",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"' ) ) . '
									';
								}
								
								$html .= '
										<div class="clear"></div>
									
								</div>
								';
								
								if( $vars['list_type'] == "reorder" )
								{
									$html .= Common::getHtml( "get-reorder-tab", array() );
								}
								
						$html .= '
	
							</div>
						</li>
						';
					
					}//end loop through views
					
				}//if there are any views
				else
				{
					$html .= '
						<li id="view_item_0">
							<div class="article_container center" style="border-style:hidden;">
								There are 0 Views, sucka!!!
							</div>
						</li>
					';
				}
				
				$html .= '
					</ul>
					';
				
				$return = array( 'html' => $html );
				break;
											
			case "manage-users":
				$title = "Manage Site Users";
				
				$title_button = '
				<div class="title_button_container" style="width:auto;">
				
				' . Common::getHtml( "get-button-round", array(
						'id' => "user",
						'process' => "show_add",
						'pk_name' => "user_id",
						'pk_value' => "0",
						'button_value' => "+",
						'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
						'link_style' => 'style="float:right;"') 
					) . '
					
					' . Common::getHtml( "get-button-round", array(
						'id' => "user_type",
						'process' => "show_manager",
						'pk_name' => "user_type_id",
						'pk_value' => "0",
						'button_value' => "Manage Types",
						'inner_div_style' => 'style="padding-top:4px;padding-left:1px;font-size:10px;"',
						'link_style' => 'style="float:right;width:90px;"') 
					) . '
					
					' . Common::getHtml( "get-button-round", array(
						'id' => "permission",
						'process' => "show_manager",
						'pk_name' => "permission_id",
						'pk_value' => "0",
						'button_value' => "Manage Permissions",
						'inner_div_style' => 'style="padding-top:4px;padding-left:1px;font-size:10px;"',
						'link_style' => 'style="float:right;width:120px;"') 
					) . '
				 
				 </div>
				';
				
				$users = User::getUsers( "active", "1" );
				$user_list = $this->getHtml( "get-user-list", array( 'records' => $users ) );
				$user_type_manager = UserType::getHtml( "get-manager", array() );
				
				$add_form = User::getHtml( "get-edit-form", array( 
					'active_record' => new User( 0 ),
					'active_user' => $this->m_user ) 
				);
				
				$html = '
				<div class="item_list_container">
					
					<div id="user_type_manager" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
						' . $user_type_manager['html'] . '
					</div>
					
					<div id="permission_manager" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
						' . $user_type_manager['html'] . '
					</div>

					
					<div id="user_canvas_add" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
						' . $add_form['html'] . '
					</div>
	
					<div class="rounded_corners border_dark_grey user_container" id="user_list_container">
						' . $user_list['html'] . '
					</div>
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "get-user-list":
			
				$users = $vars['records'];
				
				$items_per_row = 2;
				$num_items = count( $users );
				$num_rows = ceil( $num_items / $items_per_row );
				
				$html .= '
				<table class="user_grid">
					';
				
				if( $num_items > 0 )
				{
				
					for( $i = 0; $i < $num_rows; $i++ )
					{
						$html .= '
					<tr>			
								';
							
						for( $j = 1; $j <= $items_per_row; $j++ )
						{
						
							$key = $j + ( $items_per_row * $i );
							
							if( $key > $num_items )
							{
								//add empty cell
								$html .= '
						<td>
							&nbsp;
						</td>
						';
								break;
							}
							
							$item = $users[$key];
							$view_form = User::getHtml( "get-view-form", array( 
								'active_record' => $item,
								'active_user' => $this->m_user ) 
							);
							
							$html .= '
						<td>
							<div id="user_info_' . $item->m_user_id . '" class="item_container bg_color_light_tan border_dark_grey" hover_enabled="1" style="margin-top:0px;">
								
								' . $view_form['html'] . '
								
								<div class="title_button_container" id="item_control" style="display:none;">
									' . Common::getHtml( "get-button-round", array(
										'id' => "user",
										'process' => "delete",
										'pk_name' => "user_id",
										'pk_value' => $item->m_user_id,
										'button_value' => "m",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"') 
									) . '
									' . Common::getHtml( "get-button-round", array(
										'id' => "user",
										'process' => "show_delete",
										'pk_name' => "user_id",
										'pk_value' => $item->m_user_id,
										'button_value' => "x",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"') 
									) . '
								</div>
								
							</div>
						</td>
						';
						
						}
						
						$html .= '
					</tr>
					';	
								
					}		
				}
				else
				{
					$html .= '
					<tr>
						<td class="center" colspan="2">
							There are 0 users in this album. Check back later...
						</td>
					</tr>
					';
				}
				
				$html .= '
				</table>
				';
									
				$return = array( 'html' => $html );
				break;
				
			case "manage-account":
				$title = "Manage Your Account Info.";
				$title_button = "";
				$html = '
				<div>
				</div>';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
		}//end switch
		
		return $return;
		
	}//getNav()
	
	public function getSubNavOptions( $view_title )
	{
		switch( strtolower( $view_title ) )
		{
			case "index":
				$links = array(
					"Edit Tagline",
					"Edit Home Article",
					"Edit Features"
				);
				break;
				
			case "productions":
				$links = array(
					"Manage Videos",
					"Manage Movie Reviews"
				);
				break;
				
			case "news":
				$links = array(
					"Manage News Articles"
				);
				break;
				
			case "jobs":
				$links = array(
					"Review Submissions",
					"Manage Casting Calls"
				);
				break;
				
			case "contacts":
				$links = array(
					"Edit Contact Address",
					"Edit Contact Email",
					"Manage Crew",
					"Manage Job Titles"
				);
				break;
				
			default:
				throw new Exception( "Error: View '" . $view_title . "' is invalid." );
				break;
		}
		
		$return = '
							<ul style="position:relative;top:-5px;">
		';
		
		foreach( $links as $title )
		{
			$url_action = $this->m_common->convertViewAlias( $title, "url" );
			
			if( $url_action != $this->m_controller_vars['id1'] )
			{
				//make link
				$link = array( 
					'v' => $this->m_controller_vars['v'], 
					'sub' => strtolower( $view_title ),
					'id1' => $url_action 
				);
				
				$url = $this->m_common->makeLink( $link );
				$option = '<a href="' . $url . '" class="menu_link">' . $title . '</a>';
			}
			else 
			{
				$option = '<span class="active_sub_nav_option">' . $title . ' > ></span>';
			}
			
			$return .= '
								<li style="margin-left:-1.5em;padding-bottom:5px;">
									' . $option . '
								</li>';
		}
		
		$return .= '
							</ul>
							';
		
		return $return;
		
	}//getSubNavOptions()
	
	/**
	 * @see classes/base/Controller#getContent()
	 */
	public function getContent() {
		return $this->m_content;
	}//getContent()
		
}//class Admin
?>