<div id="castAndCrew">
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
					<div class="pull-right btn-group-sm">
						<button class="btn btn-default tooltips editPerson" data-personid="{{ $persons->id }}" data-placement="top" data-toggle="tooltip" data-original-title="Edit">
							<i class="fa fa-pencil-square-o"></i>
						</button>
						<button class="btn btn-danger removePerson tooltips" data-personid="{{ $persons->id }}" data-placement="top" data-toggle="tooltip" data-original-title="Delete">
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
<div id="editPersonForm"></div>
<div id="newPersonFormDiv"></div>

<script>
$(document).ready(function(){
	$('#addNewPersonModalOpen').click(function(){
		autoCloseMsgHide();

		$.post('/titles/metadata/castAndCrew/getNewPersonForm', function(response){
			$("#newPersonFormDiv").html(response);
			$('#addNewPersonModal').modal('show');
		});

	});

	//Remove Person
	$(".removePerson").click(function() {
		autoCloseMsgHide();
		var personID = $(this).data('personid');
		bootbox.confirm('Do you really want to delete this Person?', function(result) {
			if(result) {
				$('.loading').show();				
				$.post('/titles/metadata/castAndCrew/personRemove', {personID:personID},function(response){
					if(!response.error) {
						$('#castAndCrew').html(response);
						$('.loading').hide();
					}else {
						$('.loading').hide();
						autoCloseMsg(1, response.error, 5000);
					}					
				});
			}
		});			
	});	

	//Open person edit form modal
	$('.editPerson').click(function(e){
		e.stopPropagation();
		autoCloseMsgHide();
		var personID = $(this).data('personid');
		
		$.post('/titles/metadata/castAndCrew/getPersonEditForm', {personID:personID}, function(response){
			if(!response.error) {
				$('#editPersonForm').html(response);
				$('#editPersonModal').modal('show');
			}else
				autoCloseMsg(1, response.error, 5000);
		});
		
	});	
	
});
</script>



