<link rel="stylesheet" type="text/css" href="/assets/select2/select2.css" />

<div class="modal fade" id="addNewSeoItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Add Keywords & Description</h4>
		</div>
		<form id="addNewKeywords" name="addNewKeywords" role="form">
			<div class="modal-body">		
				<div class="form-group">
					<select class="selectBoxWithSearch" name="countries" data-placeholder="Choose a Country...">
						@if(isset($allLocales) && is_array($allLocales))
							@foreach($allLocales as $key => $value)	
								<option value="{{ $key }}">{{ $value }}</option>
							@endforeach
						@endif
					</select>
				</div>
				<div class="form-group">
					<label for="seoKeywords">Keyword</label>
					<textarea class="form-control" name="keywords" id="seoKeywords"></textarea>				
				</div>			
				<div class="form-group">
					<label for="seoDescription">Description</label>
					<textarea class="form-control" name="description" id="seoDescription"></textarea>				
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="addSeoItemButton" data-dismiss="modal">Add</button>
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
			<input type="hidden" name="filmId" value="{{ $film->id }}">
		</form>
    </div>
  </div>
</div>
<script>
	$(document).ready(function(){
		$(document).on('click', '#addSeoItemButton', function(){
			var addNewKeywords = $('#addNewKeywords').serialize();
			var filmId = $('input[name="filmId"]').val();
			$.post('{{url()}}/titles/metadata/castAndCrew/addSeoItem', addNewKeywords, function(data){
				if(data) {
					xhr('{{url()}}/titles/metadata/getTemplate','POST',{filmId:filmId,template:'seo'},function(data){							
						if(data) {
							$('#seo').html(data);
							//$('a[href="#tabBasicLocale_'+locale+'"]').tab('show');
							autoCloseMsg(0, 'Language is added', 5000);	
							$('.loading').hide();
						}							
					});
				}
			});
		})
	});
</script>