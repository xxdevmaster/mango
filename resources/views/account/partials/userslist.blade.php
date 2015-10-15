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
             <th><span class="vertical-text"> Metadata Management</span></th>
             <th><span class="vertical-text"> Rights Management</span></th>
             <th><span class="vertical-text"> Media Management</span></th>
             <th><span class="vertical-text"> Interface Management</span></th>
             <th><span class="vertical-text"> Subscriptions</span></th>
             <th><span class="vertical-text"> Channels Management</span></th>
             <th><span class="vertical-text"> Users Management</span></th>
             <th><span class="vertical-text"> Live Publishing</span></th>
             <th><span class="vertical-text"> Sales &amp; Reporting</span></th>
             <th><span class="vertical-text"> Account Settings</span></th>
             <th></th>
         </tr>
    </thead>
    <tbody>
        @foreach($account_users as $user)

            @foreach($globalRoles as $role)
              <?php
                 //$userRolesCheckboxesHtml = '<td><input type="checkbox"  name="rights['.$user->id.' ][]" value="metadata" class="perms"></td>';
              ?>
            @endforeach



            @if($user->is('owner'))
                <tr>
                  <td class="userName"><span class="name">{{ $user->person  }}</span></td>
                  <td class="cmsRole">&nbsp;Owner</td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>

                  <td> </td>
                </tr>
             @elseif($user->current)
              <tr>
                  <td class="userName"><span class="name">{{ $user->email  }}</span></td>
                  <td class="cmsRole">&nbsp;Administrator</td>

                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>
                  <td ><input type="checkbox" checked="checked" disabled="disabled" class="perms"></td>

                  <td></td>
                </tr>

            @else
                <tr rel="{{ $user->id }}">
                    <td class="userName"><span class="name">{{ $user->title  }} </span></td>
                    <td class="cmsRole">
                        <select name="cms_role_{{ $user->id }}" id="cms_role_{{ $user->id }}" class="userRoleSelect ">
                            <option value="administrator">Administrator</option>
                            <option value="manager">Manager</option>
                            <option value="accountant">Accountant</option>
                            <option value="custom">Custom</option>
                        </select>
                    </td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="metadata" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="rights" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="media" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="interface" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="subscriptions" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="channels" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="users" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="publishing" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="sales" class="perms"></td>
                    <td><input type="checkbox" checked="checked" name="rights[{{ $user->id }}][]" value="account.settings" class="perms"></td>
                    <td>
                        <input type="hidden" name="users[]" value="{{ $user->id }}">
                        <div class="dropdown pull-right">
                            <button class="btn btn-default dropdown-toggle" type="button" id="uset_{{ $user->id }}" data-toggle="dropdown" aria-expanded="true">
                            &nbsp;<span aria-hidden="true" class="glyphicon glyphicon-cog"></span>
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="uset_{{ $user->id }}">
                                <li role="presentation"><a role="menuitem" tabindex="-1" class="reSendInvitation cp">Re-send Invitation</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" class="removeUser cp">Remove User</a></li>
                            </ul>
                         </div>
                    </td>
                </tr>


            @endif
        @endforeach



    </tbody>
</table>
<button type="button" class="btn-success btn save-accUsers pull-right">Save Changes</button>
</form>
<script>

    var roles = <?php echo json_encode($globalRoles); ?>;
    var tmp;

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


        $(".userRoleSelect").on("change", function() {
            var role = '';
            role = $(this).val();
            var user_id = $(this).parent().parent().attr("rel");
            $('[name="rights['+user_id+'][]"]').each( function() {
                if (role !='' && role !='custom'){
                    tmp =  $(this).val();
                    if (roles[role][tmp] != undefined)
                        $(this).prop( "checked", true );
                    else
                        $(this).prop( "checked", false );
                }
                else
                    $(this).prop( "checked", true );
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
                var rolePerms= Object.keys(v);
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