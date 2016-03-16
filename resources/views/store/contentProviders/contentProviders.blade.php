@extends('layout')
@section('content')
<div class="title">
    <h1 class="h1">Content Providers</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Content Providers Filter</div>
    <div class="panel-body">
        <form id="filter" autocomplete="off">
            <div class="form-group row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" name="searchWord" value="" placeholder="Title" class="form-control">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary" id="searchButton">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="form-group">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#attacheContentProvider" >
        + Add New Content Provider
    </button>
</div>
<div id="container" class="table-responsive">
    @include('store.contentProviders.list_partial')
</div>

<div class="modal fade" id="attacheContentProvider" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="label">Add New Content Provider</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post" role="form" id="attacheContentProviderForm">
                    <div class="form-group">
                        <label for="input-cp">Content Provider Name</label>
                        <input type="text" class="form-control" name="contentProviderName" value="" placeholder="">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="attache">Add</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editContentProvider" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true"></div>
<script>
    function filterSearch() {
        var searchWord = $("input[name='searchWord']").val();
        $('.loading').show();

        $.post('/store/contentProviders/filterSearch', {searchWord:searchWord},function(data){
            $("#container").html(data);
            $('.loading').hide();
        });
    }

    $(document).ready(function(){
        $("#filter").submit(function(e){
            e.preventDefault();
            filterSearch();
        })

        $("#searchButton").click(function(){
            filterSearch();
        });

        $('#attache').click(function(){
            $('.loading').show();
            var contentProviderName = $('input[name="contentProviderName"]').val();
            $('#attacheContentProvider').modal('hide');
            $.post('/store/contentProviders/createNewContentProvider', {contentProviderName:contentProviderName},function(data){
                $("#container").html(data);
                $('.loading').hide();
                $('input[name="contentProviderName"]').val("");
            });
        });
    });
</script>
@stop