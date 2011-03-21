/**
 * A class to handle common functionality.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */
 
$(document).ready(function() {
	
	//init pickers
	//initColorbox();
    //initDatepickers();
    //initTimepickers();
    
    //submit input
	$( ".input_clear" )
		.live( "click", function(){
	    	clearInputValue( $( this ) );
	    })
	    
	  	.live( "focus", function(){
	    	clearInputValue( $( this ) );
	    })
	
	
		.live( "blur", function(){
			restoreInputValue( $( this ) );
		});
    	
    $( ".admin_button" )
    	.live( "mouseenter", function( event ){
    	
    		//cancel hover
    		event.preventDefault();
    		
    		if( hasAttr( $( this ), "active" ) == false )
    		{
    			$( this ).removeClass( "border_color_white" ).addClass( "no_hover border_color_orange" );
    		}
    	})
    	
    	.live( "mouseleave", function(){
    		if( hasAttr( $( this ), "active" ) == false )
    		{
    			$( this ).removeClass( "no_hover border_color_orange" ).addClass( "border_color_white" );
    		}
    	});
    	
    $( ".button" )
    	.live( "mouseenter", function(){
			$( this ).removeClass( "bg_color_white" ).addClass( "bg_color_orange" );
			$( this ).css( "color", "#FFF" );
    	})
    	
    	.live( "mouseleave", function(){
    		$( this ).removeClass( "bg_color_orange" ).addClass( "bg_color_white" );
			$( this ).css( "color", "" );
    	});
    	
    $( ".item_container" )
    	.live( "mouseenter", function(){

    		if( hasAttr( $( this ), "hover_enabled" ) && 
    			$( this ).attr( "hover_enabled" ) == "1" )
    		{
	    		//remove bg color and buttons from other article_containers
	    		$( ".item_container" ).removeClass( "bg_color_tan" ).addClass( "bg_color_light_tan" );
	    		$( ".item_container" ).each( function(){
	    			$( this ).find( "#item_control" ).hide();
	    		});
	    		
	    		//show selected bg color and admin options for current item_container
	    		$( this ).removeClass( "bg_color_light_tan" ).addClass( "bg_color_tan" );
	    		$( this ).find( "#item_control" ).show();
	    	}
    	})
    	
    	.live( "mouseleave", function(){
    	
    		if( hasAttr( $( this ), "hover_enabled" ) && 
    			$( this ).attr( "hover_enabled" ) == "1" )
    		{
	    		//change bg color to normal and hide admin options
	    		$( this ).removeClass( "bg_color_tan" ).addClass( "bg_color_light_tan" );
	    		$( this ).find( "#item_control" ).hide();
	    	}
    	});
    
    $( "#list_item" )
    	.live( "click", function( event ){
    		
    		//cancel link action
    		event.preventDefault();
    		
    		var process = $( this ).attr( "process" ).toLowerCase();
    		var item_id = $( this ).attr( "item_id" );
    		
    		switch( process )
    		{
    			case "add":
    				
    				//not all add buttons will be paired with a manager
    				if( $( "#item_manager" ).length > 0 )
    				{
    					$( "#item_manager" ).hide();
    				}
    				
		    		$( "#item_add_0" ).show();
    				break;
    				
    			case "mod":
    	    		$( "#item_add_0" ).hide();
		    		$( "#item_view_" + item_id ).hide();
		    		$( "#item_delete_" + item_id ).hide();
		    		$( "#item_mod_" + item_id ).show();
		    		break;
		    		
    			case "delete":
    				$( "#item_add_0" ).hide();
		    		$( "#item_view_" + item_id ).hide();
		    		$( "#item_mod_" + item_id ).hide();
		    		$( "#item_delete_" + item_id ).show();
		    		break;
		    		
    			case "view":
    				
    				$( "#item_add_0" ).hide();
    				
    				//only show view if id != 0
    				if( item_id != "0" )
    				{
	    				$( "#item_mod_" + item_id ).hide();
			    		$( "#item_delete_" + item_id ).hide();
			    		$( "#item_view_" + item_id ).show();
    				}
    				break;
    		}		
    	});
    
    $( "#manager" )
	.live( "click", function( event ){
		
		//cancel link action
		event.preventDefault();
		
		var process = $( this ).attr( "process" ).toLowerCase();
		
		switch( process )
		{
			case "show":
				$( "#item_add_0" ).hide();
				$( "#item_manager" ).show();
				break;
				
			case "hide":
				$( "#item_manager" ).hide();
				break;
		}		
	});
    
    if( $( "#post_body_0" ).length > 0 )
    {
    	/*
    	$( "#post_body_0" ).cleditor( { width:650, height:225, controls:
            "bold | italic | underline | strikethrough | font size | " +
            "style | color | highlight | bullets | numbering | " +
            "indent | alignleft | center | alignright | justify | undo | redo | " +
    		"rule | image | link | cut | copy | paste | pastetext" } );
    	*/
    }
});

