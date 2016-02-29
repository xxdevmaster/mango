@extends('layout')
@section('content')
<h1 class=""h1>Store Profile</h1>
<div class="movie-box">
    <div class="panel panel-default">
        <div class=" panel-body ">
			Please enter information about your platform. It is important to keep this information up to date as other members of the Cinehost ecosystem have access to your platform profile.
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="h2">Store Logo</h2>
        </div>
        <div class=" panel-body ">
            <table class="table">
                <tr>
                    <td>
                        <a href="#" class="thumbnail">
                            <img width="130" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($store->logo) ? $store->logo : 'nologo.png' }}" id="logo_imgview" >
                        </a>
                    </td>
                    <td>
                        <div class="form-group">Logo Image [350x350px, JPG or PNG, 500KB max size]</div>
                        <div class="form-group" id="logo_text"></div>
                        <div class="form-group" id="uploadifive-logo_img">
                            <input type="file" id="uploadifive-logo_img" name="logo_img" />
                        </div>
                    </td>
					<td>
						<button class="pull-right btn btn-default btn-sm" type="button" id="removeStoreLogo" data-locale="en">
							<i class="fa fa-close"></i> 
						</button>
					</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
             <h3 class="h3">Store Information</h3>
        </div>
        <div class="panel-body" id="profileStore">
            @include('store.profile.profileInfo_partial')
        </div>
    </div>
</div>
<script>

$(document).ready(function(){
    CHUpload("/store/profile/uploadLogo", "uploadifive-logo_img", 'Upload Image', {"_token":"{{csrf_token()}}"}, function(data){
        var response = JSON.parse(data);
        if(!response.error){
            autoCloseMsg(0, "Logo was uploaded succesfully", 5000);
            $("#logo_imgview").attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/'+response.message);
        }
        else {
            autoCloseMsg(1, response.message, 5000);
        }
    });	
	
	$("#removeStoreLogo").click(function(){
		autoCloseMsgHide();
		$(".loading").show();
		$.post('/store/profile/removeLogoCP', function(){
			$("#logo_imgview").attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/nologo.png');
			$(".loading").hide();
		});
	});
});
</script>
@stop