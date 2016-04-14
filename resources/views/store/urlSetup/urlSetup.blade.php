@extends('layout')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">URL Setup Information</h3>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="media">
                <div class="media-body">
                    <h5 class="media-heading">In order to park your VOD platform on your URL of choice, you need to complete the following two steps:</h5>
                </div>
            </div>
            <hr>
            <div class="media">
                <div class="media-left media-middle">
                    <p class="w-80 text-primary m-l-20">Step 1</p>
                </div>
                <div class="media-body">
                    <p class="media-heading">
                        Log into the control panel of your DNS provider and point your domain's CNAME record(s) to <strong class="text-primary">cactus.cinehost.tv</strong>.
                    </p>
                </div>
            </div>
            <hr>
            <div class="media">
                <div class="media-left media-middle">
                    <p class="w-80 text-primary m-l-20">Step 2</p>
                </div>
                <div class="media-body">
                    <p class="media-heading">
                         If you need to use your root domain as well, create a record and point it to <strong class="text-primary">54.197.231.236</strong> IP address or create a domain forwarding to subdomain created in previous step.
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop