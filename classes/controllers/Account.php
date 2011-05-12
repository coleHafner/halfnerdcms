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

class Account extends Controller{
	
	/**
	 * Constructs the controller object.
	 * @return 	Index
	 * @param	array			$controller_vars		$_GET array.				
	 */
	public function __construct( $controller_vars )
	{
		parent::setControllerVars( $controller_vars );
		
		$this->m_valid_views = array(
			'update-contact' => "", 
			'update-login' => "", 
			'update-photo' => "", 
			'update-permissions' => ""
		);
		
	}//constructor
	
	/**
	 * This sets the content for this controller. 
	 * @see classes/base/Controller#setContent()
	 */
	public function setContent() 
	{
		//validate sub-option
		$user_edit_approved = FALSE;
		
		//check if user edit has been requested
		if( !in_array( $this->m_controller_vars['sub'], array_keys( $this->m_valid_views ) ) )
		{
			if( !User::usernameExists( $this->m_controller_vars['sub'] ) ||
				!$this->m_user->permissionUserHasAny( array( "spr" ) ) ||
				strtolower( $this->m_controller_vars['sub'] ) == strtolower( $this->m_user->m_username ) )
			{
				//if user edit has been requested, 
				//but current user has no permissions OR
				//requested username does not exist, 
				//default to current users profile
				$this->m_controller_vars['sub'] = $this->validateCurrentView();
			}
			else
			{
				$user_edit_approved = TRUE;
				$this->m_controller_vars['id1'] = $this->validateControllerVar( $this->m_controller_vars['id1'] );
			}
		}
		
		//get requested user object
		if( $user_edit_approved )
		{
			$requested_user_id = $this->m_common->m_db->getIdFromTitle( $this->m_controller_vars['sub'], array(
				'table' => "common_Users",
				'pk_name' => "user_id",
				'title_field' => "username" )
			);
			
			$requested_user = new User( $requested_user_id );
		}
		
		//grab html
		$active_user = ( !$user_edit_approved ) ? $this->m_user : $requested_user;
		$nav = $this->getHtml( "account-nav-options", array( 'active_user' => &$active_user ) );
		$content = $this->getHtml( $this->m_controller_vars['sub'], array( 'active_user' => $this->m_user ) );
		
		$side_bar = Common::getHtml( "get-side-bar", array( 'view' => $this->m_linked_objects['view'] ) );
		
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
			case "update-login":
				
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$user_html = User::getHtml( "get-login-form", array( 'active_record' => &$vars['active_user'], 'render_form_tag' => TRUE ) );
				
				//compile html
				$html = '
				<div class="' . $item_classes['html'] .'" style="position:relative;">
					' .  $user_html['html'] . ' 							
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "update-permissions":
				
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$user_html = User::getHtml( "get-permission-form", array( 'active_record' => &$vars['active_user'], 'render_form_tag' => TRUE ) );
				
				//compile html
				$html = '
				<div class="' . $item_classes['html'] . '" style="position:relative;">
					' .  $user_html['html'] . ' 							
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "update-contact":
				
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$user_html = User::getHtml( "get-contact-form", array( 'active_record' => &$vars['active_user'], 'render_form_tag' => TRUE ) );
				
				//compile html
				$html = '
				<div class="' . $item_classes['html'] . '" style="position:relative;">
					' .  $user_html['html'] . ' 							
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "update-photo":
				
				$item_classes = Common::getHtml( "get-admin-list-item-classes", array() );
				$user_html = User::getHtml( "get-photo-form", array( 'active_record' => &$vars['active_user'] ) );
				
				//compile html
				$html = '
				<div class="' . $item_classes['html'] . '" style="position:relative;">
					' .  $user_html['html'] . ' 							
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			case "account-nav-options":
				
				$user_edit_requested = ( $this->m_user->m_user_id != $vars['active_user']->m_user_id ) ? TRUE : FALSE;
				 
				$html = '
				<div class="title_bar padder_15 bg_gradient_linear">
					
					<a href="' . $this->m_common->makeLink( array( 'v' => "users", 'sub' => $vars['active_user']->m_username ) ) . '">
						< < View My Profile
					</a>
					
					<div class="title_button_container" style="width:400px;">
					';
				
				$buttons = array(
					'update-contact' => array( 'title' => "Contact", 'width' => "60px" ),
					'update-login' => array( 'title' => "Login", 'width' => "60px" ),
					'update-photo' => array( 'title' => "Photo", 'width' => "60px" ),
					'update-permissions' => array( 'title' => "Permissions", 'width' => "75px" )
				);
				
				foreach( $buttons as $sub => $info )
				{
					$display = TRUE;
					$selected_val = ( !$user_edit_requested ) ? $this->m_controller_vars['sub'] : $this->m_controller_vars['id1'];
					$selected = ( $selected_val == $sub ) ? TRUE : FALSE;
					
					if( $sub == "update-permissions" )
					{
						if( !$this->m_user->permissionUserHasAny( array( 'spr' ) ) ||
							$vars['active_user']->m_user_id == $this->m_user->m_user_id )
						{
							$display = FALSE;
						}	
					}
					
					if( $display )
					{
						$link_vars = array( 'v' => "account", 'sub' => $sub );
						
						if( $user_edit_requested )
						{
							$link_vars = array( 'v' => "account", 'sub' => $vars['active_user']->m_username, 'id1' => $sub );
						} 
						
						$html .=
						Common::getHtml( "get-button-round", array(
							'href' => $this->m_common->makeLink( $link_vars ),
							'button_value' => $info['title'],
							'inner_div_style' => 'style="position:relative;font-size:10px;padding-top:4px;"',
							'link_style' => 'style="position:relative;float:right;width:' . $info['width'] . ';"',
							'selected' => $selected ) 
						);
					}
				}

				$html .= '
					</div>
				</div>
				';
				
				$return = array( 'html' => $html );
				break;
				
			default:
				
				//get requested user
				$requested_user_id = $this->m_common->m_db->getIdFromTitle( $this->m_controller_vars['sub'], array(
					'table' => "common_Users",
					'pk_name' => "user_id",
					'title_field' => "username" )
				);
				
				//validate options
				$requested_user = new User( $requested_user_id );
				$return = $this->getHtml( $this->m_controller_vars['id1'], array( 'active_user' => &$requested_user ) );
				break;
		}
		
		return $return;
	}//getHtml()
		
}//class Account
?>
