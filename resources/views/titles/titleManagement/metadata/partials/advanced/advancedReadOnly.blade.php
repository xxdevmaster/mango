<div>
	<div class="form-group">
		<label for="form-dt">Year</label>
		<span class="form-control readOnly">{{ @isset($film->dt) ? $film->dt : '' }}</span>
	</div>
	<div class="form-group">
		<label for="duration">Duration (minutes)</label>
		<span class="form-control readOnly">{{ @isset($film->duration) ? $film->duration : '' }}</span>
	</div>
	<div class="form-group">
		<label>Genre</label>
		<span class="form-control readOnly">
			@if(isset($metadata['advanced']['filmGenres']))
				@foreach($metadata['advanced']['filmGenres'] as $genres)		
					<li class="token-input-token-facebook" style="list-style:none;">{{isset($genres->title) ? $genres->title : $genres->title}}</button>
				@endforeach
			@endif					
		</span>
	</div>		
	<div class="form-group">
		<label>Original Languages</label>
		<span class="form-control readOnly">
			@if(isset($metadata['advanced']['filmLanguages']))
				@foreach($metadata['advanced']['filmLanguages'] as $languages)		
					<li class="token-input-token-facebook" style="list-style:none;">{{isset($languages->title) ? $languages->title : $languages->title}}</button>
				@endforeach
			@endif					
		</span>
	</div>
	<div class="form-group">
		<label>Production companies</label>
		<span class="form-control readOnly">
			@if(isset($metadata['advanced']['filmProdCompanies']))
				@foreach($metadata['advanced']['filmProdCompanies'] as $prodCompanies)		
					<li class="token-input-token-facebook" style="list-style:none;">{{isset($prodCompanies->title) ? $prodCompanies->title : $prodCompanies->title}}</button>
				@endforeach
			@endif					
		</span>
	</div>
	<div class="form-group">
		<label>Countries</label>
		<span class="form-control readOnly">
			@if(isset($metadata['advanced']['filmCountries']))
				@foreach($metadata['advanced']['filmCountries'] as $countries)		
					<li class="token-input-token-facebook" style="list-style:none;">{{isset($countries->title) ? $countries->title : $countries->title}}</button>
				@endforeach
			@endif					
		</span>
	</div>
	<div class="form-group">
		<label>Comment (internal use)</label>
		<span class="form-control readOnly" style="height:174px;overflow-y:auto;">{{ @isset($film->admcomment) ? $film->admcomment : '' }}</span>
	</div>
</div>