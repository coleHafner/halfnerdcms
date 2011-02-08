/**
 * A class to handle article records.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20110125
 */
 
$( document ).ready( function(){

	$( "#setting" )
		.live( "click", function( event ){
		
			//cancel event
			event.preventDefault();
			
			//get vars
			var process = $( this ).attr( "process" );
			var setting_id = ( hasAttr( $( this ), "setting_id" ) ) ? $( this ).attr( "setting_id" ) : 0;
			
			//new alert
			var setting = new Setting( setting_id );
			
			//do action
			switch( process.toLowerCase() )
			{
				case "add":
					setting.validateAddModForm( "add", setting.setting_id );
					break;
					
				case "modify":
					setting.validateAddModForm( "modify", setting.setting_id );
					break;
			}//end switch
		});
});

function Setting( setting_id )
{
	this.setting_id = setting_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/
	this.modify = function( form_name ) {
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=setting&process=modify&setting_id=' + this.setting_id,
			data:$( form_name ).serialize( true ),
			success: function(){
				
				//show success message
				showMessage( "Setting Saved", 1 );
			}
		});
	}//modify()
	
	this.toggleAlert = function( status ) {
		
		//toggle alert flag in the database.
		$.ajax({
			type: "POST",
			url: "/ajax/halfnerd_helper.php?task=setting&process=toggle-status&setting_id=0",
			data: { alert_status: status },
			success: function( reply ){
				
				//toggle alert in $_SESSION
				$.ajax({
					type: "POST",
					url: "/ajax/halfnerd_helper.php?task=setting&process=toggle-alert&setting_id=0",
					data: { alert_status:status },
					success: function( reply ){
						window.location.reload();
					}
				});
			}
		});
		
	}//toggleAlert()
	
	
/**********************************************************************************************************************************
Ui functions
**********************************************************************************************************************************/

	this.validateAddModForm = function( process, setting_id )
	{
		var form_name = "#setting_form_" + setting_id;
		
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=setting&process=validate',
			data: $( form_name ).serialize( true ),
			success: function( reply ) {		
				
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				var inner = new Setting( setting_id );
				
				//do action
				if( result == 1 )
				{
					switch( process.toLowerCase() )
					{
						case "add":
							inner.modify( form_name );
							break;
							
						case "modify":
							inner.modify( form_name );
							break;
													
					}//end switch
				}
				else
				{
					showMessage( message, 0 );	
				}
			}
		});
		
	}//validateAddModForm()
	
}//class Setting