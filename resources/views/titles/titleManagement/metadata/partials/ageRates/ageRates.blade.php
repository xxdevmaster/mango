<?
$select = '';
?>
<form id="ageRatesForm" role="form">
	@if(isset($metadata['ageRates']['ageRate']))
		@foreach($metadata['ageRates']['ageRate'] as $value)
			<div class="well ageRatesWell">
				<div class="row">
					<p class="col-lg-5">{{ $value[0]->countryTitle }}</p>
					<div class="cInputs col-lg-7">
						<select class="form-control" name="ageRates[{{ $value[0]->id }}]">
							<option value="0" {{ $select }}>Select Rating </option>
							@if(isset($value))
								@foreach($value as $v)
									@if(in_array($v->id, $metadata['ageRates']['filmRates']))
										<?php
											$select = 'selected';
										?>
									@else
										<?php
											$select = '';
										?>
									@endif
									<option value="{{$v->id}}" <?php echo $select;?>>{{ $v->code }} - {{ $v->title }}</option>
								@endforeach
							@endif
						</select>
					</div>
				</div>
			</div>
		@endforeach
	@endif
</form>