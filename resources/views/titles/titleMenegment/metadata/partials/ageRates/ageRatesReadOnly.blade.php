<?
$select = '';
?>
<div class="miniwell countries">
    <div>
		@if(isset($metadata['ageRates']['ageRate']))
			@foreach($metadata['ageRates']['ageRate'] as $key => $value)
				<div class="well ageRatesWell">
					<div class="row">
						<p class="col-lg-5">{{ $value[0]->countryTitle }}</p>
						<div class="cInputs col-lg-7">
							<div class="form-control readOnly">
								@foreach($value as $v)
									@if(in_array($v->countryId,$metadata['ageRates']['filmRates']))
										<?php
											$select = 'selected="selected"';
										?>
										<span>{{ $v->title }}</span>
										<?php return;?>
									@endif								
								@endforeach
								<span>No Information</span>
							</div>
						</div>
					</div>
				</div>
			@endforeach
		@endif
    </div>
</div>