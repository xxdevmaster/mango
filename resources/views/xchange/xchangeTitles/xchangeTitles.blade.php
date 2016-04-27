@extends('layout')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Xchange</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-color panel-inverse">
                <div class="panel-heading">
                    <h3 class="panel-title">Xchange Filter</h3>
                </div>
                <div class="panel-body">
                    <form id="titlesFilter" autocomplete="off">
                        <div class="form-group row">
                            <div class="col-lg-12" style="margin-top:30px;">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Title" value="" name="filter[searchWord]">
                                     <span class="input-group-btn">
                                        <button type="submit" class="btn btn-effect-ripple btn-primary" id="titleSearch">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 m-t-10">
                                <select name="filter[vaultStatus]" class="form-control filterSelect">
                                    <option value="0">All Titles in Xchange</option>
                                    <option value="1">Included in My Store</option>
                                    <option value="2">Not Included in My Store</option>
                                </select>
                            </div>
                            <div class="col-lg-6 m-t-10">
                                @if(isset($companies))
                                    <select name="filter[pl]" id="filter[pl]" class="form-control filterSelect">
                                        <option value="" selected="selected">Content Providers</option>
                                        @foreach($companies as $companyID => $companyTitle)
                                            <option value="{{ $companyID }}">{{ $companyTitle }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="filter[order]" value="">
                        <input type="hidden" name="filter[orderType]" value="asc">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div id="xchangeTitles" class="panel-body">
            @include('xchange.xchangeTitles.list')
        </div>
    </div>
    <script>
        jQuery(document).ready(function() {
            jQuery(".selectBoxWithSearch").select2({
                width: '100%',
            });
        });

        function titlesFilter(){
            $('.loading').show();
            $("#ordertype").val("ASC");

            var titlesFilter = $('#titlesFilter').serialize();

            $.post('/xchange/titlesFilter', titlesFilter, function(response){
                if(response.error)
                    $('#xchangeTitles').html('<h3 class="text-center text-danger">' + response.message + '</h3>');
                else
                    $('#xchangeTitles').html(response);
                $('.loading').hide();
            });
        }

        $('#titlesFilter').submit(function(e){
            e.preventDefault();
            titlesFilter();
        });

        $('#titleSearch').click(function(){
            titlesFilter();
        });

        $(".filterSelect").change(function(){
            titlesFilter();
        });
    </script>

    <script>
        function setClickToDetails(){
            $( ".showCountries" ).click(function(){
                var rel = $(this).attr("rel");
                $.ajax({
                    type: "POST",
                    url: "classes/Vault/engine.php",
                    data: "act=showCountries&film_id="+rel,
                    dataType: "json",
                    success: function (data) {
                        $( "#dtls"+rel ).html(data);
                        $( "#dtls"+rel ).fadeToggle();
                    }
                });

                return false;
            });
        }
        function setClickToConnectDisconnectCP2PL(){
            $( ".connectCP2PL" ).click(function(){
                var rel = $(this).attr("rel");
                var film_id = $(this).data("filmid");
                $.ajax({
                    type: "POST",
                    url: "classes/Vault/engine.php",
                    data: "act=connectCP2PL&cp_id="+rel+"&film_id="+film_id,
                    dataType: "json",
                    success: function (data) {
                        $( "#dtls"+film_id ).html(data);
                    }
                });
                return false;
            });
            $( ".disconnectCP2PL" ).click(function(){
                if (confirm("You are about to deactivate the selected territories for this title in your your store. Are you sure? (Don’t worry, you can always activate them again later.)")) {
                    var rel = $(this).attr("rel");
                    var film_id = $(this).data("filmid");
                    $.ajax({
                        type: "POST",
                        url: "classes/Vault/engine.php",
                        data: "act=disconnectCP2PL&cp_id="+rel+"&film_id="+film_id,
                        dataType: "json",
                        success: function (data) {
                            $( "#dtls"+film_id ).html(data);
                        }
                    });
                }
                return false;
            });

        }
    </script>
@stop