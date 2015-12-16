<div class="panel panel-default ">
	<div class="panel-heading">
	  <h3 class="panel-title">Add New Trailer Subtitle</h3>
	</div>
	<div class="panel-body">
		<div class="media">
			<div class="media-body">
				<form id="newTrailerSubtitleForm" action="" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<input type="text" value="" placeholder="Language" class="form-control trailerSubTitle" name="trailerSubTitle" />
					</div>
					<div class="form-group">
						<div id="uploadifive-tsubtitle_file" class="uploadifive-button"> Upload File
							<input type="file" id="addTrailerSubtitleFile" name="addTrailerSubtitleFile" value=""/>
						</div>
						<h5>File must be in SRT (.srt) format.</h5>
						<h4 class="text-danger" id="tResponse_in_error"></h4>
						<h4 class="text-success" id="tResponse_in_success"></h4>
					</div>
					<input type="hidden" name="tsubtitleFile" value="" />
					<input type="hidden" name="filmId" value="{{ $film->id }}" />
				</form>
			</div>
			<button class="btn btn-default pull-right" id="addTrailerSubtitle">+Add</button>
		</div>
	</div>            
	<hr/>
	<form id="editTrailerSubtitleForm">
		@if(isset($metadata['subtitles']['trailerSubtitles']))
			@foreach($metadata['subtitles']['trailerSubtitles'] as $value)
				<div class="panel-body">
					<div class="media">
						<div class="media-body">
							<div class="form-group">
								<input type="text" value="{{ $value->title }}" placeholder="Language" class="form-control" name="tSubtitleNames[{{$value->id}}]" />
							</div>
							<div class="form-group">
								<div id="uploadifive-tsubtitle_file_{{ $value->id }}" class="uploadifive-button">Edit File	
									<input type="file" id="addTrailerSubtitleFile" name="addTrailerSubtitleFile" value=""/>
								</div>
								<input type="hidden" name="tsubtitleFile_{{$value->id}}" value="{{ $value->file }}" />
								<h5>File must be in SRT (.srt) format.</h5>
								<h4 class="text-danger" id="tresponse_in_error_{{ $value->id }}"></h4>
								<h4 class="text-success" id="tresponse_in_success{{ $value->id }}"></h4>
							</div>							
							<button class="btn btn-default pull-right removeTrailerSubtitle" data-id="{{ $value->id }}">
								<i class="fa fa-close"></i> 
							</button>
							<a href="http://cinecliq.assets.s3.amazonaws.com/subtitles/{{ $value->file }}" id="tdownloadFile_{{ $value->id }}" download class="btn btn-default pull-right" data-id="{{ $value->id }}">
								<i class="fa fa-cloud-download"></i>
							</a>
						</div>
					</div>
				</div>
				<script>
					$(document).ready(function(){
						var filmId = $('input[name="filmId"]').val();
						
						CHUpload("{{ url() }}/titles/metadata/subtitles/uploadFile", "uploadifive-tsubtitle_file_{{ $value->id }}", {filmId:filmId, fileName:"t", "_token":"{{ csrf_token() }}" }, function(data){
							var response = JSON.parse(data);
							if(!response.error){
								$("#tdownloadFile_{{ $value->id }}").attr('href', 'http://cinecliq.assets.s3.amazonaws.com/subtitles/'+filmId+'/'+'f/'+response.message);
								$("input[name='tSubtitleFile_{{$value->id}}']").val(response.message);
								$("#tresponse_in_success{{ $value->id }}").html(response.message);			
								$("#tresponse_in_error_{{ $value->id }}").html('');			
							}			
							else {
								$("#tresponse_in_error_{{ $value->id }}").html(response.message);
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
	
	CHUpload("{{ url() }}/titles/metadata/subtitles/uploadFile", "uploadifive-tsubtitle_file", {filmId:filmId, fileName:"t", "_token":"{{ csrf_token() }}" }, function(data){
		var response = JSON.parse(data);
		if(!response.error){
			$("input[name='tsubtitleFile']").val(response.message);
			$("#tresponse_in_success").html(response.message);			
			$("#tResponse_in_error").html('');			
		}			
		else {
			$("#tResponse_in_error").html(response.message);
		}				
	});			
	
	$('#addTrailerSubtitle').click(function(){
		var newTrailerSubtitleForm = $("#newTrailerSubtitleForm").serialize();
		
		$.post('{{url()}}/titles/metadata/subtitles/CreateNewTrailerSubtitle', newTrailerSubtitleForm, function(data){
			if(data) {
				$.post('{{url()}}/titles/metadata/basic/getTemplate', {filmId:filmId, template:'subtitles'},function(data){
					if(data) {
						var trailerSubtitles = $(data).find("#trailerSubtitles").html();
						$("#trailerSubtitles").html(trailerSubtitles);
					}
				});
			}
		});
	});
	
	$(".removeTrailerSubtitle").click(function(){
		var trailerSubTitleId = $(this).data("id");
		
		bootbox.confirm('Do you realy want to delete this Trailer subtilte', function(result) {
			if(result) {
				$('.loading').show();				
				$.post('{{url()}}/titles/metadata/subtitles/removeTrailerSubtitle', {trailerSubTitleId:trailerSubTitleId},function(data){					
					if(data) {
						$.post('{{url()}}/titles/metadata/basic/getTemplate', {filmId:filmId, template:'subtitles'},function(data){
							if(data) {
								var trailerSubtitles = $(data).find("#trailerSubtitles").html();
								$("#trailerSubtitles").html(trailerSubtitles);
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