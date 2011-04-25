$( document ).ready( function() {
	
	$( ".show_slide" ).click( function( event ){
		
		event.preventDefault();
		
		var requested_slide = $( this ).attr( "slide_num" );
		var current_slide = $( "#current_slide" ).attr( "value" );
		var slide_height = parseInt( $( ".slide" ).css( 'height' ).toString().replace( "px", "" ) );
		
		if( requested_slide != current_slide )
		{
			var requested_position = requested_slide * slide_height;
			var scroll_to = ( requested_position * -1 ) + slide_height;
			
			$( ".slide_controls" ).children().removeClass( "selected" );
			$( "#nav_item" + requested_slide ).addClass( "selected" );
			$( "#slide_holder" ).animate( { top:scroll_to.toString() }, 1000, function(){ 
				$( "#current_slide" ).attr( "value", requested_slide );
			});
		}
	});
	
	$( ".show_slide_blog" ).click( function( event ){
		
		event.preventDefault();
		
		var requested_slide = $( this ).attr( "slide_num" );
		var current_slide = $( "#current_slide_blog" ).attr( "value" );
		var slide_width = parseInt( $( ".blog_slide" ).css( 'width' ).toString().replace( "px", "" ) ) + 12;
		
		if( requested_slide != current_slide )
		{
			var requested_position = requested_slide * slide_width;
			var scroll_to = ( requested_position * -1 ) + slide_width;
			
			blogActivateButton( $( this ), function(){} );
			blogScrollTo( scroll_to.toString(), requested_slide, function(){} );
		}
	});
	
	$( "#blog_search_button" ).click( function( event ){
		
		event.preventDefault();
		
		//scroll to home page
		blogActivateButton( $( "#blog_control_1" ), function(){} );
		blogScrollTo( 0, 1, function(){
			//run search
			$( "#blog_content_1" ).html( "Searching blog entries..." );
		} );
	});
	
	$( ".show_slide_p" ).click( function( event ){
		
		event.preventDefault();
		
		var direction = $( this ).attr( "direction" );
		var max_slides = parseInt( $( "#max_slides_p" ).attr( "value" ) );
		var current_slide = parseInt( $( "#current_slide_p" ).attr( "value" ) );
		var requested_slide = ( direction == "forward" ) ? current_slide + 1 : current_slide - 1;
		var slide_width = parseInt( $( ".p_slide" ).css( 'width' ).toString().replace( "px", "" ) ) + 12;
		
		if( requested_slide != current_slide && 
			requested_slide > 0 && 
			requested_slide <= max_slides )
		{
			var requested_position = requested_slide * slide_width;
			var scroll_to = ( requested_position * -1 ) + slide_width;
			
			$( "#p_holder" ).animate( { left:scroll_to.toString() }, 1000, function(){
				$( "#current_slide_p" ).attr( "value", requested_slide );
			});
		}
	});
});

function blogScrollTo( scroll_to, requested_slide, callback )
{
	$( "#blog_holder" ).animate( { left:scroll_to.toString() }, 1000, function(){
		$( "#current_slide_blog" ).attr( "value", requested_slide );
		callback();
	});
}//blogScrollTo()

function blogActivateButton( el, callback )
{
	$( ".blog_nav td a" ).removeClass( "selected_blog" );
	el.addClass( "selected_blog" );
	callback();
}//blogActivateButton()