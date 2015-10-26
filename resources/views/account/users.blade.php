@extends('layout')


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Account Users & Rights</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="">
                        @include('account.partials.userslist')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop