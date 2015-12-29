<?php
	$personLocale = '';
	$localeId = '';
	$localeTitle = '';
	$localeBrief = '';
	
	$tabClassActive = '';
	$personLocaleNavTabs = '';
	$personLocaleTabContents = '';	
?>
@if(isset($LocalePersons) && is_object($LocalePersons))
	@foreach($LocalePersons as $locale)
		@if(array_key_exists($locale->locale, $allLocales))
<?php
	if(isset($locale->locale))
		$personLocale = $locale->locale;
	if(isset($locale->id))
		$localeId = $locale->id;	
	if(isset($locale->title))
		$localeTitle = $locale->title;	
	if(isset($locale->brief))
		$localeBrief = $locale->brief;
	$personLocaleNavTabs = '
		<li class=""> 
			<a href="#tabPersonLocale_'.$personLocale.'" class="tab-level2" data-toggle="tab" aria-expanded="true"> 
				<span class="visible-xs">'.
					ucfirst(array_search($allLocales[$personLocale], $allLocales))
				.'</span> 
				<span class="hidden-xs">'.
					$allLocales[$personLocale]											
				.'</span> 
			</a> 
		</li>	
	';
	
	$personLocaleTabContents = '
		<div class="tab-pane" id="tabPersonLocale_'.$personLocale.'">
			<input type="hidden" name="persons['.$personLocale.'][localeId]" value="'.$localeId.'">
			<div class="form-group">
				<span class="pull-right" id="removePersonLocale" style="cursor:pointer" data-localeid="'.$localeId.'">
					<i class="glyphicon glyphicon-remove-circle fa-lg"></i>  
				</span>								
				<label for="title">Name</label>
				<input type="text" class="form-control" id="title" name="persons['.$personLocale.'][title]" value="'.$localeTitle.'">
			</div>												
			<div class="form-group">
				<label class="col-md-2 control-label">Bio</label>
				<textarea class="form-control" name="persons['.$personLocale.'][brief]" style="resize:none;min-height:50px">'.$localeBrief.'</textarea>
			</div>																					
		</div>		
	';
?>
		@endif
	@endforeach
@endif
<div class="modal fade" id="editPersonModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Edit Cast/Crew Member</h4>
			</div>
			<div class="modal-body" id="editFormInner">
				<form id="editPersonModalForm" role="form">
					<div class="form-group" id="person_image_text"></div>
					<div class="media-body">
						<output id="list">
							<img src="http://cinecliq.assets.s3.amazonaws.com/persons/{{$thisPerson[0]->img}}" class="person_image pull-left" width="120" style="margin:0 20px 14px 0;" id="person_image" />
						</output>
						<div class="form-group">							
							<span class="pull-right" id="removePersonImage" style="cursor:pointer">
								<i class="glyphicon glyphicon-remove-circle fa-lg"></i>  
							</span>	
						</div>
						<div class="form-group">We strongly recommend the following format: 375x375px, JPG or PNG, 500KB maximum size.</div>
						<div class="form-group">
							<div id="uploadifive-person" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/personImageUpload">Upload Image								
							</div>
						</div>
						<input type="hidden" name="personImage" value="{{$thisPerson[0]->img}}" />
					</div>
					<div class="clearfix" style="clear:both">
						<ul class="nav nav-tabs ">										
							<li class="active"> 
								<a href="#tabPersonLocale_en" class="tab-level2" data-toggle="tab" aria-expanded="true"> 
									<span class="visible-xs"><?php echo ucfirst(array_search($allLocales['en'], $allLocales));?></span> 
									<span class="hidden-xs">
										@if(array_key_exists('en', $allLocales))
											{{$allLocales['en']}}
										@endif
									</span> 
								</a> 
							</li>
							{!! $personLocaleNavTabs !!}
						</ul>
						<div class="tab-content">
							@if(isset($thisPerson))
								@foreach($thisPerson as $person)
									<input type="hidden" name="personId" value="{{isset($person->id) ? $person->id : ''}}"> 
									<input type="hidden" name="person[en][personId]" value="{{$person->id}}">
									<div class="tab-pane active" id="tabPersonLocale_en">
										<div class="form-group">
											<label for="title">Name</label>
											<input type="text" class="form-control" id="title" name="title" value="{{isset($person->title) ? $person->title : ''}}">
										</div>												
										<div class="form-group">
											<label class="col-md-2 control-label">Bio</label>
											<textarea class="form-control" rows="1" name="brief" style="resize:none;min-height:50px">{{isset($person->brief) ? $person->brief : ''}}</textarea>
										</div>																					
									</div>
								@endforeach
							@endif								
							{!! $personLocaleTabContents !!}	
							<div class="form-group">
								<select class="form-control" id="personNewLocale" name="personNewLocale">
									<option selected="selected" value="">+ Add New Metadata Language</option>
									@if(isset($allUniqueLocales) && is_array($allUniqueLocales))
										@foreach($allUniqueLocales as $val => $key)														
											<option value="{{ $val }}">{{ $key }}</option>													
										@endforeach
									@endif
								</select>
							</div>				
						</div>
					</div>
					<input type="hidden" name="filmId" value=""/>
				</form>
			</div>
			<input type="hidden" name="template" value="basic"> 
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="personEdit">Add</button>
			</div>
		</div>
  </div>
