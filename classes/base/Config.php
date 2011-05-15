<?
/**
 * A class to impor the configuration settings.
 * @since	20110514, hafner
 */
require_once( 'base/Common.php' );

class Config
{
	/**
	 * Instance of the FormHandler class.
	 * @var	Form
	 */
	protected static $m_conf = FALSE;
	
	/**
	 * Constructs the object.
	 * @since	20110514, hafner
	 * @return	State
	 */
	public function Config() {}//constructor
	
	public static function setConf()
	{
		$conf_file = FALSE;
		$conf_file_name = "config.conf";
		$paths = Common::getPathInfoStatic();
		$environments = array( "local", "dev", "live" );
		
		foreach( $environments as $env )
		{
			$path = $paths[$env]['absolute'];
			$full_file = $path . "/" . $conf_file_name;
			
			if( file_exists( $full_file ) )
			{
				//validate readability
				if( !is_readable( $full_file ) ) { throw new Execption( "Error: Config file '" . $full_file . "' is not readable. This must be fixed before site can function."  ); }
				$conf_file = $full_file;
				break;
				
			}//if file exists
			
		}//loop through environments
		
		//validate existence
		if( $conf_file === FALSE ) { throw new Exception( "Error: No config file found. This must be fixed before site can function." ); }
		
		include( $conf_file );
		self::$m_conf = $conf;
		
	}//setConf()
	
	/**
	 * Gets a value from the conf file.
	 * @since	20110514, hafner
	 * @return	mixed
	 * Returns config setting if key exists. 
	 */
	public static function getSetting( $key )
	{
		//set conf
		self::setConf();
		
		//validate key
		if( !array_key_exists( $key, self::$m_conf ) ) { throw new Exception( "Error: Config setting '" . $key . "' does not exist." ); }
		return self::$m_conf[$key];
		
	}//getSetting()
	
}//class View
?>