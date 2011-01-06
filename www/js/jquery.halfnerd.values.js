/**
 * A class to handle single value records.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */

$( document ).ready( function(){
	
    $( "#authentication" )
    	.live( "click", function(){
    	
    	//cancel event
		event.preventDefault();
		
		//new SingleValue
		var single_val = new SingleValue();
		
		//get vars
		var single_val_id = ( hasAttr( $( this ), "single_val_id" ) ) ? $( this ).attr( "single_val_id" ) : 0;
		var process = $( this ).attr( "process" );
		
		//do action
		switch( process.toLowerCase() )
		{
			case "add":
				checkInput( "add", single_val_id );
				break;
				
			case "modify":
				checkInput( "modify", single_val_id );
				break;
				
			case "delete":
				delete( single_val_id );
				break;
				
			default:
				//show colorbox
				$.colorbox({ href:'/ajax/halfnerd_helper.php?task=single_value&process=' + process + '&single_val_id=' + single_val_id });
				break;
		}
	});
    
});//end document.ready

function SingleValue()
{

/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/

	this.add = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=single_value&process=add',
			data: $( "#single_val_form" ).serialize( true ),
			success: function( reply ){
				showMessage( "Value Added", 1 );
			}
		});
	
	}//add()
	
	this.modify = function( single_val_id )
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=single_value&process=modify&single_val_id=' + single_val_id,
			data: $( "#single_val_form" ).serialize( true ),
			success: function( reply ){
				reloadPage( 0 );
			}
		});
	
	}//modify()
	
	this.deleteRecord = function( single_val_id )
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=single_value&process=modify&single_val_id=' + single_val_id,
			data: $( "#single_val_form" ).serialize( true ),
			success: function( reply ){
				reloadPage( 0 );
			}
		});
	
	}//modify()
	
	/**********************************************************************************************************************************
	validation functions
	**********************************************************************************************************************************/
	
	this.validateAddModForm = function( process, single_val_id )
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=single_value&process=validate',
			data: $( "#single_val_form" ).serialize( true ),
			success: function( reply ) {
				
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				
				//clear form
				if( result == 1 )
				{
					var inner = new SingleValue();
					
					switch( process )
					{
						case "add":
							inner.add();
							break;
							
						case "modify":
							inner.modify( single_val_id );
							break;
							
						case "delete":
							inner.deleteRecord( single_val_id );
							break;
					}
				}
				else
				{
					showMessage( message, 0 );
				}
			}
		});
		
	}//validateAddModForm()
	
}//SingleValue class