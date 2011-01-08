/**
 * A class to handle authentication records and session functions.
 * @package	halfnerdCMS
 * @author	Hafner
 * @since	20101215
 */

$( document ).ready( function(){
	
	$( "#auth_auto_login" )
		.live( "keypress", function( event ){
	
			//login on enter
			if( event.keyCode == 13 )
			{
	    		auth = new Authentication( 0 );
	    		auth.validateLoginAttempt();
	    	}
		});
	
	$( "#authentication" )
    	.live( "click", function( event ){
    	
    	//cancel event
		event.preventDefault();
		
		//get vars
		var auth_id = ( hasAttr( $( this ), "auth_id" ) ) ? $( this ).attr( "auth_id" ) : 0;
		var process = $( this ).attr( "process" );
		
		//create authentication object
		var auth = new Authentication( auth_id );
		
		//do action
		switch( process.toLowerCase() )
		{
			case "change_password":
				auth.validatePasswordChange();	
				break;
				
			case "login":
				auth.validateLoginAttempt();
				break;
				
			case "kill_login":
				auth.killLogin();
				break;
				
			case "kill_session":
				auth.killSession();
				break;
				
			default:
				//show colorbox
				$.colorbox({ href:'/ajax/halfnerd_helper.php?task=authentication&process=' + process + '&auth_id=' + auth.auth_id });
				break;
		}
	});
    
});//end document.ready


function Authentication( auth_id )
{
	this.auth_id = auth_id;
	
/**********************************************************************************************************************************
action functions
**********************************************************************************************************************************/

	this.doPasswordChange = function()
	{
		$.ajax({
			type:"POST",
			url:'/ajax/halfnerd_helper.php?task=authentication&process=change_password',
			data: $( "#auth_password_form" ).serialize( true ),
			success: function( reply ){
			
				//show success message
				showMessage( "Password changed. Logging out...", 1 );
				
				//close colorbox with 1 second delay
				closeColorbox( 3000 );
				
				//log out
				var inner_auth = new Authentication(); 
				inner_auth.killLogin( 3000 );
			}
		});
		
	}//doPasswordChange()
	
	this.userHasValidLogin = function()
	{
		return $.ajax({
			type: 'post',
			async:false,
			dataType: "jsonp",
			url: '/ajax/halfnerd_helper.php?task=authentication&process=check_current_login'
		}).responseText;
		
	}//userHasValidLogin()
	
/**********************************************************************************************************************************
session functions
**********************************************************************************************************************************/
	
	this.killLogin = function( delay )
	{
		$.ajax({
			type: "POST",
			url: '/ajax/halfnerd_helper.php?task=session&process=kill_login',
			data: "",
			success: function( reply ) {
				reloadPage( delay );
			}
		});
		
	}//killLogin()
	
	this.doLogin = function()
	{
		$.ajax({
			type:"POST",
			url:'/ajax/halfnerd_helper.php?task=session&process=store_login',
			data: $( "#auth_login_form" ).serialize( true ),
			success: function( reply ){
				
				reloadPage( 0 );
			}
		});
		
	}//doLogin()
	
	this.killSession = function()
	{	
		$.ajax({
			type: "POST",
			url: '/ajax/halfnerd_helper.php?task=session&process=kill',
			data: "",
			success: function( reply ){
				reloadPage( 0 );
			}
		});
		
	}//killSession()
	
	this.getSessionVar = function( key )
	{
		var return_value = 
			$.ajax({
				type:'post',
				url: '/ajax/halfnerd_helper.php?task=session&process=get_var',
				data: { key: key },
				async:false,
				dataType: "jsonp"
			}).responseText;
		
		return return_value;
	}//getSessionVar()
	
	this.setSessionVar = function( key, value )
	{
		$.ajax({
			type:'post',
			url: '/ajax/halfnerd_helper.php?task=session&process=set_var',
			data:{ key: key, value: value  }
		});
		
	}//setSessionVar()
	
/**********************************************************************************************************************************
validation functions
**********************************************************************************************************************************/

	this.validateLoginAttempt = function()
	{
		$.ajax({
			type:"POST",
			url:'/ajax/halfnerd_helper.php?task=session&process=validate_login',
			data: $( "#auth_login_form" ).serialize( true ),
			success: function( reply ){
				
				//get result
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				
				if( result == 1 )
				{
					var inner = new Authentication( 0 ); 
					inner.doLogin();
				}
				else
				{
					showMessage( message, 0, function(){} );
				}
			}
		});
		
	}//validateLoginAttempt()
	
	this.validatePasswordChange = function()
	{
		$.ajax({
			type:"post",
			url:'/ajax/halfnerd_helper.php?task=authentication&process=validate_change_password',
			data: $( "#auth_password_form" ).serialize( true ),
			success: function( reply ){
				
				//get result
				var reply_split = reply.split( "^" );
				var result =  reply_split[0];
				var message = reply_split[1];
				
				if( result == 1 )
				{
					var inner_auth = new Authentication(); 
					inner_auth.doPasswordChange();
				}
				else
				{
					showMessage( message, 0 );
				}
			}
		});
		
	}//validatePasswordChange()

}//class Authentication