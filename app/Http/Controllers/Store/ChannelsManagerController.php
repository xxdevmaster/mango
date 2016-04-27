<?php

namespace App\Http\Controllers\Store;

use App\FilmOwners;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Film;
use App\Models\Subchannels;
use App\Models\FkSubChannelsFilms;
use App\Libraries\CHhelper\CHhelper;

class ChannelsManagerController extends Controller
{
    private $request;

    private $authUser;

    private $accountID;

    private $storeID;

    private $companyID;

    private $subChannelID;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->accountID = $this->authUser->account->id;
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function channelsManagerShow()
    {
        $subChannels = $this->loadSubChannels();

        return view('store.channelsManager.channelsManager', compact('subChannels'));
    }

    public function loadSubChannels()
    {
        $custom = Subchannels::where('channels_id', $this->storeID)->where('source', 'custom')->get();
        //$custom = "SELECT c.*,c.title as xtitle FROM cc_subchannels c WHERE c.source = 'custom' AND c.channels_id='{$this->storeID}'";

        $genres = Subchannels::leftJoin('cc_genres', 'cc_subchannels.source_id', '=', 'cc_genres.id')
                            ->where('cc_subchannels.source', 'genres')
                            ->where('cc_subchannels.channels_id', $this->storeID)->get();

       // $genres = "SELECT c.*,g.title as xtitle FROM cc_subchannels c LEFT JOIN cc_genres g ON c.source_id = g.id WHERE c.source = 'genres' AND c.channels_id='{$this->storeID}'";

       // dd($custom, $genres);
        return $custom->merge($genres);
        //dd(array_merge(Subchannels::hydrateRaw($custom)->toArray(), Subchannels::hydrateRaw($genres)->toArray()), $x->toArray());
        //$this->assortSubChannels(array_merge($custom,$genres));
        //foreach ($this->getDevices() as $device => $deviceName)
         //   usort($this->o[$device], array("SubChannelsEditor", "compareSubChannelsPriority"));

    }

