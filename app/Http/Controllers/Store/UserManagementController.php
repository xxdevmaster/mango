<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\CHhelper\CHhelper;
use Auth;
use App\Countries;
use App\Models\ZaccountsView;
use App\Models\ZOrders;


class UserManagementController extends Controller
{
    private $request;

    private $authUser;

    private $storeID;

    private $companyID;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function usersShow()
    {
        $countries = Countries::where('deleted', '0')->orderBy('title')->get();
        $ageRanges = CHhelper::getAgeRanges(20, 90, 5);
        $users = $this->getUsers();

        return view('store.users.usersManagement', compact('countries', 'ageRanges'), $users);
    }

    /**
     *@POST("/store/usersManagement/drawUsers")
     * @Middleware("auth")
     */
    public function drawUsers(){
        $users = $this->getUsers();
        return view('store.users.list_partial', $users);
    }

    private function getUsers(){
        $condition = '';
        $orderBy = '';
        $order = 'u_regdate';
        $orderType = 'ASC';

        $filter = !empty($this->request->Input('filter') && is_array($this->request->Input('filter'))) ? $this->request->Input('filter') : false ;

        if($filter){
            if ($filter['orderType'])
                $orderType= $filter['orderType'];
            if ($filter['age']){
                $curYear = date('Y');
                $range = explode(',',$filter['age']);
                $ageCond = array();
                for ($i = $range[0];$i<=$range[1];$i++)
                    $ageCond[]= "z_accounts_view.u_bdate LIKE '%".($curYear - $i)."%'";
                $condition .= " AND ( ".implode(' OR ',$ageCond)." )";


            }
            if (!empty($filter['sex']))
                $condition .= " AND z_accounts_view.u_gender='".$filter['sex']."'";
            if (!empty($filter['country']))
                $condition .= " AND z_accounts_view.geo_country LIKE '".$filter['country']."%'";
            if (!empty($filter['searchWord'])){
                $condition .= " AND (z_accounts_view.u_fname LIKE '".$filter['searchWord']."%'  OR z_accounts_view.u_email LIKE '".$filter['searchWord']."%' OR z_accounts_view.u_lname LIKE '".$filter['searchWord']."%' )";
            }
            if (!empty($filter['order'])){
                $order = $filter['order'];
                $orderBy = " ORDER BY ".$filter['order']." ".$orderType." ";
            }
        }


        // cinehost
        if($this->companyID == 1){
            $users = ZaccountsView::getUsersInAuthCinehost($condition, $orderBy, $this->limit, $this->offset);
            $usersTotalCount = ZaccountsView::getUsersTotalInAuthCinehost($condition);
        }
        else{
            $users = ZaccountsView::getUsers($this->storeID, $condition, $orderBy, $this->limit, $this->offset);
            $usersTotalCount = ZaccountsView::getUsersTotal($this->storeID, $condition);
        }

        if($usersTotalCount->isEmpty())
            $usersTotalCount = 0;
        else
            $usersTotalCount = $usersTotalCount->first()->count;

        $users = new LengthAwarePaginator($users, $usersTotalCount, $this->limit, $this->page);
        return compact('users', 'order', 'orderType');
    }

    /**
     *@POST("/store/usersManagement/pager")
     * @Middleware("auth")
     */
    public function pager()
    {
        $this->page = !empty($this->request->Input('page')) && is_numeric($this->request->Input('page')) ? CHhelper::filterInputInt($this->request->Input('page')) : 0;
        if($this->page){
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

            $users = $this->getUsers();
            return view('store.users.list_partial', $users);
        }
    }

    /**
     *@POST("/store/usersManagement/getUserDetails")
     * @Middleware("auth")
     */
    public function getUserDetails()
    {
        $userID = (!empty($this->request->Input('userID')) && is_numeric($this->request->Input('userID'))) ? CHhelper::filterInputInt($this->request->Input('userID')) : false;
        $renPurchFilms = collect();

        if($userID){
            if($this->companyID == 1)
                $renPurchFilms = ZOrders::where('user_id', $userID)
                    ->where('status', '1')
                    ->where('test', '0')->get();
            else
                $renPurchFilms = ZOrders::where('user_id', $userID)
                    ->where('wl', $this->storeID)
                    ->where('status', '1')
                    ->where('test', '0')->get();

            if(!$renPurchFilms->isEmpty())
                $renPurchFilms = $renPurchFilms->first()->usersFilms;
        }

        return view('store.users.rentPurchFilms', compact('renPurchFilms'))->render();
    }
}
