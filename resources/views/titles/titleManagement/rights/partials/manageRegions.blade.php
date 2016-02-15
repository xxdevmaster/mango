@if(isset($geoTemplates))
    <div class="countriesBoxSiza">
        <select class="form-control" id="geoTemplates">
            @foreach($geoTemplates as $k => $v)
                <option value="{{$k}}">{{ $v->title }}</option>
            @endforeach
        </select>
    </div>
@endif
<form name="addCountries" id="addCountries" class="addCountriesPL" role="form">
    <input type="hidden" name="deal_id" value=""/>
    <div id="TransferContainer"></div>
</form>
<script>
$(function() {
	var t = $('#TransferContainer').bootstrapTransfer({
		'target_id': 'multi-select-input',
		'height': '15em',
		'hilite_selection': true
	});
	t.populate(
		{!! json_encode($converted['remaining']) !!},
		{!! json_encode($converted['target']) !!}
	);

	t.populate();
});
</script>