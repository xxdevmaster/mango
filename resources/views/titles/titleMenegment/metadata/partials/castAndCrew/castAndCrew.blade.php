<div id="castAndCrew">
@if(isset($castAndCrew['person']))
	@foreach($castAndCrew['person'] as $persons)
		<div class="panel personsPanel">
			<div class="panel-body p-t-10">
				<div class="media-main">
					<a class="pull-left" href="#">
						@if(isset($persons->img))
							<img class="thumb-md img-circle bx-s" src="http://cinecliq.assets.s3.amazonaws.com/persons/{{$persons->img}}" alt="" />
						@else
							<img class="thumb-md img-circle bx-s" src="http://cinecliq.assets.s3.amazonaws.com/persons/nophoto.png" alt="" />
						@endif
					</a>			
					<div class="pull-right btn-group-sm">
						<button class="btn btn-default tooltips editPerson" data-personid="{{$persons->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Edit">
							<i class="fa fa-pencil-square-o"></i>
						</button>
						<button class="btn btn-danger removePerson tooltips" data-personid="{{$persons->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Delete">
							<i class="fa fa-close"></i>
						</button>
					</div>
					<div class="info col-md-3">
						<h4 style="margin-top:0">{{ @isset($persons->title) ? $persons->title : '' }}</h4>					
					</div>
					
					<ol class="breadcrumb pull-left margin-top10">
					  <li>{{ @isset($persons->jobs_title) ? $persons->jobs_title : '' }}</li>
					</ol>				
				</div>
				<div class="clearfix"></div>
			</div>
		</div>		
	@endforeach
@endif

<button class="btn btn-default margin-top10" id="addNewPersonModalOpen">+Add Person</button>
</div>	

@include('titles.titleMenegment.metadata.partials.castAndCrew.forms.newPersonForm')
<div id="editPersonForm">
	
</div>