<script>
$(document).ready(function(){
	var filmId = $('input[name="filmId"]').val();
	var personId = $('input[name="personId"]').val();
	var _token = $('input[name="_token"]').val();
	
	CHUpload('{{url()}}/titles/metadata/castAndCrew/personImageUpload', 'uploadifive-person', 'Upload Image', {'filmId':filmId, 'personId':personId, '_token':_token }, function(data){
		var response = JSON.parse(data);
		if(!response.error) {
			$('#person_image').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/persons/'+response.message);
			$('input[name="personImage"]').val(response.message);
		}
		else {
			autoCloseMsg(response.error, response.message, 5000);
		}
	});


	$('#personNewLocale').change(function() {
		autoCloseMsgHide();
		var title = $('#personNewLocale option:selected').html();
		var locale = $('#personNewLocale option:selected').val();
		var personId = $('input[name="personId"]').val();
		var confirmText = 'Please Confirm adding '+title+' translation';
		
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();				
				$.post('{{url()}}/titles/metadata/castAndCrew/personAddNewLocale', {filmId:filmId, personId:personId,locale:locale},function(response){					
					if(response.error == 0) {
						$.post('/titles/metadata/castAndCrew/getPersonEditForm', {filmId:filmId, personId:personId},function(data){
							if(data) {
								$("#editFormInner").html($(data).find("#editPersonModalForm"));
								$('a[href="#tabPersonLocale_'+locale+'"]').tab('show');
								$('.loading').hide();
							}else {
								$('.loading').hide();
								autoCloseMsg(1, title+' translation is dont Added', 5000);
							}
						});
					}else {
						$('.loading').hide();
						autoCloseMsg(1, title+' translation is dont Added', 5000);
					}					
				});				
			}			
		});				
	});
	//End
	
	//Remove person locale
	$('#removePersonLocale').click(function(){
		var localeId = $(this).data("localeid");
		var personId = $('input[name="personId"]').val();
		confirmText = 'Do you realy want to delete person locale';
		
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();				
				$.post('{{url()}}/titles/metadata/castAndCrew/removePersonLocale', {localeId:localeId},function(data){					
					if(data) {
						$.post('{{url()}}/titles/metadata/castAndCrew/getPersonEditForm', {personId:personId},function(data){							
							if(data) {
								$("#editFormInner").html($(data).find("#editPersonModalForm"));
								$('.loading').hide();
							}else {
								$('.loading').hide();
							}
						});						
					}else {
						$('.loading').hide();
					}					
				});
			}
		});			
	});
	//End	

	//Edit person
	$('#personEdit').click(function() {
		autoCloseMsgHide();
		
		$("#editPersonModalForm input[name='filmId']").val(filmId);
		var editPersonModalForm = $("#editPersonModalForm").serialize();
		$("#editPersonModal").modal('hide');
		$.post('{{url()}}/titles/metadata/castAndCrew/personEdit', editPersonModalForm, function(data){					
			if(data) {
				$.post('{{url()}}/titles/metadata/basic/getTemplate', {filmId:filmId, template:'castAndCrew'},function(data){							
					if(data) {
						$('#castAndCrew').html(data);
						autoCloseMsg(0, 'Person is updated', 5000);
					}							
				});						
			}else {
				autoCloseMsg(1, 'Person is dont updated', 5000);
			}					
		});
	});	
	//End edit person
	

	//Remove person Image
	$('#removePersonImage').click(function(){
		$('.loading').show();
		$('input[name="personImage"]').val('nophoto.png');
		$('#person_image').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/persons/nophoto.png');
		$('.loading').hide();
	});
	//End	
});
</script>
</div>