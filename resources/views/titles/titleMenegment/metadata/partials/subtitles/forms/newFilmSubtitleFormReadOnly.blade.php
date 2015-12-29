<div class="panel-heading">
  <h3 class="panel-title">Add New Film Subtitle</h3>
</div>
<div>
	@if(isset($metadata['subtitles']['filmSubtitles']))
		@foreach($metadata['subtitles']['filmSubtitles'] as $value)
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