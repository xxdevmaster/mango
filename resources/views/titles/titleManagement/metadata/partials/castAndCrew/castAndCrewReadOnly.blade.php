<div>
@if(isset($metadata['castAndCrew']['person']))
	@foreach($metadata['castAndCrew']['person'] as $persons)
		<div class="panel personsPanel">
			<div class="panel-body p-t-10">
				<div class="media-main">
					<span class="pull-left">
						@if(isset($persons->img))
							<img class="thumb-md img-circle bx-s" src="http://cinecliq.assets.s3.amazonaws.com/persons/{{$persons->img}}" alt="" />
						@else
							<img class="thumb-md img-circle bx-s" src="http://cinecliq.assets.s3.amazonaws.com/persons/nophoto.png" alt="" />
						@endif
					</span>
					<div class="info col-md-3">
						<h4 style="margin-top:0">{{ @isset($persons->title) ? $persons->title : '' }}</h4>					
					</div>
					<div class="pull-right btn-group-sm">
						<button class="btn btn-default tooltips showPerson" data-personid="{{$persons->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Show Info">
							Show Info
						</button>
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
</div>	
<div id="editPersonForm"></div>
<script>
$(document).ready(function(){
	//Open person edit form modal
	$('.showPerson').click(function(e){
		e.stopPropagation();
		autoCloseMsgHide();
		var filmId = $('input[name="filmId"]').val();
		var personId = $(this).data('personid');
		
		$.post('{{url()}}/titles/metadata/castAndCrew/getPersonEditForm', {filmId:filmId, personId:personId}, function(data){
			$('#editPersonForm').html(data);
			$('#editPersonModal').modal('show');
		});
		
	});
	//End	
});
</script>



