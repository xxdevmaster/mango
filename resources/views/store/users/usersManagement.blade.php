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
        <form id="storesFilter">
            <div class="form-group row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" name="filter[searchWord]" value="" placeholder="User Name or E-mail" class="form-control">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-effect-ripple btn-primary filterSearch" id="titleSearch">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-4">
                    <select name="filter[sex]" class="form-control">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <select name="filter[country]" class="form-control">
                        <option value="">All Countries</option>
                        @if(isset($countries))
                            @foreach($countries as $val)
                                <option value="{{ isset($val->title) ? $val->title : ""}}">{{ isset($val->title) ? $val->title : ""}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-4">
                    <select name="filter[age]" id="filter[age]" class="form-control">
                        <option value="">All Ages</option>
                        @if(isset($ageRanges))
                            @foreach($ageRanges as $key => $val)
                                <option value="{{ isset($key) ? $key : ""}}">{{ isset($val) ? $val : ""}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="usersContainer">
    @include('store.users.list_partial')
</div>
@stop