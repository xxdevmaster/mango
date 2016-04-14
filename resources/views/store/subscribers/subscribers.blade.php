@extends('layout')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Subscribers Management</h3>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Subscribers Filter</div>
        <div class="panel-body m-t-20">
            <form id="subscribersFilter" autocomplete="off">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Subscriber Name or E-mail" name="filter[searchWord]">
							 <span class="input-group-btn">
								<button type="button" class="btn btn-effect-ripple btn-primary" id="titleSearch">
                                    <i class="fa fa-search"></i>
                                </button>
							</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 m-t-10">
                        <select name="filter[sex]" class="form-control filterSelect">
                            <option value="">All Genders</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-lg-4 m-t-10">
                        <select name="filter[country]" class="form-control filterSelect">
                            <option value="">All Countries</option>
                            @if(isset($allCountries))
                                @foreach($allCountries as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-lg-4 m-t-10">
                        <select name="filter[age]" id="filter[age]" class="form-control filterSelect">
                            <option value="" selected="selected">All Ages</option>
                            @if(isset($ageRanges))
                                @foreach($ageRanges as $ageRangeKey => $ageRange)
                                    <option value="{{ $ageRangeKey }}">{{ $ageRange }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 m-t-10">
                        <select name="filter[status]" class="form-control filterSelect">
                            <option value="">Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="trial">Trial</option>
                        </select>
                    </div>
                    <div class="col-lg-4 m-t-10">
                        <select name="filter[channels]" class="form-control filterSelect">
                            <option value=""> All Channles</option>
                            @if(isset($channels))
                                @foreach($channels as $channelID => $channel)
                                    <option value="{{ $channelID }}">{{ $channel }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <input type="hidden" name="filter[order]" value="">
                <input type="hidden" name="filter[orderType]" value="asc">
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <div id="subscribersContainer" class="panel-body">
            @include('store.subscribers.list')
        </div>
    </div>
    <script>
        function titlesFilter(){
            autoCloseMsgHide();
            $('.loading').show();

            var subscribersFilter = $('#subscribersFilter').serialize();

            $.post('/store/subscribersFilter', subscribersFilter, function(data){
                if(data.error != 1)
                    $('#subscribersContainer').html(data);
                else
                    autoCloseMsg(1, data.error, 5000);
                $('.loading').hide();
            });
        }

        $('#subscribersFilter').submit(function(e){
            e.preventDefault();
            titlesFilter();
        });

        $('#titleSearch').click(function(){
            titlesFilter();
        });

        $(".filterSelect").change(function(){
            titlesFilter();
        });
    </script>
@stop