<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\CHhelper\CHhelper;
use App\Models\Vcbunches;
use App\Models\Zvcodes;

class GiftVoucherController extends Controller
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

    public function giftVoucherShow()
    {
        $voucher = $this->getData();
        return view('store.giftVoucher.giftVoucher', compact('voucher'));
    }

    private function getData()
    {
        return Vcbunches::where('channels_id', $this->storeID)->where('deleted', 0)->get()->keyBy('id');
    }

    private function drawContent($voucher)
    {
        return view('store.giftVoucher.list', compact('voucher'))->render();
    }

    /**
     *@POST("/store/giftVoucher/attacheVoucher")
     * @Middleware("auth")
     */
    public function attacheVoucher()
    {
        $totalCount = (!empty($this->request->Input('totalCount')) && is_numeric($this->request->Input('totalCount')) && $this->request->Input('totalCount') > 0 && $this->request->Input('totalCount') <= 20) ? CHhelper::filterInputInt($this->request->Input('totalCount')) : false;
        $voucherTitle = !empty($this->request->Input('vaucherTitle')) ? CHhelper::filterInput($this->request->Input('vaucherTitle')) : false;

        if($totalCount && $voucherTitle) {
            $voucherID = Vcbunches::create([
                'title' => $voucherTitle ,
                'channels_id' => $this->storeID ,
                'dt' => 'NOW()'
            ])->id;

            Zvcodes::whereNull('vcbunches_id')->limit($totalCount)->update([
                'vcbunches_id' => $voucherID ,
                'owner_id' => -1
            ]);
        }

        $voucher = $this->getData();
        return $this->drawContent($voucher);
    }

    /**
     *@POST("/store/giftVoucher/getGiftVoucherEditor")
     * @Middleware("auth")
     */
    public function getGiftVoucherEditor()
    {
        $vcbunchesID = (!empty($this->request->Input('vcbunchesID')) && is_numeric($this->request->Input('vcbunchesID'))) ? CHhelper::filterInputInt($this->request->Input('vcbunchesID')) : false;
        if($vcbunchesID) {
            $voucher = Zvcodes::where('vcbunches_id', $vcbunchesID)->get();

           foreach($voucher as $value) {
               if ($value->used != 0)
                   $vouchers['used'][] = $value->code;
               else
                   $vouchers['free'][] = $value->code;
           }

           return [
               'error' => '0',
               'html' => view('store.giftVoucher.giftVoucherInfo', compact('vouchers'))->render()
           ];
        }
        return [
            'error' => '1',
            'message' => 'Vaucher id doesn`t exsist'
        ];
    }

}
