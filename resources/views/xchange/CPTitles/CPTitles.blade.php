@extends('layout')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">All Titles</h3>
        </div>
    </div>
    <div class="panel panel-color panel-inverse">
        <div class="panel-heading">
            <h3 class="panel-title">My Titles Filter</h3>
        </div>
        <div class="panel-body m-t-20">
            <form id="titlesFilter" autocomplete="off" role="form">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Title" value="" name="filter[searchWord]">
							 <span class="input-group-btn">
								<button type="submit" class="btn btn-effect-ripple btn-primary" id="titleSearch">
                                    <i class="fa fa-search"></i>
                                </button>
							</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6 m-t-10">
                        <select name="filter[vaultStatus]" class="filterSelect selectBoxWithSearch">
                            <option value="0">All Titles</option>
                            <option value="1">Included in  Xchange</option>
                            <option value="2">Not Included in Xchange</option>
                        </select>
                    </div>
                    <div class="col-lg-6 m-t-10">
                        @if(isset($stores))
                            <select name="filter[pl]" class="filterSelect selectBoxWithSearch">
                                <option value="" selected="selected">Stores</option>
                                @foreach($stores as  $storeID => $storeTitle)
                                    <option value="{{ $storeID }}">{{ $storeTitle }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                <input type="hidden" name="filter[order]" value="">
                <input type="hidden" name="filter[orderType]" value="asc">
            </form>
        </div>
    </div>
    <div class="panel panel-default">
         <div id="CPTitles"  class="panel-body">
             @include('xchange.CPTitles.list')
        </div>
    </div>

    <script>
        jQuery(document).ready(function() {
            jQuery(".selectBoxWithSearch").select2({
                width: '100%',
            });
        });

        function titlesFilter(){
            $('.loading').show();
            $("#ordertype").val("ASC");

            var titlesFilter = $('#titlesFilter').serialize();

            $.post('/CPTitles/titlesFilter', titlesFilter, function(response){
                if(response.error)
                    $('#CPTitles').html('<h3 class="text-center text-danger">' + response.message + '</h3>');
                else
                    $('#CPTitles').html(response);
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