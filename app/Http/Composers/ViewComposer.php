<?php namespace App\Http\Composers;

use Illuminate\Support\Facades\Auth;
use App\Libraries\CHpermissions\CHpermissions;

class ViewComposer {

    public function header($view){
        $view->with('user', Auth::user());
    }

    public function nav($view){
        $CHpermissions = new CHpermissions();
        $user =  Auth::user();

        $view->with(compact('user','CHpermissions'));
    }



} 