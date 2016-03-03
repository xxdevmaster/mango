@extends('layout')
@section('content')
<div class="title">
    <h1 class="h1">
        Users Manager
    </h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Users Filter</div>
    <div class="panel-body">
        <form id="usersFilter">
            <div class="form-group row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" name="filter[searchWord]" value="" placeholder="User Name or E-mail" class="form-control">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-effect-ripple btn-primary" id="searchButton">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-4">
                    <select name="filter[sex]" class="form-control filterSelect">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <select name="filter[country]" class="form-control filterSelect">
                        <option value="">All Countries</option>
                        @if(isset($countries))
                            @foreach($countries as $val)
                                <option value="{{ isset($val->title) ? $val->title : ""}}">{{ isset($val->title) ? $val->title : ""}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-4">
                    <select name="filter[age]" class="form-control filterSelect">
                        <option value="">All Ages</option>
                        @if(isset($ageRanges))
                            @foreach($ageRanges as $key => $val)
                                <option value="{{ isset($key) ? $key : ""}}">{{ isset($val) ? $val : ""}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <input type="hidden" name="filter[order]" value="u_regdate">
            <input type="hidden" name="filter[orderType]" value="ASC">
        </form>
    </div>
</div>
<div id="usersContainer">
    @include('store.users.list_partial')
</div>
<script>
$(document).ready(function(){
    $(document).on('click', '.filter', function(){
        var order = $(this).attr('data-order');
        var orderType = ($('input[name="filter[orderType]"]').val() == "ASC")?"DESC":"ASC";

        $('input[name="filter[order]"]').val(order);
        $('input[name="filter[orderType]"]').val(orderType);

        getUsers();
    });

    $('#searchButton').click(function(){
        getUsers();
    });

    $('.filterSelect').change(function(){
        getUsers();
    });

    $("#usersFilter").submit(function(e){
        e.preventDefault();
        getUsers();
    });
});

function getUsers(){
    $('.loading').show();
    var usersFilter = $("#usersFilter").serialize();
    $.post('/store/usersManagement/drawUsers', usersFilter, function(data){
        $("#usersContainer").html(data);
        $('.loading').hide();
    });
}
</script>
@stop