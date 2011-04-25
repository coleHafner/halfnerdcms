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
require_once( "cms/Portfolio.php" );
require_once( "cms/PortfolioType.php" );
require_once( "cms/Skill.php" );

class Admin extends Controller{
	
	/**
	 * Constructs the Contact controller object.
	 * @return 	Contact
	 * @param	array			$controller_vars		array of variables for current layout.				
	 */
	public function __construct( $controller_vars )
	{
		parent::setControllerVars( $controller_vars );
		
		$this->m_valid_views = array(
			'greeting' => "Welcome", 
			'manage-pages' => "Pages",
			'manage-permissions' => "Permissions", 
			'manage-posts' => "Posts",
			'manage-settings' => "Settings",
			'manage-users' => "Users",
			'manage-portfolio' => "Portfolio"
		);
		
	}//constructor
	
	/**
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() 
	{
		$this->m_controller_vars['sub'] = $this->validateCurrentView();
		
		$content = $this->getHtml( $this->m_controller_vars['sub'] );
		$nav = $this->getHtml( "admin-nav" );
		
		$this->m_content = '
		
		<div class="grid_3">
			<div class="go_to_site_link">
				<a href="' . $this->m_common->makeLink( array( 'v' => "index" ) ) . '">
					&lt;&lt; Go To Site
				</a>
			</div>	
			' . $nav['html'] . '
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
				
				$html = '
				<div class="admin_menu_container rounded_corners padder bg_color_light_tan">
				
					<div class="bg_color_white center header padder rounded_corners color_orange">
						Admin Options
					</div>
					
					<div class="padder_15 padder_no_top padder_no_bottom">
						<ul class="main_nav_list">
						';
						
			foreach( $this->m_valid_views as $sub => $display )
			{
				if( $sub != "greeting" )
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
				}//skip greeting
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
				$articles = Article::getArticles( "active", "1" );
				$add_button = Common::getHtml( "get-admin-item-add-button", array() );
				$section_manager = Section::getHtml( "get-section-manager", array() );
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$add_form = Article::getHtml( "get-edit-form", array( 'active_record' => new Article( 0 ) ) );
				
				$title_button = '
				<div class="title_button_container">
					
					' . $add_button['html'] . '
						
					' . Common::getHtml( "get-button-round", array(
						'id' => "manager",
						'process' => "show",
						'pk_name' => "manager_id",
						'pk_value' => "0",
						'button_value' => "Manage Sections",
						'inner_div_style' => 'style="padding-top:4px;padding-left:1px;"',
						'link_style' => 'style="float:right;width:100px;font-size:10px;"'
					) ) . '

				</div>
				';
				
				$list_vars = array(
					'records' => $articles,
					'is_static' => TRUE,
					'id' => 'id="article_list"',
					'active_controller' => "Article",
					'html_cmd' => 'get-admin-list-item',
					'empty_message' => "There are 0 posts at this time. Click the '+' button to add one."
				);
				
				$article_list = Common::getHtml( "display-list", $list_vars );
				
				$html = '
				<div class="item_list_container" id="article_list_container">
				
					<div id="item_manager" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $section_manager['html'] . '
					</div>
					
					<div id="item_add_0" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $add_form['html'] . '
					</div>
					
					' . $article_list['html'] . '
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "manage-pages":
				
				$title = 'Manage Pages';
				$add_button = Common::getHtml( "get-admin-item-add-button", array() );
				$list_type = ( array_key_exists( "list_type", $vars ) ) ? $vars['list_type'] : "normal";
				$views = ( array_key_exists( "records", $vars ) ) ? $vars['records'] : View::getViews( "active", "1" );
				$hover_enabled = ( array_key_exists( "hover_enabled", $vars ) && $vars['hover_enabled'] === FALSE ) ? FALSE : TRUE;
				$show_item_ids = ( $list_type == "reorder" ) ? TRUE : FALSE;
				
				$title_button = '
				<div class="title_button_container">
				
				' . $add_button['html'] . '
				 
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
				
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				
				//grab form
				if( $list_type == "normal" )
				{
					$add_form = View::getHtml( "get-edit-form", array( 'active_record' => new View( 0 ) ) );
					
					$form = '
					<div id="item_add_0" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $add_form['html'] . '
					</div>
					';
				}
				else if( $list_type == "reorder" )
				{
					$reorder_form = View::getHtml( "get-reorder-form", array() );
					
					$form .= '
					<div id="view_canvas_reorder" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $reorder_form['html'] . '
					</div>
					';
				}
				
				//grab list
				$list_vars = array(
					'records' => $views,
					'is_static' => TRUE,
					'id' => 'id="view_list"',
					'record_pk' => "m_view_id",
					'active_controller' => "View",
					'show_item_ids' => $show_item_ids,
					'html_cmd' => 'get-admin-list-item',
					'empty_message' => "There are 0 views at this time. Click the '+' button to add one.",
					'html_vars' => array( 'hover_enabled' => $hover_enabled, 'list_type' => $vars['list_type'] )
				);
				
				$list = Common::getHtml( "display-list", $list_vars );
				
				//compile HTML
				$html = '
				<div class="item_list_container" id="view_list_container">
					' . $form . '			
					' . $list['html'] . '
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "manage-permissions":
				
				$title = "Manage User Permissions";
				$permissions = Permission::getPermissions( "active", "1" );
				$add_button = Common::getHtml( "get-admin-item-add-button", array() );
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$add_form = Permission::getHtml( "get-edit-form", array( 'active_record' => new Permission( 0 ) ) );
				
				$title_button = '
				<div class="title_button_container" style="width:auto;">
					' . $add_button['html'] . '
				 </div>
				';
				
				$list_vars = array(
					'records' => $permissions,
					'is_static' => TRUE,
					'id' => 'id="permission_list"',
					'active_controller' => "Permission",
					'html_cmd' => 'get-admin-list-item',
					'empty_message' => "There are 0 permissions at this time. Click the '+' button to add one."
				);
				
				$list = Common::getHtml( "display-list", $list_vars );
				
				$html = '
				<div class="item_list_container">
					
					<div id="item_add_0" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $add_form['html'] . '
					</div>
	
					<div class="user_container" id="permission_list_container">
						' . $list['html'] . '
					</div>
					
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
				
			case "manage-settings":
				
				$title = 'Manage Settings';
				$settings = Setting::getSettings( "active", "1" );
				$add_button = Common::getHtml( "get-admin-item-add-button", array() );
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$add_form = Setting::getHtml( 'get-add-form', array( 'active_record' => new Setting( 0 ) ) );
				
				$title_button = '
				<div class="title_button_container">
					' . $add_button['html'] . '
				</div>
				';
				
				$grid_vars = array(
					'records' => $settings,
					'is_static' => TRUE,
					'records_per_row' => 2,
					'id' => 'id="setting_grid"',
					'extra_classes' => 'class="user_grid"',
					'active_controller' => "Setting",
					'html_cmd' => 'get-admin-list-item',
					'empty_message' => "There are 0 settings at this time. Click the '+' button to add one."
				);
				
				$grid = Common::getHtml( "display-grid", $grid_vars );
				
				$html = '
				<div id="item_add_0" class="' . $item_classes['html'] . '" style="display:none;">
					' . $add_form['html'] . '
				</div>
					
				<div class="item_list_container border_dark_grey rounded_corners margin_10_top" id="setting_list_container">
				
					<div id="setting_grid_container">
						' . $grid['html'] . '
					</div>
						
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
				break;
							
			case "manage-users":
				
				$title = "Manage Users";
				$users = User::getUsers( "active", "1" );
				$user_type_manager = UserType::getHtml( "get-manager", array() );
				$add_button = Common::getHtml( "get-admin-item-add-button", array() );
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$add_form = User::getHtml( "get-edit-form", array( 'active_record' => new User( 0 ) ) );
				
				$title_button = '
				<div class="title_button_container" style="width:auto;">
				
					' . $add_button['html'] . '
					
					' . Common::getHtml( "get-button-round", array(
						'id' => "manager",
						'process' => "show",
						'pk_name' => "item_id",
						'pk_value' => "0",
						'button_value' => "Manage Types",
						'inner_div_style' => 'style="padding-top:4px;padding-left:1px;font-size:10px;"',
						'link_style' => 'style="float:right;width:90px;"') 
					) . '
				 
				 </div>
				';
					
				$grid_vars = array(
					'records' => $users,
					'is_static' => TRUE,
					'records_per_row' => 2,
					'id' => 'id="user_grid"',
					'extra_classes' => 'class="user_grid"',
					'active_controller' => "User",
					'html_cmd' => 'get-view-form-badge',
					'html_vars' => array( 'show_controls' => TRUE, 'hover_enabled' => TRUE ),
					'empty_message' => "There are 0 users ( how are you logged in? ). Click the '+' button to add one."
				);
				
				$grid = Common::getHtml( "display-grid", $grid_vars );
				
				$html = '
				<div class="item_list_container">
					
					<div id="item_manager" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $user_type_manager['html'] . '
					</div>
					
					<div id="item_add_0" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $add_form['html'] . '
					</div>
	
					<div class="rounded_corners border_dark_grey margin_10_top" id="user_grid_container">
						' . $grid['html'] . '
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
				
			case "manage-portfolio":
				
				$title = "Manage Portfolio Entries";
				$p_entries = Portfolio::getPortfolioEntries( "active", "1" );
				$manager = PortfolioType::getHtml( "get-manager", array() );
				$add_button = Common::getHtml( "get-admin-item-add-button", array() );
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$add_form = Portfolio::getHtml( "get-edit-form", array( 'active_record' => new User( 0 ) ) );
				
				$title_button = '
				<div class="title_button_container" style="width:auto;">
				
					' . $add_button['html'] . '
					
					' . Common::getHtml( "get-button-round", array(
						'id' => "manager",
						'process' => "show",
						'pk_name' => "item_id",
						'pk_value' => "0",
						'button_value' => "Manage Types",
						'inner_div_style' => 'style="padding-top:4px;padding-left:1px;font-size:10px;"',
						'link_style' => 'style="float:right;width:90px;"') 
					) . '
					
					' . Common::getHtml( "get-button-round", array(
						'id' => "manager",
						'process' => "show",
						'pk_name' => "item_id",
						'pk_value' => "0",
						'button_value' => "Manage Skills",
						'inner_div_style' => 'style="padding-top:4px;padding-left:1px;font-size:10px;"',
						'link_style' => 'style="float:right;width:90px;"') 
					) . '
				 
				 </div>
				';
					
				$list_vars = array(
					'records' => $portfolio_entries,
					'is_static' => TRUE,
					'id' => 'id="permission_list"',
					'active_controller' => "Permission",
					'html_cmd' => 'get-admin-list-item',
					'empty_message' => "There are 0 portfolio entries at this time. Click the '+' button to add one."
				);
				
				$list = Common::getHtml( "display-list", $list_vars );
				
				$html = '
				<div class="item_list_container">
					
					<div id="item_manager" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $manager['html'] . '
					</div>
					
					<div id="item_add_0" class="' . $item_classes['html'] . '" style="display:none;" hover_enabled="0">
						' . $add_form['html'] . '
					</div>
	
					<div class="user_container center" id="permission_list_container">
						' . $list['html'] . '
					</div>
				</div>
				';
				
				$return = array( 'title' => $title, 'title_button' => $title_button, 'html' => $html );
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