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



/*
	string Url 
	string RequestMethod POST or GET
	object send proparties Params
	string CallbackMessage
*/
function xmlhttprequest(Url,RequestMethod,CallbackMessage,Params)
{
	$.when(
		$.ajax({
			type: RequestMethod,
			url : Url,
			data: Params,
		})
	).done(function(data){
		//alert(data);
		if(data){
			getTemplate('/account/users/getTemplate','POST','#users'); //updating users list
			$('.loading').hide();//loading close
			autoCloseMsg(0,CallbackMessage,5000);//show results message
		}else
			$('.loading').hide();//loading close
	}).fail(function(){
		$('.loading').hide();//loading close
		autoCloseMsg(1,'Bad Request',7000); //show error message
	});
}



function CHxhr(Url,RequestMethod,Params,callback)
{
	console.log('something');
	/*$.when(
		$.ajax({
			type: RequestMethod,
			url : Url,
			data: Params,
		})
	).done(function(data){
		if(data){
			if(callback && typeof(callback) === "function") {
				callback(data);
			}
		}
	}).fail(function(){
		$('.loading').hide();  //loading close
		autoCloseMsg(1,'Bad Request',5000);  //show error message		
	});*/
}


function CHUpload( url, id, params, callback ) {
	console.log(url);
	console.log(id);
	console.log(params);
	console.log(callback);
	$('.'+id).uploadifive({
		'buttonText' 	 	 : 'Upload Image',
		'auto'           	 : true,
		'queueID' 		  	 : false,
		'removeCompleted'	 : true,
		'removeTimeout'		 : 1,
		'itemTemplate'	 	 : '',
		'width' 		 	 : '129',
		'height' 		 	 : '29',
		'scriptData'	 	 : {},
		'formData'       	 : params,
		'uploadScript'   	 : url,
		'onUploadComplete'	 : function(file, data) { 
									if(data){
										if(callback && typeof(callback) === "function") {
											callback(data);
										}
									}
								}
	});			
}