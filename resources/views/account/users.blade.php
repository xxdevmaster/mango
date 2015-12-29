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
	$(document).on("click", ".save-accUsers", function() {
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
	
	$(document).on("click", ".destroy", function() {	
		autoCloseMsgHide();//message closing		
		var id = $(this).data('id'); //user id	
		var title = $(this).data('title'); //user title
		var confirmText = 'Do you really want to delete '+title+'?';
		bootbox.confirm(confirmText, function(result) {
			if(result)
			{
				$('.loading').show(); //show loading
				$.post('{{route('account/users/destroy')}}',{id:id}, function(data){
					if(data) {
						getTemplate('{{route('account/users/getTemplate')}}','POST','#users');
						$('.loading').hide();
						autoCloseMsg(0, 'User removed!', 5000);
					}
				});
			}
		});
	});	
	
	$(document).on("click", ".reSendInvitation", function() {
		autoCloseMsgHide();//message closing
		$('.loading').show(); //show loading
		var id = $(this).data("id"); //user id
		$.post('{{route('account/users/reSendInvitation')}}',{id:id}, function(){
			$('.loading').hide();
			autoCloseMsg(0, 'Invitation has been sent successfully!', 5000);			
		});
	});	
});
</script>
@stop