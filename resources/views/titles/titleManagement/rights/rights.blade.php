@extends('titles.titleManagement.titleManagement')
@section('titleManagement')
<script>
	$('form').submit(function(e){
		e.preventDefault();
	});
</script>
<div id="contentCPPL">
	@if($rightsPermission['action'] === 'CPPL' || $rightsPermission['action'] === 'PL')
		@include('titles.titleManagement.rights.partials.changeCp.changeCp')
	@elseif($rightsPermission['action'] === 'CP')
		@include('titles.titleManagement.rights.partials.changeCp.changeCp')
	@endif
</div>
<button type="button" class="btn btn-success" id="saveChanges">Save Changes</button>
	<script>
		$(document).on('click', '#saveChanges', function(){
			autoCloseMsgHide();
			$('.loading').show();
			var thisEllement = $(this);
			thisEllement.html('Saving...');

			var rentalInfoForm = $('#rentalInfoForm').serialize();
			var contriesPrices = $('#contriesPrices').serialize();
			var channelDeal    = $('#channelDeal').serialize();


			var targetList = $(".target > option").map(function() {
				var arr = [];
				arr.push({value:$(this).val(), content:$(this).text()});
				console.log(arr);
				return arr;
			}).get();
			$.when(
					$.ajax({
						type: 'POST',
						url: '/titles/rights/saveDealsCountriesPL',
						data: {targetList:targetList, cpid:cpid},
					}),
					$.ajax({
						type: 'POST',
						url: '/titles/rights/saveContriesPrices',
						data: contriesPrices ,
					}),
					$.ajax({
						type: 'POST',
						url: '/titles/rights/saveContractSharePL',
						data: channelDeal ,
					}),
					$.ajax({
						type: 'POST',
						url: '/titles/rights/saveRentalInfo',
						data: rentalInfoForm+'&cpid='+cpid,
					})
			).done(function(resposne){
						$.post('/titles/rights/drawCountries', {cpid:cpid}, function(data){
							$("#allCountries").html(data);
							$('.loading').hide();
							thisEllement.html('Save Changes');
							autoCloseMsg(0,'Saved Successfully',5000);
						});
					}).fail(function(){
						$('.loading').hide();
						thisEllement.html('Save Changes');
						autoCloseMsg(1,'Bad Request',5000);
					});
		});
	@if($rightsPermission['action'] === 'CPPL')
		function changeCPPL(type,filmId){
			$.post('/titles/rights/getChangeCPPL', {type:type, filmId:filmId}, function(response){
				if(!response.error){
					$("#contentCPPL").html('&#65279;'+response);
				}else
					autoCloseMsg(1, response.message, 5000);
			});
		}
	@endif
	</script>
@stop