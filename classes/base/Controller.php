<?
/**
 * Abstract class for all controllers.
 * @since	20100425, halfNerd
 */

require_once( "base/Common.php" );
require_once( "base/View.php" );
require_once( "base/User.php" );

abstract class Controller{
	
	/**
	 * Array of variables for the current controller.
	 * @var	array
	 */
	protected $m_controller_vars;
	
	/**
	 * HTML Content.
	 * @var	string
	 */
	protected $m_content;
	
	/**
	 * Instance of the common object.
	 */
	protected $m_common;
	
	/**
	 * Id of the view associated with this controller class.
	 * @var	int
	 */
	protected $m_view_id;
	
	/**
	 * Collection of objects linked to this controller class.
	 * @var	array
	 */
	protected $m_linked_objects;
	
	/**
	 * Instance of the User object ( current login ).
	 * @var	int
	 */
	protected $m_user;
	
	/**
	 * Collection of valid views for this controller.
	 * @var	array
	 */
	protected $m_valid_views;
	
	/**
	 * A function to set the member vars.
	 * Always returns TRUE.
	 * @return	boolean
	 * @param 	array		$controller_vars	array of variables for the current controller
	 */
	public function setControllerVars( $get, $objects = TRUE ) 
	{
		$this->m_common = new Common();
		$this->m_controller_vars = $get;
		$this->m_view_id = $this->setViewId();
		$this->m_linked_objects = ( $objects ) ? $this->setLinkedObjects() : array();
		$this->m_user = FALSE;
		
		return TRUE;
		
	}//setControllerVars()
	
	/**
	 * Sets the linked objects for this View object.
	 * Returns array of linked objects.
	 * @since	20100907, Hafner
	 * @return	array
	 */
	public function setLinkedObjects()
	{
		return array(
			'view' => new View( $this->m_view_id, FALSE )
		);
	}//setLinkedObjects()
	
	public function getAuthStatus() 
	{ 
		return $this->m_linked_objects['view']->m_requires_auth;
		 
	}//getAuthStatus()
	
	public function setViewId()
	{
		$sql = "
		SELECT view_id
		FROM common_Views
		WHERE LOWER( TRIM( controller_name ) ) = '" . strtolower( trim ( $this->m_controller_vars['v'] ) ) . "'";
		
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		$row = $this->m_common->m_db->fetchRow( $result );
		
		return $row[0];
		
	}//setViewId()
	
	public function setUser( $user_id )
	{
		$this->m_user = new User( $user_id, TRUE );
	}//setUser()
	
	public function hasValidAuthLogin()
	{
		$return = FALSE;
		
		if( is_object( $this->m_user ) &&
			$this->m_user->m_user_id > 0 )
		{
			$return = TRUE;
		}
		
		return $return;
		
	}//hasValidAuthLogin()
	
	public function getActiveUser()
	{
		return $this->m_user;
	}//getActiveUser()
	
	public function validateCurrentView()
	{
		reset( $this->m_valid_views );
		$default = key( $this->m_valid_views );
		$return = ( array_key_exists( "sub", $this->m_controller_vars ) && in_array( $this->m_controller_vars['sub'], array_keys( $this->m_valid_views ) ) ) ? $this->m_controller_vars['sub'] : $default;
		return $return;
		
	}//validateCurrentView()
	
	public function validateControllerVar( $val )
	{
		reset( $this->m_valid_views );
		$default = key( $this->m_valid_views );
		$return = ( in_array( $val, array_keys( $this->m_valid_views ) ) ) ? $val : $default;
		return $return;
		
	}//validateControllerVar()
		
	/**
	 * Sets the content for the current controller
	 * Always returns TRUE
	 * @return	boolean
	 */
	abstract function setContent();
	
	/**
	 * Returns this->m_content
	 * Always returns TRUE
	 * @return	boolean
	 */
	abstract function getContent();
	
	/**
	 * Returns HTML content.
	 * @since	20100907, Hafner
	 * @return	string
	 * @param	string			$cmd		determines what to return
	 * @param	array			$vars		array of variables
	 */
	//abstract static function getHtml( $cmd, $vars = array() );
	
}//abstract class Controller
?>