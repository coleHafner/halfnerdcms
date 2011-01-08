<?

/**
 * A class to handle the interactions with the database.
 * @since	20100508, hafner
 */

class Database {
	
	/**
	 * Host for the database.
	 * @var string
	 */
	protected $m_host;
	
	/**
	 * User for the database.
	 * @var string
	 */
	protected $m_user;
	
	/**
	 * Password for the database user.
	 * @var string
	 */
	protected $m_pass;
	
	/**
	 * Name of the database to connect to.
	 * @var string
	 */
	protected $m_db_name;
	
	/**
	 * Result of mysql_connect()
	 * @var mysql_resource
	 */
	protected $m_resource;
	
	/**
	 * Contstructs the database object.
	 * @return 	Database
	 */
	public function __construct( $paths ) {
	
		$this->m_host = $paths['db_host'];
		$this->m_user = $paths['db_user'];
		$this->m_pass = $paths['db_pass'];
		$this->m_db_name = $paths['db_name'];
		
		$this->m_resource = $this->connect();
		$this->selectDb();
	}//constructor()
	
	/**
	 * Connects to the mysql database.
	 * Returns TRUE on success, throws exception otherwise. 
	 * @return 	mysql_resource
	 */
	public function connect() {
		
		$return = mysql_connect( $this->m_host, $this->m_user, $this->m_pass );
		
		if( !$return ) {
			echo "db connection failed. Mysql said '" . mysql_error() . "'"; 
			throw new exception( 
				"Error: Database connection failed. <br/>\n 
				Mysql said '" . mysql_error() . "'" 
			);
		} 
		
		return $return;
	}//connect()

	/**
	 * Selects the database.
	 * Returns TRUE on success, throws exception otherwise.
	 * @return	boolean
	 */
	public function selectDb() {

		$return = mysql_select_db( $this->m_db_name, $this->m_resource );
		 
		if( !$return ) {
			throw new exception( 
				"Error: Could not select Database '" . $this->m_db_name . "'. <br/>\n
				Mysql said: '" . mysql_error() . "'" 
			);
		}
		
		return TRUE;
	}//selectDb()
	
	/**
	 * Queries the database.
	 * @return	mysql_resource
	 * @param	string			$sql			query
	 * @param	string			$file			file it's being called from
	 * @param	int				$line			line it's being called from
	 */
	public function query( $sql, $file, $line ) {
		
		$return = mysql_query( $sql, $this->m_resource );
		
		if( !$return ) {
			throw new Exception( 
				"Error: Query failed @ file '" . $file . "' on line #" . $line . ". <br/>\n
				Mysql said: '" . mysql_error() . "'" 
			);
		}
		
		return $return;
	}//query()
	
	/**
	 * Wrapper class for mysql_fetch_assoc.
	 * Returns associative array of query results.
	 * @return	array
	 * @param	mysql_resource			$result			result of $this->query()
	 */
	public function fetchAssoc( $result ) {
		
		return mysql_fetch_assoc( $result ); 
	}//fetchAssoc()
	
	/**
	 * Wrapper class for mysql_fetch_row.
	 * Returns numerically indexed array of query results.
	 * @return	array
	 * @param	mysql_resource			$result			result of $this->query()
	 */
	public function fetchRow( $result ) {
		
		return mysql_fetch_row( $result ); 
	}//fetchAssoc()
	
	/**
	 * Wrapper class for mysql_num_rows.
	 * Returns the number of rows from a query.
	 * @return	array
	 * @param	mysql_resource			$result			result of $this->query()
	 */
	public function numRows( $result ) {
		
		return mysql_num_rows( $result ); 
	}//fetchAssoc()
	
