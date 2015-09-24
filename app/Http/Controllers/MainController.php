<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    //
    public function dashboard(){
        $current_menu = 'dashboard';
        return view('dashboard', compact('current_menu'));
    }
}
