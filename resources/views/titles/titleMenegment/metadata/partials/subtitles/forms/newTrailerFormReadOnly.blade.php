<div class="panel-heading">
    <h3 class="panel-title">Add New Trailer Subtitle</h3>
</div>
<div>
	@if(isset($metadata['subtitles']['trailerSubtitles']))
		@foreach($metadata['subtitles']['trailerSubtitles'] as $value)
			<div class="panel-body">
				<div class="media">
					<div class="media-body">
						<div class="form-group">
							<span class="form-control readOnly">{{isset( $value->title)?  $value->title : ''}}</span>
						</div>
					</div>
				</div>
			</div>			
			<hr/>
		@endforeach
	@endif
</div>