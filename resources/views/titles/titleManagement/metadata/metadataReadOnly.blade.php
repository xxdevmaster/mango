@extends('titles.titleManagement.titleManagementReadOnly')
@section('titleManagementReadOnly')	
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
					@include('titles.titleManagement.metadata.partials.basic.basicReadOnly')
				</div>
				<div class="tab-pane fade" id="advanced"> 
					@include('titles.titleManagement.metadata.partials.advanced.advancedReadOnly')
				</div> 
				<div class="tab-pane fade" id="castAndCrew"> 
					@include('titles.titleManagement.metadata.partials.castAndCrew.castAndCrewReadOnly')
				</div> 
				<div class="tab-pane fade" id="images"> 
					@include('titles.titleManagement.metadata.partials.images.imagesReadOnly')
				</div> 								
				<div class="tab-pane fade" id="subtitles"> 
					@include('titles.titleManagement.metadata.partials.subtitles.subtitlesReadOnly')
				</div> 
				<div class="tab-pane fade" id="ageRates"> 
					@include('titles.titleManagement.metadata.partials.ageRates.ageRatesReadOnly')
				</div> 
				<div class="tab-pane fade" id="series"> 
					@include('titles.titleManagement.metadata.partials.series.seriesReadOnly')
				</div> 
				<div class="tab-pane fade" id="seo"> 
					@include('titles.titleManagement.metadata.partials.seo.seoReadOnly')
				</div> 
			</div> 
		</div>						
	</div>
@stop