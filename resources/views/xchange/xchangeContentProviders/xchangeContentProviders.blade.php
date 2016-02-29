@extends('layout')
@section('content')
<div class="title">
    <h1 class="h1">
        <a href="/xchangeTitles" class="text-primary" >Xchange </a> / Content Providers
    </h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Content Providers Filter</div>
    <div class="panel-body">
        <form id="contentProviderFilter">
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

<div id="contentProvidersContainer" class="table-responsive">
	@include('xchange.xchangeContentProviders.list_partial')
</div>
<script>

function filterSearch() {
    var searchWord = $("input[name='searchWord']").val();
    $('.loading').show();

    $.post('/xchange/contentProviders/filterSearch', {searchWord:searchWord},function(data){
        $("#contentProvidersContainer").html(data);
        $('.loading').hide();
    });
}

$(document).ready(function(){
    $("#contentProviderFilter").submit(function(e){
        e.preventDefault();
        filterSearch();
    })

    $(".filterSearch").click(function(){
        filterSearch();
    });
});

</script>
@stop