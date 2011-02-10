<?
/**
 * Controls the content page.
 * @since	20100425, halfNerd
 */

require_once( "base/View.php" );
require_once( "base/User.php" );
require_once( "base/Article.php" );
require_once( "base/Section.php" );
require_once( "base/Setting.php" );
require_once( "base/Controller.php" );
require_once( "base/Permission.php" );
require_once( "base/Authentication.php" );

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
	public function setContent() 
	{
		$valid_subs = array( 
			"manage-posts", 
			"manage-pages", 
			"manage-users",
			"manage-settings",
			"manage-sections",
			"manage-permissions" 
		);
		
		$this->m_controller_vars['sub'] = ( in_array( $this->m_controller_vars['sub'], $valid_subs ) ) ? $this->m_controller_vars['sub'] : "greeting";
		$content = $this->getHtml( $this->m_controller_vars['sub'] );
		$nav = $this->getHtml( "admin-nav" );
		
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
				
			case "admin-nav":
			
				$title = "";
				$title_button = '';
				
				$basic_options = array( 
					'manage-pages' => "Pages",
					'manage-permissions' => "Permissions",
					'manage-posts' => "Posts",
					'manage-settings' => "Settings",
					'manage-users' => "Users"
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
				
			case "manage-posts":
			
				$title = 'Manage Posts';
				
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
				$article_list = $this->getHtml( "get-post-list", array( 'records' => $articles ) );
				
				$html = '
				<div class="item_list_container" id="article_list_container">
					' . $article_list['html'] . '
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "get-post-list":
			
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
				
			case "manage-pages":
				$title = 'Manage Pages';
				
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
				$view_list = $this->getHtml( 'get-page-list', array( 'records' => $views, 'hover_enabled' => TRUE, 'list_type' => "normal" ) );
				
				$html = '
				<div class="item_list_container" id="view_list_container">			
					' . $view_list['html'] . '
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "get-page-list":
							
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
				$title = "Manage Users";
				
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
				 
				 </div>
				';
				
				$user_type_manager = UserType::getHtml( "get-manager", array() );
				$user_list = $this->getHtml( "get-user-list", array( 'records' => User::getUsers( "active", "1" ) ) );
				$add_form = User::getHtml( "get-edit-form", array( 'active_record' => new User( 0 ) ) );
				
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
				$return = User::getHtml( "get-user-grid", array(
					'records' => $vars['records'], 
					'container_class' => "item_container",
					'container_style' => 'style="margin-top:0px;"',
					'show_controls' => TRUE,
					'hover_enabled' => "1" ) 
				);
				break;
				
			case "manage-permissions":
				$title = "Manage User Permissions";
				
				$title_button = '
				<div class="title_button_container" style="width:auto;">
				
				' . Common::getHtml( "get-button-round", array(
						'id' => "permission",
						'process' => "show_add",
						'pk_name' => "permission_id",
						'pk_value' => "0",
						'button_value' => "+",
						'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
						'link_style' => 'style="float:right;"') 
					) . '
				 
				 </div>
				';
				
				
				$permission_list = $this->getHtml( "get-permission-list", array( 'records' => Permission::getPermissions( "active", "1" ) ) );
				$add_form = Permission::getHtml( "get-edit-form", array( 'active_record' => new Permission( 0 ) ) );
				
				$html = '
				<div class="item_list_container">
					
					<div id="permission_canvas_add" class="item_container padder_10 rounded_corners bg_color_light_tan" style="display:none;" hover_enabled="0">
						' . $add_form['html'] . '
					</div>
	
					<div class="user_container" id="permission_list_container">
						' . $permission_list['html'] . '
					</div>
					
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "get-permission-list":
				
				$records = $vars['records'];
				
				$html = '
				<ul id="permission_list">
				';
				
				if( count( $records ) > 0 )
				{
					foreach( $records as $i => $p )
					{
						$view_form = Permission::getHtml( "get-view-form", array( 'active_record' => &$p ) );
						$mod_form = Permission::getHtml( "get-edit-form", array( 'active_record' => &$p ) );
						$delete_form = Permission::getHtml( "get-delete-form", array( 'active_record' => &$p ) );
						
						$html .= '
						<li id="item_' . $p->m_permission_id . '">
							<div class="item_container padder_10 rounded_corners bg_color_light_tan" hover_enabled="1">
							
								<div id="permission_info_' . $p->m_permission_id . '">						
									' . $view_form['html'] . '
								</div>
													
								<div id="permission_canvas_mod_' . $p->m_permission_id . '" style="display:none;">
									' . $mod_form['html'] . '
								</div>
								
								<div id="permission_canvas_delete_' . $p->m_permission_id . '" style="display:none;">
									' . $delete_form['html'] . '
								</div>
								
								<div class="title_button_container" id="item_control" style="display:none;">
								';
										
								if( strtolower( $p->m_alias ) != "spr" )
								{
									$html .= '
									' . Common::getHtml( "get-button-round", array(
										'id' => "permission",
										'process' => "show_mod",
										'pk_name' => "permission_id",
										'pk_value' => $p->m_permission_id,
										'button_value' => "m",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"' ) ) . '

									' . Common::getHtml( "get-button-round", array(
										'id' => "permission",
										'process' => "show_delete",
										'pk_name' => "permission_id",
										'pk_value' => $p->m_permission_id,
										'button_value' => "x",
										'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
										'link_style' => 'style="float:right;"' ) ) . '
									';
								}
								else 
								{
									$html .= '
									<div class="font_no padder" style="float:right;">
										Cannot Edit
									</div>
									';
								}
								
								$html .= '
										<div class="clear"></div>
									
								</div>
								
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
				
			case "manage-settings":
				
				$title = 'Manage Settings';
				
				$title_button = '
				<div class="title_button_container">
					' . Common::getHtml( "get-button-round", array(
						'id' => "setting",
						'process' => "show_add",
						'pk_name' => "setting_id",
						'pk_value' => "0",
						'button_value' => "+",
						'inner_div_style' => 'style="padding-top:2px;padding-left:1px;"',
						'link_style' => 'style="float:right;"'
					 ) ) . '
				</div>
				';
				
				$setting_list = $this->getHtml( 'get-setting-list', array( 'records' => Setting::getSettings( "active", "1" ) ) );
				
				$html = '
				<div class="item_list_container" id="setting_list_container">			
					' . $setting_list['html'] . '
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "get-setting-list":
				
				$records = $vars['records'];
				$items_per_row = 2;
				$num_items = count( $records );
				
				$num_rows = ceil( $num_items / $items_per_row );
				$active_user = new User( Authentication::getLoginUserId() );
				$container_style = ( array_key_exists( "container_style", $vars ) ) ? $vars['container_style'] : "";
				
				if( $num_items > 0 )
				{
					$html = '
				<table class="settings_grid">
				';
				
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
							
							$item = $records[$key];
							$view_form = Setting::getHtml( "get-view-form", array( 
								'active_record' => $item,
								'active_user' => $active_user,
								'show_controls' => $vars['show_controls'] ) 
							);
							
							$html .= '
						<td valign="top">
							<div id="user_info_' . $item->m_user_id . '" class="' . $vars['container_class'] . ' bg_color_light_tan border_dark_grey" ' . $container_style . ' hover_enabled="' . $vars['hover_enabled'] . '">
								' . $view_form['html'];
							
							if( $vars['show_controls'] )
							{
								$html .= '
								<div class="title_button_container" id="item_control" style="display:none;width:100px;height:40px;">
									' . Common::getHtml( "get-button-round", array(
										'href' => $common->makeLink( array(
											'v' => "account",
											'sub' => $item->m_username, 
											'id1' => "update-contact" ) ),
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
								';
							}
							
							$html .= '
							</div>
						</td>
						';
						
						}
						
						$html .= '
					</tr>
					';	
								
					}

					$html .= '
				</table>
				';
					
				}
				else
				{
					$html = '
					<div class="center padder_10">
						There are 0 settings...
					</div>
					';
				}
				
				$return = array( 'html' => $html );
				break;
			
			default:
				throw new Exception( "Error: HTML command '" . $cmd . "' is invalid." );
				break;
				
		}//end switch
		
		return $return;
		
	}//getHtml()
	
	/**
	 * @see classes/base/Controller#getContent()
	 */
	public function getContent() {
		return $this->m_content;
	}//getContent()
		
}//class Admin
?>