/**********************************************************************************************************************************
common functions
**********************************************************************************************************************************/

function initDatepickers()
{
    $.datepicker.setDefaults( {
		dateFormat: "mm-dd-yy",
		buttonImageOnly:false
	});

	$( ".datepicker_input" ).datepicker();
	
}//initDatepickers()

function initTimepickers()
{	
	$( ".timepicker_input" ).timepicker({
		ampm: true,
		stepMinute: 5,
		timeFormat: 'h:mm TT'
	});
	
}//initTimepickers()

function initColorbox()
{
    $.fn.colorbox.settings.transition = "fade";
    $.fn.colorbox.settings.bgOpacity = "0.7";
    $.fn.colorbox.settings.contentCurrent = "({current}/{total})";
    $.fn.colorbox.settings.current = "Member {current} of {total}";
    
}//initColorbox()

function closeColorbox( reload )
{
	var reload_cmd = "";
	if( reload == 1 )
	{
		reload_cmd = 'window.location.reload();';
	}
	
	setTimeout( '$.colorbox.close();' + reload_cmd, 1000 );
	
}//closeColorbox()


/**********************************************************************************************************************************
form functions
**********************************************************************************************************************************/

function showMessage( message, message_status, callback )
{
	//hide message
	if( $( "#form_result_message" ).length > 0 )
	{
		$( "#form_result_message" ).remove();
	}
	
	callback = ( typeof( callback ) == "undefined" ) ? function(){} : callback;
	var bg_color = ( message_status == 1 ) ? "bg_color_accent" : "bg_color_red";
	
	$( 'body' ).append( '<div class="form_result_message ' + bg_color + ' center header font_color_white" id="form_result_message">' + message + '</div>' );
	
	$( "#form_result_message" ).delay( 1500 ).fadeOut( "slow", function(){
		
		$( this ).remove();
		
		callback();
	});
	
}//showMessage()

function trimLastCharFromString( string )
{
	//set vars
	var second_to_last_char = string.length - 1;
	var return_string = string.substr( 0, second_to_last_char );
	
	return return_string;
	
}//trimLastCharFromString()

function countString( string, delim )
{
	//set vars
	var string_array = string.split( delim );
	var return_value = string_array.length;
	
	return return_value;
	
}//countString()

function clearInputValue( el )
{
	var clear_if = el.attr( "clear_if" ).toLowerCase();
	var cur_val = el.val().toLowerCase();
	
	if( clear_if == cur_val )
	{	
		//update value
		el.val( "" );			
	}
	
	//clear result
	if( typeof( $( "#result" ) ) != undefined )
	{
		$( "#result" ).html( "" );
	}
	
}//clearInputValue()

function restoreInputValue( el )
{
	if( el.val() == "" )
	{	
		//update value
		el.val( el.attr( "clear_if" ) );
	}
}//restoreInputValue()

function listToString( list_array, delim_char )
{
	var list_string = "";
	var delim_char = ( typeof( delim_char ) == "undefined" ) ? "^" : delim_char;
	
	for( var i in list_array )
	{
		var delim = ( i == ( list_array.length - 1 ) ) ? "" : delim_char;
		list_string += list_array[i].toString().replace( "item_", "" ) + delim;
	}
	
	return list_string;

}//listToString();

function validateImageFile( el_id )
{
	//set vars
	var return_value = false;
	var valid_extensions = [ "png", "gif", "jpg", "jpeg" ];
	var file = $( el_id ).val();
	
	//check strlen
	if( file.length == 0 )
	{
		return_value = "You must select a file.";
	}
	
	if( return_value == false )
	{
		var val_split = file.split( "." );
		var last_element = val_split.length - 1;
		var extension = val_split[last_element];
		
		if( $.inArray( extension.toLowerCase(), valid_extensions ) == -1 )
		{
			return_value = "File must be an image.";
		}
	}
	
	return return_value;
	
}//validateFileUploadForm()

/**********************************************************************************************************************************
other functions
**********************************************************************************************************************************/

function showLoader( element, style )
{
	var loader_html = '<div class="loader_div"' + style + '><img src="/images/ajax-loader.gif"/></div>';
	
	$( element ).html( loader_html );
	
}//showLoader()

function hasAttr( el, attr_name )
{
	return ( typeof( el.attr( attr_name ) ) !== "undefined" ) ? true : false;
}//hasAttr

function reloadPage( delay )
{
	window.location.reload( delay );
}//reloadPage()

function appendAdminOptions( el, type, pk_id )
{
	switch( type.toLowerCase() )
	{
		case "article":
			var article = new Article( pk_id );
			article.showAdminOptions( el );
			break;
	}
	
}//appendAdminOptions()