	/**
	 * Inserts a blank record into the specified table.
	 * Returns the most recent pk for the specified table.
	 * @since	201006016, hafner
	 * @return	int
	 * @param 	string 		$table				name of the table to insert into
	 * @param 	string 		$pk					name of the primary key for the specified table
	 * @param 	array 		$req_fields			array( 'field_name' => value )
	 */
	public function insertBlank( $table, $pk, $req_fields = array() )
	{
		$req_f = "";
		$req_v = "";
		
		//compile required fields
		if( is_array( $req_fields ) && count( $req_fields ) > 0 )
		{
			foreach( $req_fields as $field => $val )
			{
				$req_f .= ", " . $field;
				$req_v .= ", " . $val;
			}
		}
		
		$sql = "
		INSERT 
		INTO " . $table . "( " . $pk . $req_f . "  )
		VALUES( DEFAULT " . $req_v . " )";
		
		$this->query( $sql, __FILE__, __LINE__ );
		return $this->getLastInsertId( $table, $pk );

	}//insertBlank()
	
	/**
	 * Gets the last inserted pk id for the specified table.
	 * @since	20100616, hafner
	 * @return	int
	 * @param	string			$table			name of the table
	 * @param	string			$pk				name of the primary key for the specified table
	 */
	public function getLastInsertId( $table, $pk )
	{
		$sql = "
		SELECT " . $pk . "
		FROM " . $table . "
		ORDER BY ". $pk . " DESC";
		$result = $this->query( $sql, __FILE__, __LINE__ );
		$row = $this->fetchRow( $result );
		
		return $row[0];
	}//getLastInsertId()
	
	/**
	 * Converts a database boolean ( 1 or 0 ) into php boolean ( TRUE or FALSE )
	 * @since	20100616, hafner
	 * @return	boolean
	 * @param 	int 		$bool		either 1 or 0
	 */
	public function fixBoolean( $bool )
	{
		return ( $bool == 1 ) ? TRUE : FALSE;
	}//fixDbBoolean()
	
	/**
	 * Fixes the string. Escapes all single and double quotes.
	 * @since	2010618, hafner
	 * @return 	string
	 * @param	string			$string			string to escape
	 */
	public function escapeString( $string )
	{
		return addSlashes( $string );
	}//escapeString()
	
	/**
	 * Checks for a duplicate record.
	 * Returns the PK of the record if already exists, FALSE if record does not already exist.
	 * @since	20100620, hafner
	 * @return	mixed
	 * @param	array			$dup_check		array( 'table_name' => string, 'pk_name' => string, 'check_values' => array( '[field_name1]' => '[value1]', '[field_name2]' => '[value2]' etc. . . ) )
	 */
	public function checkDuplicate( $dup_check )
	{
		$return = FALSE;
		$common = new Common();
		
		$sql = "
		SELECT " . $dup_check['pk_name'] . "
		FROM " . $dup_check['table_name'] .
		$common->compileSqlConstraints( $dup_check['check_values'] ) . "
		AND ( active = 1 OR active = 0 )";
		
		$result = $this->query( $sql, __FILE__, __LINE__ );
		
		if( $this->numRows( $result ) > 0 )
		{
			$row = $this->fetchRow( $result );
			$return = $row[0];
		}
		
		return $return;
		
	}//checkDuplicate()
	
	/**
	 * Adds a record via the object passed.
	 * Returns the object on success, throws exception otherwise.
	 * @since	20100620, hafner
	 * @return	int
	 * @param	mixed			$object			object
	 * @param	array			$input			collection of input data required for the record to be added.		
	 */
	public function addRecord( $object, $input, $dup_check )
	{
		$dup_check = $this->checkDuplicate( $dup_check );
		
		if( !$dup_check )
		{
			$add_result = $object->add( $input );
			
			if( is_string( $add_result ) )
			{
				throw new exception( "Error: Record add failed. Class '" . get_class( $object ) . "' said '" . $add_result . "' \n\n<br/><br/>" );
			}
			else
			{				
				echo get_class( $object ) . " record added. \n<br/>";
				$return = $object;
			}
		}
		else
		{
			echo get_class( $object ) . " record already exists. \n<br/>";
			echo "dup_check: " . $dup_check;
			$return = new $object( $dup_check );
		}
		
		return $return;
		
	}//addRecord()
	
