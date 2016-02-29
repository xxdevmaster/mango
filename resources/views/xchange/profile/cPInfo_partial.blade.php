<div class="table-responsive">
    <table class="table video-info-list company_profile">
        <tr>
            <td>Company Name</td>
            <td>{{ isset($CP->title) ? $CP->title : '' }}</td>
        </tr>
        <tr>
            <td>Name of Contact</td>
            <td>{{ isset($CP->person) ? $CP->person : '' }}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td>{{ isset($CP->address) ? $CP->address : '' }}</td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>{{ isset($CP->phone) ? $CP->phone : '' }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>
                <a href="mailto:{{ isset($CP->email) ? $CP->email : '' }}" class="table-link val">{{ isset($CP->email) ? $CP->email : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>Website</td>
            <td>
                <a href="{{ isset($CP->website) ? $CP->website : '' }}" target="blank" class="table-link val">{{ isset($CP->website) ? $CP->website : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>About the Company</td>
            <td>{{ isset($CP->brief) ? $CP->brief : '' }}</td>
        </tr>
        <tr>
            <td class="w7"></td>
            <td class="w7"></td>
        </tr>
    </table>
</div>
<div class="pull-left">
    <button class="btn btn-primary btn-md edit-CP" type="button">Edit</button>
</div>

<script>
$(document).ready(function(){
    $(".edit-CP").click(function(){
		autoCloseMsgHide();
		$(".loading").show();
        $.post('/xchange/profile/drawEditCP', function(data){
            $(".profileCP").html(data);
			$(".loading").hide();
        });
    });
});
</script>