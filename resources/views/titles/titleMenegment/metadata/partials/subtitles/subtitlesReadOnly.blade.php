<ul class="nav nav-tabs">
	<li class="active">
		<a href="#filmSubtitles"  data-toggle="tab" aria-expanded="true">Film Subtitles</a>
	</li>	
	<li class="">
		<a href="#trailerSubtitles"  data-toggle="tab" aria-expanded="true">Trailer Subtitles</a>
	</li>
</ul>

<div class="tab-content">
	<div class="tab-pane fade in active" id="filmSubtitles">
		@include('titles.titleMenegment.metadata.partials.subtitles.forms.newFilmSubtitleFormReadOnly')
	</div>	
	<div class="tab-pane fade" id="trailerSubtitles">
		@include('titles.titleMenegment.metadata.partials.subtitles.forms.newTrailerFormReadOnly')
	</div>
</div>