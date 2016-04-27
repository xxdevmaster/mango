<?php
	$personLocale = '';
	$localeId = '';
	$localeTitle = '';
	$localeBrief = '';
	
	$tabClassActive = '';
	$personLocaleNavTabs = '';
	$personLocaleTabContents = '';	
?>
@if(isset($localePersons) && is_object($localePersons))
	@foreach($localePersons as $locale)
		@if(array_key_exists($locale->locale, $allLocales))
<?php
	if(isset($locale->locale))
		$personLocale = $locale->locale;
	if(isset($locale->id))
		$localeID = $locale->id;
	if(isset($locale->title))
		$localeTitle = $locale->title;	
	if(isset($locale->brief))
		$localeBrief = $locale->brief;
	$personLocaleNavTabs .= '
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
	
	$personLocaleTabContents .= '
		<div class="tab-pane" id="tabPersonLocale_'.$personLocale.'">
			<input type="hidden" name="persons['.$personLocale.'][localeID]" value="'.$localeID.'">
			<div class="form-group">
				<button class="pull-right btn btn-default btn-xs removePersonLocale" data-localeid="'.$localeID.'" type="button">
					<i class="fa fa-close fa-sm"></i>
				</button>
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
							<img src="http://cinecliq.assets.s3.amazonaws.com/persons/{{$person[0]->img}}" class="person_image pull-left" width="120" style="margin:0 20px 14px 0;" id="person_image" />
						</output>
						<div class="form-group">							
							<button class="pull-right btn btn-default btn-sm" id="removePersonImage" type="button">
								<i class="fa fa-close fa-sm"></i>
							</button>
						</div>
						<div class="form-group">We strongly recommend the following format: 375x375px, JPG or PNG, 500KB maximum size.</div>
						<div class="form-group">
							<div id="uploadifive-person" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/personImageUpload">Upload Image								
							</div>
						</div>
						<input type="hidden" name="personImage" value="{{$person[0]->img}}" />
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
							@if(isset($person))
								@foreach($person as $personInfo)
									<input type="hidden" name="personID" value="{{ isset($personInfo->id) ? $personInfo->id : '' }}">
									<input type="hidden" name="person[en][personID]" value="{{ isset($personInfo->id) ? $personInfo->id : '' }}">
									<div class="tab-pane active" id="tabPersonLocale_en">
										<div class="form-group">
											<label for="title">Name</label>
											<input type="text" class="form-control" id="title" name="title" value="{{isset($personInfo->title) ? $personInfo->title : ''}}">
										</div>												
										<div class="form-group">
											<label class="col-md-2 control-label">Bio</label>
											<textarea class="form-control" rows="1" name="brief" style="resize:none;min-height:50px">{{isset( $personInfo->brief) ? $personInfo->brief : '' }}</textarea>
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
					<script>
						$(document).ready(function(){
							var filmID = $('input[name="filmID"]').val();
							var personID = $('input[name="personID"]').val();
							var _token = $('input[name="_token"]').val();

							CHUpload('/titles/metadata/castAndCrew/personImageUpload', 'uploadifive-person', 'Upload Image', {'filmID':filmID, 'personID':personID, '_token':_token }, function(data){
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
								var personID = $('input[name="personID"]').val();

								bootbox.confirm('Please Confirm adding '+title+' translation', function(result) {
									if(result) {
										$('.loading').show();
										$.post('/titles/metadata/castAndCrew/personAddNewLocale', {personID:personID, locale:locale},function(response){
											if(!response.error) {
												$("#editFormInner").html($(response).find("#editPersonModalForm"));
												$('a[href="#tabPersonLocale_'+locale+'"]').tab('show');
												$('.loading').hide();
											}else {
												$('.loading').hide();
												autoCloseMsg(1, response.error, 5000);
											}
										});
									}
								});
							});
							//End

							//Remove person locale
							$('.removePersonLocale').click(function(){
								var localeID = $(this).data("localeid");
								var personID = $('input[name="personID"]').val();

								bootbox.confirm('Do you realy want to delete person locale', function(result) {
									if(result) {
										$('.loading').show();
										$.post('/titles/metadata/castAndCrew/removePersonLocale', {localeID:localeID, personID:personID},function(response){
											if(!response.error) {
												$("#editFormInner").html($(response).find("#editPersonModalForm"));
												$('.loading').hide();
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

								var editPersonModalForm = $("#editPersonModalForm").serialize();
								$("#editPersonModal").modal('hide');
								$.post('{{url()}}/titles/metadata/castAndCrew/personEdit', editPersonModalForm, function(response){
									if(!response.error) {
										$('#castAndCrew').html(response);
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
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="personEdit">Add</button>
			</div>
		</div>
  </div>
</div>