<div id="seo">
	<div class="row">
		<ol class="list">
			<li>This section allows you to add, edit and override <strong>meta keywords</strong> and <strong>meta description</strong> for this title.</li>
			<li>For best results, we recommend you add a pair of keywords and a description for every language your platform supports.</li>
			<li>Limitations: Meta description – 160 characters, Meta keywords – No more than 10 keyword phrases.</li>
		</ol>
	</div>
	<hr>
	<div id="seoContent">
		@if(isset($metadata['seo']['keywords']))
				<div id="seoItemParseJson" style="display:none">
					{{json_encode($metadata['seo']['keywords'])}}
				</div>				
			@foreach($metadata['seo']['keywords'] as $key => $value)
				<div class="panel personsPanel">
					<div class="panel-body p-t-10">
						<div class="media-main">		
							<div class="pull-right btn-group-sm" style="margin-top:-9px;margin-bottom:3px">
								<button class="btn btn-default pos" id="editKeywordsModalOpen" data-locale="{{  $key }}" data-toggle="modal" data-target="#editSeoItem">
									<i class="fa fa-pencil-square-o"></i>
								</button>
							</div>
							<div class="info col-md-3">
								<h4 style="margin:0;padding:0;">
									@if(array_key_exists($key, $allLocales))
										{{ $allLocales[$key] }}
									@endif								
								</h4>					
							</div>				
						</div>
						<div class="clearfix"></div>
					</div>
				</div>	
			@endforeach
		@endif				
	</div>	
</div>
<script>
	$(document).ready(function(){
		$(document).on('click', '#editKeywordsModalOpen', function(){
			var keywords = JSON.parse($('#seoItemParseJson').html())[$(this).data('locale')];
			$('#editKeywords').children().find('#seoKeywords').val(keywords['keywords']);
			$('#editKeywords').children().find('#seoDescription').val(keywords['description']);
			$('#keywordLocale').html(keywords['locale']);
		});
	});
</script>
@include('titles.titleMenegment.metadata.partials.seo.forms.editKeywordsReadOnly')