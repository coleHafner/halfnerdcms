/**
 * A class to handle mail functionality.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */
 
$(document).ready(function(){

	$( "#mail" )
		.click( function( event ){
			
			//cancel event
			event.preventDefault();
			
			//new mail
			var mail = new Mail();
			
			//send mail
			mail.validateMailForm();
		});
});

function Mail()
{
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/
	this.validateMailForm = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=mail&process=validate',
			data: $( "#email_form" ).serialize( true ),
			success: function( reply ){
				
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];  
				
				//clear form
				if( result == 1 )
				{
					var inner = new Mail();
					inner.sendMail();
				}
				else
				{
					showMessage( message, 0 );
				}
			}
		});
	
	}//validateMailForm()
	
	/**********************************************************************************************************************************
	action functions
	**********************************************************************************************************************************/
	
	this.sendMail = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=mail&process=send',
			data: $( "#email_form" ).serialize( true ),
			success: function( reply ){
				showMessage( "Mail Sent", 1 );
			}
		});
	
	}//sendMail()
	
}//Mail class