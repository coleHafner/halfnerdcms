$( document ).ready( function(){

	$( "#file" )
		.live( "click", function( event ){
		
			//cancel default
			event.preventDefault();
			
			//new file
			var file_obj = new File();
			
			//show loader
			$( "#result" ).removeAttr( "style" );
			$( "#result" ).addClass( "normal_font" )
			$( "#result" ).html( '<img src="/images/loader_small.gif" />&nbsp;Uploading...' );
			
			//get file
			var file = $( "#file_to_upload" ).attr( "value" );
			
			//upload file
			file_obj.validateUploadForm();
		});
});

function File()
{

/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateUploadForm = function( file_name )
	{
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=file&process=validate',
			data: { file_path_and_name: file, is_image: "1" },
			success: function( validation_result ){
				
				validation_result = $.trim( validation_result );
				
				if( validation_result == "0" )
				{
					var inner = new File();
					inner.uploadFile();
				}
				else
				{
					showMessage( message, 0 )
				}
			}
		});
	
	}//validateUploadForm()
	
	this.validateFileUploadForm = function()
	{
		//set vars
		var return_value = false;
		
		//check strlen
		if( $( "#file_to_upload" ).attr( "value" ).length == 0 )
		{
			return_value = "You must select a file.";
		}
		
		return return_value;
		
	}//validateFileUploadForm()
	
	this.validateImageUploadForm = function()
	{
		//set vars
		var return_value = false;
		var valid_extensions = [ "png", "gif", "jpg", "jpeg" ];
		var file = $( "#file_to_upload" ).attr( "value" );
		
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
		
		if( return_value == false )
		{
			this.checkDuplicateFile( file );
			
			//check unique flag ( controlled by checkDuplicateFile() )
			var file_is_unique = $( "#file_is_unique" ).val();
			if( file_is_unique == "0" )
			{
				return_value = "That file already exists.";
			}
		}
		
		return return_value;
		
	}//validateFileUploadForm()
	
	this.checkDuplicateFile = function( file )
	{
		//check duplicate file name
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=file&process=check-dup-file-name',
			data: { file_path_and_name: file },
			success: function( reply ) {
				reply = $.trim( reply );
				
				if( reply != "0" )
				{
					$( "#file_is_unique" ).val( "0" );
				}
				else
				{
					$( "#file_is_unique" ).val( "1" );
				}
				
				return "hello world";
			}
		});
	}//checkDuplicateFile()
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/
	
	this.uploadFile = function()
	{
		//upload file
		$( "#file_upload_form" ).submit();	
		setTimeout( 'window.location.reload();', 7000 );
		
	}//uploadFile()
	
}//File class