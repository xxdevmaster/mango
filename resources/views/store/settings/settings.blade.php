@extends('layout')
@section('content')
    <h1 class=""h1>Store Settings</h1>
    <div class="movie-box">
        <div class="panel panel-default">
            <div class=" panel-body ">
                Please enter information about your platform.
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body ">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#settings" data-toggle="tab">Store Settings</a>
                    </li>
                    <li><a href="#templates" data-toggle="tab">Store Templates</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active " id="settings">
                        @include('store.settings.settings_tab')
                    </div>
                    <div class="tab-pane fade" id="templates">
                        @include('store.settings.templates_tab')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

    </script>
@stop