	/**
	 * Compiles a query from an array of input data.
	 * Returns FALSE if no results or !$gimme, array of query results data otherwise.
	 * @since	20100625, hafner
	 * @return 	mixed
	 * @param 	array 		$data		array( 'tables' => string, 'get' => string,  )  
	 * @param 	unknown_type $gimme
	 */
	public function doQuery( $sql, $assoc = TRUE )
	{
		$return = FALSE;
		$result = $this->query( $sql, __FILE__, __LINE__ );
		
		if($this->numRows( $result ) > 0 )
		{
			if( $assoc )
			{
				while( $row = $this->fetchAssoc( $result ) )
				{
					$row = array_change_key_case( $row, CASE_LOWER );
					$return[] = $row;
				}
			}
			else 
			{
				while( $row = $this->fetchRow( $result ) )
				{
					$return[] = $row;
				}
			}
		}
		
		return $return;
		
	}//doQuery()
	
	/**
	 * Evaluates a value to see if it needs to be assigned null.
	 * @since	20100620, hafner
	 * @return	string
	 * @param 	mixed			$value			either string or int
	 * @param 	boolean			$is_string		TRUE if string, FALSE otherwise.
	 */
	public function determineNull( $value, $is_string )
	{
		$l = ( $is_string ) ? "'" : "";
		$r = ( $is_string ) ? "'" : "";
		$new_value = ( $is_string ) ? $l . $this->escapeString( ucwords( $value ) ) : $value;
		return ( strlen( $value ) > 0 ) ? $l . $new_value . $r : "NULL";
		
	}//determineNull()
	
	/**
	 * Makes a string pretty.
	 * @since	2010620, hafner
	 * @return	string
	 * @param 	string		$string			string to prettify
	 */
	public function fixDbString( $string )
	{
		return $this->escapeString( ucwords( $string ) );
	}//fixDbString
	
	public function keyExists( $constraints )
	{
		$return = FALSE;
		
		$sql = "
		SELECT count(*)
		FROM " . $constraints['table_name'] . "
		WHERE " . $constraints['pk_name'] . " = " . $constraints['pk_value'];
		
		$result = $this->query( $sql, __FILE__, __LINE__ );
		$row = $this->fetchRow( $result );
		
		if( $row[0] > 0 )
		{
			$return = TRUE;
		}
		
		return $return;
		
	}//keyExists()
	
	/**
	 * Gets id from title.
	 * Returns 0 on failure, > 0 otherwise.
	 * @since	20100728, hafner
	 * @return	int
	 * @param	string			$title			title of a record.
	 * @param	array			$vars			array( 'table' => "", 'pk_name' => "", 'title_field' => "" )
	 */
	public function getIdFromTitle( $title, $vars )
	{
		$return = 0;
		$common = new Common();
		
		$sql = "
		SELECT " . $vars['pk_name'] . "
		FROM " . $vars['table'] . "
		WHERE LOWER( TRIM( " . $vars['title_field'] . " ) ) = '" . strtolower( trim( $title ) ) . "'";
		
		$result = $this->query( $sql, __FILE__, __LINE__ );
		
		if( $this->numRows( $result ) > 0 )
		{
			$row = $this->fetchRow( $result );
			$return = $row[0];
		}
		
		return $return;
			
	}//getIdFromTitle()
	
	public function getTitleFromId( $id, $vars )
	{
		$return = 0;
		$common = new Common();
		
		$sql = "
		SELECT " . $vars['title_field'] . "
		FROM " . $vars['table'] . "
		WHERE " . $vars['pk_name'] . " = " . $id;
		
		$result = $this->query( $sql, __FILE__, __LINE__ );
		
		if( $this->numRows( $result ) > 0 )
		{
			$row = $this->fetchRow( $result );
			$return = $row[0];
		}
		
		return $return;

	}//getTitleFromId()
	
}//class Database
?>