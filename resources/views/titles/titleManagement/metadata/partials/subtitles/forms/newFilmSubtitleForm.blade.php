<div class="panel-heading">
  <h3 class="panel-title">Add New Film Subtitle</h3>
</div>
<div class="panel-body">
	<div class="media">
		<div class="media-body">
			<form id="newFilmSubtitleForm" action="" method="post" enctype="multipart/form-data" onsubmit="return false">
				<div class="form-group">
					<input type="text" value="" placeholder="Language" class="form-control filmSubTitle" name="filmSubTitle" />
				</div>
				<div class="form-group">
					<div id="uploadifive-subtitle_file" class="uploadifive-button"> Upload File
						<input type="file" id="addSubtitleFile" name="addSubtitleFile" value=""/>
					</div>
					<h5>File must be in SRT (.srt) format.</h5>
					<h4 class="text-muted" id="response_in_success"></h4>
				</div>
				<input type="hidden" name="fsubtitleFile" value="" />
			</form>
		</div>
		<button class="btn btn-default pull-right" id="addFilmSubtitle">+Add</button>
	</div>
</div>            
<hr/>
<form id="editFilmSubtitleForm" onsubmit="return false">
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
<?
	$fileName = explode ('/', $value->file);
	$fileName = end($fileName);
?>							
							<input type="hidden" name="fsubtitleFile_{{$value->id}}" value="{{ $fileName }}" />
							<h5>File must be in SRT (.srt) format.</h5>							
							<h4 class="text-muted" id="file_name_{{ $value->id }}">{{ $fileName }}</h4>
							<h4 class="text-muted" id="response_in_success_{{ $value->id }}"></h4>
						</div>							
						<button class="btn btn-default pull-right removeFilmSubtitle" data-id="{{ $value->id }}" type="button">
							<i class="fa fa-close"></i> 
						</button>
						<a href="http://cinecliq.assets.s3.amazonaws.com/subtitles/{{ $value->file }}" id="downloadFile_{{ $value->id }}" class="btn btn-default pull-right" data-id="{{ $value->id }}" style="margin-right:3px">
							<i class="fa fa-cloud-download"></i>
						</a>
					</div>
				</div>
			</div>
			<script>
			$(document).ready(function(){
				var filmID = $('input[name="filmID"]').val();
				
				CHUpload("/titles/metadata/subtitles/uploadFile", "uploadifive-subtitle_file_{{ $value->id }}", 'Edit File', {filmID:filmID, fileName:"f", "_token":"{{ csrf_token() }}" }, function(data){
					$('.loading').show();
					var response = JSON.parse(data);
					if(response.error == 0){
						$("#downloadFile_{{ $value->id }}").attr('href', 'http://cinecliq.assets.s3.amazonaws.com/subtitles/'+filmID+'/'+'f/'+response.fileName);
						$("input[name='fsubtitleFile_{{$value->id}}']").val(response.fileName);
						$("#file_name_{{ $value->id }}").remove();
						$("#response_in_success_{{ $value->id }}").html(response.fileName);
						autoCloseMsg(0, response.message, 5000);
						$('.loading').hide();						
					}else {
						$('.loading').hide();
						autoCloseMsg(1, response.message, 5000);
					}							
				});						
			});
			</script>
			<hr/>
		@endforeach
	@endif
</form>

<script>
$(document).ready(function(){
	var filmID = $('input[name="filmID"]').val();
	
	CHUpload("{{ url() }}/titles/metadata/subtitles/uploadFile", "uploadifive-subtitle_file", 'Upload File', {filmID:filmID, fileName:"f", "_token":"{{ csrf_token() }}" }, function(data){
		$('.loading').show();	
		var response = JSON.parse(data);
		if(response.error == 0){
			$("input[name='fsubtitleFile']").val(response.fileName);
			$("#response_in_success").html(response.fileName);
			autoCloseMsg(0, response.message, 5000);
			$('.loading').hide();
		}else {
			$('.loading').hide();
			autoCloseMsg(1, response.message, 5000);
		}				
	});		
	
	$('#addFilmSubtitle').click(function(){
		autoCloseMsgHide();
		$('.loading').show();	
		var newFilmSubtitleForm = $("#newFilmSubtitleForm").serialize();
		
		$.post('/titles/metadata/subtitles/CreateNewFilmSubtitle', newFilmSubtitleForm, function(response){
			var filmSubtitles = $(response).find("#filmSubtitles").html();
			$("#filmSubtitles").html(filmSubtitles);
			$('.loading').hide();
		});
	});

	$(".removeFilmSubtitle").click(function(){
		autoCloseMsgHide();
		var subTitleID = $(this).data("id");

		bootbox.confirm('Do you realy want to delete this Film subtilte', function(result) {
			if(result) {
				$('.loading').show();
				$.post('/titles/metadata/subtitles/removeFilmSubtitle', {subTitleID:subTitleID}, function(response){
					var filmSubtitles = $(response).find("#filmSubtitles").html();
					$("#filmSubtitles").html(filmSubtitles);
					$('.loading').hide();
				});
			}
		});
		return false;
	});
	
});
</script>