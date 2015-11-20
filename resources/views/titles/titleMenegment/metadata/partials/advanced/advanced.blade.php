<form name="advancedForm" id="advancedForm" role="form">
    <input type="hidden" name="filmId" value="{{ @isset($film) ? $film->id : '' }}">
    <input type="hidden" name="" value="">
	<div class="form-group">
		<label for="form-dt">Year</label>
		<input type="text" class="form-control" id="form-dt" name="dt" placeholder="" value="{{ @isset($film) ? $film->dt : '' }}">
	</div>
	<div class="form-group">
		<label for="duration">Duration (minutes)</label>
		<input type="text" class="form-control" id="duration" name="duration" placeholder="" value="{{ @isset($film) ? $film->duration : '' }}">
	</div>
	<div class="form-group">
		<label for="input-genre">Genre</label>	
		<input type="text" id="input-genre" name="inputToken" value="" />
		<script type="text/javascript">
		$(document).ready(function() {
			$("#input-genre").tokenInput("{{url()}}/titles/metadata/advanced/getTokenGenres", {
				theme: "facebook",
				tokenFormatter:function(item){ return '<li><input type="hidden" name="genres['+item.id+']" /><p>' + item.title + '</p></li>' }
			});
			@if(isset($advanced['filmGenres']))
				@foreach($advanced['filmGenres'] as $genres)
				<?php
					echo '$("#input-genre").tokenInput("add", {id: "'.$genres->id.'", title: "'.$genres->title.'"});';
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
			@if(isset($advanced['filmLanguages']))
				@foreach($advanced['filmLanguages'] as $languages)
				<?php
					echo '$("#input-originalLang").tokenInput("add", {id: "'.$languages->id.'", title: "'.$languages->title.'"});';
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
			@if(isset($advanced['filmProdCompanies']))
				@foreach($advanced['filmProdCompanies'] as $prodCompanies)
				<?php
					echo '$("#input-productCompanies").tokenInput("add", {id: "'.$prodCompanies->id.'", title: "'.$prodCompanies->title.'"});';
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
			@if(isset($advanced['filmCountries']))
				@foreach($advanced['filmCountries'] as $countries)
				<?php
					echo '$("#input-countries").tokenInput("add", {id: "'.$countries->id.'", title: "'.$countries->title.'"});';
				?>
				@endforeach
			@endif
		});
		</script>
	</div>
	<div class="form-group">
		<label for="admcomment">Comment (internal use)</label>
		<textarea class="form-control" id="admcomment" name="admcomment" style="resize:none">{{ @isset($film) ? $film->admcomment : '' }}</textarea>
	</div>
</form>