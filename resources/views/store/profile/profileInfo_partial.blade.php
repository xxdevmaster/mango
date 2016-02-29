<div class="table-responsive">
    <table class="table video-info-list store_profile">
        <tr>
            <td>Store Name</td>
            <td>{{ isset($store->title) ? $store->title : '' }}</td>
        </tr>
        <tr>
            <td>Name of Contact</td>
            <td>{{ isset($store->person) ? $store->person : '' }}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td>{{ isset($store->address) ? $store->address : '' }}</td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>{{ isset($store->phone) ? $store->phone : '' }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>
                <a href="mailto:{{ isset($store->email) ? $store->email : '' }}" class="table-link val">{{ isset($store->email) ? $store->email : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>Website</td>
            <td>
                <a href="{{ isset($store->website) ? $store->website : '' }}" target="blank" class="table-link val">{{ isset($store->website) ? $store->website : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>About the Store</td>
            <td>{{ isset($store->brief) ? $store->brief : '' }}</td>
        </tr>
        <tr>
            <td class="w7"></td>
            <td class="w7"></td>
        </tr>
    </table>
</div>
<div class="pull-left">
    <button class="btn btn-primary btn-md editStore" type="button">Edit</button>
</div>

<script>
$(document).ready(function(){
    $(".editStore").click(function(){
		autoCloseMsgHide();
		$(".loading").show();
        $.post('/store/profile/drawEditstore', function(data){
            $("#profileStore").html(data);
			$(".loading").hide();
        });
    });
});
</script>