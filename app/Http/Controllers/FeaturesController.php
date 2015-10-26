<?php

namespace App\Http\Controllers;

use App\Features;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FeaturesController extends Controller
{
    //
    public function features(){
        $current_menu = 'features_manager';
        $features = Features::all();



        return view('admin.features', compact('current_menu', 'features'));
    }
}
