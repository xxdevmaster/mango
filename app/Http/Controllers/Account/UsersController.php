<?php

namespace App\Http\Controllers\Account;
use App\User;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;


class UsersController extends Controller
{
    public function listAll(){
   
        $current_menu = 'account_users';

        $user_info = Auth::user();
        $account_info = $user_info->account;

        $account_users = $account_info->users()->with('roles')->get();




        foreach($account_users as $user){
            $user->current = false;
            if ($user_info->id == $user->id)
                $user->current = true;
            if (!empty($user->roles->first()->permissions))
                $user->permissions = json_decode($user->roles->first()->permissions);
            if (!empty($user->userPermissions()->get()))
                $user->permissions = json_decode($user->userPermissions()->get());
        }

        $globalRoles = array();
        $roles = Role::all();
        $roles = $roles->keyBy('slug');

        foreach($roles AS $key => $val){
            $permissions = $val->permissions->keyBy('slug');
            $globalRoles[$key] = $permissions;
        }
        return view('account.users', compact('current_menu', 'account_users', 'globalRoles'));
    }
    public function update(Request $request){
        $R = $request->all();
        $allRoles = Role::all()->keyBy('slug');
        $allPremisttions = Permission::all()->keyBy('slug');
        foreach ($R['users'] AS $k => $user_id){
            $user = User::find($user_id);
            $user->detachAllRoles();
            if ($R['cms_role_'.$user_id] !=  'custom') {
                $userNewRole = $allRoles[$R['cms_role_' . $user_id]];
                $user->attachRole($userNewRole);
            }
            else {
                if ($R['rights'][$user_id]){
                    foreach ($R['rights'][$user_id] AS $k => $permSlug) {
                        $user->attachPermission($allPremisttions[$permSlug]);
                    }
                }

            }
            return json_encode(true);
        }

    }
}
