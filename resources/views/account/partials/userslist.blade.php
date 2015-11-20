<?php
    $userRolesCheckboxesHtml = '';
?>
<form id="accUsers">
    {{csrf_field()}}
<table class="table accountProfiles">
    <thead>
        <tr>
             <th>User Name</th>
             <th>Role</th>
			 @foreach($permissionsAll as $permissionsSlug => $permissionsInfo)
				<th><span class="vertical-text"> {{$permissionsInfo['name']}}</span></th>
			 @endforeach
             <th></th>
         </tr>
    </thead>
    <tbody>
        @foreach($account_users as $user)
            <?php
                $userRolesCheckboxesHtml = '';
                $userRoleSelectBoxHtml = '';
                    foreach ($globalRoles['owner']['permissions'] AS $k => $r){

                        $disabled = ($user->current)?'disabled="disabled"':'name="rights['.$user->id.'][]" value="'.$r->slug.'" class="perms"';
                        if ($user->roleSlug == 'owner'){
                            $userRolesCheckboxesHtml .= '<td ><input type="checkbox" checked="checked" disabled="disabled" ></td>';
                        }
                        else {
                            $userRolesCheckboxesHtml .= '<td ><input type="checkbox" '.(in_array($r->slug,$user->permissions)?'checked="checked"':'').'  '.$disabled.' ></td>';

                        }
                    }
                    if($user->roleSlug == 'owner')
                        $userRoleSelectBoxHtml = '&nbsp;Owner';
                    else{
                        $userRoleSelectBoxHtml = '<select name="cms_role_'.$user->id.'" id="cms_role_'.$user->id.'" class="userRoleSelect">';
                        foreach($globalRoles AS $roleSlug => $roleInfo){
                            if($roleSlug != 'owner')
                                $userRoleSelectBoxHtml .= '<option value="'.$roleSlug.'" '.($user->roleSlug == $roleSlug?'selected="selected"':'').'>'.$roleInfo['info']->name.'</option>';
                        }
                        $userRoleSelectBoxHtml .= '</select>';
                    }
            ?>

                <tr rel="{{ $user->id }}">
                    <td class="userName">
					
						<span class="name">{{ $user->title  }} </span>				
						@if($user->roleSlug != 'owner')
							@if( strtotime(date('Y-m-d')) > strtotime(date("Y-m-d",strtotime("+1 month", strtotime(date($user->invite_dt))))) )
								<?php $class = 'text-danger';?> 
							@else
								<?php $class = 'text-success';?> 	
							@endif						
							<p class="{{$class}}">Invitation Expired on  <?=date("d/m/Y",strtotime("+1 month", strtotime(date($user->invite_dt))))?></p>
						@endif
					</td>
                    <td class="cmsRole"><?php echo $userRoleSelectBoxHtml;?></td>
                    <?php echo $userRolesCheckboxesHtml;?>
                    <td>
                        @if($user->roleSlug != 'owner' && !$user->current)
                            <input type="hidden" name="users[]" value="{{ $user->id }}">
                            <div class="dropdown pull-right">
                                <button class="btn btn-default dropdown-toggle" type="button" id="uset_{{ $user->id }}" data-toggle="dropdown" aria-expanded="true">
                                &nbsp;<span aria-hidden="true" class="glyphicon glyphicon-cog"></span>
                                <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="uset_{{ $user->id }}">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" class="reSendInvitation cp" data-id="{{$user->id}}">Re-send Invitation</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" class="removeUser cp destroy" data-title="{{$user->title}}" data-id="{{$user->id}}" data-bb="confirm">Remove User</a></li>
                                </ul>
                             </div>
                        @endif
                    </td>
                </tr>
        @endforeach



    </tbody>
</table>
<button type="button" class="btn-success btn save-accUsers pull-right">Save Changes</button>
</form>
<script>

    var roles = <?php echo json_encode($globalRoles); ?>;
    var tmp;

    $(document).ready(function(){


        $(".userRoleSelect").on("change", function() {
            var role = '';
            role = $(this).val();
            var user_id = $(this).parent().parent().attr("rel");
            $('[name="rights['+user_id+'][]"]').each( function() {
                if (role !='' && role !='custom'){
                    tmp =  $(this).val();
                    if (roles[role]['permissions'][tmp] != undefined)
                        $(this).prop( "checked", true );
                    else
                        $(this).prop( "checked", false );
                }
                else
                    $(this).prop( "checked", false );
            });
        });


        $(".perms").on("change", function() {

            var user_id = $(this).parent().parent().attr("rel");
            var searchIDs = $("[name=\'rights["+user_id+"][]\']:checkbox:checked").map(function(){
                return $(this).val();
            }).get();
            var f1 = 0;
            var f2 = 0;
            var isR = false;
            jQuery.each(roles, function(k,v) {
                var rolePerms= Object.keys(v['permissions']);
                f1 = rolePerms.subtract( searchIDs ).length;
                f2 = searchIDs.subtract( rolePerms ).length;
                if (f1==0 && f2==0){
                    isR = true;
                    jQuery("#cms_role_"+user_id).val(k);
                }
            });
            if (!isR)
                jQuery("#cms_role_"+user_id).val('custom');
        });
		

      
		
		
    });
	

</script>