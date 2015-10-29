@extends('layout')


@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Account Features</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal">
                <div class="list-group">
                    @foreach($account_features as $feature)
                    <div class="list-group-item">
                        <h4 class="list-group-item-heading">{{ $feature->feature_name  }}</h4>
                        <p class="list-group-item-text">{{ $feature->feature_description  }}</p>

                        <div class="control-label">
                            @if($feature->status)
                            <button type="button" class="btn btn-success btn-sm m-b-5">Enable</button>
                            @else
                            <button type="button" class="btn btn-danger btn-sm m-b-5">Disable</button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                </form>
            </div> <!-- Panel-body -->
        </div> <!-- panel -->
    </div> <!-- col -->
</div>

@stop

@section('footer')
<script src="/assets/toggles/toggles.min.js"></script>

<script>
            jQuery(document).ready(function() {


                // Form Toggles
                jQuery('.toggle').toggles({
                });
            });
        </script>
@stop