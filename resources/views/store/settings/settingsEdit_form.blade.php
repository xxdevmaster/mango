<form class="form-horizontal" id="saveStore" role="form">
    <div class="form-group">
        <div class="col-sm-2  pull-left">
            <img width="32" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($store->favicon) ? $store->favicon : 'def_favicon.png' }}" id="favicon_imgview">
        </div>
        <div class="col-sm-3">
            <div>
                <p>Favicon Image [32x32px, ICO or PNG, 100KB max size]</p>
             </div>
            <div id="uploadifive-favicon_img">
                <input type="file" id="uploadifive-favicon_img" name="logo_img" />
            </div>
        </div>
        <div class="col-sm-7">
            <button class="pull-right btn btn-default btn-sm" type="button" id="removeFavicon">
                <i class="fa fa-close"></i>
            </button>
        </div>
        <input type="hidden" value="{{ !empty($store->favicon) ? $store->favicon : 'def_favicon.png' }}" name="favicon">
    </div>
    <hr>
    <div class="form-group">
        <div class="col-sm-2  pull-left">
            <img width="150" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($store->seo_image) ? $store->seo_image : 'def_seo_image.jpg' }}" id="seo_image_view">
        </div>
        <div class="col-sm-3">
            <div>
                <p>Search Engine Image [1200x1200px, JPG or PNG]</p>
            </div>
            <div id="uploadifive-seo_img">
                <input type="file" id="uploadifive-seo_img" name="logo_img" />
            </div>
        </div>
        <div class="col-sm-7">
            <button class="pull-right btn btn-default btn-sm" type="button" id="removeSeoImage">
                <i class="fa fa-close"></i>
            </button>
        </div>
        <input type="hidden" value="{{ !empty($store->seo_image) ? $store->seo_image : 'def_seo_image.jpg' }}" name="seo_image">
    </div>
    <hr>
    <div class="form-group">
        <label class="col-sm-1">SEO Title:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->seo_title) ? $store->seo_title : '' }}" class="form-control" name="seo_title" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1">SEO Meta Keywords:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->seo_keys) ? $store->seo_keys : '' }}" class="form-control" name="seo_keys" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1">SEO Meta Description:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->seo_description) ? $store->seo_description : '' }}" class="form-control" name="seo_description" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1">Facebook URL:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->fbpage) ? urldecode($store->fbpage) : '' }}" class="form-control" name="fbpage" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1">Twitter URL:</label>
        <div class="col-sm-8">
            <input type="email" value="{{ isset($store->twpage) ? urldecode($store->twpage) : '' }}" class="form-control" name="twpage" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1">Google Analytics Code:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->ga_code) ? $store->ga_code : '' }}" class="form-control" name="ga_code" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1">Terms of Use:</label>
        <div class="col-sm-8">
            <textarea name="terms" class="form-control" placeholder="">{{ isset($store->terms) ? $store->terms : '' }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10">
            <div class="pull-left">
                <button class="btn btn-primary btn-md" id="saveSettings" type="button">Save</button>
                <button class="btn btn-default btn-md" id="cancelSettings" type="button">Cancel</button>
            </div>
        </div>
    </div>
</form>

<script>
    CHUpload("/store/settings/uploadFavicon", "uploadifive-favicon_img", 'Upload Favicon', {"_token":"{{csrf_token()}}"}, function(data){
        var response = JSON.parse(data);
        if(!response.error){
            autoCloseMsg(0, "Logo was uploaded succesfully", 5000);
            $("#favicon_imgview").attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/'+response.message);
            $('input[name="favicon"]').val(response.message);
        }
        else {
            autoCloseMsg(1, response.message, 5000);
        }
    });

    CHUpload("/store/settings/uploadSeoImage", "uploadifive-seo_img", 'Upload Image', {"_token":"{{csrf_token()}}"}, function(data){
        var response = JSON.parse(data);
        if(!response.error){
            autoCloseMsg(0, "Logo was uploaded succesfully", 5000);
            $("#seo_image_view").attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/'+response.message);
            $('input[name="seo_image"]').val(response.message);
        }
        else {
            autoCloseMsg(1, response.message, 5000);
        }
    });

    $(document).ready(function(){
        $("#saveSettings").click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            var saveStore = $("#saveStore").serialize()
            $.post('/store/settings/saveStore', saveStore, function(data){
                $("#settings").html(data);
                $(".loading").hide();
            });
        });

        $("#cancelSettings").click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            $.post('/store/settings/drawStore', function(data){
                $("#settings").html(data);
                $(".loading").hide();
            });
        });

        $('#removeFavicon').click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            $.post('/store/settings/removeFavicon', function(){
                $("#favicon_imgview").attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/def_favicon.png');
                $('input[name="favicon"]').val('');
                $(".loading").hide();
            });
        });

        $('#removeSeoImage').click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            $.post('/store/settings/removeSeoImage', function(){
                $("#seo_image_view").attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/def_seo_image.jpg');
                $('input[name="seo_image"]').val('');
                $(".loading").hide();
            });
        });
    });
</script>