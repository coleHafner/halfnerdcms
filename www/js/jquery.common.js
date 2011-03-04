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
		var slide_width = parseInt( $( ".blog_slide" ).css( 'width' ).toString().replace( "px", "" ) ) + 12;//12 to account for 10px margin + 1px border on each side
		if( requested_slide != current_slide )
		{
			var requested_position = requested_slide * slide_width;
			var scroll_to = ( requested_position * -1 ) + slide_width;
			//$( ".slide_controls" ).children().removeClass( "selected" );
			//$( "#nav_item" + requested_slide ).addClass( "selected" );
			$( "#blog_holder" ).animate( { left:scroll_to.toString() }, 1000, function(){ 
				$( "#current_slide_blog" ).attr( "value", requested_slide );
			});
			
		}
	});
	
});