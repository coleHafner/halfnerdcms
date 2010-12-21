/**
 * A class to handle common functionality.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */
 
$(document).ready(function(){
	
	//init pickers
	initColorbox();
    initDatepickers();
    initTimepickers();
    
    //colorbox close
    $( "#close_colorbox" )
    	.live( "click", function() {
    		closeColorbox( 0 );
	    });
    
	//submit input
    $( ".input_clear" )
    	.live( "click", function(){
	    	clearInputValue( $( this ) );
	    })
    
    	.live( "blur", function(){
    		restoreInputValue( $( this ) );
    	});
    	
    $( ".admin_button" )
    	.live( "mouseenter", function( event ){
    	
    		//cancel hover
    		event.preventDefault();
    		
			$( this ).removeClass( "border_color_white" ).addClass( "no_hover border_color_orange" );
    	})
    	
    	.live( "mouseleave", function(){
    		$( this ).removeClass( "no_hover border_color_orange" ).addClass( "border_color_white" );
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
	    		
	    		//show selected bg color and admin options for current article_container
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
    	
    $( ".section_title" )
    	.live( "click", function(){
    		$( "#result_section_0" ).html( "" );
    	});
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

function showMessage( message, message_status, process, pk_id )
{
	var id = "#result_" + process + "_" + pk_id;
	
	if( message_status == 1 )
	{
		$( id ).removeClass( "result_failure" ).addClass( "color_orange" ).html( message );
	}
	else
	{
		$( id ).removeClass( "color_orange" ).addClass( "result_failure" ).html( message );
	}
	
}//showMessage()

function showGlobalMessage( message, status, callback )
{
	var run_callback = true;
	
	if( typeof( callback ) == "undefined" )
	{
		callback = "default";
		run_callback = false;
	}
	
	$( 'body' ).append( '<div class="global_message rounded_corners border_dark_grey bg_color_orange center header font_color_white" id="global_message">' + message + '</div>' );
	
	$( "#global_message" ).delay( 1500 ).fadeOut( "slow", function(){
	
		$( this ).remove();
		
		if( run_callback )
		{
			callback();
		}
	});
	
}//showGlobalMessage()

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
	return ( typeof( el.attr( attr_name ) ) !== undefined ) ? true : false;
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

