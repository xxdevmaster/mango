<form class="row" id="castAndCrewForm" name="castAndCrewForm" action="" method="post" role="form">
@if(isset($castAndCrew['person']))
	@foreach($castAndCrew['person'] as $persons)
		<div class="panel" style="padding: 5px 10px;">
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
						<a class="btn btn-default tooltips editPerson" data-personid="{{$persons->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Edit">
							<i class="fa fa-pencil"></i>
						</a>
						<a href="#" class="btn btn-danger removePerson tooltips" data-personid="{{$persons->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Delete">
							<i class="fa fa-close"></i>
						</a>
					</div>
					<div class="info col-md-3">
						<h4 style="margin-top:0">{{ @isset($persons->title) ? $persons->title : '' }}</h4>					
					</div>
					
					<ol class="breadcrumb pull-left">
					  <li>{{ @isset($persons->jobs_title) ? $persons->jobs_title : '' }}</li>
					</ol>				
				</div>
				<div class="clearfix"></div>
			</div>
		</div>		
	@endforeach
@endif

<a class="btn btn-default" data-toggle="modal" data-target="#addNewPersonModal">+Add Person</a>
</form>	
<script>
$(document).ready(function(){
	$(".editPerson").click(function(e){
		e.stopPropagation();
		
		var personId = $(this).data('personid');
		
		$.post('{{url()}}/titles/metadata/castAndCrew/getPersonEditForm', {personId:personId}, function(data){
			$('#editPersonForm').html(data);
			$('#editPersonModal').modal('show')
		});
		
	});
	
	$(document).on('click', '.removePerson', function() {
		autoCloseMsgHide();
		
		var personId = $(this).data('personid');
		var confirmText = 'Do you really want to delete : Person?';
		
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();				
				xhr('{{url()}}/titles/metadata/castAndCrew/personRemove','POST',{personId:personId},function(data){					
					if(data != 0) {
						xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{personId:personId, template:'castAndCrew'},function(data){							
							if(data) {
								$('#editPersonForm').html(data);
								$('#editPersonModal').modal('show');
								$('a[href="#personNewLocale'+locale+'"]').tab('show');
								//autoCloseMsg(0, 'Language is added', 5000);	
								$('.loading').hide();
							}							
						});						
					}else {
						autoCloseMsg(1, 'Language is dont Deleted', 5000);
					}					
				});
			}
		});			
	});	
	
	
});
</script>
@include('titles.titleMenegment.metadata.partials.castAndCrew.forms.newPersonForm')
@include('titles.titleMenegment.metadata.partials.castAndCrew.forms.editPersonForm')