@extends('layout')
@section('content')
<div class="title">
    <h1 class="h1">
        Stores
    </h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Stores Filter</div>
    <div class="panel-body">
        <form id="storesFilter">
            <div class="form-group row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" name="searchWord" value="" placeholder="Title" class="form-control">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-effect-ripple btn-primary filterSearch" id="titleSearch">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="partnerStoresContainer">
    @include('partnerStores.list_partial')
</div>
<script>
    function filterSearch() {
        var searchWord = $("input[name='searchWord']").val();
        $('.loading').show();

        $.post('/partner/stores/filterSearch', {searchWord:searchWord},function(data){
            $("#partnerStoresContainer").html(data);
            $('.loading').hide();
        });
    }

    $(document).ready(function(){
        $("#storesFilter").submit(function(e){
            e.preventDefault();
            filterSearch();
        })

        $(".filterSearch").click(function(){
            filterSearch();
        });
    });
</script>
@stop