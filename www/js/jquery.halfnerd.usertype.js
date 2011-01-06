/**
 * A class to handle user type records.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101228
 */
 
$( document ).ready( function(){

	$( "#user_type" )
		.live( "click", function( event ){
		
			//cancel event
			event.preventDefault();
			
			//get vars
			var user_type_id = ( hasAttr( $( this ), "user_type_id" ) ) ? $( this ).attr( "user_type_id" ) : 0;
			var process = $( this ).attr( "process" );
			
			//new UserType
			var user_type = new UserType( user_type_id );
			
			//do action
			switch( process.toLowerCase() )
			{
				case "add":
					user_type.validateAddModForm( "add", user_type.user_type_id );
					break;
					
				case "modify":
					user_type.validateAddModForm( "modify", user_type.user_type_id );
					break;
					
				case "delete":
					user_type.deleteRecord();
					break;
					
				case "show_manager":
					user_type.showManager();
					break;
					
				case "hide_manager":
					user_type.hideManager( function(){} );
					break;
					
				case "show_add":
					user_type.showCanvasAdd( user_type.user_type_id );
					break;
					
				case "cancel_add":
					user_type.hideCanvasAdd();
					break;
					
				case "show_modify":
					user_type.showCanvasMod( user_type.user_type_id );
					break;
					
				case "cancel_modify":
					user_type.hideCanvasMod( user_type.user_type_id );
					break;
					
				case "show_delete":
					user_type.showCanvasDelete( user_type.user_type_id );
					break;
					
				case "cancel_delete":
					user_type.hideCanvasDelete( user_type.user_type_id, function(){} );
					break;
					
				default:
					$.colorbox({ href:'/ajax/halfnerd_helper.php?task=user_type&process=' + process + '&user_type_id=' + user_type.user_type_id });
					break;
			}//end switch
		});
});

function UserType( user_type_id )
{
	this.user_type_id = user_type_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/
	
	this.add = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=user_type&process=add&user_type_id=0',
			data: $( form_name ).serialize( true ),
			success: function( user_type_id ){	
			
				//new object
				var inner = new UserType( user_type_id );
				
				//refresh section list
				inner.refreshList();
			}
		});
		
	}//add()
	
	this.modify = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=user_type&process=modify&user_type_id=' + this.user_type_id,
			data: $( form_name ).serialize( true ),
			success: function( user_type_id ){		
			
				//new object
				var inner = new UserType( user_type_id );
				
				//refresh section list
				inner.refreshList();
			}
		});
		
	}//modify()
	
	this.deleteRecord = function()
	{
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=user_type&process=delete&user_type_id=' + this.user_type_id,
			data: "",
			success: function( user_type_id ){
				
				//new object
				var inner = new UserType( user_type_id );
				
				//refresh section list
				inner.refreshList();
			}
		});
	}//delete()
		
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateAddModForm = function( process, user_type_id )
	{
		var form_name = "#user_type_form_" + user_type_id;
		
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=user_type&process=validate',
			data: $( form_name ).serialize( true ),
			success: function( reply ) {		
				
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				var inner = new UserType( user_type_id );
				
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
					showMessage( message, 0, "user_type", "0" );	
				}
			}
		});
		
	}//validateAddModForm()
	
/**********************************************************************************************************************************
Ui functions
**********************************************************************************************************************************/

	this.showManager = function()
	{
		//hide section
		var user = new User( 0 );
		
		//hide section canvas
		user.hideCanvasAdd( function(){ $( "#user_type_manager" ).slideDown(); } );
		
	}//showManager()
	
	this.hideManager = function( callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		$( "#user_type_manager" ).slideUp( function(){
			callback();
		});
		
	}//hideManager()
	
	this.showCanvasMod = function( user_type_id )
	{
		//show text input
		$( "#user_type_title_" + user_type_id ).fadeOut( function(){
		
			$( "#user_type_title_box_" + user_type_id ).fadeIn();
		});
		
		//show modify buttons
		$( "#user_type_mod_init_" + user_type_id ).fadeOut( function(){
			$( "#user_type_mod_confirm_" + user_type_id ).fadeIn();
		});
		
	}//showModCanvas()
	
	this.hideCanvasMod = function( user_type_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//show modify buttons
		$( "#user_type_mod_confirm_" + user_type_id ).fadeOut( function(){
		
			$( "#user_type_mod_init_" + user_type_id ).fadeIn();
		});
		
		//show modify text box
		$( "#user_type_title_box_" + user_type_id ).fadeOut( function(){
		
			$( "#user_type_title_" + user_type_id ).fadeIn();
		});
		
	}//hideCanvasMod()
	
	this.showCanvasDelete = function( user_type_id )
	{
		//show delete buttons
		$( "#user_type_delete_init_" + user_type_id ).fadeOut( function( ){
		
			$( "#user_type_delete_confirm_" + user_type_id ).fadeIn();
		});
		
	}//showCanvasDelete()
	
	this.hideCanvasDelete = function( user_type_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//show delete buttons
		$( "#user_type_delete_confirm_" + user_type_id ).fadeOut( function( ){
		
			$( "#user_type_delete_init_" + user_type_id ).fadeIn();
		});
		
	}//hideCanvasDelete()
	
	this.refreshList = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=user_type&process=get-type-list&user_type_id=0',
			data: {},
			success: function( section_list ){
				$( "#user_type_items_container" ).html( section_list );
			}
		});
	}//refreshSectionList()
		
}//class UserType