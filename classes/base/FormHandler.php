<?
/**
* A class to manage forms
* @since	20100616, hafner
*/

require_once( "base/Common.php" );

class FormHandler
{
	/**
	* Whether or not we have returned from a form submission
	* @var bool
	*/
	public $m_returned;
	
	/**
	* FALSE if no errors have been found, the error message otherwise
	* @var mixed
	*/
	public $m_error;
	
	/**
	* Instance of the common class
	* @var Common
	*/
	public $m_common;
	
	/**
	* Creates an instance of the FormHandler class. 
	* @since 	20100616, hafner
	* @param	int			$returned		1 or 0
	*/
	function __construct( $returned )
	{
		$this->m_common = new Common();
		$this->m_returned = ( $returned == 1 ) ? TRUE : FALSE;
		$this->m_error = FALSE;
		
	}//constructor

	/**
	* Make sure a required field holds a value.
	* Returns FALSE when no errors are encountered, a string containing the error otherwise
	* @since	20100616, hafner
	* @return	mixed
	* @param		string		$check					The input to check
	* @param		string		$field_string			A string to indicate the field that contains an error to the end user
	*/
	public function checkBlank( $check, $field_string )
	{
		if( $this->m_returned )
		{
			if( !$this->m_error )
			{
				if( strlen( trim( $check ) ) < 1 )
				{
					$this->m_error = $field_string . " cannot be empty.";
				}
			}
			return $this->m_error;
		}
	}//checkBlank()

	
	/**
	* Raise an error if an email address looks invalid.
	* Returns FALSE when no errors are encountered, a string containing the error otherwise.
	* @since	20100616, hafner
	* @return	mixed
	* @param	string			$check					The input to check.
	*/
	public function checkEmail( $check )
	{
		if( $this->m_returned )
		{
			if( !$this->m_error )
			{
				if( strlen( $check ) > 0 )
				{
					if( ( !ereg( "[a-zA-Z0-9].*@[a-zA-Z0-9].*\.[a-zA-Z].*", $check ) ) || ( strlen( $check ) < 6 ) )
					{
						$this->m_error = "\"" . $check . "\" does not appear to be a valid e-mail address.";
					}
				}
			}
			return $this->m_error;
		}
	}//checkEmail()

	/**
	* Raise an error if two input values (such as password and verification) do not match.
	* Returns FALSE when no errors are encountered, a string containing the error otherwise
	* @since	20100616, hafner 
	* @return	mixed
	* @param	string			$check1					The first input value to compare.
	* @param	string			$check2					The second input value to compare.
	* @param	string			$field_string			A string to indicate the field that contains an error to the end user
	*/
	public function checkMismatch( $check1, $check2, $field_string )
	{
		if( $this->m_returned )
		{
			if( !$this->m_error )
			{
				if( $check1 != $check2 )
				{
					$this->m_error = "Your " . $field_string . " did not match.";
				}
			}
			return $this->m_error;
		}
	} // end checkMismatch

