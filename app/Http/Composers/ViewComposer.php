<?php namespace App\Http\Composers;

use Illuminate\Support\Facades\Auth;

class ViewComposer {

    public function header($view){
        $view->with('user', Auth::user());
    }

    public function nav($view){
//        $user_info = Auth::user();
/*

            $permissions = $user_info->roles->first()->permissions;*/


        $view->with('user', Auth::user());
    }



} 