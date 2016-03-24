@extends('layout')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{isset($current_menu) ? $current_menu : ''}} </h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-color panel-inverse">
                <div class="panel-heading">
                    <h3 class="panel-title">Titles Filter </h3>
                </div>
                <div class="panel-body m-t-20">
                    <form id="titlesFilter" autocomplete="off">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Title" value="" name="filter[searchWord]">
                                     <span class="input-group-btn">
                                        <button type="button" class="btn btn-effect-ripple btn-primary" id="titleSearch">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 m-t-5">
                                @if(isset($companies))
                                    <select name="filter[cp]" id="filter[cp]" class="form-control filterSelect">
                                        <option value="" selected="selected">Content Providers</option>
                                        @foreach($companies as $companyID => $companyTitle)
                                            <option value="{{ $companyID }}">{{ $companyTitle }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-lg-6 m-t-5">
                                @if(isset($stores))
                                    <select name="filter[pl]" class="form-control filterSelect">
                                        <option value="" selected="selected">Stores</option>
                                        @foreach($stores as  $storeID => $storeTitle)
                                            <option value="{{ $storeID }}">{{ $storeTitle }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="filter[order]" value="">
                        <input type="hidden" name="filter[orderType]" value="ASC">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default row">
            <div id="allTitles" class="panel-body">
                @include('titles.partials.list')
            </div>
        </div>
    </div>
    @include('titles.partials.newTitle')
    <script>
        function titlesFilter(){
            $('.loading').show();
            $("#ordertype").val("ASC");

            var titlesFilter = $('#titlesFilter').serialize();

            $.post('/titles/titlesFilter', titlesFilter, function(response){
                if(response.error)
                    $('#allTitles').html('<h3 class="text-center text-danger">' + response.message + '</h3>');
                else
                    $('#allTitles').html(response);
                $('.loading').hide();
            });
        }

        $('#titlesFilter').submit(function(e){
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