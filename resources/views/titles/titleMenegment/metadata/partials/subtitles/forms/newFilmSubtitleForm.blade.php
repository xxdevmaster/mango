<div class="panel panel-default ">
	<div class="panel-heading">
	  <h3 class="panel-title">Add New Film Subtitle</h3>
	</div>
	<div class="panel-body">
		<div class="media">
			<div class="media-body">
				<form id="newFilmSubtitleForm" action="" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<input type="text" value="" placeholder="Language" class="form-control filmSubTitle" name="filmSubTitle" />
					</div>
					<div class="form-group">
						<div id="uploadifive-subtitle_file" class="uploadifive-button"> Upload File
							<input type="file" id="addSubtitleFile" name="addSubtitleFile" value=""/>
						</div>
						<h5>File must be in SRT (.srt) format.</h5>
						<h4 class="text-danger" id="response_in_error"></h4>
						<h4 class="text-success" id="response_in_success"></h4>
					</div>
					<input type="hidden" name="fsubtitleFile" value="" />
					<input type="hidden" name="filmId" value="{{ $film->id }}" />
				</form>
			</div>
			<button class="btn btn-default pull-right" id="addFilmSubtitle">+Add</button>
		</div>
	</div>            
	<hr/>
	<form id="editFilmSubtitleForm">
		@if(isset($metadata['subtitles']['filmSubtitles']))
			@foreach($metadata['subtitles']['filmSubtitles'] as $value)
				<div class="panel-body">
					<div class="media">
						<div class="media-body">
							<div class="form-group">
								<input type="text" value="{{ $value->title }}" placeholder="Language" class="form-control" name="subtitleNames[{{$value->id}}]" />
							</div>
							<div class="form-group">
								<div id="uploadifive-subtitle_file_{{ $value->id }}" class="uploadifive-button" accept="image/*">Edit File	
									<input type="file"  name="addSubtitleFile" value="" />
								</div>
								<input type="hidden" name="fsubtitleFile_{{$value->id}}" value="{{ $value->file }}" />
								<h5>File must be in SRT (.srt) format.</h5>
								<h4 class="text-danger" id="response_in_error_{{ $value->id }}"></h4>
								<h4 class="text-success" id="response_in_success_{{ $value->id }}"></h4>
							</div>							
							<button class="btn btn-default pull-right removeFilmSubtitle" data-id="{{ $value->id }}">
								<i class="fa fa-close"></i> 
							</button>
							<a href="http://cinecliq.assets.s3.amazonaws.com/subtitles/{{ $value->file }}" id="downloadFile_{{ $value->id }}" download class="btn btn-default pull-right" data-id="{{ $value->id }}">
								<i class="fa fa-cloud-download"></i>
							</a>
						</div>
					</div>
				</div>
				<script>
				$(document).ready(function(){
					var filmId = $('input[name="filmId"]').val();
					
					CHUpload("{{ url() }}/titles/metadata/subtitles/uploadFile", "uploadifive-subtitle_file_{{ $value->id }}", {filmId:filmId, fileName:"f", "_token":"{{ csrf_token() }}" }, function(data){
						var response = JSON.parse(data);
						if(!response.error){
							$("#downloadFile_{{ $value->id }}").attr('href', 'http://cinecliq.assets.s3.amazonaws.com/subtitles/'+filmId+'/'+'f/'+response.message);
							$("input[name='fsubtitleFile_{{$value->id}}']").val(response.message);
							$("#response_in_success_{{ $value->id }}").html(response.message);			
							$("#response_in_error_{{ $value->id }}").html('');			
						}			
						else {
							$("#response_in_error_{{ $value->id }}").html(response.message);
						}				
					});						
				});
				</script>
				<hr/>
			@endforeach
		@endif
		<input type="hidden" name="filmId" value="{{ $film->id }}">
	</form>
</div>
<script>
$(document).ready(function(){
	var filmId = $('input[name="filmId"]').val();
	
	CHUpload("{{ url() }}/titles/metadata/subtitles/uploadFile", "uploadifive-subtitle_file", {filmId:filmId, fileName:"f", "_token":"{{ csrf_token() }}" }, function(data){
		var response = JSON.parse(data);
		if(!response.error){
			$("input[name='fsubtitleFile']").val(response.message);
			$("#response_in_success").html(response.message);			
			$("#response_in_error").html('');			
		}			
		else {
			$("#response_in_error").html(response.message);
		}				
	});		
	
	$('#addFilmSubtitle').click(function(){
		var newFilmSubtitleForm = $("#newFilmSubtitleForm").serialize();
		
		$.post('{{url()}}/titles/metadata/subtitles/CreateNewFilmSubtitle', newFilmSubtitleForm, function(data){
			if(data) {
				$.post('{{url()}}/titles/metadata/basic/getTemplate', {filmId:filmId, template:'subtitles'},function(data){
					if(data) {
						var filmSubtitles = $(data).find("#filmSubtitles").html();
						$("#filmSubtitles").html(filmSubtitles);
					}
				});
			}
		});
	});
	
	$(".removeFilmSubtitle").click(function(){
		var subTitleId = $(this).data("id");
		
		bootbox.confirm('Do you realy want to delete this Film subtilte', function(result) {
			if(result) {
				$('.loading').show();				
				$.post('{{url()}}/titles/metadata/subtitles/removeFilmSubtitle', {subTitleId:subTitleId},function(data){					
					if(data) {
						$.post('{{url()}}/titles/metadata/basic/getTemplate', {filmId:filmId, template:'subtitles'},function(data){
							if(data) {
								var filmSubtitles = $(data).find("#filmSubtitles").html();
								$("#filmSubtitles").html(filmSubtitles);
								$('.loading').hide();
							}
						});
					}					
				});
			}
		});
		return false;
	});
	
});
</script>