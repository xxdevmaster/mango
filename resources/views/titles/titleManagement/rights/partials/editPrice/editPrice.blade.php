<h2 class="h2 text-muted" style="margin-top:0">Date & Price</h2>
<div class="form-group row">
    <label class="col-lg-2" for="price">Rent Price ($)</label>
    <div class="col-lg-3">
        <input type="text" id="rent_price" name="rent_price" class="form-control" value="">
    </div>
    <div class="col-lg-3">
        <button class="btn btn-default btn-sm allrentprice" type="button">Apply to All Countries</button>
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-2" for="buy_price_nominal">Buy Price ($)</label>
    <div class="col-lg-3">
        <input type="text" id="buy_price" name="buy_price" class="form-control" value="">
    </div>
    <div class="col-lg-3">
        <button class="btn btn-default btn-sm allbuyprice" type="button">Apply to All Countries</button>
    </div>
</div>
<hr>

<div class="form-group row">
	<label class="col-lg-2" for="start_date">Start Date</label>
	<div class="col-lg-3">
		<input type="text" id="start_date" name="start_date" class="form-control dt" value="">
	</div>
</div>
<div class="form-group row">
    <label class="col-lg-2" for="end_date">End Date</label>
	<div class="col-lg-3">
		<input type="text" id="end_date" name="end_date" class="form-control dt" value="">
	</div>
</div>
<div class="row">
    <div class="col-lg-5">
        <button class="btn btn-default btn-sm alldate  pull-right" type="button">Apply to All Countries</button>
    </div>
</div>
<hr>

<div id="allCountries">
	@include('titles.titleManagement.rights.partials.editPrice.partials.allCountries')
</div>
<script>
var cpid = $("#film_CPs").val();
function priceCalc(ccode,pNom,pNat,pReal){
	var realPrice = 0.00;
	var pfc = parseFloat(pNat.val());

	if (pfc == 0 || pfc == NaN ){
		pfc =  parseFloat(pNom.val());
		if (pfc == 0 || pfc == NaN)
			pReal.val(realPrice);
		else
			pReal.val(pfc);
	}
	else {
		var cprice = parseFloat(ccode.val());
		if (cprice != NaN || cprice != 0)
			realPrice = pfc/cprice;
		pReal.val( parseFloat(realPrice).toFixed(2));
	}
}
function ChangeRentBuy(type){
	if (type == "buy"){
		$("#rentPriceLi").removeClass("active");
		$("#buyPriceLi").addClass("active");
		$(".buyPrices").show();
		$(".rentPrices").removeClass('priceHide');
		$(".rentPrices").hide();
	}
	else{
		$("#rentPriceLi").addClass("active");
		$("#buyPriceLi").removeClass("active");
		$(".buyPrices").hide();
		$(".rentPrices").show();
	}
}

$(".allrentprice").click(function(){
	autoCloseMsgHide();
	$('.loading').show();
	var rentPrice = $("#rent_price").val();
	
	$.post('/titles/rights/allRentPrice', {rentPrice:rentPrice, cpid:cpid}, function(data){
		if(data.error == 0){
			$("#rent_price").val('');
			$("#allCountries").html(data.html);
			$('.loading').hide();
			autoCloseMsg(0, data.message, 5000);
		}else{
			$('.loading').hide();
			autoCloseMsg(1, data.message, 5000);
		}			
	});
});             

$(".allbuyprice").click(function(){
	autoCloseMsgHide();
	$('.loading').show();
	var buyPrice = $("#buy_price").val();

	$.post('/titles/rights/allBuyPrice', {buyPrice:buyPrice, cpid:cpid}, function(data){
		if(data.error == 0){
			$("#buy_price").val('');
			$("#allCountries").html(data.html);
			$('.loading').hide();
			autoCloseMsg(0, data.message, 5000);
		}else{
			$('.loading').hide();
			autoCloseMsg(1, data.message, 5000);
		}			
	});
});    

$(".alldate").click(function(){
	autoCloseMsgHide();
	$('.loading').show();
	var startDate = $("#start_date").val();
	var endDate = $("#end_date").val();

	if(startDate != '' && endDate != ''){
		$.post('/titles/rights/allDate', {startDate:startDate, endDate:endDate, cpid:cpid}, function(data){
			if(data.error == 0){
				$("#start_date").val('');
				$("#end_date").val('');
				$("#allCountries").html(data.html);
				$('.loading').hide();
				autoCloseMsg(0, data.message, 5000);
			}else{
				$('.loading').hide();
				autoCloseMsg(1, data.message, 5000);
			}
		});
	}else{
		$('.loading').hide();
		autoCloseMsg(1, 'Select Start Date And End Date', 5000);
	}
});

	$(function(){
		 $(".rent_price_nominal").keyup(function(){
			priceCalc($(this).parent().children(".ccode"), $(this).parent().children(".rent_price_nominal"), $(this).parent().siblings().children(".rent_price_national"),$(this).parent().siblings().children(".rent_price"));
		  });
		 $(".rent_price_national").keyup(function(){
			priceCalc($(this).parent().siblings().children(".ccode"),$(this).parent().siblings().children(".rent_price_nominal"),$(this).parent().children(".rent_price_national"),$(this).parent().siblings().children(".rent_price"));
		  });
		 $(".buy_price_national").keyup(function(){
			priceCalc($(this).parent().siblings().children(".ccode"),$(this).parent().siblings().children(".buy_price_nominal"),$(this).parent().children(".buy_price_national"),$(this).parent().siblings().children(".buy_price"));
		  });
		 $(".buy_price_nominal").keyup(function(){
			priceCalc($(this).parent().siblings().children(".ccode"),$(this).parent().children(".buy_price_nominal"),$(this).parent().siblings().children(".buy_price_national"),$(this).parent().siblings().children(".buy_price"));
		  });
	});
	$(function(){
		
		$( "#buypriceHeader" ).click(function() {
			PricesTabsToOpen = "buy";
		});
		$( "#rentpriceHeader" ).click(function() {
			PricesTabsToOpen = "rent";
		});
	});
            
</script>