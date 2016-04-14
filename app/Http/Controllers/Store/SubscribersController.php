<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\ZOrdersSubscriptionFragmented;
use App\Countries;
use App\Models\Subchannels;
use App\Libraries\CHhelper\CHhelper;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;
class SubscribersController extends Controller
{

    private $request;

    private $authUser;

    private $accountID;

    private $storeID;

    private $companyID;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    private $orderBy = 'u_regdate';

    private $orderType = 'asc';

    private $searchWord = '';

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->accountID = $this->authUser->account->id;
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function subscribersShow()
    {
        $this->getUserDetails();
        $channels = $this->getChannels();
        $allCountries = $this->getCountries();
        $ageRanges = CHhelper::getAgeRanges(20, 90, 5);
        $subscribers = $this->getSubscribers();
        return view('store.subscribers.subscribers', compact('allCountries', 'ageRanges', 'channels'), $subscribers);
    }

    private function getSubscribers()
    {
        $filter = (!empty($this->request->input('filter')) && is_array($this->request->input('filter'))) ? $this->request->input('filter') : false;
        $this->searchWord = !empty($filter['searchWord']) ? CHhelper::filterInput($filter['searchWord']) : false;

        $x = ZOrdersSubscriptionFragmented::leftJoin('z_accounts', 'z_orders_subscription_fragmented.user_id', '=', 'z_accounts.id')
                                            ->leftJoin('z_orders_subscription', 'z_orders_subscription.id', '=', 'z_orders_subscription_fragmented.orders_subscriptions_id')
                                            ->leftJoin('cc_subchannels', 'cc_subchannels.id', '=', 'z_orders_subscription.subchannels_id')
                                            ->where('z_accounts.tester', 0)
                                            ->where('z_accounts.payment_tester', 0)
                                            ->where('z_accounts.activated', 1)
                                            ->where('z_accounts.login_source', $this->storeID)
                                            ->select(
                                                DB::raw('sum(z_orders_subscription_fragmented.amount) as total'),
                                                DB::raw('DATE_FORMAT(FROM_UNIXTIME(z_accounts.u_regdate), "%d/%m/%Y") as regdate'),
												'z_orders_subscription_fragmented.amount',
												'z_orders_subscription_fragmented.currency',
												'z_orders_subscription_fragmented.billing_date',
												'z_orders_subscription_fragmented.billing_month',
												'z_accounts.id as accountsId',
												'z_accounts.u_name',
												'z_accounts.u_fname',
												'z_accounts.u_lname',
												'z_accounts.u_mname',
												'z_accounts.u_email',
												'z_accounts.u_bdate',
												'z_accounts.u_gender',
												'z_accounts.u_regdate',
												'z_accounts.geo_country',
												'z_accounts.u_credits',
												'z_accounts.u_person',
												'z_accounts.u_avatar',
												'z_accounts.u_avatar_src',
												'z_accounts.login_provider',
												'z_accounts.login_source',
												'cc_subchannels.title as subchannelTitle',
												'cc_subchannels.channels_id',
												'cc_subchannels.device',
												'cc_subchannels.bundles_id',
												'cc_subchannels.model',
												'cc_subchannels.locale',
												'z_orders_subscription_fragmented.id'
                                            );

        if (!empty($filter['sex'])) {
           if($filter['sex'] === 'male' || $filter['sex'] === 'female') {
               $x = $x->where('z_accounts.u_gender', $filter['sex']);
           }
        }
        if (!empty($filter['channels'])) {
            if(is_numeric($filter['channels']))
                $x = $x->where('z_orders_subscription.subchannels_id', $filter['channels']);
        }

        if (!empty($filter['country']))
                $x = $x->where('z_accounts.geo_country', 'like', "%".CHhelper::filterInput($filter['country'])."%");

        if (isset($this->searchWord)){
            $x = $x->where(function($query) {
                $query->where('z_accounts.u_fname', 'like', CHhelper::filterInput($this->searchWord)."%")
                      ->orWhere('z_accounts.u_email', 'like', CHhelper::filterInput($this->searchWord)."%")
                      ->orWhere('z_accounts.u_lname', 'like', CHhelper::filterInput($this->searchWord)."%");
            });
        }

        if(!empty($filter['age'])){
            $curYear = date('Y');
            $range = explode(',', $filter['age']);
            $ageCond = ['01/01/'.($curYear - $range[1]), '12/30/'.($curYear - $range[0])];
            $x = $x->whereBetween('z_accounts.u_bdate', $ageCond);
        }

        if(!empty($filter['status'])) {
            if ($filter['status'] == 'active')
                $x = $x->where(function($query) {
                    $query->where('z_orders_subscription.end_date', '0000-00-00 00:00:00')
                          ->orWhereNull('z_orders_subscription.end_date');
                 })->where('z_orders_subscription.trial_end_date', '<', 'NOW()');

            elseif($filter['status'] == 'inactive')
                $x = $x->where('z_orders_subscription.end_date', '>', '0000-00-00 00:00:00');

            elseif($filter['status'] == 'trial')
                $x = $x->where(function($query) {
                    $query->where('z_orders_subscription.end_date', '0000-00-00 00:00:00')
                        ->orWhereNull('z_orders_subscription.end_date');
                })->where(function($query) {
                    $query->whereNull('z_orders_subscription.trial_end_date')
                          ->orWhere('z_orders_subscription.trial_end_date', '>', 'NOW()');
                });
        }

        if (!empty($filter['order'])){
            if ($filter['order'] === "bdate") {
                $this->orderBy = 'u_bdate';
                if ($filter['orderType'] === "desc")
                    $this->orderType = "asc";
                else
                    $this->orderType = "desc";
            }

            elseif($filter['order'] === "geo_country") {
                $this->orderBy = 'geo_country';
                if ($filter['orderType'] === "desc")
                    $this->orderType = "asc";
                else
                    $this->orderType = "desc";
            }
        }

        $subscribers = DB::table(DB::raw("({$x->groupBy('z_accounts.id')->toSql()}) as tmpTable"))->mergeBindings($x->getQuery())
                            ->orderBy($this->orderBy, $this->orderType)
                            ->limit($this->limit)
                            ->skip($this->offset)
                            ->get();
        $subscribersTotal = DB::table(DB::raw("({$x->groupBy('z_accounts.id')->toSql()}) as tmpTable"))->mergeBindings($x->getQuery())->count();

        $items = new LengthAwarePaginator($subscribers, $subscribersTotal, $this->limit, $this->page);

        $orderBy = $this->orderBy;
        $orderType = $this->orderType;

        return compact('items', 'orderBy', 'orderType');
    }

