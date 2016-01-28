<div id="CPPLEditor">
    <div class="panel panel-default">
        <div class="panel-heading" id="actionType">
            You are now acting as a <span class="proxBold"><b>Store</b></span> - <a class="text-primary cp" onclick="changeCPPL('CP', {{$film->id}})">Change to Content Provider</a>.
        </div>
    </div>
</div>

<div id="DealContent">
    <input type="hidden" name="pid" value="deals">
    <ul class="nav nav-tabs dealTabs">
        <li class="active"><a href="#tab-basic" data-toggle="tab" class="basic">Rental Information</a></li>
        <li><a href="#tab-geo-price" data-toggle="tab" class="basic">Geo-blocking &amp; Pricing</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-basic">
			<div class="row">
                <form id="rentalInfoForm" role="form">
                    <div class="form-group rentalDur">
                        <label class="col-md-2" for="lease_duration">Rental Duration</label>
                        <div class="col-md-2">
							<input type="text" class="form-control col-md-1" id="lease_duration" name="lease_duration" placeholder="" value="{{ $film->lease_duration }}">
                            <span>Hours</span>
                        </div>
                    </div>
                    <input type="hidden" name="filmId" value="{{ $film->id }}">
                </form>
            </div>
		</div>
        <div class="tab-pane" id="tab-geo-price">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form name="geoSharing" id="geoSharing">
						<div class="checkbox">
							<input type="checkbox" class="" name="film_geo" style="display: block; float: left; margin-right: 10px;">
						</div>
                        <input type="hidden" name="filmId" value="{{$film->id}}">
                        <p style="display: block; float: left;">Show this title in territories where rental and/or purchase are not available (N/A will be shown instead of price info)</p>
                    </form>
                </div>
            </div>




            <div class="AttachContentProvider">
                <div class="form-group">
                    <button class="btn btn-primary btn-xs " data-toggle="modal" data-target="#AttachContentProvider">
                        + Attach Content Provider
                    </button>
                </div>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="AttachContentProvider" tabindex="-1" role="dialog" aria-labelledby="AttachContentProvider" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="addNewTitleModalLabel">Attach Content Provider</h4>
                        </div>
                        <div class="modal-body">
						    <div class="form-group">
								<label for="input-genre">Content Provider</label>
								<input type="text" id="input-genre" name="inputToken" value="" />
								<script type="text/javascript">
									$(document).ready(function() {
										$("#input-genre").tokenInput("/titles/rights/getCP", {
											theme: "facebook",
											tokenFormatter:function(item){ return '<li><input type="hidden" name="cp['+item.id+']" /><p>' + item.title + '</p></li>' },
										});
									});
								</script>
							</div>
                            <div id="cpAddNew" style="display:none;">
                                <div class="form-group">
                                    <label for="form-title">Content Provider Name</label>
                                    <input type="text" placeholder="" name="cpname" id="cpname" class="form-control">
                                </div>
                                <button class="btn-success btn cpNameAddSave" type="button">+ Add</button>
                                <button class="btn-success btn cancelCPAdd" type="button">Cancel</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success  btn-sm cpAttach">+ Attach</button>
                            <button class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>




            <div id="GeoPricing">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-4">
								<select name="film_CPs" id="film_CPs" class="film_CPs form-control">
									<option value="" selected="selected">Select Content Provider</option>
									<option value="378">N1</option>
								</select>
							</div>
                        </div>
                    </div>
                </div>
            </div>
			
            <div id="GeoPricingCPContent">
				
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-success" id="saveChanges">Save Changes</button>


    <script>
        $('#pagetitle').html('VaterMark / Rights');

        var deal_id = "4253";
        var film_id = "{{ $film->id }}";
        var filmId = "{{ $film->id }}";
        var tabToOpenAfterReload = "addCountries";

        $(function(){
            $("#start-date").datepicker();
            $("#end_date").datepicker();
            $("#content-providers-tokens").tokenInput("engine.php?act=tokens-get-cps", {
                theme: "facebook",
                tokenLimit:1,
                onAdd: function(item){
                    if(item.id == "-1"){
                        $("#cpAddToken").hide();
                        $("#cpAddNew").show();
                        $(this).tokenInput("clear");
                    }
                    $(".token-input-list-facebook").animate({borderColor:"green"},50,function(){$(this).css("borderColor","#8496ba")});
                }
            });

            $(".cancelCPAdd").click(function(){
                $("#cpname").val();
                $("#cpAddToken").show();
                $("#cpAddNew").hide();
            });

            $(".cpNameAddSave").click(function(){

                $("#content-providers-tokens").val();
                var cpname = $("#cpname").val();
                $.ajax({
                    type: "POST",
                    url: "engine.php",
                    data: "act=AddNewCPNamePL&cpname="+cpname,
                    dataType: "json",
                    success: function (data) {
                        $("#content-providers-tokens").tokenInput("add", {id: data, name: cpname});
                        $("#cpname").val();
                        $("#cpAddToken").show();
                        $("#cpAddNew").hide();
                    }
                });

            });
            $(".cpAttach").click(function(){
                var cpid = $("#content-providers-tokens").val();
                var cpname = $(".token-input-token-facebook p").text();
                $.ajax({
                    type: "POST",
                    url: "engine.php",
                    data: "act=attachCPToTitle&cpid="+cpid+"&film_id="+film_id,
                    dataType: "json",
                    success: function (data) {
                        $("#content-providers-tokens").tokenInput("clear");
                        $("#film_CPs").append("<option value=\""+cpid+"\">"+cpname+"</option>");
                        $("#AttachContentProvider").modal("hide");
                    }
                });
            });
        });

        function loadNewGeotemplate(geoId,targetList)
        {
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: $("#addCountries").serialize()+"&targetList="+encodeURIComponent(JSON.stringify(targetList))+"&geoId="+geoId,
                dataType: "json",
                success: function (data) {
                    $("#TransferContainer").html("");
                    $(function() {
                        var t = $("#TransferContainer").bootstrapTransfer(
                                {"target_id": "multi-select-input",
                                    "height": "15em",
                                    "hilite_selection": true});

                        t.populate(
                                (data && data.remaining) || [],
                                (data && data.target) || []
                        );
                    });
                }
            });
        }
        function DeAttachContentProvider(cpid,film_id)
        {
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: "act=DeAttachContentProvider&cpid="+cpid+"&film_id="+film_id,
                dataType: "json",
                success: function (data) {
                    $("option[value=\"\"]").prop("selected",true);
                    $("option[value=\""+cpid+"\"]").remove();
                    $("#GeoPricingCPContent").html("");
                }
            });
        }
        $( "#film_CPs" ).change(function() {
            reloadCountriesEdit();

        });
        function reloadCountriesEdit(){
            var cpid = $("#film_CPs").val();
            if(cpid!=""){
				$.post("/titles/rights/loadCpCounties", {cpid:cpid, filmId:filmId}, function(resposne){
					$("#GeoPricingCPContent").html(resposne);

					$( "#GeoPricingCPContent li").removeClass("active");
					$( "#GeoPricingCPContent .tab-content div").removeClass("active");

					$("."+tabToOpenAfterReload).parent().addClass("active");
					$("#tab-"+tabToOpenAfterReload).addClass("active");

					$(".save-deal").text("SAVE CHANGES");
					ChangeRentBuy(PricesTabsToOpen);					
				});
            }
            else
                $("#GeoPricingCPContent").html("");
        }
		
        function changeShareType(){
            var type = $( "#share_type option:selected" ).val();
            if (type == 0){
                $("#share_fee").attr({ "disabled": "disabled" });
                $("#share_pl").removeAttr("disabled");
            }else if (type == 1){
                $("#share_fee").removeAttr("disabled");
                $("#share_pl").attr({ "disabled": "disabled" });
            }else if(type == 2){
                $("#share_fee").removeAttr("disabled");
                $("#share_pl").removeAttr("disabled");
            }
            return false;
        }


        function saveDealsCountries(){

            $(".filter-input").val("");
            $(".filter-input-target").val("");
            $(".glyphicon-search").trigger("click");
            var cpid = $("#film_CPs").val();
            if (cpid != ""){
                var targetList = $(".target > option").map(function() {
                    var arr = [];
                    arr.push({value:$(this).val(), content:$(this).text()});
                    return arr;
                }).get();
                return  $.ajax({
                    type: "POST",
                    url: "engine.php",
                    data: "targetList="+encodeURIComponent(JSON.stringify(targetList))+"&film_id="+film_id+"&deal_id="+deal_id+"&act=save-deals-countriesPL&film_id="+film_id+"&cpid="+cpid,
                    success: function (data) {
                        loadNewGeotemplate(1,targetList);
                    }
                });
            }
            else
                return  $.ajax({type: "POST",url: "engine.php",data: "act=buttonTextTrue"});


        }
        function saveCountriesPrices() {
            return $.ajax({type: "POST",url: "engine.php",data:  $("#contriesPrices").serialize()});
        }
        function saveCountryItem(id) {
            return $.ajax({type: "POST",url: "engine.php",data:  $("#contriesPrices").serialize()+"&item_id="+id});
        }

        function saveDealInfo() {return $.ajax({type: "POST",url: "engine.php",data:  $("#dealInfo").serialize()});}

        function channelDeal() {return $.ajax({type: "POST",url: "engine.php", data:  $("#channelDeal").serialize()}); }


        // $("#saveChanges").click(function(){
            // $(this).text("Saving...");
            // $.when(
				// $.ajax({
					// type: "POST",
					// url: "engine.php",
					// data: "act=saveGeoSharing&"+$("#geoSharing").serialize(),
				// }),
				// saveDealInfo(),
				// saveDealsCountries(),
				// saveCountriesPrices(),
				// channelDeal()
            // ).done(function(){
                        // $(".save-deal").text("SAVE CHANGES");
                        // reloadCountriesEdit();
                    // }).fail(function(){
                        // $(".save-deal").text("SAVE CHANGES");
                    // }).always(function(){
                        // $(".save-deal").text("SAVE CHANGES");
                    // });
					
			
        // });

        $(".alldate").click(function(){
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: "start_date="+start_date+"&end_date="+end_date+"&act=alldatePL&deal_id="+deal_id+"&film_id="+film_id ,
                dataType: "json",
                success: function (data) {
                    saveDealInfo();
                    reloadAll("basic");
                }
            });


        });
        $(".allrentprice").click(function(){
            var rent_price = $("#rent_price").val();
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: "rent_price="+rent_price+"&act=allrentprice_PL&deal_id="+deal_id+"&film_id="+film_id ,
                dataType: "json",
                success: function (data) {
                    saveDealInfo();
                    reloadCountriesEdit();
                }
            });


        });
        $(".allbuyprice").click(function(){
            var buy_price = $("#buy_price").val();
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: "buy_price="+buy_price+"&act=allbuyprice_PL&deal_id="+deal_id+"&film_id="+film_id ,
                dataType: "json",
                success: function (data) {
                    saveDealInfo();
                    reloadCountriesEdit();
                }
            });


        });
        function ChangeRentBuy(type){
            $("#rentpriceHeader").removeClass("act");
            $("#buypriceHeader").removeClass("act");
            $("#"+type+"priceHeader").addClass("act");

            $(".rentprice").removeClass("priceHide");
            $(".buyprice").removeClass("priceHide");
            if (type == "buy")
                $(".rentprice").addClass("priceHide");
            else
                $(".buyprice").addClass("priceHide");
        }
    </script>

</div>