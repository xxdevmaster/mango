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
						@if(isset($geoTemplates))
							<div class="panel  w318">
								<select class="form-control" id="geoTemplates">
									@foreach($geoTemplates as $k => $v)
										<option value="{{$k}}">{{ $v->title }}</option>																										
									@endforeach
								</select>
							</div>
						@endif

							<form name="addCountries" id="addCountries" class="addCountriesPL" role="form">
								<input type="hidden" name="deal_id" value=""/>
								<input type="hidden" name="film_id" value=""/>
								<input type="hidden" name="act" value="load-new-geoTemplate_PL"/>
								<div id="TransferContainer"></div>
							</form>
						
					
						
							<script>
								$(function() {
									var t = $('#TransferContainer').bootstrapTransfer(
										{'target_id': 'multi-select-input',
										 'height': '15em',
										 'hilite_selection': true});
									
									t.populate([
										".$JsArrays['remaining']."
									],
									[
										".$JsArrays['target']."
									]
									);
									
									t.populate();
									//t.set_values(['2', '4']);
									//console.log(t.get_values());
								});
							</script>
					</div>
                    <div class="tab-pane" id="tab-countriesPrices"></div>
                    <div class="tab-pane" id="tab-contractsShares"></div>
                </div>

            <script>
            $(function(){
            $("#geoTemplates").on("change", function() {
                var targetList = $(".target > option").map(function() {
                     var arr = [];
                     arr.push({value:$(this).val(), content:$(this).text()});
                     return arr;
                 }).get();
                loadNewGeotemplate($(this).val(),targetList);
            });
            
            
$("#GeoPricingCPContent li a").click(
  function(e){
    tabToOpenAfterReload = $(this).attr("class");
  }
);



});
        </script>