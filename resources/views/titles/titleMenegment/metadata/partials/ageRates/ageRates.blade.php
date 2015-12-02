<?
$select = '';
?>
<div class="miniwell countries">
    <form name="ageRates" id="ageRates">
        <input type="hidden" name="films_id" id="films_id" value="2505">
        <input type="hidden" name="act" value="saveAgeRates">
		@if(isset($ageRates['ageRate']))
			@foreach($ageRates['ageRate'] as $key => $value)
				<div class="well ageRatesWell">
					<div class="row">
						<p class="col-lg-5">{{ $value[0]->countryTitle }}</p>
						<div class="cInputs col-lg-7">
							<select class="form-control" name="ar[3]" id="ar[3]">
								<option value="0" {{ $select }}>Select Rating </option>
								@foreach($value as $v)
									@if(in_array($v->countryId,$ageRates['filmRates']))
										<?php
											var_dump(11);
											$select = 'selected="selected"';
										?>
									@endif									
									<option value="">{{ $v->title }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			@endforeach
		@endif
    </form>
</div>