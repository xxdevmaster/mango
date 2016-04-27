<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Add Keywords & Description</h4>
		</div>
		<form id="addNewKeywords" name="addNewKeywords" role="form">
			<div class="modal-body">
				<div class="form-group">
					<select class="form-control" name="countries" data-placeholder="Choose a Country...">
						@if(isset($metadata['seo']['seoAllUniqueLocales']) && is_array($metadata['seo']['seoAllUniqueLocales']))
							@foreach($metadata['seo']['seoAllUniqueLocales'] as $key => $value)
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
		</form>
	</div>
</div>
<script>
	$(document).ready(function(){
		$("#addSeoItemButton").click(function(){
			var addNewKeywords = $('#addNewKeywords').serialize();
			$('.loading').show();

			$.post('/titles/metadata/seo/addSeoItem', addNewKeywords, function(data){
				$('#seoContent').html(data);
				$('.loading').hide();
			});
		})
	});
</script>