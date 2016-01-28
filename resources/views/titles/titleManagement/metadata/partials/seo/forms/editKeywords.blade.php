<link rel="stylesheet" type="text/css" href="/assets/select2/select2.css" />

<div class="modal fade" id="editSeoItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
						@if(isset($metadata['seo']['seoAllUniqueLocales']) && is_array($metadata['seo']['seoAllUniqueLocales']))
							@foreach($metadata['seo']['seoAllUniqueLocales'] as $key => $value)
								<option value="{{ $key }}">{{ $value }}</option>
							@endforeach
						@endif
					</select>
				</div>
				<div class="form-group">
					<label for="seoKeywords">Keyword</label>
					<textarea class="form-control" name="keywords" id="seoKeywords2"></textarea>				
				</div>			
				<div class="form-group">
					<label for="seoDescription">Description</label>
					<textarea class="form-control" name="description" id="seoDescription"></textarea>				
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="editSeoItemButton" data-dismiss="modal">Add</button>
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
			<input type="hidden" name="keywordsId" value="{{ $film->id }}">
		</form>
    </div>
  </div>
</div>
		
		<!--script type="text/javascript" src="/assets/jquery-multi-select/jquery.multi-select.js"></script>
		<script type="text/javascript" src="/assets/spinner/spinner.min.js"></script>
		<script src="/assets/select2/select2.min.js" type="text/javascript"></script-->

<script>
	jQuery(document).ready(function() {
		jQuery(".selectBoxWithSearch2").select2({
			width: '100%',
		});	
	});
	$(document).ready(function(){
		$("#editSeoItemButton").click(function(){

			var filmId = $('input[name="filmId"]').val();
			var editKeywords = $('#editKeywords').serialize();
			
			$.post('{{url()}}/titles/metadata/castAndCrew/editSeoItem', editKeywords, function(data){
				if(data) {
					$.post('{{url()}}//titles/metadata/basic/getTemplate', {filmId:filmId,template:'seo'}, function(data){							
						if(data) {
							$('#seo').html(data);
							//$('a[href="#tabBasicLocale_'+locale+'"]').tab('show');
							autoCloseMsg(0, 'Keyword is added', 5000);	
							$('.loading').hide();
						}							
					});
				}				
			});
		})
	});
</script>		
		