	/**
	* Raise an error if an input value such as a password does not match a minimum set of requirements.
	* Returns FALSE when no errors are encountered, a string containing the error otherwise
	* @since	20100616, hafner
	* @return	mixed
	* @param	string			$check_pass				The input value to check.
	* @param	string			$check_user				The username to compare against if $not_in_username is TRUE
	* @param	string			$field_string			A string to indicate the field that contains an error to the end user
	*/
	public function checkPassStrength( $check_pass, $check_user, $field_string )
	{
		$min_length = 8;
		$require_mix_case = FALSE;
		$require_mix_char = TRUE;
		$not_in_username = FALSE;
		$enforce_aging = TRUE;

		if( $this->m_returned )
		{
			if( !$this->m_error )
			{
				// check for minimum length
				if( $min_length > strlen( $check_pass ) )
				{
					$this->m_error = TRUE;
				}

				// check for mixed case
				if( $require_mix_case )
				{
					$upper_pass = strtoupper( $check_pass );
					$str_compare1 = strcmp( $upper_pass, $check_pass );

					$lower_pass = strtolower( $check_pass );
					$str_compare2 = strcmp( $lower_pass, $check_pass );

					if( ($str_compare1 == 0) || ($str_compare2 == 0) )
					{
						$this->m_error = TRUE;
					}
				} // end mix_case check

				// check for mixed characters
				if( $require_mix_char )
				{
					$found_alpha = FALSE;
					$found_other = FALSE;
					$mixed = FALSE;
					
					$alpha = ereg_replace( "[^a-zA-Z]", "", $check_pass );
					if( strlen( $alpha ) > 0 )
					{
						$found_alpha = TRUE;
					}
					
					$other = ereg_replace( "[a-zA-Z]", "", $check_pass );
					if( strlen( $other ) > 0 )
					{
						$found_other = TRUE;
					}
					
					if( $found_other && $found_alpha )
					{
						$mixed = TRUE;
					}
					
					if( !$mixed )
					{
						$this->m_error = TRUE;
					}
				} // end mix_char check

				// make sure password is not in username
				if( $not_in_username )
				{
					$in_user = FALSE;
					if( eregi( $check_pass, $check_user ) || eregi( $check_user, $check_pass ) )
					{
						$in_user = TRUE;
					}
					if( $in_user )
					{
						$this->m_error = TRUE;
					}
				} // end not_in_username check
				
				// if there was an error, form a string to return to the user
				if( $this->m_error )
				{
					if( $min_length )
					{
						$show_causes = "<li>" . $field_string . " must be at least " . $min_length . " characters long</li>";
					}
					if( $require_mix_case )
					{
						$show_causes .= "<li>" . $field_string  . " must contain upper and lower case characters</li>";
					}
					if( $require_mix_char )
					{
						$show_causes .= "<li>" . $field_string . " must contain a mix of letters and other characters</li>";
					}
					if( $not_in_username )
					{
						$show_causes .= "<li>" . $field_string . " must be a string not contained in \"" . $check_user . "\"</li>";
					}
					
					$this->m_error = "There was a problem with your " . $field_string . ".<br/>
					The following criteria must be met:
					<ul>".
					$show_causes
					."</ul>";
				}

			} // end if !$error
			// force m_error to go back to a boolean in case we messed it up
			if( !$this->m_error )
			{
				$this->m_error = FALSE;
			}
			return $this->m_error;
		}
	}//checkPassStrength()

	/**
	* Determines if record with 1, 2 or 3 passed (numeric) Primary Keys exists
	* If Add-check, error if already exists. If Modify-check, error if does not.
	* Returns FALSE when no errors are encountered, a string containing the error otherwise
	* @since	20100616, hafner
	* @return	mixed
	* @param	bool			$is_add					True = add; False = modify.
	* @param	array			$values					array( 'table_name' => string, 'check_values' => array( 'field_name1' => value1, 'field_name2' => value2 etc . .  )
	
	*/
	public function checkKeyExists( $is_add, $values )
	{	
		
		if( !$this->m_error )
		{
			$sql = "
			SELECT count(*)
			FROM " . $values['table_name'] . 
			$this->m_common->compileSqlConstraints( $values['check_values'] );
			
			$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
			$row = $this->m_common->m_db->fetchRow( $result );
			
			if( $row[0] == 0 )
			{
				$this->m_error = "That record does not exist.";
			}
				
		}
		
		return $this->m_error;
		
	}//checkKeyExists()
	
   /**
	* Raise an error if an uploaded file is empty or zero length. 
	* Returns FALSE when no errors are encountered, a string containing the error otherwise
	* @since	20100616,
	* @return	mixed
	* @param	array			$file			An array created by PHP when uploading a file
	*/
	public function checkEmptyFile( $file )
	{
		if( $this->m_returned )
		{
			if( !$this->m_error )
			{
				if( !is_array( $file ) || !array_key_exists( 'size', $file ) || !$file['size'] || $file['size'] < 1 )
				{
					$this->m_error = "The file to upload cannot be blank.";
				}
			}
			
			return $this->m_error;
		}
	} // end checkEmptyFile

	/**
	* Controls read access to protected and private member variables from non-related sources.
	* @since	20100616, hafner
	* @return	mixed
	* @param	string		$var_name		The variable of operation
	*/
	public function __get( $var_name )
	{
		$exclusions = array();
		if( !in_array( $var_name, $exclusions ) )
		{
			return $this->$var_name;
		}
		else
		{
			echo "<h3>Access to get member " . get_class( $this ) . "::" . $var_name . " denied.</h3><br/>\n";
			return FALSE;
		}
		
	}//__get()
	
	/**
	* Controls write access to protected and private member variables from non-related sources.
	* @since	20100616, hafner
	* @return	bool
	* @param	string			$var_name			The variable of operation
	* @param	mixed			$var_value			The value to which the variable is to be set
	*/
	public function __set( $var_name, $var_value )
	{
		$exclusions = array( );
		
		if( !in_array( $var_name, $exclusions ) )
		{
			$this->$var_name = $var_value;
			return TRUE;
		}
		else
		{
			echo "<b>Access to set member " . get_class( $this ) . "::" . $var_name . " denied.</b><br/>\n";
			return FALSE;
		}
	}//__set()

}//FormHandler class
?>