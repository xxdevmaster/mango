<div id="seo">
	<div class="row">
		<ol class="list">
			<li>This section allows you to add, edit and override <strong>meta keywords</strong> and <strong>meta description</strong> for this title.</li>
			<li>For best results, we recommend you add a pair of keywords and a description for every language your platform supports.</li>
			<li>Limitations: Meta description – 160 characters, Meta keywords – No more than 10 keyword phrases.</li>
		</ol>
	</div>
	<div class="form-group" style="margin:15px 10px;">
		<button id="newSeoItemShow" type="button" class="btn btn-default">+ Add Keywords &amp; Description</button>
		<hr>
	</div>
	<div id="seoContent">
		@include('titles.titleManagement.metadata.partials.seo.list')
	</div>
</div>
<div class="modal fade" id="addNewSeoItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="editSeoItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<script>
	$(document).ready(function(){
		/* Show New Seo Item Modal Form*/
		$('#newSeoItemShow').click(function(){
			$.post('/titles/metadata/seo/showNewSeoItemForm', function(data){
				$('#addNewSeoItem').html(data);
				$('#addNewSeoItem').modal('show');
			});
		});
	});
</script>