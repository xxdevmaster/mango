<table class="table accountProfiles">
    <thead>
        <tr>
             <th>User Name</th>
             <th>Role</th>
             <th>Metadata Management</th>
             <th>Rights Management</th>
             <th>Media Management</th>
             <th>Interface Management</th>
             <th>Subscriptions</th>
             <th>Channels Management</th>
             <th>Users Management</th>
             <th>Live Publishing</th>
             <th>Sales &amp; Reporting</th>
             <th></th>
         </tr>
    </thead>
    <tbody>
        @foreach($account_users as $user)
            @if($user->is('owner'))
                <tr>
                  <td class="userName"><span class="name"> Quincy Newell</span></td>
                  <td class="cmsRole">&nbsp;Owner</td>
                  <td colspan="9"></td>
                  <td> </td>
                </tr>
            @else
                <tr rel="436">
                    <input type="hidden" name="users[]" value="436">
                    <td class="userName"><span class="name">{{ $user->title  }}</span></td>
                    <td class="cmsRole"><select name="cms_role_436" id="cms_role_436" class="cmsRoleSelect "><option value="1" selected="selected">Administrator</option><option value="2">Manager</option><option value="3">Accountant</option><option value="4">Custom</option></select></td>
                    <td><input type="checkbox" checked="checked" name="rights[436][]" value="metadata" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="rights" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="media" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="interface" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="subscriptions" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="channels" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="users" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="livepublishing" class="perms"></td> <td><input type="checkbox" checked="checked" name="rights[436][]" value="sales" class="perms"></td>
                    <td>
                         <div class="dropdown pull-right">
                             <button class="btn btn-default dropdown-toggle" type="button" id="uset_436" data-toggle="dropdown" aria-expanded="true">
                             &nbsp;<span aria-hidden="true" class="glyphicon glyphicon-cog"></span>
                               <span class="caret"></span>
                             </button>
                             <ul class="dropdown-menu" role="menu" aria-labelledby="uset_436">
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