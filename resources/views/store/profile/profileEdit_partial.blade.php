<form class="form-horizontal" id="editStore" role="form">
    <div class="form-group">
        <label class="col-sm-1 control-label">Store Name:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->title) ? $store->title : '' }}" class="form-control" name="title">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1 control-label">Name of Contact:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->person) ? $store->person : '' }}" class="form-control" name="person">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1 control-label">Address:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->address) ? $store->address : '' }}" class="form-control" name="address">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1 control-label">Phone:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->phone) ? $store->phone : '' }}" class="form-control" name="phone">
        </div>
    </div>
    <div class="form-group text-left">
        <label class="col-sm-1 control-label">Email:</label>
        <div class="col-sm-8">
            <input type="email" value="{{ isset($store->email) ? $store->email : '' }}" class="form-control" name="email">
        </div>
    </div>
    <div class="form-group text-left">
        <label class="col-sm-1 control-label">Website:</label>
        <div class="col-sm-8">
            <input type="text" value="{{ isset($store->website) ? $store->website : '' }}" class="form-control" name="website">
        </div>
    </div>
    <div class="form-group text-left">
        <label class="col-sm-1 control-label">About the Store:</label>
        <div class="col-sm-8">
            <textarea name="brief" class="form-control">{{ isset($store->brief) ? $store->brief : '' }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10">
            <input type="hidden" name="id" value="{{ isset($store->id) ? $store->id : '' }}">
            <div class="pull-left">
                <button class="btn btn-primary btn-md save-store" type="button">Save</button>
                <button class="btn btn-default btn-md cancel-store " type="button">Cancel</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
        $(".save-store").click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            var editStore = $("#editStore").serialize()
            $.post('/store/profile/editStore', editStore, function(data){
                $("#profileStore").html(data);
                $(".loading").hide();
            });
        });

        $(".cancel-store").click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            $.post('/store/profile/drawStore', function(data){
                $("#profileStore").html(data);
                $(".loading").hide();
            });
        });
    });
</script>