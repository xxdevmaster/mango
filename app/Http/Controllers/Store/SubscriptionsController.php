<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\CHhelper\CHhelper;
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
        $currencies = CHhelper::getCurrencies();
        $euroPlans = CHhelper::getEuroPlans();

        $subscriptions = $this->getSubscriptions();
        return view('store.subscriptions.subscriptions', compact('subscriptions', 'currencies', 'euroPlans'));
    }


    /**
     * Get all subscriptions.
     * @return collection
    */
    private function getSubscriptions()
    {
        return Subscriptions::where('channels_id', $this->storeID)->where('deleted', 0)->get()->keyBy('id');
    }

    /**
     *@POST("/store/subscriptions/createSubscription")
     * @Middleware("auth")
    */
    public function createSubscription()
    {
        $currencies = CHhelper::getCurrencies();
        $euroAmounts = CHhelper::getEuroAmount();

        $currency = $this->request->Input('currency');
        //$euroAmounts = $euroAmounts->search($this->request->Input('plan_id'), true);

        $title = !empty($this->request->Input('title')) ? CHhelper::filterInput($this->request->Input('title')) : false;		
		$currency = !empty($this->request->Input('currency')) ? CHhelper::filterInput($this->request->Input('currency')) : false;
        $planID = !empty($this->request->Input('plan_id')) ? CHhelper::filterInput($this->request->Input('plan_id')) : false;
		$trialAmount = !empty($this->request->Input('trial_amount')) ? CHhelper::filterInputInt($this->request->Input('trial_amount')) : false;
		$trialFrequency = !empty($this->request->Input('trial_frequency')) ? CHhelper::filterInputInt($this->request->Input('trial_frequency')) : false;
		$trialPeriod = !empty($this->request->Input('trial_period')) ? CHhelper::filterInputInt($this->request->Input('trial_period')) : false;
		$regularAmount = !empty($this->request->Input('regular_amount')) ? CHhelper::filterInputInt($this->request->Input('regular_amount')) : false;
		$regularFrequency = !empty($this->request->Input('regular_frequency')) ? CHhelper::filterInputInt($this->request->Input('regular_frequency')) : false;
		$regularPeriod = !empty($this->request->Input('regular_period')) ? CHhelper::filterInputInt($this->request->Input('regular_period')) : false;

        if($currency == 'EUR')
        {
            $trialAmount = 0;
            $trialFrequency = 14;
            $regularFrequency = 1;
            $regularPeriod = 'month';

        }
		
        $subscrptionNewID = Subscriptions::create([
            'title' => $title ,
            'regular_amount' => $regularAmount ,
            'currency' => $currency ,
            'regular_period' => $regularPeriod ,
            'regular_frequency' => $regularFrequency ,
            'trial_amount' => $trialAmount ,
            'trial_period' => $trialPeriod ,
            'trial_frequency' => $trialFrequency ,
            'channels_id' => $this->storeID ,
            'plan_id' => $planID
        ])->id;

        if($subscrptionNewID)
        {
            $subscriptions = $this->getSubscriptions();
            return [
                'error' => '0',
                'message' => 'New subscription created successfully!',
                'html' => view('store.subscriptions.list', compact('subscriptions'))->render()
            ];
        }else
            return [
                'error' => '1',
                'message' => 'New subscription doesn`t created!'
            ];
    }

    /**
     *@POST("/store/subscriptions/removeSubscription")
     * @Middleware("auth")
     */
    public function removeSubscription()
    {
        $subscriptionID = !empty($this->request->Input('subscriptionID')) && is_numeric($this->request->Input('subscriptionID')) ? CHhelper::filterInputInt($this->request->Input('subscriptionID')) : false;

        if($subscriptionID)
        {
            Subscriptions::where('channels_id', $this->storeID)->where('deleted', 0)->where('id', $subscriptionID)->update(['deleted' => 1]);
            $subscriptions = $this->getSubscriptions();
            return [
                'error' => '0',
                'message' => 'Subscription destroyed successfully!',
                'html' => view('store.subscriptions.list', compact('subscriptions'))->render()
            ];
        }else
            return [
                'error' => '1',
                'message' => 'New subscription doesn`t created!'
            ];
    }

    /**
     *@POST("/store/subscriptions/getSubscriptionEditor")
     * @Middleware("auth")
     */
    public function getSubscriptionEditor()
    {
        $subscriptionID = !empty($this->request->Input('subscriptionID')) && is_numeric($this->request->Input('subscriptionID')) ? CHhelper::filterInputInt($this->request->Input('subscriptionID')) : false;

        if($subscriptionID)
        {
            $currencies = CHhelper::getCurrencies();
            $euroPlans = CHhelper::getEuroPlans();
            $subscriptions = Subscriptions::where('channels_id', $this->storeID)->where('deleted', 0)->where('id', $subscriptionID)->get()->first();
            return view('store.subscriptions.subscriptionEditor', compact('subscriptions', 'currencies', 'euroPlans'))->render();
        }else
            return [
                'error' => '1',
                'message' => 'New subscription doesn`t created!'
            ];
    }

    /**
     *@POST("/store/subscriptions/updateSubscription")
     * @Middleware("auth")
     */
    public function updateSubscription()
    {
        $currencies = CHhelper::getCurrencies();
        $euroAmounts = CHhelper::getEuroAmount();

        $currency = $this->request->Input('currency');
        //$euroAmounts = $euroAmounts->search($this->request->Input('plan_id'), true);

        $subscriptionID = !empty($this->request->Input('subscriptionID')) ? CHhelper::filterInputInt($this->request->Input('subscriptionID')) : false;
        $title = !empty($this->request->Input('title')) ? CHhelper::filterInput($this->request->Input('title')) : false;
        $currency = !empty($this->request->Input('currency')) ? $this->request->Input('currency') : false;
        $planID = !empty($this->request->Input('plan_id')) ? CHhelper::filterInput($this->request->Input('plan_id')) : false;
        $trialAmount = !empty($this->request->Input('trial_amount')) ? $this->request->Input('trial_amount') : false;
        $trialFrequency = !empty($this->request->Input('trial_frequency')) ? CHhelper::filterInputInt($this->request->Input('trial_frequency')) : false;
        $trialPeriod = !empty($this->request->Input('trial_period')) ? CHhelper::filterInputInt($this->request->Input('trial_period')) : false;
        $regularAmount = !empty($this->request->Input('regular_amount')) ? $this->request->Input('regular_amount') : false;
        $regularFrequency = !empty($this->request->Input('regular_frequency')) ? CHhelper::filterInputInt($this->request->Input('regular_frequency')) : false;
        $regularPeriod = !empty($this->request->Input('regular_period')) ? CHhelper::filterInputInt($this->request->Input('regular_period')) : false;

        if($currency == 'EUR')
        {
            $trialAmount = 0;
            $trialFrequency = 14;
            $regularFrequency = 1;
            $regularPeriod = 'month';

        }

        $subscrptionUpdate = Subscriptions::where('id', $subscriptionID)->where('channels_id', $this->storeID)->update([
            'title' => $title ,
            'regular_amount' => $regularAmount ,
            'currency' => $currency ,
            'regular_period' => $regularPeriod ,
            'regular_frequency' => $regularFrequency ,
            'trial_amount' => $trialAmount ,
            'trial_period' => $trialPeriod ,
            'trial_frequency' => $trialFrequency ,
            'channels_id' => $this->storeID ,
            'plan_id' => $planID
        ]);

        if($subscrptionUpdate)
        {
            $subscriptions = $this->getSubscriptions();
            return view('store.subscriptions.list', compact('subscriptions'))->render();
        }else
            return [
                'error' => '1',
                'message' => 'New subscription doesn`t created!'
            ];
    }

}
