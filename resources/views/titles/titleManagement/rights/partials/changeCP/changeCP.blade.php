<div id="CPPLEditor">
    <div class="panel panel-default">
        <div class="panel-heading" id="actionType">
            @if($rightsPermission['action'] === 'CPPL')
			    You are now acting as a <span class="proxBold"><b>Content Provider</b></span> - <a class="text-primary cp" onclick="changeCPPL('Store', {{ $film->id }})">Change to Store</a>.
            @elseif($rightsPermission['action'] === 'CP')
                You are now acting as a <span class="proxBold">Content Provider</span>.
            @endif
        </div>
    </div>
</div>

<div id="DealContent">
    <input type="hidden" name="pid" value="deals">
    <input type="hidden" name="film_CPs" id="film_CPs" value="{{$authCompanyID}}">
    <ul class="nav nav-tabs dealTabs">
        <li class="active"><a href="#tab-basic" data-toggle="tab" class="basic" >Rental Information</a></li>
        <li><a href="#tab-addCountries" data-toggle="tab" class="addCountries" >Manage Regions</a></li>
        <li><a href="#tab-countriesPrices" data-toggle="tab" class="countriesPrices">Edit Prices</a></li>
        <li><a href="#tab-contentProvider" data-toggle="tab" class="contentProvider">Revenue Sharing</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-basic"> <div class="miniwell">
                <form id="dealInfo">
                    <div class="form-group row"></div>
                    <div class="form-group row rentalDur">
                        <label class="col-lg-2" for="form-lease_duration">Rental Duration</label>
                        <div class="col-lg-3"><input type="text" class="form-control" id="form-lease_duration" name="lease_duration" placeholder="" value="{{ $film->lease_duration }}">
                            <span>Hours</span>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                    <input type="hidden" name="act" value="saveDealInfo">
                    <input type="hidden" name="film_id" value="2505">
                    <input type="hidden" name="deal_id" value="10697">
                </form>
            </div>
        </div>
        <div class="tab-pane" id="tab-addCountries">
            @include('titles.titleManagement.rights.partials.manageRegions')
        </div>
        <div class="tab-pane" id="tab-countriesPrices">
            @include('titles.titleManagement.rights.partials.editPrice.editPrice')
        </div>
        <div class="tab-pane" id="tab-contentProvider">
            @include('titles.titleManagement.rights.partials.changeCP.revenueSharingCp')
        </div>
    </div>

</div>