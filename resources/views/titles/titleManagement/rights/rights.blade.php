@extends('titles.titleManagement.titleManagement')
@section('titleManagement')
	<script>
		var PricesTabsToOpen = "rent";
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
				var cprice = parseFloat(rates[ccode.val()]);
				if (cprice != NaN || cprice != 0)
					realPrice = pfc/cprice;


				pReal.val( parseFloat(realPrice).toFixed(2));


			}


		}

	</script>

	<div id="contentCPPL">
		@include('titles.titleManagement.rights.partials.changeStore')
	</div>

	<script>
		function changeCPPL(type,filmId){
			$.post('/titles/rights/getChangeCPPL', {type:type, filmId:filmId}, function(response){
				if(!response.error){
					$("#contentCPPL").html('&#65279;'+response);
				}else
					autoCloseMsg(1, response.message, 5000);
			});
		}

		
		//$(document).ready(function(){
			$(document).on('click', '#saveChanges', function(){
				autoCloseMsgHide();
				var thisEllement = $(this);
				thisEllement.html('Saving...');	
				var rentalInfoForm = $('#rentalInfoForm').serialize();

				$('.loading').show();
					$.when(
						$.ajax({
							type: 'POST',
							url: '/titles/rights/saveRentalInfo',
							data: rentalInfoForm,
						})
					).done(function(){
						$('.loading').hide();
						thisEllement.html('Save Changes');
					}).fail(function(){
						$('.loading').hide();
						thisEllement.html('Save Changes');
						autoCloseMsg(1,'Bad Request',5000);	
					});		
			});
			
			$('form').submit(function(e){
				e.preventDefault();
			});
		//});		
	
	</script>



@stop