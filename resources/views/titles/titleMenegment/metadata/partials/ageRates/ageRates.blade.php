<?
$select = '';
?>
<div class="miniwell countries">
    <form name="ageRates" id="ageRates">
        <input type="hidden" name="films_id" id="films_id" value="2505">
        <input type="hidden" name="act" value="saveAgeRates">
		@if(isset($metadata['ageRates']['ageRate']))
			@foreach($metadata['ageRates']['ageRate'] as $key => $value)
				@foreach($value as $v)
					<?php
						//if(in_array($v->id,$metadata['ageRates']['filmRates']))
							//echo 100000;
					?>
				@endforeach
				<div class="well ageRatesWell">
					<div class="row">
						<p class="col-lg-5">{{ $value[0]->countryTitle }}</p>
						<div class="cInputs col-lg-7">
							<select class="form-control" name="ar[3]" id="ar[3]">
								<option value="0" {{ $select }}>Select Rating </option>
								@foreach($value as $v)
									@if(in_array($v->id,$metadata['ageRates']['filmRates']))
										<?php
											$select = 'selected';
										?>
									@endif									
									<option value="{{$v->id}}" <?php echo $select;?>>{{ $v->code }} - {{ $v->title }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			@endforeach
		@endif
    </form>
</div>