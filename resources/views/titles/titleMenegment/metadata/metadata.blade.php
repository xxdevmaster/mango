@extends('titles.titleMenegment.titleMenegment')
@section('titleMenegment')
<div class="m-h-50">
	<div class="col-lg-12"> 
		<ul class="nav nav-tabs"> 
			<li class="active" data-placement="top" data-toggle="tooltip" data-original-title="Basic"> 
				<a href="#basic" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
					<span class="visible-sm visible-xs"><i class="fa fa-home"></i></span> 
					<span class="hidden-sm hidden-xs">Basic</span> 
				</a> 
			</li> 
			<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Advanced"> 
				<a href="#advanced" data-toggle="tab" class="tab-level0" aria-expanded="true"> 
					<span class="visible-sm visible-xs"><i class="fa fa-info-circle"></i></span> 
					<span class="hidden-sm hidden-xs">Advanced</span> 
				</a> 
			</li> 
			<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Cast & Crew"> 
				<a href="#castAndCrew" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
					<span class="visible-sm visible-xs"><i class="fa fa-user"></i></span> 
					<span class="hidden-sm hidden-xs">Cast & Crew</span> 
				</a> 
			</li> 
			<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Images"> 
				<a href="#images" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
					<span class="visible-sm visible-xs" ><i class="fa fa-picture-o"></i></span>
					<span class="hidden-sm hidden-xs">Images</span> 
				</a> 
			</li>								
			<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Subtitles"> 
				<a href="#subtitles" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
					<span class="visible-sm visible-xs"><i class="fa fa-newspaper-o"></i></span> 
					<span class="hidden-sm hidden-xs">Subtitles</span> 
				</a> 
			</li> 
			<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Age Ratings"> 
				<a href="#ageRates" data-toggle="tab" class="tab-level0" aria-expanded="true"> 
					<span class="visible-sm visible-xs"><i class="fa fa-flag-o"></i></span>  
					<span class="hidden-sm hidden-xs">Age Ratings</span> 
				</a> 
			</li> 
			<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Series"> 
				<a href="#series" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
					<span class="visible-sm visible-xs"><i class="fa fa-film"></i></span> 
					<span class="hidden-sm hidden-xs">Series</span> 
				</a> 
			</li> 
			<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Seo"> 
				<a href="#seo" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
					<span class="visible-sm visible-xs"><i class="fa fa-star-o"></i></span> 
					<span class="hidden-sm hidden-xs">Seo</span> 
				</a> 
			</li> 
		</ul> 
		<div class="tab-content">
			<div class="tab-pane fade in active" id="basic">
				@include('titles.titleMenegment.metadata.partials.basic.basic')
			</div>
			<div class="tab-pane fade" id="advanced"> 
				@include('titles.titleMenegment.metadata.partials.advanced.advanced')
			</div> 
			<div class="tab-pane fade" id="castAndCrew"> 
				@include('titles.titleMenegment.metadata.partials.castAndCrew.castAndCrew')
			</div> 
			<div class="tab-pane fade" id="images"> 
				@include('titles.titleMenegment.metadata.partials.images.images')
			</div> 								
			<div class="tab-pane fade" id="subtitles"> 
				@include('titles.titleMenegment.metadata.partials.subtitles.subtitles')
			</div> 
			<div class="tab-pane fade" id="ageRates"> 
				@include('titles.titleMenegment.metadata.partials.ageRates.ageRates')
			</div> 
			<div class="tab-pane fade" id="series"> 
				@include('titles.titleMenegment.metadata.partials.series.series')
			</div> 
			<div class="tab-pane fade" id="seo"> 
				@include('titles.titleMenegment.metadata.partials.seo.seo')
			</div> 
		</div> 
	</div>						
</div>
<div class="col-lg-12">
	<button class="btn btn-success" id="saveChanges">Save Changes</button>
</div>	
<script>
$(document).ready(function(){
	$(document).on('click', '#saveChanges', function(){
		autoCloseMsgHide();
		var thisEllement = $(this);
		thisEllement.html('Saving...');	
		var basicForm = $('#basicForm').serialize();
		var advancedForm = $('#advancedForm').serialize();
		var seriesForm = $('#seriesForm').serialize();
		var editFilmSubtitleForm = $('#editFilmSubtitleForm').serialize();
		var editTrailerSubtitleForm = $('#editTrailerSubtitleForm').serialize();
		$('.loading').show();

            $.when(
                $.ajax({
                    type: 'POST',
                    url: '{{ url() }}/titles/metadata/basicSaveChanges',
                    data: basicForm,
                }),
                $.ajax({
                    type: 'POST',
                    url: '{{ url() }}/titles/metadata/advancedSaveChanges',
                    data: advancedForm,
                }),
                $.ajax({
                    type: 'POST',
                    url: '{{ url() }}/titles/metadata/seriesSaveChanges',
                    data: seriesForm,
                }),
                $.ajax({
                    type: 'POST',
                    url: '{{ url() }}/titles/metadata/subtitles/subtitlesSaveChanges',
                    data: editFilmSubtitleForm+'&'+editTrailerSubtitleForm
                })
            ).done(function(){
				$('.loading').hide();
				thisEllement.html('Save Changes');
            }).fail(function(){
				$('.loading').hide();
				thisEllement.html('Save Changes');
				autoCloseMsg(1,'Bad Request',5000);  //show error message	
            });		
	});
	
	$('form').submit(function(){
		return false;
	});
});
</script>	



<script>
// Cast And Crew
$(document).ready(function(){
	//Person Image Upload

	/*$(document).on('click', '#uploadifive-person', function(){

		var personId = $('input[name="personId"]').val();
		var _token = $('input[name="_token"]').val();
		var url = this_.data('url');
		console.log('opens');

	});	*/
	//End	
});
</script>				
@stop