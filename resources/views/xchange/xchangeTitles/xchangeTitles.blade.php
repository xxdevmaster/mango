@extends('layout')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{isset($current_menu) ? $current_menu : ''}} </h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-color panel-inverse">
                <div class="panel-heading">
                    <h3 class="panel-title">Titles Filter </h3>
                </div>
                <div class="panel-body">
                    <form id="titlesFilter">
                        <div class="form-group row">
                            <div class="col-lg-12" style="margin-top:30px;">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Title" value="" name="filter[searchWord]">
                                     <span class="input-group-btn">
                                        <button type="button" class="btn btn-effect-ripple btn-primary" id="titleSearch">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <select name="filter[vaultStatus]" class="form-control filter_select">
                                    <option value="0">All Titles in Xchange</option>
                                    <option value="1">Included in My Store</option>
                                    <option value="2">Not Included in My Store</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                @if(isset($companies))
                                    <select name="filter[pl]" id="filter[pl]" class="form-control filter_select">
                                        <option value="" selected="selected">Content Providers</option>
                                        @foreach($companies as $val => $key)
                                            <option value="{{$key->id}}">{{ $key->title  }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="topPager" class="text-right">
        {!! $paginator->render() !!}
    </div>
    <form id="vaultBulkForm" onsubmit="return false" class="panel panel-default">
        <div id="VaultCPContainer">
            <table class="table" id="platformsRows">
                <tr>
                    <td class="bulkAct" style="width:20px;">
                        <div class="dropdown pull-left">
                            <input type="checkbox" id="bulkActCheckbox">
                            <a id="bulkActPopup" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                <span class="caret"></span>
                            </a>
                            <ul aria-labelledby="bulkActPopup" role="menu" class="dropdown-menu">
                                <li role="presentation"><a class=" cp" tabindex="-1" role="menuitem" rollapp-href="" id="bulkActAddToStore">Add to My Store</a></li>
                                <li role="presentation"><a class=" cp" tabindex="-1" role="menuitem" rollapp-href="" id="bulkActDeleteFromStore">Remove from My Store</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>Poster</td>
                    <td>
                        <a class="cp pull-left" rel="id">ID</a>
                        <span class=" pull-left AscDescIcon"></span>
                    </td>
                    <td>
                        <a class="cp pull-left" rel="title" >Title</a>
                        <span class="pull-left AscDescIcon "></span>
                    </td>
                    <td>Stores</td>
                    <td>Xchange</td>
                    <td></td>
                </tr>
                <tbody id="listContent">
                    @include('xchange.xchangeTitles.list_partial')
                </tbody>
            </table>
        </div>
    </form>
    <div id="bottomPager" class="text-right">
        {!! $paginator->render() !!}
    </div>
    <script>
        $( document ).ready(function() {
            $(".TF").tooltip();
            $(".tip").tooltip();
            setClickToPager();
            setClickToUpDouwn();
            setClickToBulkAct();
            //setClickToSoloAct();
        });
    </script>

    <script>
        function titlesFilter(){
            $('.loading').show();
            $("#ordertype").val("ASC");

            var titlesFilter = $('#titlesFilter').serialize();

            $.post('/titles/titlesFilter', titlesFilter, function(response){
                if(response.error)
                    $('#allTitles').html('<h3 class="text-center text-danger">' + response.message + '</h3>');
                else
                    $('#allTitles').html(response);
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

        $(".filter_select").change(function(){
            titlesFilter();
        });
    </script>



    <script>

        function setClickToPager(){
            $( ".VaultPager" ).click(function(){
                var rel = $(this).attr("rel");
                if(rel>-1){
                    $.ajax({
                        type: "POST",
                        url: "classes/Vault/engine.php",
                        data: $("#vaultFilter").serialize()+"&act=get_vault&page="+rel,
                        dataType: "json",
                        success: function (data) {
                            $( "#VaultContainer" ).html(data);
                        }
                    });
                }
                return false;
            });
        }
        $("#pagetitle").html("Xchange");



        function setClickToUpDouwn(){

            $( ".orderASC" ).click(function(){
                var rel = $(this).attr("rel");
                $.ajax({
                    type: "POST",
                    url: "classes/Vault/engine.php",
                    data: $("#vaultFilter").serialize()+"&filter[ordertype]=ASC&filter[order]="+rel+"&act=get_vault&page=0",
                    dataType: "json",
                    success: function (data) {
                        $( "#VaultContainer" ).html(data);
                        $("#ordertype").val("ASC");
                        $("#order").val(rel);
                    }
                });
                return false;
            });
            $( ".orderDESC" ).click(function(){
                var rel = $(this).attr("rel");

                $.ajax({
                    type: "POST",
                    url: "classes/Vault/engine.php",
                    data: $("#vaultFilter").serialize()+"&filter[ordertype]=DESC&filter[order]="+rel+"&act=get_vault&page=0",
                    dataType: "json",
                    success: function (data) {
                        $( "#VaultContainer" ).html(data);
                        $("#ordertype").val("DESC");
                        $("#order").val(rel);
                    }
                });

                return false;
            });


        }
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
        function setClickToBulkAct(){

            $( "#bulkActCheckbox" ).click(function(){
                var actType = $(this).prop("checked");
                $(".itemCheckbox").prop("checked",actType);

            });
			
            $( "#bulkActAddToStore" ).click(function(){
				autoCloseMsgHide();
				$(".loading").show();				
                $.post('/xchange/bulkActAddToStore', $("#vaultBulkForm").serialize(), function(data){
					$('#listContent').html(data);
                    $("#bulkActCheckbox").prop('checked', false);
					$(".loading").hide();
                });
            });
			
			
            $( "#bulkActDeleteFromStore" ).click(function(){
				bootbox.confirm('You are about to remove the selected titles from your store. Are you sure?', function(result) {
					if(result) {
						autoCloseMsgHide();
						$(".loading").show();						
						$.post('/xchange/bulkActDeleteFromStore', $("#vaultBulkForm").serialize(), function(data){
							$('#listContent').html(data);
                            $("#bulkActCheckbox").prop('checked', false);
							$(".loading").hide();
						});						
					}
				});	
            });		


            $( ".itemCheckbox" ).click(function(){
                var actType = $(this).prop("checked");
                if (actType==true){
                    $("#bulkActCheckbox").prop("checked",true);
                    $(".itemCheckbox").each(function(){
                        if($(this).prop("checked")==false)
                            $("#bulkActCheckbox").prop("checked",false);
                    });

                }
                else
                    $("#bulkActCheckbox").prop("checked",false);
            });


        }
        function setClickToSoloAct(){

            $( ".soloActAddToStore" ).click(function(){
                var film_id = $(this).data("filmid");
                $.ajax({
                    type: "POST",
                    url: "classes/Vault/engine.php",
                    data: "act=soloActAddToStore&film_id="+film_id,
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        var curpage = $(".pgCurrent").data("curpage");
                        $.ajax({
                            type: "POST",
                            url: "classes/Vault/engine.php",
                            data: $("#vaultFilter").serialize()+"&act=get_vault&page="+curpage,
                            dataType: "json",
                            success: function (data) {
                                $( "#VaultContainer" ).html(data);
                            }
                        });
                    }
                });

            });
            $( ".soloActDeleteFromStore" ).click(function(){
                var film_id = $(this).data("filmid");
                $.ajax({
                    type: "POST",
                    url: "classes/Vault/engine.php",
                    data: "act=soloActDeleteFromStore&film_id="+film_id,
                    dataType: "json",
                    success: function (data) {
                        var curpage = $(".pgCurrent").data("curpage");
                        $.ajax({
                            type: "POST",
                            url: "classes/Vault/engine.php",
                            data: $("#vaultFilter").serialize()+"&act=get_vault&page="+curpage,
                            dataType: "json",
                            success: function (data) {
                                $( "#VaultContainer" ).html(data);
                            }
                        });
                    }
                });

            });

        }




        $( ".filter_select" ).change(function(){
            $("#ordertype").val("ASC");
            $.ajax({
                type: "POST",
                url: "classes/Vault/engine.php",
                data: $("#vaultFilter").serialize()+"&act=get_vault&page=0",
                dataType: "json",
                success: function (data) {
                    $( "#VaultContainer" ).html(data);
                }
            });

        });

        $('input[name="filter[search_word]"]').keyup(function(e) {
            if (e.keyCode==13){
                $("#ordertype").val("DESC");
                $.ajax({
                    type: "POST",
                    url: "classes/Vault/engine.php",
                    data: $("#vaultFilter").serialize()+"&act=get_vault&page=0",
                    dataType: "json",
                    success: function (data) {
                        $( "#VaultContainer" ).html(data);
                    }
                });
            }
        });
        $( ".filter_search" ).click(function(){
            $("#ordertype").val("DESC");
            $.ajax({
                type: "POST",
                url: "classes/Vault/engine.php",
                data: $("#vaultFilter").serialize()+"&act=get_vault&page=0",
                dataType: "json",
                success: function (data) {
                    $( "#VaultContainer" ).html(data);
                }
            });

        });

    </script>
@stop