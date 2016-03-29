<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Subscriptions;

class SubscriptionsController extends Controller
{
    private $request;

    private $authUser;

    private $accountID;

    private $storeID;

    private $companyID;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->accountID = $this->authUser->account->id;
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function subscriptionsShow()
    {
        $subscriptions = $this->getSubscriptions();
        return view('store.subscriptions.subscriptions', compact('subscriptions'));
    }


    /**
     * Get all subscriptions.
     * @return collection
    */
    private function getSubscriptions()
    {
        return Subscriptions::where('channels_id', $this->storeID)->where('deleted', 0)->get()->keyBy('id');
    }
}
