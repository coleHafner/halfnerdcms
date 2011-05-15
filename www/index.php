<?
//start session
session_start();

//include files
require_once( "base/Config.php" );
require_once( "base/Common.php" );
require_once( 'base/LayoutAdmin.php' );
require_once( 'base/Authentication.php' );

//determine layout details
$layout_class = Config::getSetting( "layout_class" );
$layout_file = Config::getSetting( "layout_file" );
require_once( "base/" . $layout_file );

//guarantee vars
$_GET['session'] = $_SESSION;
$_GET['v'] = Common::validateView( $_GET );
$admin_controllers = array( "admin", "posts", "users", "account", "setting" );

//setup objects
$layout = ( in_array( strtolower( $_GET['v'] ), $admin_controllers ) ) ? new LayoutAdmin( $_GET ) : new $layout_class( $_GET );
$auth = new Authentication( 0 );
$common = &$auth->m_common;

//display under construction?
if( Config::getSetting( "construction_mode" ) === TRUE ) 
{ 
	$c = Common::getHtml( "under-construction", array() );
	
	//render page
	echo $layout->getHtmlHeadSection();
	echo $c['html']; 
	echo $layout->getClosingTags();
	exit;
}

//compile controller name
$requested_controller = $layout->m_active_controller_name . ".php";

//make sure controller file exists
if( !$common->controllerFileExists( $requested_controller ) )
{
	//include styles and scripts
	echo $layout->getHtmlHeadSection();
	
	//show error message
	echo Common::getHtml( "show-missing-controller-message", array( 
		'requested_controller' => $requested_controller, 
		'controller_path' => $common->compileControllerLocationBasePath() ) 
	);
	echo $layout->getClosingTags();
	exit;
}

//create new controller
require_once( "controllers/" . $requested_controller );

$login_string = '';
$controller = new $layout->m_active_controller_name( $_GET, TRUE );

//set content
$content = $auth->controlPageAccess( $controller );

//process login
if( $controller->hasValidAuthLogin() )
{
	$html = Authentication::getHtml( 'get-login-string', array( 'user' => $controller->getActiveUser() ) );
	$login_string = $html['html'];
}

//render page
echo $layout->getHtmlHeadSection();
echo $layout->getHtmlBodySection( $login_string );
echo $content;
echo $layout->getHtmlFooterSection();
echo $layout->getClosingTags();
?>