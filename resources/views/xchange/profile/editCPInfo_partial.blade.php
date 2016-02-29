<form class="form-horizontal" id="editCP" role="form">
    <div class="form-group">
        <label class="col-sm-1 control-label">Company Name:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($CP->title) ? $CP->title : '' }}" class="form-control" name="title">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1 control-label">Name of Contact:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($CP->person) ? $CP->person : '' }}" class="form-control" name="person">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1 control-label">Address:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($CP->address) ? $CP->address : '' }}" class="form-control" name="address">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1 control-label">Phone:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($CP->phone) ? $CP->phone : '' }}" class="form-control" name="phone">
        </div>
    </div>
    <div class="form-group text-left">
        <label class="col-sm-1 control-label">Email:</label>
        <div class="col-sm-8">
            <input type="email" value="{{ isset($CP->email) ? $CP->email : '' }}" class="form-control" name="email">
        </div>
    </div>
    <div class="form-group text-left">
        <label class="col-sm-1 control-label">Website:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($CP->website) ? $CP->website : '' }}" class="form-control" name="website">
        </div>
    </div>
    <div class="form-group text-left">
        <label class="col-sm-1 control-label">About the Company:</label>
        <div class="col-sm-8">
            <textarea name="brief" class="form-control">{{ isset($CP->brief) ? $CP->brief : '' }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10">
            <input type="hidden" name="id" value="{{ isset($CP->id) ? $CP->id : '' }}">
            <div class="pull-left">
                <button class="btn btn-primary btn-md save-CP" type="button">Save</button>
                <button class="btn btn-default btn-md cancel-CP " type="button">Cancel</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(".save-CP").click(function(){
		autoCloseMsgHide();
		$(".loading").show();		
        var editCP = $("#editCP").serialize()
        $.post('/xchange/profile/editCP', editCP, function(data){
            $(".profileCP").html(data);
			$(".loading").hide();
        });
    });

    $(".cancel-CP").click(function(){
		autoCloseMsgHide();
		$(".loading").show();		
        $.post('/xchange/profile/drawCP', function(data){
            $(".profileCP").html(data);
			$(".loading").hide();
        });
    });
});
</script>