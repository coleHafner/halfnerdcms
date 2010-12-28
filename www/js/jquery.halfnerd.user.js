/**
 * A class to handle user records
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */

$( document ).ready( function(){
	
    $( "#user" )
    	.live( "click", function( event ){
    	
    	//cancel event
		event.preventDefault();
		
		//get vars
		var user_id = ( hasAttr( $( this ), "user_id" ) ) ? $( this ).attr( "user_id" ) : 0;
		var process = $( this ).attr( "process" );
		
		//create authentication object
		var user = new User( user_id );
		
		//do action
		switch( process.toLowerCase() )
		{
			case "add":
				user.validateAddModForm( "add", user.user_id );
				break;
				
			case "modify":
				user.validateAddModForm( "modify", user.user_id );
				break;
				
			case "delete":
				user.deleteRecord();
				break;
				
			case "show_add":
				user.showCanvasAdd();
				break;
				
			case "cancel_add":
				user.hideCanvasAdd();
				break;
				
			default:
				//show colorbox
				$.colorbox({ href:"ajax/halfnerd_helper.php?task=user&process=" + process + "&user_id=" + user.user_id });
				break;
		}
	});
    
});//end document.ready


function User( user_id )
{
	this.user_id = user_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/

	this.add = function()
	{
		$.ajax({
			type: 'post',
			url: "ajax/halfnerd_helper.php?task=user&process=add&user_id=0",
			data: $( "#auth_add_mod_form" ).serialize( true ),
			success: function(){
				closeColorbox( 0 );
			}
		});
	
	}//add()
	
	this.modify = function()
	{
		$.ajax({
			type: 'post',
			url: "ajax/halfnerd_helper.php?task=user&process=modify&user_id=" + this.user_id,
			data: $( "#auth_add_mod_form" ).serialize( true ),
			success: function(){
				closeColorbox( 0 );
			}
		});
	
	}//modify()
	
	this.deleteRecord = function()
	{
		$.ajax({
			type: 'post',
			url: "ajax/halfnerd_helper.php?task=user&process=delete&user_id=" + this.user_id,
			data: $( "#auth_add_mod_form" ).serialize( true ),
			success: function(){
				closeColorbox( 0 );
			}
		});
	
	}//modify()
	
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateAddModForm = function( process, user_id )
	{
		$.ajax({
			type: 'post',
			url: 'ajax/halfnerd_helper.php?task=user&process=validate&user_id=' + user_id,
			data: $( "#auth_add_mod_form" ).serialize( true ),
			success: function( reply ){
			
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];  
				
				//clear form
				if( result == 1 )
				{
					var inner = new User( user_id ); 
					
					switch( process.toLowerCase() )
					{
						case "add":
							inner.add();
							break;
							
						case "modify":
							inner.modify();
							break;
							
						case "delete":
							inner.deleteRecord();
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
	
/**********************************************************************************************************************************
ui functions
**********************************************************************************************************************************/

this.showCanvasMod = function( user_id )
	{
		//hide info
		$( "#user_info_" + user_id ).fadeOut( function( ){
		
			$( "#user_canvas_delete_" + user_id ).fadeOut( function( ){
			
				//new obj
				var inner = new Article( 0 );
				
				var callback = function(){
					//show canvas
					$( "#user_canvas_mod_" + user_id ).slideDown();
					
					//disable hover
					$( "#user_canvas_mod_" + user_id ).attr( "hover_enabled", "0" );
				}
				
				//hide add
				inner.hideCanvasAdd( callback );
				
			});
		});
	}//showCanvas()
	
	this.hideCanvasMod = function( user_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#user_canvas_mod_" + user_id ).slideUp( function(){
		
			//show info
			$( "#user_info_" + user_id ).fadeIn( function(){
				
				callback();
			});
			
		});
	}//hideCanvasMod()
	
	this.showCanvasAdd = function( callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//show add form
		$( "#user_canvas_add" ).slideDown( function(){
			callback();
		});
				
	}//showCanvas()
	
	this.hideCanvasAdd = function( callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide info
		$( "#user_canvas_add" ).slideUp( function(){
			callback();
		});
		
	}//hideCanvas()
		
	this.showCanvasDelete = function( user_id )
	{
		//hide info
		$( "#user_info_" + user_id ).fadeOut( function( ){
		
			$( "#user_canvas_mod_" + user_id ).fadeOut( function( ){
			
				//show canvas
				$( "#user_canvas_delete_" + user_id ).slideDown();
				
			});
		});
		
	}//showCanvas()
	
	this.hideCanvasDelete = function( user_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#user_canvas_delete_" + user_id ).slideUp( function(){
		
			//show info
			$( "#user_info_" + user_id ).fadeIn( function(){
				
				//run callback
				callback();	
			});
			
		});
	}//hideCanvas()
	
}//class User