/**
 * A class to handle article records.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */
 
$( document ).ready( function(){

	$( "#article" )
		.live( "click", function( event ){
		
			//cancel event
			event.preventDefault();
			
			//get vars
			var article_id = ( hasAttr( $( this ), "article_id" ) ) ? $( this ).attr( "article_id" ) : 0;
			var process = $( this ).attr( "process" );
			
			//new article
			var article = new Article( article_id );
			
			//do action
			switch( process.toLowerCase() )
			{
				case "add":
					article.validateAddModForm( "add", article.article_id );
					break;
					
				case "modify":
					article.validateAddModForm( "modify", article.article_id );
					break;
					
				case "delete":
					article.deleteRecord();
					break;
					
				case "show_add":
					article.showCanvasAdd( article.article_id );
					break;
					
				case "cancel_add":
					article.hideCanvasAdd();
					break;
					
				case "show_mod":
					article.showCanvasMod( article.article_id );
					break;
					
				case "cancel_modify":
					article.hideCanvasMod( article.article_id );
					break;
					
				case "show_delete":
					article.showCanvasDelete( article.article_id );
					break;
					
				case "cancel_delete":
					article.hideCanvasDelete( article.article_id );
					break;
					
				case "refresh_section_selector":
					article.refreshSectionSelector( article.article_id );
					break;
					
				default:
					$.colorbox({ href:'/ajax/halfnerd_helper.php?task=article&process=' + process + '&article_id=' + article.article_id });
					break;
			}//end switch
		});
});

function Article( article_id )
{
	this.article_id = article_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/
	
	this.add = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=article&process=add&article_id=0',
			data: $( form_name ).serialize( true ),
			success: function( article_id ){	
			
				//show success message
				showMessage( "Article Added", 1, function(){ reloadPage( 1000 ); } );
			}
		});
		
	}//add()
	
	this.modify = function( form_name )
	{
		//add or modify article
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=article&process=modify&article_id=' + this.article_id,
			data: $( form_name ).serialize( true ),
			success: function( article_id ){
			
				//show success message
				showMessage( "Article Saved", 1, function(){ reloadPage( 1000 ) } );
			}
		});
		
	}//modify()
	
	this.deleteRecord = function()
	{
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=article&process=delete&article_id=' + this.article_id,
			data: "",
			success: function( article_id ){
			
				//show success message
				showMessage( "Article Deleted", 1, function(){ reloadPage( 1000 ) } );
			}
		});
	}//delete()
	
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateAddModForm = function( process, article_id )
	{
		var form_name = "#article_form_" + article_id;
		
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=article&process=validate',
			data: $( form_name ).serialize( true ),
			success: function( reply ) {		
			
				//get vars
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				var inner = new Article( article_id );
				
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

	this.showCanvasMod = function( article_id )
	{
		//hide info
		$( "#article_info_" + article_id ).fadeOut( function( ){
		
			$( "#article_canvas_delete_" + article_id ).fadeOut( function( ){
			
				//new obj
				var inner = new Article( 0 );
				
				var callback = function(){
					//show canvas
					$( "#article_canvas_mod_" + article_id ).slideDown();
					
					//disable hover
					$( "#article_canvas_mod_" + article_id ).attr( "hover_enabled", "0" );
				}
				
				//hide add
				inner.hideCanvasAdd( callback );
				
			});
		});
	}//showCanvas()
	
	this.hideCanvasMod = function( article_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#article_canvas_mod_" + article_id ).slideUp( function(){
		
			//show info
			$( "#article_info_" + article_id ).fadeIn( function(){
				
				callback();
			});
			
		});
	}//hideCanvas()
	
	this.showCanvasAdd = function( article_id )
	{
		//hide section
		var section = new Section( 0 );
		
		//hide section canvas
		section.hideManager( function(){ $( "#article_canvas_add" ).slideDown(); } );
				
	}//showCanvas()
	
	this.hideCanvasAdd = function( callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide info
		$( "#article_canvas_add" ).slideUp( function(){
		
			callback();
		});
	}//hideCanvas()
	
	this.showCanvasDelete = function( article_id )
	{
		//hide info
		$( "#article_info_" + article_id ).fadeOut( function( ){
		
			$( "#article_canvas_mod_" + article_id ).fadeOut( function( ){
			
				//show canvas
				$( "#article_canvas_delete_" + article_id ).slideDown();
				
			});
		});
		
	}//showCanvas()
	
	this.hideCanvasDelete = function( article_id, callback )
	{
		callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
		
		//hide canvas
		$( "#article_canvas_delete_" + article_id ).slideUp( function(){
		
			//show info
			$( "#article_info_" + article_id ).fadeIn( function(){
				
				//run callback
				callback();	
			});
			
		});
	}//hideCanvas()

	this.refreshSectionSelector = function( article_id )
	{
		$.ajax({
			type: 'post',
			url: '/ajax/halfnerd_helper.php?task=article&process=refresh_section_selector&article_id=' + article_id,
			data: {},
			success: function( section_selector ){
				$( ".article_section_selector_" + article_id ).html( section_selector );
			}
		});
	}//refreshSectionSelector()
	
}//class Article