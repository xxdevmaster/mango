<?php

namespace App\Http\Controllers\Account;

use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    //
    public function listAll(){
        $current_menu = 'account_users';

        $user_info = Auth::user();

        $account_info = $user_info->account;

        $account_users = $account_info->users()->with('roles')->get();

        foreach($account_users as $user){
            $user->permissions = $user->roles->first()->permissions;
        }


        /*
        $ownerRole = Role::create([
            'name' => 'Owner',
            'slug' => 'owner'
        ]);

        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'administrator'
        ]);

        $managerRole = Role::create([
            'name' => 'Manager',
            'slug' => 'manager'
        ]);

        $accountantRole = Role::create([
            'name' => 'Accountant',
            'slug' => 'accountant'
        ]);


        //Permissions


        $metadataPermission = Permission::create([
            'name' => 'Metadata Management',
            'slug' => 'metadata',
        ]);

        $ownerRole->attachPermission($metadataPermission);
        $adminRole->attachPermission($metadataPermission);
        $managerRole->attachPermission($metadataPermission);

        $rightsPermission = Permission::create([
            'name' => 'Rights Management',
            'slug' => 'rights',
        ]);
        $ownerRole->attachPermission($rightsPermission);
        $adminRole->attachPermission($rightsPermission);

        $mediaPermission = Permission::create([
            'name' => 'Media Management',
            'slug' => 'media',
        ]);
        $ownerRole->attachPermission($mediaPermission);
        $adminRole->attachPermission($mediaPermission);
        $managerRole->attachPermission($mediaPermission);

        $interfacePermission = Permission::create([
            'name' => 'Interface Management',
            'slug' => 'interface',
        ]);
        $ownerRole->attachPermission($interfacePermission);
        $adminRole->attachPermission($interfacePermission);

        $subscriptionsPermission = Permission::create([
            'name' => 'Subscriptions',
            'slug' => 'subscriptions',
        ]);
        $ownerRole->attachPermission($subscriptionsPermission);
        $adminRole->attachPermission($subscriptionsPermission);
        $managerRole->attachPermission($subscriptionsPermission);

        $channelsPermission = Permission::create([
            'name' => 'Channels Management',
            'slug' => 'channels',
        ]);
        $ownerRole->attachPermission($channelsPermission);
        $adminRole->attachPermission($channelsPermission);
        $managerRole->attachPermission($channelsPermission);

        $usersPermission = Permission::create([
            'name' => 'Users Management',
            'slug' => 'users',
        ]);
        $ownerRole->attachPermission($usersPermission);
        $adminRole->attachPermission($usersPermission);
        $managerRole->attachPermission($usersPermission);

        $livePublishingPermission = Permission::create([
            'name' => 'Live Publishing',
            'slug' => 'publishing',
        ]);
        $ownerRole->attachPermission($livePublishingPermission);
        $adminRole->attachPermission($livePublishingPermission);
        $accountantRole->attachPermission($livePublishingPermission);

        $salesPermission = Permission::create([
            'name' => 'Sales & Reporting',
            'slug' => 'sales',
        ]);
        $ownerRole->attachPermission($salesPermission);
        $adminRole->attachPermission($salesPermission);
        $accountantRole->attachPermission($salesPermission);

        $accountSettingsPermission = Permission::create([
            'name' => 'Account Settings',
            'slug' => 'account.settings',
        ]);
        $ownerRole->attachPermission($accountSettingsPermission);
        $adminRole->attachPermission($accountSettingsPermission);






        $user_info->attachRole($ownerRole);

        $account_users[1]->attachRole($managerRole);
*/

        $roles = Role::all();

        return view('account.users', compact('current_menu', 'account_users', 'roles'));
    }
}
