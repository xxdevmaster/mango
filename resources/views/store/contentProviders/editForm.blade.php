<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close clear-form" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Edit Content Provider</h4>
        </div>
        <div class="modal-body">
            <form id="contentProviderEditForm" autocomplete="off">
                <div class="form-group col-md-6">
                    <img width="173" id="logo_imgview"  title="" alt="" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($company->logo) ? $company->logo : "nologo.png" }}" id="cp_logo_{{ isset($company->id) ? $company->id : "" }}"><br><br>
                </div>
                <div class="form-group col-md-6" id="uploadifive-logo_img">
                    <input type="file" id="uploadifive-logo_img" name="logo_img" />
                </div>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label class="ff-label">Title</label>
                    <input type="text" name="title" class="form-control" id="title" value="{{ isset($company->title) ? $company->title : '' }}" placeholder="">
                </div>
                <div class="form-group">
                    <label class="ff-label">Website</label>
                    <input type="text" name="website" class="form-control" id="website" value="{{ isset($company->website) ? $company->website : '' }}" placeholder="">
                </div>
                <div class="form-group">
                    <label class="ff-label">About the Company</label>
                    <textarea class="form-control" name="brief" placeholder="">{{ isset($company->brief) ? $company->brief : '' }}</textarea>
                </div>
                <input type="hidden" name="contentProviderID" value="{{ isset($company->id) ? $company->id : ''}}">
                <input type="hidden" name="logo" value="{{ isset($company->logo) ? $company->logo : "nologo.png" }}">
            </form>
            <div class="accountOwnerinformation">

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm clear-form" data-dismiss="modal" aria-hidden="true">Close</button>
            <button type="button" class="btn btn-primary btn-sm" id="saveContentProviderInfo">Save</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        CHUpload("/store/contentProviders/uploadLogo", "uploadifive-logo_img", 'Upload Image', {"_token":"{{csrf_token()}}"}, function(data){
            var response = JSON.parse(data);
            if(!response.error){
                $("#logo_imgview").attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/'+response.message);
                $('input[name="logo"]').val(response.message);
            }
            else {
                autoCloseMsg(1, response.message, 5000);
            }
        });
        $("#saveContentProviderInfo").click(function(){
            var contentProviderEditForm = $('#contentProviderEditForm').serialize();
            $('#editContentProvider').modal('hide');
            $('.loading').show();
            $.post('/store/contentProviders/editContentProviderInfo', contentProviderEditForm, function(data){
                $("#container").html(data);
                $('.loading').hide();
            });
        });
    });
</script>