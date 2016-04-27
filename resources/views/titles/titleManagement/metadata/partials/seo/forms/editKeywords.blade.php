<div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Edit Keyword & Description</h4>
		</div>
		<form id="editKeywords" name="editKeywords" role="form">
			<div class="modal-body">		
				<div class="form-group">
					<select class="selectBoxWithSearch2" name="countries" data-placeholder="Choose a Country...">
						@if(isset($allLocales) && is_array($allLocales))
							@foreach($allLocales as $key => $value)
								@if(in_array($keywords->locale, $allLocales))
									<?php $selected = 'selected="selected"';?>
								@else
									<?php $selected = '';?>
								@endif
								<option {{ $selected }} value="{{ $key }}">{{ $value }}</option>
							@endforeach
						@endif
					</select>
				</div>
				<div class="form-group">
					<label for="seoKeywords">Keyword</label>
					<textarea class="form-control" name="keywords" id="seoKeywords2">{{ $keywords->keywords }}</textarea>
				</div>			
				<div class="form-group">
					<label for="seoDescription">Description</label>
					<textarea class="form-control" name="description" id="seoDescription">{{ $keywords->description }}</textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="editSeoItemButton" data-dismiss="modal">Save</button>
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
			<input type="hidden" name="keywordID" value="{{ $keywords->id }}">
		</form>
    </div>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery(".selectBoxWithSearch2").select2({
			width: '100%',
		});	
	});
	$(document).ready(function(){
		$("#editSeoItemButton").click(function(){
			var editKeywords = $('#editKeywords').serialize();
			$('.loading').show();

			$.post('{{url()}}/titles/metadata/seo/editSeoItem', editKeywords, function(data){
				$('#seoContent').html(data);
				$('.loading').hide();
			});
		})
	});
</script>		
		
