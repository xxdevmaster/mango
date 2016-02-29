<?php namespace App\Http\Composers;

use Illuminate\Support\Facades\Auth;
use App\Libraries\CHpermissons\CHpermissons;

class ViewComposer {

    public function header($view){
        $view->with('user', Auth::user());
    }

    public function nav($view){
        $CHpermissons = new CHpermissons();
        $user =  Auth::user();

        $view->with(compact('user','CHpermissons'));
    }



} 