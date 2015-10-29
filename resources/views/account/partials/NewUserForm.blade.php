<div class="modal fade in" id="inviteNewUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close clear-form" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myModalLabel">Invite New User</h4>
			</div>
			<div class="modal-body">
				<div class="popmsq msgOnTop"></div>
				<form action="{{url()}}/account/users/create" method="post" id="inviteNewUserForm" name="inviteNewUserForm">
					{{csrf_field()}}
					<table class="table accountProfiles">
						<thead>
							<tr>
								<td colspan="8">User E-mail</td>
								<td></td>
								<td>Role</td>
							</tr>
						</thead>
						<tbody>
							<tr rel="0">
								<td colspan="8">
									<div class="form-group">
										<input type="text" name="email" class="form-control" id="email">
									</div>
								</td>
								<td></td>
								<td width="150px">
									<select name="cms_role_0" id="cms_role_0" class="cmsRoleSelect userRoleSelect form-control">									
									@foreach($globalRoles AS $roleSlug => $roleInfo)
										@if ($roleInfo['info']->name != 'Owner' && $roleInfo['info']->name != 'Custom')
											<option value="{{$roleSlug}}">{{$roleInfo['info']->name}}</option>
										@endif
										@if($roleInfo['info']->name === 'Custom')
											<option value="{{$roleSlug}}" selected="selected">{{$roleInfo['info']->name}}</option>
										@endif
									@endforeach
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="table accountProfiles">						 
						 <tbody>
							@foreach($permissionsAll as $permissionsSlug => $permissionsInfo)
								<tr rel="0">
									 <td style="padding:4px">
										 <span>{{$permissionsInfo['name']}}</span> 
									 </td>
									 <td style="text-align:left;padding:4px">
										<input type="checkbox" name="rights[0][]" value="{{$permissionsSlug}}" class="perms">  
									</td>
								</tr>
								<tr></tr>
							@endforeach
						</tbody>
					</table>
					<input type="hidden" name="accounts_id" value="{{$userId}}">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">Close</button>
				<button type="submit" class="btn btn-primary btn-sm">Invite</button>
			</div>
			</form>
		    <script>
			$(document).ready(function(){				
				$('#inviteNewUserForm').submit(function(event){
					event.preventDefault();
					var form_data = $('#inviteNewUserForm').serialize();
					$.when(
						$.ajax({
							type: "POST",
							url:'{{route('account/users/create')}}',
							data: form_data,
						})
					).done(function(data){
						autoCloseMsg(data.error,data.msg,7000);
						if(!data.error)
						{
							$('#inviteNewUserModal').modal('toggle');
							getTemplate('{{route('account/users/getTemplate')}}','POST','#users');
							$('#email').val('');
							$('.cmsRoleSelect option').each(function(){
								if($(this).val() == 'custom')
								{
									$(this).attr('selected','selected');
								}
							});
						}
					}).fail(function(){
						autoCloseMsg(1,'Bad Request',7000);
					});
				});
			});	
		    </script>
         </div>
	</div>
</div>