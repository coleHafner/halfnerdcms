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
					
				case "delete":
					setting.deleteRecord( setting.setting_id );
					break;
					
				default:
					alert( "Error: jquery.halfnerd.setting.js says 'Process '" + process + "' is invalid.'" );
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
	
	this.add = function( form_name ) {
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=setting&process=add&setting_id=' + this.setting_id,
			data:$( form_name ).serialize( true ),
			success: function( reply ){
				
				//show success message
				showMessage( "Setting Added", 1, function(){ setTimeout( 'window.location.reload()', 1000 ) } );
			}
		});
	}//add()
	
	this.modify = function( form_name ) {
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=setting&process=modify&setting_id=' + this.setting_id,
			data:$( form_name ).serialize( true ),
			success: function(){
				
				//show success message
				showMessage( "Setting Saved", 1, function(){ setTimeout( 'window.location.reload()', 1000 ) } );
			}
		});
	}//modify()
	
	this.deleteRecord = function( setting_id ) {
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=setting&process=delete&setting_id=' + setting_id,
			success: function( reply ){
				
				//show success message
				showMessage( "Setting Deleted", 1, function(){ setTimeout( 'window.location.reload()', 1000 ) } );
			}
		});
	}//deleteRecord()
	
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/
	
	this.validateAddModForm = function( process, setting_id )
	{
		var form_name = "#setting_form_" + setting_id;
		
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=setting&process=validate&setting_id=' + setting_id,
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
							inner.add( form_name );
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