    /**
     *@POST("/store/subscribersFilter")
     * @Middleware("auth")
    */
    public function subscribersFilter()
    {
        $subscribers = $this->getSubscribers();
        return view('store.subscribers.list', $subscribers)->render();
    }

    /**
     * Get all countries.
     * @return collection
     */
    private function getCountries()
    {
        return Countries::orderBy('title', 'asc')->lists('title');
    }

    /**
     * Get all channels.
     * @return collection
     */
    private function getChannels()
    {
        return Subchannels::where('channels_id', $this->storeID)->orderBy('title', 'asc')->lists('title', 'id');
    }

    /**
     *@POST("/store/subscribers/getUserDetails")
     * @Middleware("auth")
     */
    public function getUserDetails()
    {
        $subscriberID = (!empty($this->request->Input('subscriberID')) && is_numeric($this->request->Input('subscriberID'))) ? CHhelper::filterInputInt($this->request->Input('subscriberID')) : false;
        if($subscriberID) {
            $subscriberDetails = ZOrdersSubscriptionFragmented::leftJoin('z_accounts', 'z_orders_subscription_fragmented.user_id', '=', 'z_accounts.id')
                ->leftJoin('z_orders_subscription', 'z_orders_subscription.id', '=', 'z_orders_subscription_fragmented.orders_subscriptions_id')
                ->join('cc_subchannels', 'cc_subchannels.id', '=', 'z_orders_subscription.subchannels_id')
                ->where('z_accounts.tester', 0)
                ->where('z_accounts.payment_tester', 0)
                ->where('z_accounts.activated', 1)
                ->where('z_accounts.login_source', $this->storeID)
                ->where('z_accounts.id', $subscriberID)
                ->select(
                    DB::raw('count(z_orders_subscription_fragmented.id) AS totalmonth'),
                    DB::raw('sum(z_orders_subscription_fragmented.amount) AS mCount'),
                    'z_orders_subscription.*',
                    'cc_subchannels.title as subchannelTitle',
                    'z_orders_subscription_fragmented.*',
                    'z_accounts.*',
                    'z_accounts.id as uid',
                    'z_orders_subscription_fragmented.orders_subscriptions_id'

                )->orderBy('z_orders_subscription_fragmented.orders_subscriptions_id')->get();

            return view('store.subscribers.subscriberDetails', compact('subscriberDetails'));
        }
        else
            return [
              'error' => '1',
              'message' => 'Invalid subscriber id'
            ];
    }

    /**
     *@POST("/store/subscribers/pager")
     * @Middleware("auth")
     */
    public function pager()
    {
        $this->page = (!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))) ? CHhelper::filterInputInt($this->request->Input('page')) : 0;


        if(($this->page - 1) != 0)
            $this->offset  = ($this->page - 1)*20;

        $subscribers = $this->getSubscribers();
        return view('store.subscribers.list', $subscribers)->render();
    }
}
