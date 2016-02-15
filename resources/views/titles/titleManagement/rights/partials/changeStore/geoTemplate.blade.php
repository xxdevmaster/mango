<div class="DeAttachContentProvider">
    <div class="form-group">
        <button  class="btn btn-primary btn-xs " onclick="DeAttachContentProvider()">
            - Deattach Content Provider
        </button>
    </div>
</div>
<ul class="nav nav-tabs dealTabs">
    <li class="active"><a href="#tab-addCountries" data-toggle="tab" class="addCountries" >Manage Regions</a></li>
    <li><a href="#tab-countriesPrices" data-toggle="tab" class="countriesPrices">Edit Prices</a></li>
    <li><a href="#tab-contractsShares" data-toggle="tab" class="contractsShares">Revenue Sharing</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="tab-addCountries">
        @include('titles.titleManagement.rights.partials.manageRegions')
    </div>
    <div class="tab-pane" id="tab-countriesPrices">
        @include('titles.titleManagement.rights.partials.editPrice.editPrice')
    </div>
    <div class="tab-pane" id="tab-contractsShares">
        @include('titles.titleManagement.rights.partials.changeStore.revenueSharing')
    </div>
</div>

<script>
$(function(){
	$("#geoTemplates").on("change", function() {
		var targetList = $(".target > option").map(function() {
			var arr = [];
			arr.push({value:$(this).val(), content:$(this).text()});
			return arr;
		}).get();
		loadNewGeoTemplate($(this).val(),targetList);
	});
	
	$("#GeoPricingCPContent li a").click(
		function(e){
			tabToOpenAfterReload = $(this).attr("class");
		}
	);
});
</script>