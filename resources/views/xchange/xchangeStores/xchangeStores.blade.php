@extends('layout')
@section('content')
<div class="title">
    <h1 class="h1">
        <a href="/CPTitles" class="text-primary" >Xchange </a> / {{ isset($current_menu) ? $current_menu : "Stores"}}
    </h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Stores Filter</div>
    <div class="panel-body">
        <form id="PLFilter">
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

<div id="PlatformsContainer" class="table-responsive">
	@include('xchange.xchangeStores.list_partial')
</div>
<script>

function filterSearch() {
    var searchWord = $("input[name='searchWord']").val();
    $('.loading').show();

    $.post('/xchange/stores/filterSearch', {searchWord:searchWord},function(data){
        $("#PlatformsContainer").html(data);
        $('.loading').hide();
    });
}

$(document).ready(function(){
    $("#PLFilter").submit(function(e){
        e.preventDefault();
        filterSearch();
    })

    $(".filterSearch").click(function(){
        filterSearch();
    });
});

function removePlatformItem(PL_ID,ACC_ID){
    var curpage = $(".pgCurrent").data("curpage");
    $.ajax({
        type: "POST",
        url: "engine.php",
        data: "&act=removePlatformItem&PL_ID="+PL_ID+"&ACC_ID="+ACC_ID,
        dataType: "json",
        success: function (data) {
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: $("#PLFilter").serialize()+"&act=get_platforms&page="+curpage,
                dataType: "json",
                success: function (data) {
                    $( "#PlatformsContainer" ).html(data);
                }
            });
        }
    });
}





function setClickToFilmsPager(){
    $( ".PlatformsFilmsPager" ).click(function(){
        var rel = $(this).attr("rel");
        if(rel>-1){
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: "act=get_platform_films&page="+rel,
                dataType: "json",
                success: function (data) {
                    $( "#PlatformsContainer" ).html(data);
                }
            });
        }
        return false;
    });
}

function addNewPlatform(){
    var platformname = $("#platformname").val();
    $.ajax({
        type: "POST",
        url: "engine.php",
        data: "act=AddNewPlatformName&platformname="+platformname,
        dataType: "json",
        success: function (data) {
            $("#cpname").val("");
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: $("#PlatformsForm").serialize()+"&act=get_platforms&page=0",
                dataType: "json",
                success: function (data) {
                    $( "#PlatformsContainer" ).html(data);
                }
            });
            $("#addNewPlatfomModal").modal("hide");
            $("body").removeClass("modal-open");
            $(".modal-backdrop").remove();
        }
    });
}

function savePlatformItemEdit(){
    $.ajax({
        type: "POST",
        url: "engine.php",
        data: $("#platform-edit-form").serialize(),
        dataType: "json",
        success: function (data) {
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: $("#PlatformsForm").serialize()+"&act=get_platforms",
                dataType: "json",
                success: function (data) {
                    $("#PlatformsContainer" ).html(data);
                    //$("#editPlatformItemModal").modal("hide");
                    //$("body").removeClass("modal-open");
                    //$(".modal-backdrop").remove();
                }
            });
        }
    });
}

$(document).ready(function(){
    $(".TitlePath").html($("#SecondTitlePath").val());
})
</script>
@stop