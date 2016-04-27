<div class="panel-heading">
  <h3 class="panel-title">Add New Trailer Subtitle</h3>
</div>
<div class="panel-body">
	<div class="media">
		<div class="media-body">
			<form id="newTrailerSubtitleForm" action="" method="post" enctype="multipart/form-data" onsubmit="return false">
				<div class="form-group">
					<input type="text" value="" placeholder="Language" class="form-control trailerSubTitle" name="trailerSubTitle" />
				</div>
				<div class="form-group">
					<div id="uploadifive-tsubtitle_file" class="uploadifive-button"> Upload File
						<input type="file" id="addTrailerSubtitleFile" name="addTrailerSubtitleFile" value=""/>
					</div>
					<h5>File must be in SRT (.srt) format.</h5>
					<h4 class="text-muted" id="tResponse_in_success"></h4>
				</div>
				<input type="hidden" name="tsubtitleFile" value="" />
			</form>
		</div>
		<button class="btn btn-default pull-right" id="addTrailerSubtitle">+Add</button>
	</div>
</div>            
<hr/>
<form id="editTrailerSubtitleForm" onsubmit="return false">
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
<?
	$fileName = explode ('/', $value->file);
	$fileName = end($fileName);
?>							
							<input type="hidden" name="tsubtitleFile_{{$value->id}}" value="{{ $fileName }}" />
							<h5>File must be in SRT (.srt) format.</h5>							
							<h4 class="text-muted" id="tfile_name_{{ $value->id }}">{{ $fileName }}</h4>
							<h4 class="text-muted" id="tresponse_in_success{{ $value->id }}"></h4>
						</div>							
						<button class="btn btn-default pull-right removeTrailerSubtitle" data-id="{{ $value->id }}" type="button">
							<i class="fa fa-close"></i> 
						</button>
						<a href="http://cinecliq.assets.s3.amazonaws.com/subtitles/{{ $value->file }}" id="tdownloadFile_{{ $value->id }}" class="btn btn-default pull-right" data-id="{{ $value->id }}" style="margin-right:3px">
							<i class="fa fa-cloud-download"></i>
						</a>
					</div>
				</div>
			</div>
			<script>
				$(document).ready(function(){
					var filmID = $('input[name="filmID"]').val();
					
					CHUpload("{{ url() }}/titles/metadata/subtitles/uploadFile", "uploadifive-tsubtitle_file_{{ $value->id }}", 'Edit File', {filmID:filmID, fileName:"t", "_token":"{{ csrf_token() }}" }, function(data){
						$('.loading').show();
						var response = JSON.parse(data);
						if(response.error == 0){
							$("#tdownloadFile_{{ $value->id }}").attr('href', 'http://cinecliq.assets.s3.amazonaws.com/subtitles/'+filmID+'/'+'t/'+response.fileName);
							$("input[name='tsubtitleFile_{{$value->id}}']").val(response.fileName);
							$("#tfile_name_{{ $value->id }}").remove();
							$("#tresponse_in_success{{ $value->id }}").html(response.fileName);			
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
	
	CHUpload("{{ url() }}/titles/metadata/subtitles/uploadFile", "uploadifive-tsubtitle_file", 'Upload File', {filmID:filmID, fileName:"t", "_token":"{{ csrf_token() }}" }, function(data){
		var response = JSON.parse(data);
		if(response.error == 0){
			$("input[name='tsubtitleFile']").val(response.fileName);
			$("#tResponse_in_success").html(response.fileName);
			autoCloseMsg(0, response.message, 5000);
			$('.loading').hide();			
		}else {
			$('.loading').hide();
			autoCloseMsg(1, response.message, 5000);
		}				
	});			
	
	$('#addTrailerSubtitle').click(function(){
		autoCloseMsgHide();
		$('.loading').show();		
		var newTrailerSubtitleForm = $("#newTrailerSubtitleForm").serialize();
		
		$.post('/titles/metadata/subtitles/CreateNewTrailerSubtitle', newTrailerSubtitleForm, function(response){
			var trailerSubtitles = $(response).find("#trailerSubtitles").html();
			$("#trailerSubtitles").html(trailerSubtitles);
			$('.loading').hide();
		});
	});
	
	$(".removeTrailerSubtitle").click(function(){
		autoCloseMsgHide();
		var trailerSubTitleID = $(this).data("id");
		
		bootbox.confirm('Do you realy want to delete this Trailer subtilte', function(result) {
			if(result) {
				$('.loading').show();				
				$.post('/titles/metadata/subtitles/removeTrailerSubtitle', {trailerSubTitleID:trailerSubTitleID}, function(response){
					var trailerSubtitles = $(response).find("#trailerSubtitles").html();
					$("#trailerSubtitles").html(trailerSubtitles);
					$('.loading').hide();
				});
			}
		});
		return false;
	});
	
});
</script>