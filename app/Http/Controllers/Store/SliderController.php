<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Silders;
use App\Models\FilmSlidersImages;
use App\Store;
class SliderController extends Controller
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

    public function sliderShow()
    {
        $films = $this->getStoreFilms();

        $this->getMainSliderID();
        $sliderItems = $this->getImageitems($this->sliderID);
        return view('store.slider.slider', compact('films', 'sliderItems'), ['slider' => $this->slider, 'storeID' => $this->storeID]);
    }

    public function getMainSliderID()
    {
        if($this->storeID != 0 || $this->companyID == 1) {
            $this->slider = Silders::where('channel_id', $this->storeID)->get()->keyBy('id');
            $this->slider->each(function ($val, $key) {
                if ($val->title == 'Main Slider')
                    $this->sliderID = $key;
            });

            if(empty($this->sliderID))
                $this->sliderID = Silders::create([
                    'channel_id' => $this->storeID,
                    'title' => 'Main Slider'
                ])->id;

            return $this->sliderID;
        }
    }

    public function getImageitems($sliderID)
    {
        return FilmSlidersImages::where('sliders_id', $sliderID)->orderBy('position', 'asc')->get();

        dd($sliderItems);
        $FilmsObj = BaseElements::getPlatformFilms($_SESSION['WL_ID'],'all',$this->id);

        $res= G('DB')->query("SELECT * FROM fk_sliders_images WHERE sliders_id='".$slider_id."' ORDER BY position ASC");
        while ($row = $res->fetch(PDO::FETCH_ASSOC)){
            $Films = array();
            $item_id = $row['id'];
            if($FilmsObj){
                foreach ($FilmsObj as $key => $val){
                    $Films[] = '<option value="'.$key.'" '.($row['films_id'] == $key?'selected':'').'>'.$val.'</option>';
                }
            }
            $surl = $row['url']?$row['url']:'http://cinecliq.assets.s3.amazonaws.com/wls/'.intval($_SESSION['WL_ID']).'/'.$row['filename'];
            $imagelist[]=  '
                <li class="list-group-item" id="item-'.$item_id.'">
                    <div class="media">
                        <div class="col-sm-6 col-md-3">
                            <a href="#" class="thumbnail">
                                <img   data-src="holder.js/100x100" src="'.$surl.'" alt="...">
                            </a>
                        </div>
                        <div class="media-body">
                            <div class="form-group"><input name="title['.$item_id.']" type="text" class="form-control" placeholder="Title" value="'.$row['title'].'"></div>
                            <div class="form-group"><input name="brief['.$item_id.']" type="text" class="form-control" placeholder="Description" value="'.$row['brief'].'"></div>
                            <div class="form-group"><input name="url['.$item_id.']" type="text" class="form-control" placeholder="URL" value="'.$row['ext_url'].'"></div>
                            <div class="form-group"><select name="film['.$item_id.']" class="selectpicker form-control"><option value="">Select Film</option>'.implode('',$Films).'</select></div>
                            <button type="button" class="btn btn-xs btn-danger" onclick="deleteWLimage(\''.$item_id.'\');">Delete</button>
                        </div>
                    </div>
               </li>
                ';
        }
        return implode('',$imagelist);
    }

    private function getStoreFilms()
    {
        return Store::find($this->storeID)->storesFilms(0)->get()->keyBy('id');
    }
}