    /**
     * Get Token Movie Titles
     * @POST("/store/channelsManager/getTokenMovieTitles")
     * @Middleware("auth")
     * @return array
     */
    public function getTokenMovieTitles()
    {
        $inputToken = (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;

        if( $this->storeID > 0 && $this->companyID > 0)
        {
            $union = Film::distinct()->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                ->where('fk_films_owners.owner_id', $this->companyID)
                ->where('fk_films_owners.type', 0)
                ->where('cc_films.deleted', 0)
                ->where('cc_films.title', 'like', $inputToken.'%');

            return Film::distinct()->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                ->where('cc_channels_contracts.channel_id', $this->storeID)
                ->where('cc_films.deleted', 0)
                ->where('cc_films.title', 'like', $inputToken.'%')
                ->union($union->select('cc_films.title', 'cc_films.id'))
                ->select('cc_films.title', 'cc_films.id')->limit(10)->get();
        }
        elseif( $this->storeID > 0)
        {
            return Film::distinct()->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                ->where('cc_channels_contracts.channel_id', $this->storeID)
                ->where('cc_films.deleted', 0)
                ->where('cc_films.title', 'like', $inputToken.'%')
                ->select('cc_films.title', 'cc_films.id')->limit(10)->get();


        }
        elseif( $this->companyID > 0)
        {
            return Film::distinct()->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                ->where('fk_films_owners.owner_id', $this->companyID)
                ->where('fk_films_owners.type', 0)
                ->where('cc_films.deleted', 0)
                ->where('cc_films.title', 'like', $inputToken.'%')
                ->select('cc_films.title', 'cc_films.id')->limit(10)->get();
        }

    }

    /**
     * Get All Titles For Import To Input Token
     * @POST("/store/channelsManager/getAllTitlesForToken")
     * @Middleware("auth")
     * @return array
     */
    public function getAllTitlesForToken()
    {
        if( $this->storeID > 0 && $this->companyID > 0)
        {
            $union = Film::distinct()->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                ->where('fk_films_owners.owner_id', $this->companyID)
                ->where('fk_films_owners.type', 0)
                ->where('cc_films.deleted', 0);

            return Film::distinct()->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                ->where('cc_channels_contracts.channel_id', $this->storeID)
                ->where('cc_films.deleted', 0)
                ->union($union->select('cc_films.title', 'cc_films.id'))
                ->select('cc_films.title', 'cc_films.id')->lists('title', 'id');
        }
        elseif( $this->storeID > 0)
        {
            return Film::distinct()->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                ->where('cc_channels_contracts.channel_id', $this->storeID)
                ->where('cc_films.deleted', 0)
                ->select('cc_films.title', 'cc_films.id')->lists('title', 'id');


        }
        elseif( $this->companyID > 0)
        {
            return Film::distinct()->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                ->where('fk_films_owners.owner_id', $this->companyID)
                ->where('fk_films_owners.type', 0)
                ->where('cc_films.deleted', 0)
                ->select('cc_films.title', 'cc_films.id')->lists('title', 'id');
        }
    }

    /**
     * Add New Channel Or SubChannel
     * @POST("/store/channelsManager/addChannel")
     * @Middleware("auth")
     * @return array
     */
    public function addChannel()
    {
        $newSubChannelID = Subchannels::create([
            'subscriptions_id' => $this->request->Input('subscriptions_id') ,
            'channels_id' => $this->storeID ,
            'source_id' => '' ,
            'source' => 'custom' ,
            'active' => '1' ,
            'device' => 'web'
        ])->id;

        $i = 0;
        if(is_array($this->request->Input('sorted')))
            foreach ($this->request->Input('sorted') as $filmID => $val)
            {
                FkSubChannelsFilms::create([
                    'subchannels_id' => $newSubChannelID,
                    'films_id' => $filmID ,
                    'priority' => $i
                ]);
                ++$i;
            }

        $subChannels = $this->loadSubChannels();
        return view('store.channelsManager.subChannels', compact('subChannels'));
    }

    /**
     * Add New Channel Or SubChannel
     * @POST("/store/channelsManager/removeSubChannel")
     * @Middleware("auth")
     * @return array
    */
    public function removeSubChannel()
    {
        $this->subChannelID = (!empty($this->request->Input('subChannelID')) && is_numeric($this->request->Input('subChannelID'))) ? CHhelper::filterInputInt($this->request->Input('subChannelID')) : false;

        if($this->subChannelID)
        {
            DB::transaction(function(){
                FkSubChannelsFilms::where('subchannels_id', $this->subChannelID)->delete();

                Subchannels::where('channels_id', $this->storeID)->where('id', $this->subChannelID)->delete();
            });
        }

        $subChannels = $this->loadSubChannels();
        return view('store.channelsManager.subChannels', compact('subChannels'));
        /*
        $this->wlid=$WL_ID;
        $id = G('DB')->quote($R['id']);
        $c = G('DB')->query("SELECT c.* FROM cc_subchannels c WHERE c.id = $id")->fetch(PDO::FETCH_OBJ);

        if(ne($c))
        {
            switch($c->source)
            {
                case 'genres':
                    G('DB')->exec("DELETE FROM cc_subchannels WHERE channels_id='{$this->wlid}' AND id=$id");
                    break;
                case 'custom':
                    G('DB')->exec("DELETE FROM fk_subchannels_films WHERE subchannels_id=$id");
                    G('DB')->exec("DELETE FROM cc_subchannels WHERE channels_id='{$this->wlid}' AND id=$id");
                    break;
            }
            return '{"success":true}';
        }
        else
            return '{"success":false,"message":"SubChannel not found"}';*/
    }

    /**
     * Edit SubChannel
     * @GET("/store/channelsManager/{subChannelID}")
     * @Middleware("auth")
     * @return Response html
     */
    public function editSubChannel($subChannelID)
    {
        $subChannel = Subchannels::find($subChannelID);
        $subChannelTitles = FkSubChannelsFilms::where('subchannels_id', $subChannelID)->leftJoin('cc_films', 'cc_films.id', '=', 'fk_subchannels_films.films_id')->get();
        dd($subChannelTitles);
        return view('store.channelsManager.editSubChannel', compact('subChannel'));
    }
}
