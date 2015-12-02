<div id="seo">
	<div>
		<ol class="list">
			<li>This section allows you to add, edit and override <strong>meta keywords</strong> and <strong>meta description</strong> for this title.</li>
			<li>For best results, we recommend you add a pair of keywords and a description for every language your platform supports.</li>
			<li>Limitations: Meta description – 160 characters, Meta keywords – No more than 10 keyword phrases.</li>
		</ol>
	</div>

	<div class="form-group ">
		<a data-target="#addNewSeoItem" data-toggle="modal" type="button" class="btn btn-primary btn-xs">+ Add Keywords &amp; Description</a>
	</div>
	<hr>

	<div id="seoContent">
		<ul class="active-sort sortable-list  ">    
	@if(isset($seo['keywords']))
		<div id="seoItemParseJson" style="display:none">
			{{json_encode($seo['keywords'])}}
		</div>
		@foreach($seo['keywords'] as $key => $value)	
			<li class="active-draggable">
				<span class="name">{{ $allLocales[$key] }}</span>
				<span class="pos" class="pull-right" id="removeSeoItem" data-locale="{{  $key }}">
					<a data-toggle="modal" data-target="#editSeoItem" class="btn btn-primary btn-xs">Edit</a>
				</span>
				<span data-keywordid="{{ $value->id }}" id="removSeoItem">
					<span class="glyphicon glyphicon-remove cp delete-collection"></span>
				</span>                       
			</li> 
		@endforeach
	@endif
	   </ul>	
	</div>
</div>
<script>
	$(document).ready(function(){
		$(document).on('click', '#removeSeoItem', function(){
			var keywords = JSON.parse($('#seoItemParseJson').html())[$(this).data('locale')];
			$('#editKeywords').children().find('textarea[name="keywords"]').val(keywords['keywords']);
			$('#editKeywords').children().find('textarea[name="description"]').val(keywords['description']);
			$('#editKeywords').find('input[name="keywordsId"]').val(keywords['id']);
			var locale = $('#editKeywords').children().find('select[name="countries"] option[value="'+keywords['locale']+'"]').html();
			$('#select2-chosen-4').html(locale);
		});
		
		$(document).on('click', '#removSeoItem', function(){
			var keywordId = $(this).data('keywordid');
			$.post('{{url()}}/titles/metadata/castAndCrew/removeSeoItem', {keywordId:keywordId}, function(){
				
			});
		});
		
	});
</script>
@include('titles.titleMenegment.metadata.partials.seo.forms.addKeywords')
@include('titles.titleMenegment.metadata.partials.seo.forms.editKeywords')