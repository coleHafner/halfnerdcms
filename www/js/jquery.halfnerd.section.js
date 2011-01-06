/**
 * A class to handle article records.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */
 
$( document ).ready( function(){

	$( "#section" )
		.live( "click", function( event ){
		
			//cancel event
			event.preventDefault();
			
			//get vars
			var section_id = ( hasAttr( $( this ), "section_id" ) ) ? $( this ).attr( "section_id" ) : 0;
			var process = $( this ).attr( "process" );
			
			//new Section
			var section = new Section( section_id );
			
			//do action
			switch( process.toLowerCase() )
			{
				case "add":
					section.validateAddModForm( "add", section.section_id );
					break;
					
				case "modify":
					section.validateAddModForm( "modify", section.section_id );
					break;
					
				case "delete":
					section.deleteRecord();
					break;
					
				case "show_manager":
					section.showManager();
					break;
					
				case "hide_manager":
					section.hideManager( function(){} );
					break;
					
				case "show_add":
					section.showCanvasAdd( section.section_id );
					break;
					
				case "cancel_add":
					section.hideCanvasAdd();
					break;
					
				case "show_modify":
					section.showCanvasMod( section.section_id );
					break;
					
				case "cancel_modify":
					section.hideCanvasMod( section.section_id );
					break;
					
				case "show_delete":
					section.showCanvasDelete( section.section_id );
					break;
					
				case "cancel_delete":
					section.hideCanvasDelete( section.section_id, function(){} );
					break;
					
				default:
					$.colorbox({ href:'/ajax/halfnerd_helper.php?task=section&process=' + process + '&section_id=' + section.section_id });
					break;
			}//end switch
		});
});

function Section( section_id )
{
	this.section_id = section_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/
	
	this.add = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=section&process=add&section_id=0',
			data: $( form_name ).serialize( true ),
			success: function( section_id ){	
			
				var callback = function(){ 
					//new object
					var inner = new Section( section_id );
					
					//refresh section list
					inner.refreshSectionList();
				}
				
				showMessage( "Section Saved", 1, callback );
			}
		});
		
	}//add()
	
	this.modify = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=section&process=modify&section_id=' + this.section_id,
			data: $( form_name ).serialize( true ),
			success: function( section_id ){		
			
				var callback = function(){
					//new object
					var inner = new Section( section_id );
					
					//refresh section list
					inner.refreshSectionList();
				}
				
				showMessage( "Section Saved", 1, callback );
			}
		});
		
	}//modify()
	
	this.deleteRecord = function()
	{
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=section&process=delete&section_id=' + this.section_id,
			data: "",
			success: function( section_id ){
				
				var callback = function(){
					//new object
					var inner = new Section( section_id );
					
					//refresh section list
					inner.refreshSectionList();
				}
				
				showMessage( "Section Deleted", 1, callback );
			}
		});
	}//delete()
		
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateAddModForm = function( process, section_id )
	{
		var form_name = "#section_form_" + section_id;
		
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=section&process=validate',
			data: $( form_name ).serialize( true ),
			success: function( reply ) {		
			
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				var inner = new Section( section_id );
				
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

	this.showManager = function()
	{
		//hide section
		var article = new Article( 0 );
		
		//hide section canvas
		article.hideCanvasAdd( function(){ $( "#section_manager" ).slideDown(); } );
		
	}//showManager()
	
	this.hideManager = function( callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		$( "#section_manager" ).slideUp( function(){
			callback();
		});
		
	}//hideManager()
	
	this.showCanvasMod = function( section_id )
	{
		//show text input
		$( "#section_title_" + section_id ).fadeOut( function(){
		
			$( "#section_title_box_" + section_id ).fadeIn();
		});
		
		//show modify buttons
		$( "#section_mod_init_" + section_id ).fadeOut( function(){
			$( "#section_mod_confirm_" + section_id ).fadeIn();
		});
		
	}//showModCanvas()
	
	this.hideCanvasMod = function( section_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//show modify buttons
		$( "#section_mod_confirm_" + section_id ).fadeOut( function(){
		
			$( "#section_mod_init_" + section_id ).fadeIn();
		});
		
		//show modify text box
		$( "#section_title_box_" + section_id ).fadeOut( function(){
		
			$( "#section_title_" + section_id ).fadeIn();
		});
		
	}//hideCanvasMod()
	
	this.showCanvasDelete = function( section_id )
	{
		//show delete buttons
		$( "#section_delete_init_" + section_id ).fadeOut( function( ){
		
			$( "#section_delete_confirm_" + section_id ).fadeIn();
		});
		
	}//showCanvasDelete()
	
	this.hideCanvasDelete = function( section_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//show delete buttons
		$( "#section_delete_confirm_" + section_id ).fadeOut( function( ){
		
			$( "#section_delete_init_" + section_id ).fadeIn();
		});
		
	}//hideCanvasDelete()
	
	this.refreshSectionList = function()
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=section&process=show_section_list&section_id=0',
			data: {},
			success: function( section_list ){
				$( "#section_items_container" ).html( section_list );
			}
		});
	}//refreshSectionList()
		
}//class Section