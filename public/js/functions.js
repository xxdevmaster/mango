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
	//$('#autoCloseMsg').hide();
	if(error)
		$('#autoCloseMsg').attr('class','alert alert-danger');
	else
		$('#autoCloseMsg').attr('class','alert alert-success');
	
	$('#autoCloseMsg').children('strong').html(message);
	$('#autoCloseMsg').slideDown(250);
	if(autoCloseMsgDelay>0)
	  autoCloseMsgTimer = setTimeout(function(){$('#autoCloseMsg').slideUp(200);},autoCloseMsgDelay);
}


//close autoCloseMsg
function autoCloseMsgHide(){
	clearTimeout(autoCloseMsgTimer);
	$('#autoCloseMsg').slideUp(100);	
}



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
		$(SectionName).html(data); //updateing users list
	}).fail(function(){
		autoCloseMsg(1,'Bad Request',7000);
	});	
}



/******************
	string Url 
	string id identifire for upload button
	object Params send proparties Parametrs
	function CallbackMessage
*/

function CHUpload( url, id, buttonText, params, callback ) {
	$('#'+id).uploadifive({
		'buttonText' 	 	 : buttonText,
		'queueID' 		  	 : false,
		'removeCompleted' 	 : false,
		'removeTimeout' 	 : 0,
		'itemTemplate'		 : '',
		'width' 		 	 : '129',
		'height' 		 	 : '35',
		'multi'           	 : false,
		'formData'       	 : params,
		'uploadScript'   	 : url,
		'onError'      		 : function(errorType) {
									$('.loading').hide();
									autoCloseMsg(1,'The error was: ' + errorType,50000);
		},
		'onUpload'		 	 : function(){
									autoCloseMsgHide();
									$('.loading').show();
							   },
		'onUploadComplete'	 : function(file, data) {
									if(data){
										if(callback && typeof(callback) === "function") {
											$('.loading').hide();
											callback(data);
										}
									}
								}
	});
}