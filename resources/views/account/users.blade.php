@extends('layout')


@section('content')
@include('account.partials.NewUserForm')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Account Users & Rights</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="panel-title">
							<a data-target="#inviteNewUserModal" data-toggle="modal" type="button" href="#" class="btn btn-primary">
							+ Invite New User</a>
                        </div><hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="" id="users">
							@include('account.partials.userslist')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   $(document).ready(function(){
	$(".save-accUsers").click(function(){
		$(".save-accProfiles").text("Saving ...");
		$.when(
				$.ajax({
					type: "POST",
					url: "/account/users/update",
					data: $('#accUsers').serialize()
				})
		).done(function(data){
					console.log(data);
		}).fail(function(){

				});

	});
	
	$(document).on("click", ".destroy", function(e) {
		var id = $(this).data('id');
		$('#autoCloseMsg').hide();
		bootbox.confirm("Are you sure?", function(result) {
			if(result)
			{
				destroy('{{route('account/users/destroy')}}','POST','User removed!',{_token:'5sdUYcWkyVg4Pj3LJrK6Y6jXMhyAadSFt2VNWpL4',id:id});
			}
		});
	});	
	
	$(document).on("click", ".reSendInvitation", function() {
		var id = $(this).data("id");
		destroy('{{route('account/users/reSendInvitation')}}','POST','Invitation has been sent successfully!',{id:id});
	});
	
});
</script>
@stop