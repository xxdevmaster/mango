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
		@include('titles.titleManagement.metadata.partials.subtitles.forms.newFilmSubtitleForm')
	</div>	
	<div class="tab-pane fade" id="trailerSubtitles">
		@include('titles.titleManagement.metadata.partials.subtitles.forms.newTrailerForm')
	</div>
</div>