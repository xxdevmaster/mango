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