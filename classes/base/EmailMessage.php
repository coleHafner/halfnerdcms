<?
/**
 * A class to handle the email functionality.
 * @since	20100722, hafner
 */

require_once( "base/Common.php" ); 
require_once( "phpmailer/class.phpmailer.php" );

class EmailMessage extends PHPMailer
{
	/**
	 * Instance of the Common object.
	 * @var Common
	 */
	protected $m_common;
	
	/**
	 * Constructs the email object.
	 * @since	20100722, hafner
	 * @return	Email object.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->m_common = new Common();
			
	}//__construct()
	
	/**
	 * Validates the input from the email form on the contact page.
	 * Returns a "^" delimited string success boolean^message^( "_" delimited field names string ) 
	 * @since	2010722, hafner
	 * @return 	string
	 */
	public function validateMailForm( $input )
	{
		$return = FALSE;
		$fields = array();
		
		if( strlen( trim( $input['contact_email'] ) ) == 0 )
		{
			$return = "Please provide an email address.";
		}
		
		if( $return === FALSE )
		{
			if( !isset( $input['contact_message'] ) || 
				strlen( $input['contact_message'] ) == 0  )
			{
				$return = "Please fill out a message.";
			}
		}
		
		if( array_key_exists( 'subject', $input ) )
		{
			if( $return === FALSE )
			{
				if( strlen( trim( $input['subject'] ) ) == 0 )
				{
					$return = "Please fill out a subject.";
				}
			}
		}
		
		if( $return === FALSE )
		{
			$return = $this->validateEmailAddress( $input['contact_email'] );
		}
		
		return $return;
		
	}//validateEmailForm()
	
	/**
	 * Validates the authenticity of an email address.
	 * Returns TRUE if valid, FALSE otherwise.
	 * @since	20100909, Hafner
	 * @return	boolean
	 * @param 	string			$email			email address to validate
	 */
	public function validateEmailAddress( $email )
	{
		$return = FALSE;
		
		$email = strtolower( trim( $email ) );
		
		if( strpos( $email, "@" ) === FALSE || 
			strpos( $email, "." ) === FALSE ||
			strpos( $email, " " ) !== FALSE ||
			strpos( $email, "," ) !== FALSE )
		{
			$return = "You must provide a valid email address.";
		}
		
		return $return;
		
	}//validateEmailAddress()
	
	/**
	 * Sends out an email from the contact page.
	 * @since	20100722, hafner
	 * @return	boolean
	 * @param	array		$input			array of subject, body and from address
	 */
	public function sendMail( $input )
	{
		$mail_to = $this->getMailTo();
		
		$this->From = $input['contact_email'];
		$this->Sender = $input['contact_email'];
		$this->FromName = ( array_key_exists( "contact_name", $input ) ) ? $input['contact_name'] : "";
		$this->Subject = ( array_key_exists( "subject", $input ) ) ? $input['subject'] : "";
		$this->Body = $input['contact_message'];
		$this->AddAddress( $mail_to, "cole hafner" );
		
		//send email
		if( !$this->Send() )
		{
			throw new Exception( "Error: Email did not send" );
		}
		
	}//sendMail()
	
	public function getMailTo()
	{
		$sql = "
		SELECT value
		FROM common_Settings
		WHERE title = 'mail-to-address'";
		$result = $this->m_common->m_db->query( $sql, __FILE__, __LINE__ );
		
		if( $this->m_common->m_db->numRows( $result ) == 0 )
		{
			throw new exception( "Error: could not find mail to address in common_Settings!" );
		}
		
		$row = $this->m_common->m_db->fetchRow( $result );
		return $row[0];
		
	}//getMailTo()
	
}//class EmailMessage
?>