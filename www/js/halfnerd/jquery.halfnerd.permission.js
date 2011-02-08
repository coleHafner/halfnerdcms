/**
 * A class to handle article records.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */
 
$( document ).ready( function(){

	$( "#permission" )
		.live( "click", function( event ){
		
			//cancel event
			event.preventDefault();
			
			//get vars
			var permission_id = ( hasAttr( $( this ), "permission_id" ) ) ? $( this ).attr( "permission_id" ) : 0;
			var process = $( this ).attr( "process" );
			
			//new Permission
			var permission = new Permission( permission_id );
			
			//do action
			switch( process.toLowerCase() )
			{
				case "add":
					permission.validateAddModForm( "add", permission.permission_id );
					break;
					
				case "modify":
					permission.validateAddModForm( "modify", permission.permission_id );
					break;
					
				case "delete":
					permission.deleteRecord();
					break;
					
				case "show_add":
					permission.showCanvasAdd( permission.permission_id );
					break;
					
				case "cancel_add":
					permission.hideCanvasAdd();
					break;
					
				case "show_mod":
					permission.showCanvasMod( permission.permission_id );
					break;
					
				case "cancel_modify":
					permission.hideCanvasMod( permission.permission_id );
					break;
					
				case "show_delete":
					permission.showCanvasDelete( permission.permission_id );
					break;
					
				case "cancel_delete":
					permission.hideCanvasDelete( permission.permission_id, function(){} );
					break;
					
				default:
					$.colorbox({ href:'/ajax/halfnerd_helper.php?task=permission&process=' + process + '&permission_id=' + permission.permission_id });
					break;
					
			}//end switch
		});
});

function Permission( permission_id )
{
	this.permission_id = permission_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/
	
	this.add = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=permission&process=add&permission_id=0',
			data: $( form_name ).serialize( true ),
			success: function( permission_id ){	
				
				//show success message
				showMessage( "Permission Added", 1, function(){ reloadPage( 1000 ); } );
			}
		});
		
	}//add()
	
	this.modify = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=permission&process=modify&permission_id=' + this.permission_id,
			data: $( form_name ).serialize( true ),
			success: function( permission_id ){		
			
				//show success message
				showMessage( "Permission Saved", 1, function(){ reloadPage( 1000 ); } );
			}
		});
		
	}//modify()
	
	this.deleteRecord = function()
	{
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=permission&process=delete&permission_id=' + this.permission_id,
			data: "",
			success: function( permission_id ){
				
				//show success message
				showMessage( "Permission Deleted", 1, function(){ reloadPage( 1000 ); } );
			}
		});
	}//delete()
	
/*********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateAddModForm = function( process, permission_id )
	{
		var form_name = "#permission_form_" + permission_id;
		
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=permission&process=validate',
			data: $( form_name ).serialize( true ),
			success: function( reply ) {		
				
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				var inner = new Permission( permission_id );
				
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

	this.showCanvasMod = function( permission_id )
	{
		//hide info
		$( "#permission_info_" + permission_id ).fadeOut( function( ){
		
			$( "#permission_canvas_delete_" + permission_id ).fadeOut( function( ){
				
				//new obj
				var inner = new Permission( 0 );
				
				var callback = function(){
					//show canvas
					$( "#permission_canvas_mod_" + permission_id ).slideDown();
					
					//disable hover
					$( "#permission_canvas_mod_" + permission_id ).attr( "hover_enabled", "0" );
				};
				
				//hide add
				inner.hideCanvasAdd( callback );
				
			});
		});
	}//showCanvas()
	
	this.hideCanvasMod = function( permission_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#permission_canvas_mod_" + permission_id ).slideUp( function(){
			
			//show info
			$( "#permission_info_" + permission_id ).fadeIn( function(){
				
				//run callback
				callback();					
			});
			
		});
	}//hideCanvas()
	
	this.showCanvasAdd = function( permission_id )
	{
		//hide info
		$( "#permission_canvas_add" ).slideDown();
		
	}//showCanvas()
	
	this.hideCanvasAdd = function( callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide info
		$( "#permission_canvas_add" ).slideUp( function(){
			
			//run callback
			callback();
		});
		
	}//hideCanvas()
	
	this.showCanvasDelete = function( permission_id )
	{
		//hide info
		$( "#permission_info_" + permission_id ).fadeOut( function( ){
		
			$( "#permission_canvas_mod_" + permission_id ).fadeOut( function( ){
			
				//show canvas
				$( "#permission_canvas_delete_" + permission_id ).fadeIn();
				
			});
		});
		
	}//showCanvas()
	
	this.hideCanvasDelete = function( permission_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#permission_canvas_delete_" + permission_id ).fadeOut( function(){
		
			//show info
			$( "#permission_info_" + permission_id ).fadeIn( function(){
				
				//run callback
				callback();	
			});
			
		});
	}//hideCanvas()

	this.refreshList = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=permission&process=show_normal_views&permission_id=0',
			data:{},
			success: function( html ){
				
				//populate new list content
				$( "#permission_list_container" ).html( html );
				
				//hide reorder form
				$( "#permission_canvas_reorder" ).slideUp();
			}
		});
	}//showNormalViews()
	
}//class Permission