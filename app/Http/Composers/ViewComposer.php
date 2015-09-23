<?php namespace App\Http\Composers;

use Illuminate\Support\Facades\Auth;

class ViewComposer {

    public function compose($view){
        $view->with('user', Auth::user());
    }



} 