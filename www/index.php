<?
//start session
session_start();

//include files
require_once( "base/Common.php" );
require_once( 'base/Layout.php' );
require_once( 'base/Authentication.php' );

//setup layout class
$layout = new Layout( $_GET );
$auth = new Authentication( 0 );

//setup controller view
$_GET['session'] = $_SESSION;

//compile controller name
$requested_controller = $layout->m_active_view . ".php";

//include styles and scripts
echo $layout->getHtmlHeadSection();

//make sure controller file exists
if( !$auth->m_common->controllerFileExists( $requested_controller ) )
{
	echo Common::getHtml( "show-missing-controller-message", array( 
		'requested_controller' => $requested_controller, 
		'controller_path' => $auth->m_common->compileControllerLocationBasePath() ) 
	);
	exit;
}

//create new controller
require_once( "controllers/" . $requested_controller );
$_GET['v'] = ( !array_key_exists( "v", $_GET ) ) ? $layout->m_active_view : $_GET['v'];
$controller = new $layout->m_active_view( $_GET, TRUE );
$login_string = '';

//set content
$content = $auth->controlPageAccess( $controller );

//process login
if( $controller->hasValidAuthLogin() )
{
	$html = Authentication::getHtml( 'get-login-string', array( 'user' => $controller->getActiveUser() ) );
	$login_string = $html['html'];
}

//show rest of page
echo $layout->getHtmlBodySection( $login_string );
echo $content;
echo $layout->getHtmlFooterSection();
echo $layout->getClosingTags();
?>