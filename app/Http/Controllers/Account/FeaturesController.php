<?php

namespace App\Http\Controllers\Account;

use App\Features;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Debug\Dumper;
use Illuminate\Support\Facades\Auth;

class FeaturesController extends Controller
{
    //
    public function features(){
        $current_menu = 'features';

        $user_info = Auth::user();

        $account_info = $user_info->account;

        $account_features = $account_info->features()->with('accFeatures')->get();
        //(new Dumper)->dump($account_features->toArray());

        return view('account.features', compact('account_features', 'current_menu'));
    }
}
