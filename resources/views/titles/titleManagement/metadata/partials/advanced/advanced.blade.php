<form name="advancedForm" id="advancedForm" role="form">
    <input type="hidden" name="filmId" value="{{ @isset($film) ? $film->id : '' }}">
    <input type="hidden" name="" value="">
	<div class="form-group">
		<label for="form-dt">Year</label>
		<input type="text" class="form-control" id="form-dt" name="dt" value="{{ @isset($film->dt) ? $film->dt : '' }}">
	</div>
	<div class="form-group">
		<label for="duration">Duration (minutes)</label>
		<input type="text" class="form-control" id="duration" name="duration" value="{{ @isset($film->duration) ? $film->duration : '' }}">
	</div>
	<div class="form-group">
		<label for="input-genre">Genre</label>	
		<input type="text" id="input-genre" name="inputToken" value="" />
		<script type="text/javascript">
		$(document).ready(function() {
			$("#input-genre").tokenInput("{{url()}}/titles/metadata/advanced/getTokenGenres", {
				theme: "facebook",
				tokenFormatter:function(item){ return '<li><input type="hidden" name="genres['+item.id+']" /><p>' + item.title + '</p></li>' },
			});
			@if(isset($metadata['advanced']['filmGenres']))
				@foreach($metadata['advanced']['filmGenres'] as $genres)
				<?php
					if(isset($genres->id) && isset($genres->title)){
						$genresId = $genres->id;
						$genresTitle = $genres->title;
					}else{
						$genresId = '';
						$genresTitle = '';
					}				
					echo '$("#input-genre").tokenInput("add", {id: "'.$genresId.'", title: "'.$genresTitle.'"});';
				?>
				@endforeach
			@endif
		});
		</script>
	</div>	
	
	<div class="form-group">
		<label for="input-originalLang">Original Languages</label>
		<input type="text" id="input-originalLang" name="inputToken" value="" />
		<script type="text/javascript">
		$(document).ready(function() {
			$("#input-originalLang").tokenInput("{{url()}}/titles/metadata/advanced/getTokenOriginalLanguages", {
				theme: "facebook",
				tokenFormatter:function(item){ return '<li><input type="hidden" name="originalLanguages['+item.id+']" /><p>' + item.title + '</p></li>' }
			});
			@if(isset($metadata['advanced']['filmLanguages']))
				@foreach($metadata['advanced']['filmLanguages'] as $languages)
				<?php
					if(isset($languages->id) && isset($languages->title)){
						$languagesId = $languages->id;
						$languagesTitle = $languages->title;
					}else{
						$languagesId = '';
						$languagesTitle = '';
					}				
					echo '$("#input-originalLang").tokenInput("add", {id: "'.$languagesId.'", title: "'.$languagesTitle.'"});';
				?>
				@endforeach
			@endif
		});
		</script>
	</div>
	<div class="form-group">
		<label for="input-productCompanies">Production companies</label>
		<input type="text" id="input-productCompanies" name="inputToken" value="" />
		<script type="text/javascript">
		$(document).ready(function() {
			$("#input-productCompanies").tokenInput("{{url()}}/titles/metadata/advanced/getTokenProdCompanies", {
				theme: "facebook",
				tokenFormatter:function(item){ return '<li><input type="hidden" name="productCompanies['+item.id+']" /><p>' + item.title + '</p></li>' }
			});
			@if(isset($metadata['advanced']['filmProdCompanies']))
				@foreach($metadata['advanced']['filmProdCompanies'] as $prodCompanies)
				<?php
					if(isset($prodCompanies->id) && isset($prodCompanies->title)){
						$prodCompaniesId = $prodCompanies->id;
						$prodCompaniesTitle = $prodCompanies->title;
					}else{
						$prodCompaniesId = '';
						$prodCompaniesTitle = '';
					} 				
					echo '$("#input-productCompanies").tokenInput("add", {id: "'.$prodCompaniesId.'", title: "'.$prodCompaniesTitle.'"});';
				?>
				@endforeach
			@endif			
		});
		</script>
	</div>
	<div class="form-group">
		<label for="input-countries">Countries</label>
		<input type="text" id="input-countries" name="inputToken" value="" />
		<script type="text/javascript">
		$(document).ready(function() {
			$("#input-countries").tokenInput("{{url()}}/titles/metadata/advanced/getTokenCountries", {
				theme: "facebook",
				tokenFormatter:function(item){ return '<li><input type="hidden" name="countries['+item.id+']" /><p>' + item.title + '</p></li>' }
			});
			@if(isset($metadata['advanced']['filmCountries']))
				@foreach($metadata['advanced']['filmCountries'] as $countries)
				<?php
					if(isset($countries->id) && isset($countries->title)){
						$countriesId = $countries->id;
						$countriesTitle = $countries->title;
					}else{
						$countriesId = '';
						$countriesTitle = '';
					} 
					echo '$("#input-countries").tokenInput("add", {id: "'.$countriesId.'", title: "'.$countriesTitle.'"});';
				?>
				@endforeach
			@endif
		});
		</script>
	</div>
	<div class="form-group">
		<label for="admcomment">Comment (internal use)</label>
		<textarea class="form-control" id="admcomment" name="admcomment" style="resize:none">{{ @isset($film->admcomment) ? $film->admcomment : '' }}</textarea>
	</div>
</form>