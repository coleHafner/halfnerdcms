/**
 * A class to handle article records.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */
 
$( document ).ready( function(){

	$( "#view" )
		.live( "click", function( event ){
		
			//cancel event
			event.preventDefault();
			
			//get vars
			var view_id = ( hasAttr( $( this ), "view_id" ) ) ? $( this ).attr( "view_id" ) : 0;
			var process = $( this ).attr( "process" );
			
			//new View
			var view = new View( view_id );
			
			//do action
			switch( process.toLowerCase() )
			{
				case "add":
					view.validateAddModForm( "add", view.view_id );
					break;
					
				case "modify":
					view.validateAddModForm( "modify", view.view_id );
					break;
					
				case "delete":
					view.deleteRecord();
					break;
					
				case "reorder":
					view.reorder();
					break;
					
				case "show_add":
					view.showCanvasAdd( view.view_id );
					break;
					
				case "cancel_add":
					view.hideCanvasAdd();
					break;
					
				case "show_mod":
					view.showCanvasMod( view.view_id );
					break;
					
				case "cancel_modify":
					view.hideCanvasMod( view.view_id );
					break;
					
				case "show_delete":
					view.showCanvasDelete( view.view_id );
					break;
					
				case "cancel_delete":
					view.hideCanvasDelete( view.view_id, function(){} );
					break;
					
				case "show_reorder":
					view.showReorderList();
					break;
					
				case "cancel_reorder":
					view.showNormalList();
					break;
					
				default:
					$.colorbox({ href:'/ajax/halfnerd_helper.php?task=view&process=' + process + '&view_id=' + view.view_id });
					break;
			}//end switch
		});
});

function View( view_id )
{
	this.view_id = view_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/
	
	this.add = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=view&process=add&view_id=0',
			data: $( form_name ).serialize( true ),
			success: function( view_id ){	
			
				//new object
				var inner = new View( view_id );
				
				//show success message
				showMessage( "View Added", 1, function(){ reloadPage( 1000 ); } );
			}
		});
		
	}//add()
	
	this.modify = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=view&process=modify&view_id=' + this.view_id,
			data: $( form_name ).serialize( true ),
			success: function( view_id ){		
				
				//build callback
				var callback = function() { 
					var inner = new View( view_id );
					inner.showNormalList();
				}
				
				//show success message
				showMessage( "View Saved", 1, callback );
				
			}
		});
		
	}//modify()
	
	this.deleteRecord = function()
	{
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=view&process=delete&view_id=' + this.view_id,
			data: "",
			success: function( view_id ){
				
				//new object
				var inner = new View( view_id );
				
				//show success message
				showMessage( "View Deleted", 1, function(){ reloadPage( 1000 ); } );
			}
		});
	}//delete()
	
	this.reorder = function()
	{
		var list_array = $( "#view_list" ).sortable( "toArray" );
		var list_string = listToString( list_array );
		
		$.ajax({
			type:'post',
			data: { view_priorities: list_string },
			url: '/ajax/halfnerd_helper.php?task=view&process=reorder&view_id=0',
			success: function( reply ){
			
				//show message and reload
				showMessage( "Views Reordered", 1, function(){ reloadPage( 1000 ); } );
			}
		});
		
	}//saveOrder()
	
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateAddModForm = function( process, view_id )
	{
		var form_name = "#view_form_" + view_id;
		
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=view&process=validate',
			data: $( form_name ).serialize( true ),
			success: function( reply ) {		
			
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				var inner = new View( view_id );
				
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
							
						case "delete":
							inner.deleteRecord();
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
	
/**********************************************************************************************************************************
Ui functions
**********************************************************************************************************************************/

	this.showCanvasMod = function( view_id )
	{
		//hide info
		$( "#view_info_" + view_id ).fadeOut( function( ){
		
			$( "#view_canvas_delete_" + view_id ).fadeOut( function( ){
			
				//new obj
				var inner = new View( 0 );
				
				var callback = function(){
					//show canvas
					$( "#view_canvas_mod_" + view_id ).slideDown();
					
					//disable hover
					$( "#view_canvas_mod_" + view_id ).attr( "hover_enabled", "0" );
				};
				
				//hide add
				inner.hideCanvasAdd( callback );
				
			});
		});
	}//showCanvas()
	
	this.hideCanvasMod = function( view_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#view_canvas_mod_" + view_id ).slideUp( function(){
			
			//show info
			$( "#view_info_" + view_id ).fadeIn( function(){
				
				//run callback
				callback();					
			});
			
		});
	}//hideCanvas()
	
	this.showCanvasAdd = function( view_id )
	{
		//hide info
		$( "#view_canvas_add" ).slideDown();
		
	}//showCanvas()
	
	this.hideCanvasAdd = function( callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide info
		$( "#view_canvas_add" ).slideUp( function(){
			
			//run callback
			callback();
		});
		
	}//hideCanvas()
	
	this.showCanvasDelete = function( view_id )
	{
		//hide info
		$( "#view_info_" + view_id ).fadeOut( function( ){
		
			$( "#view_canvas_mod_" + view_id ).fadeOut( function( ){
			
				//show canvas
				$( "#view_canvas_delete_" + view_id ).fadeIn();
				
			});
		});
		
	}//showCanvas()
	
	this.hideCanvasDelete = function( view_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#view_canvas_delete_" + view_id ).fadeOut( function(){
		
			//show info
			$( "#view_info_" + view_id ).fadeIn( function(){
				
				//run callback
				callback();	
			});
			
		});
	}//hideCanvas()

	this.showReorderList = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=view&process=show_reorder_views&view_id=0',
			data:{},
			success: function( html ){
					
				//populate new list content
				$( "#view_list_container" ).html( html );
				
				//show reorder form
				$( "#view_canvas_reorder" ).slideDown();
				
				//make list sortable
				$( "#view_list" ).sortable();
			}
		});
	}//showReorderViews()
	
	this.showNormalList = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=view&process=show_normal_views&view_id=0',
			data:{},
			success: function( html ){
				
				//populate new list content
				$( "#view_list_container" ).html( html );
				
				//hide reorder form
				$( "#view_canvas_reorder" ).slideUp();
			}
		});
	}//showNormalViews()
	
}//class View