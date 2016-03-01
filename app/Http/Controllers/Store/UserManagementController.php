<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\CHhelper\CHhelper;
use Auth;
use App\Countries;
use App\Models\ZaccountsView;

class UserManagementController extends Controller
{
    private $request;

    private $authUser;

    private $storeID;

    private $companyID;

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
        return view('store.users.usersManagement', compact('countries', 'ageRanges', 'users'));
    }

    public function getUsers($filter=''){
        $condition = '';
        /*$this->u = new stdClass();
        $orderType="DESC";
        if (empty($filter['order']))
            $filter['order'] = 'u_regdate';
        if ($filter['ordertype'])
            $orderType= $filter['ordertype'];
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
            $condition .= " AND z_accounts_view.geo_country LIKE '%".$filter['country']."%'";
        if (!empty($filter['search_word'])){
            $condition .= " AND (z_accounts_view.u_fname LIKE '%".$filter['search_word']."%'  OR z_accounts_view.u_email LIKE '%".$filter['search_word']."%' OR z_accounts_view.u_lname LIKE '%".$filter['search_word']."%' )";
        }
        if (!empty($filter['order'])){

            if (  $filter['order'] == "bdate"){
                if ($orderType == "DESC")
                    $orderType = "ASC";
                else
                    $orderType = "DESC";
            }

            $orderBy = " ORDER BY ".$filter['order']." ".$orderType." ";
        }
        $this->u->filter = $filter;*/

        if($this->companyID == 1)// cinehost
            $cq = "SELECT COUNT(*)  FROM z_accounts_view WHERE  z_accounts_view.activated=1 $condition  ";
        else
            $cq = "SELECT COUNT(*)  FROM z_accounts_view WHERE z_accounts_view.login_source=".$this->storeID." AND z_accounts_view.activated=1 $condition  ";


        if($this->companyID == 1)// cinehost
            $q = "SELECT z_accounts_view.*,sum(z_orders.amount) as uamount  FROM z_accounts_view
                 LEFT JOIN z_orders ON z_orders.user_id=z_accounts_view.id AND z_orders.status=1  AND  z_orders.test=0
                 WHERE  z_accounts_view.activated=1 $condition Group by z_accounts_view.id  $orderBy LIMIT  ".($page*$this->ipp).", ".$this->ipp;
        else
            $q = "SELECT z_accounts_view.*,sum(z_orders.amount) as uamount  FROM z_accounts_view
                 LEFT JOIN z_orders ON z_orders.user_id=z_accounts_view.id AND z_orders.status=1 AND  z_orders.test=0 AND z_orders.wl='".$this->storeID."'
                 WHERE z_accounts_view.login_source='".$this->storeID."' AND z_accounts_view.activated=1 $condition Group by z_accounts_view.id";

        return ZaccountsView::hydrateRaw($q);
    }
}
