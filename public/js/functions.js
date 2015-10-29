// START global varables 
var autoCloseMsgTimer = 0;
// END global varables 
/*
	bool error 
	string message
	int autoCloseMsgDelay 	
*/
function autoCloseMsg(error,message,autoCloseMsgDelay){
	clearTimeout(autoCloseMsgTimer);
	$('#autoCloseMsg').hide();
	if(error)
		$('#autoCloseMsg').attr('class','alert alert-danger');
	else
		$('#autoCloseMsg').attr('class','alert alert-success');
	
	$('#autoCloseMsg').children('strong').html(message);
	$('#autoCloseMsg').slideToggle(350);
	if(autoCloseMsgDelay>0)
	  autoCloseMsgTimer = setTimeout(function(){$('#autoCloseMsg').fadeOut(350);},autoCloseMsgDelay);
}
$(document).on('click','#autoCloseMsgHide',function(e){
	e.preventDefault();
	clearTimeout(autoCloseMsgTimer);
	$('#autoCloseMsg').fadeOut(350);
});



//Get template with xmlHttpRequest
/*
	string Url 
	string RequestMethod POST or GET
	object send proparties Params
	string id or classname with DOM element SectionName
*/
function getTemplate(Url,RequestMethod,SectionName,Params)
{
	$.when(
		$.ajax({
			type: RequestMethod,
			url : Url,
			data: Params,
		})
	).done(function(data){
		$(SectionName).html(data);
	}).fail(function(){
		autoCloseMsg(1,'Bad Request',7000);
	});	
}


//destroy with xmlHttpRequest
/*
	string Url 
	string RequestMethod POST or GET
	object send proparties Params
	string CallbackMessage
*/
function destroy(Url,RequestMethod,CallbackMessage,Params)
{
	$.when(
		$.ajax({
			type: RequestMethod,
			url : Url,
			data: Params,
		})
	).done(function(data){
		if(data){
			getTemplate('/account/users/getTemplate','POST','#users');
			autoCloseMsg(0,CallbackMessage,5000);
		}
	}).fail(function(){
		autoCloseMsg(1,'Bad Request',7000);
	});
}