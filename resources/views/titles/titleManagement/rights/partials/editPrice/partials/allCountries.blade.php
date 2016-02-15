<ul class="nav nav-tabs" id="countriesTabs">
    <li class="active" id="rentPriceLi" onclick="ChangeRentBuy('rent');event.preventDefault();event.stopPropagation();">
        <a href="#tabRentPrice" data-toggle="tab">Rent Price</a>
    </li>
    <li id="buyPriceLi" onclick="ChangeRentBuy('buy');event.preventDefault();event.stopPropagation();">
        <a href="#tabBuyPrice" data-toggle="tab">Buy Price</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade in active" id="tabRentPrice">
        <div class="table-responsive">
            <form name="contriesPrices" id="contriesPrices" role="form">
                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <th>Country name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>USD</th>
                        <th>National</th>
                        <th>=USD</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($countries))
                        @foreach($countries as $val)
                            <tr>
                                <td>
                                    {{ $val->title }}
                                </td>                                
                                <td>
                                    <input class="dt hasDatepicker" type="text" value="{{ Carbon\Carbon::parse($val->start_date)->format('Y-m-d') }}" name="item[{{$val->id}}][start]" id="item_{{$val->id}}_start">
                                </td>
                                <td>
                                    <input class="dt hasDatepicker" type="text" value="{{ Carbon\Carbon::parse($val->end_date)->format('Y-m-d') }}" name="item[{{$val->id}}][end]" id="item_{{$val->id}}_end">
                                </td>
                                <td class="rentPrices">
									<input type="hidden" name="ccode" value="{{$val->currency_code}}" class="ccode">
                                    <input class="rentprice  rent_price_nominal" type="text" value="{{ $val->rent_price_nominal}}" name="item[{{$val->id}}][rent_price_nominal]">
                                </td>
                                <td class="rentPrices">
                                    <input class="rentprice  rent_price_national" type="text" value="{{$val->rent_price_national}}" name="item[{{$val->id}}][rent_price_national]">
                                </td>
                                <td class="rentPrices">
                                    <input class="rentprice  rent_price" type="text" value="{{$val->rent_price}}" name="item[{{$val->id}}][rent_price]" readonly="readonly">
                                </td>
                                <td class="buyPrices priceHide">
                                    <input class="buyprice buy_price_nominal" type="text" value="{{$val->buy_price_nominal}}" name="item[{{$val->id}}][buy_price_nominal]">
                                </td>
                                <td class="buyPrices priceHide">
                                    <input class="buyprice buy_price_national" type="text" value="{{$val->buy_price_national}}" name="item[{{$val->id}}][buy_price_national]">
                                </td>
                                <td class="buyPrices priceHide">
                                    <input class="buyprice buy_price" type="text" value="{{$val->buy_price}}" name="item[{{$val->id}}][buy_price]" readonly="readonly">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm sr" data-id="{{$val->id}}">
                                        <span class="glyphicon glyphicon-floppy-saved cp"></span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <div class="tab-pane fade" id="tabBuyPrice"></div>
</div>
<script>
$(document).ready(function(){
    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.dt').datepicker();
});

$('.sr').click(function(){
	autoCloseMsgHide();
	var event = $(this);
	$(this).children('span').removeClass('glyphicon-floppy-saved');
	$(this).children('span').addClass('ion-loading-a');
	var id = $(this).data('id');
	var valuesArray = {} ;
	$(this).parent().siblings().children('input').each(function(m){
		valuesArray[$(this).attr('name')] = $(this).val();
	});
	valuesArray['id'] = id;
	$.post('/titles/rights/saveCountryItem', valuesArray, function(data){
		if(data.error == 0){
			autoCloseMsg(0, data.message, 5000);
			event.children('span').removeClass('ion-loading-a');
			event.children('span').addClass('glyphicon-floppy-saved');
		}else{
			autoCloseMsg(1, data.message, 5000);
			event.children('span').removeClass('ion-loading-a');
			event.children('span').addClass('glyphicon-floppy-saved');
		}
	});
});
</script>