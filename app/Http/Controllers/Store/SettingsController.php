<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    private $request;

    private $authUser;

    private $storeID;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->storeID = $this->authUser->account->platforms_id;
    }

    public function settingsShow()
    {
        return view('store.settings.settings');
    }

    private function getStore()
    {
        return $this->authUser->account->store;
    }
}
