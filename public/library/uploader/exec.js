function updateList(type, filter,filmId){
	$.post('/titles/media/uploader/filterMediaUploaderUpdateList', {type:type, filter:filter, filmId:filmId, template:'uploader'}, function(response){
        $("#titlesBlock").html($(response).find('#titlesWithSearch'));
		$('#titles').filterByText($('#serachbox'), false);
	});
}
			
$(document).ready(function() {





    var account_id = $("#uploaderAccountid").val();
    var user_id = $("#uploaderUserid").val();
    var fileChooser = document.getElementById('file');
    var button = document.getElementById('upload-button');
    var film_id = $("input[name='filmId']").val();
    var buckets;
    var region;
    var SecretKey;
    var AccessKey;
    var avc_temp;
    /* var hash = "0bf4a7e03a9978d4ed2e5770adf23e33";
     var buckets = ["us.cinecliq.master.trailers", "us.cinecliq.master.films", "us.cinecliq.master.bonuss"];
     var region = "cloudsearch.us-east-1.amazonaws.com";
     var SecretKey = "YxnQ+urWxiEyHwc/AL8h3asoxqdyrGWBnFFYPK7c";
     var AccessKey = "AKIAJPIY5AB3KDVIDPOQ";*/
    // Get our File object

    $('.history_refresh').click(function(){
        var $icon = $(this).children('i');
        animateClass = "fa-spin";
        $icon.addClass( animateClass );
        $.post('/titles/media/uploader/getUploaderHistory',{filmId:film_id}, function(response){
            if(response.error === '0'){
                $("#uploaderHistory").html(response.html);
                $icon.removeClass( animateClass );
            }else
                $icon.removeClass( animateClass );
        });

    });

	$('#titles').filterByText($('#serachbox'), false);

	$('input[type=radio][name=bucket_type]').on('change', function () {
		var filter = $("input:radio[name=filter]:checked").val();
		updateList($(this).val(), filter,film_id);
	});

	$('input[type=radio][name=filter]').on('change', function () {
		var type = $("input:radio[name=bucket_type]:checked").val();
		updateList(type, $(this).val(),film_id);
	});
	

     
               
   
            button.addEventListener('click', function() {
                var type = $("input[name='bucket_type']:checked").val();
                var movieId = $("#titles").val();
                var track = $("#titles option:selected").data("track");
                var locale = $("#titles option:selected").data("locale");
                var movieId = $("#titles option:selected").data("movieid");
                var quality = $("input:radio[name=video_quality]:checked").val();
                var drm = $("input:radio[name=drm]:checked").val();
                var path = createPath(type, movieId, track, locale);
                var bucket = new AWS.S3({params: {Bucket: buckets}});
                $.ajax({
                    type: "POST",
                    url: "/titles/media/uploader/getAccountAmazonAssets",
                    data: "filmId="+film_id,
                    dataType: "json",
                    success: function(data) {

                        buckets = data.bucket;
                        region = data.region;//.split('.')[1];
                        SecretKey = data.secret_key;
                        AccessKey = data.access_key;
                        avc_temp = AWS.config.update({
                            accessKeyId: AccessKey,
                            secretAccessKey: SecretKey,
                            "region": region
                        }); 
                        
                        
                        


                        var file = fileChooser.files[0];
                        if (file) {

                             var avc_temp; 


                            var filename = "MASTER."+type+"."+movieId+".mp4";
                            var params = {Key: path+filename, ContentType: file.type, Body: file, queueSize: 10, partSize: 1024 * 1024 * 5};
                            bucket.upload(params, function (err, data) {
                              if(err){}
                                  //createAutoClosingAlert(".msgOnTop",err,4000);
                              else{
                                  $.ajax({
                                      type: "POST",
                                      url: "/titles/media/uploader/mediaUploaderCreateJob",
                                      dataType: "json",
                                      data: 'zencode=1&bucket='+buckets+'&media='+type+'&id='+movieId+'&dt='+moment().format('YYYY-MM')+'&track='+track+'&locale='+locale+'&quality='+quality+'&drm='+drm+'&filmId='+film_id,
                                      success: function(msg) {

                                           // createAutoClosingAlert(".msgOnTop",msg.status,4000);
                                            $("#uploading_progress").css('width', "0%");
                                            $("#file").val('');
                                            $("#titles").val('');

                                            var type = $("input:radio[name=bucket_type]:checked").val();

                                            // $("input:radio[name=filter]").filter('[value=missing]').prop('checked', true);

                                            updateList(type, filter,film_id)
                                           /* $.ajax({
                                                type: "POST",
                                                url: "engine.php",
                                                dataType: "html",
                                                data: 'act=getUploaderJobHistory&film_id='+movieId,
                                                success: function(msg) {
                                                        $("#uploaderHistory").html(msg);
                                                }
                                            });*/

                                      }
                                  });

                              }
                            }).on('httpUploadProgress', function(evt) {
                                console.log(evt);
                                $("#uploading_progress").css('width', evt.loaded*100/evt.total+"%");
                              });
                        } else {
                           // createAutoClosingAlert(".msgOnTop","Please Select File!",4000);
                            return false;
                        }
                        
                        
                        
                        
                        
                    }
                });
                
            }, false);
});

            function createPath(type, movieId, track, locale){
    var path = '';
    if(!movieId){
        //createAutoClosingAlert(".msgOnTop","Please Select Title!",4000);
    }
    var addSymbols = 5 - movieId.length;

    for(var i=0;i<addSymbols;i++){
        movieId = '0'+movieId;
    }

    console.log(movieId);

    path += type+'/';
    path += moment().format('YYYY-MM')+'/';
    path += movieId+'/';
    path += track+'/';
    path += locale+'/';


    return path;

};


            jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
                return this.each(function() {
                    var select = this;
                    var options = [];
                    $(select).find('option').each(function() {
                        options.push({value: $(this).val(), text: $(this).text(), track: $(this).data("track"), locale: $(this).data("locale") });
                    });

                    $(select).data('options', options);
                    $(textbox).bind('change keyup', function() {
                        var options = $(select).empty().data('options');
                        var search = $(this).val().trim();
                        var regex = new RegExp(search,"gi");

                        $.each(options, function(i) {
                            var option = options[i];
                            if(option.text.match(regex) !== null) {
                                $(select).append(
                                    $('<option>', { 'data-locale': option.locale, 'data-track': option.track }).text(option.text).val(option.value)
                                );
                            }
                        });
                        if (selectSingleMatch === true && $(select).children().length === 1) {
                            $(select).children().get(0).selected = true;
                        }
                    });
                });
            };
