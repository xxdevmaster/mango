<?php

namespace App\Http\Controllers\Store;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UrlSetupController extends Controller
{
    /**
     * Url Setup Show.
     *
     * @return \Illuminate\Http\Response
     */
    public function urlSetupShow()
    {
        return view('store.urlSetup.urlSetup');
    }

}
