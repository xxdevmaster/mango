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
							@if(isset($thisPerson[0]->img))
								<?
									$personImg = $thisPerson[0]->img;
								?>
							@else
								<?
									$personImg = 'nophoto.png';
								?>
							@endif
							<img src="http://cinecliq.assets.s3.amazonaws.com/persons/{{$personImg}}" class="person_image" style="width: 120px;margin:0 20px 14px 0; float:left;" id="person_image" />
						</output>
						<div class="form-group">							
							<span class="pull-right" id="removePersonImage" style="cursor:pointer">
								<i class="glyphicon glyphicon-remove-circle fa-lg"></i>  
							</span>	
						</div>
						<div class="form-group">We strongly recommend the following format: 375x375px, JPG or PNG, 500KB maximum size.</div>
						<div class="form-group">
							<div id="uploadifive-person" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/personImageUpload" style="height: 29px; line-height: 29px; overflow: hidden; position: relative; text-align: center; width: 129px;">Upload Image
								<input type="file" id="person_img" name="person_img" style="display: none;">
								<input type="file" style="font-size: 29px; opacity: 0; position: absolute; right: -3px; top: -3px; z-index: 999;" multiple="multiple">
							</div>
						</div>			
					</div>
					<div class="clearfix" style="clear:both">
						<ul class="nav nav-tabs ">										
									<li class="active"> 
										<a href="#tabPersonLocale_en" class="tab-level2" data-toggle="tab" aria-expanded="true"> 
											<span class="visible-xs"><?php echo ucfirst(array_search($allLocales['en'], $allLocales));?></span> 
											<span class="hidden-xs">{{$allLocales['en']}}</span> 
										</a> 
									</li>
							@if(isset($LocalePersons))
								@foreach($LocalePersons as $locale)
									<li class=""> 
										<a href="#tabPersonLocale_{{$locale->locale}}" class="tab-level2" data-toggle="tab" aria-expanded="true"> 
											<span class="visible-xs"><?php echo ucfirst(array_search($allLocales[$locale->locale], $allLocales));?></span> 
											<span class="hidden-xs">{{$allLocales[$locale->locale]}}</span> 
										</a> 
									</li>
								@endforeach
							@endif					
						</ul>
						<div class="tab-content">
							@if(isset($thisPerson))
								@foreach($thisPerson as $person)
									<input type="hidden" name="personId" value="{{$person->id}}"> 
									<input type="hidden" name="person[en][personId]" value="{{$person->id}}">
									<div class="tab-pane active" id="tabPersonLocale_en">
										<div class="form-group">
											<label for="title">Name</label>
											<input type="text" class="form-control" id="title" name="title" value="{{$person->title}}">
										</div>												
										<div class="form-group">
											<label class="col-md-2 control-label">Bio</label>
											<textarea class="form-control" rows="4" name="brief" style="resize:none;">{{$person->brief}}</textarea>
										</div>																					
									</div>
								@endforeach
							@endif								
							@if(isset($LocalePersons))
								@foreach($LocalePersons as $locale)
									<div class="tab-pane" id="tabPersonLocale_{{$locale->locale}}">
										<input type="hidden" name="persons[{{$locale->locale}}][localeId]" value="{{$locale->id}}">
										<div class="form-group">
											<span class="pull-right" id="removePersonLocale" style="cursor:pointer" data-localeid="{{ $locale->id }}">
												<i class="glyphicon glyphicon-remove-circle fa-lg"></i>  
											</span>								
											<label for="title">Name</label>
											<input type="text" class="form-control" id="title" name="persons[{{$locale->locale}}][title]" value="{{$locale->title}}">
										</div>												
										<div class="form-group">
											<label class="col-md-2 control-label">Bio</label>
											<textarea class="form-control" rows="4" name="persons[{{$locale->locale}}][brief]" style="resize:none;">{{$locale->brief}}</textarea>
										</div>																					
									</div>
								@endforeach
							@endif	
							<div class="form-group">
								<select class="form-control" id="personNewLocale" name="personNewLocale">
									<option selected="selected" value="">+ Add New Metadata Language</option>
									@if(isset($allLocales) && is_array($allLocales))
										@foreach($allLocales as $val => $key)														
											<option value="{{ $val }}">{{ $key }}</option>													
										@endforeach
									@endif
								</select>
							</div>				
						</div>
					</div>
				</form>
			</div>
			<input type="hidden" name="template" value="basic"> 
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="personEdit">Add</button>
			</div>
		</div>
  </div>
</div>
<script>
$(document).ready(function(){
	//Create person new locale

	var personId = $('input[name="personId"]').val();
	var _token = $('input[name="_token"]').val();
	var url = '{{url()}}/titles/metadata/castAndCrew/personImageUpload';
	CHUpload(url, 'uploadifive-button', {'personId':personId, '_token':_token }, function(data){
		var response = JSON.parse(data);
		if(!response.error) {
			$('#person_image').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/persons/'+response.message);
			CHxhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId, personId:personId, template:'castAndCrew'},function(data){
				if(data) {
					$('#castAndCrew').html(data);
				}
			});
		}
		else {
			$(this_).parent().find('.media-body').find('.responseMessage').remove();
			$('#person_image_text').html('<span class="text-danger responseMessage">'+response.message+'</span>')
		}
	});


	$(document).on('change', '#personNewLocale', function() {







		autoCloseMsgHide();
		var title = $('#personNewLocale option:selected').html();
		var locale = $('#personNewLocale option:selected').val();
		var personId = $('input[name="personId"]').val();
		var confirmText = 'Please Confirm adding '+title+' translation';
		
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();				
				xhr('{{url()}}/titles/metadata/castAndCrew/personAddNewLocale','POST',{personId:personId,locale:locale},function(data){					
					if(data) {
						xhr('/titles/metadata/castAndCrew/getPersonEditForm','POST',{personId:personId},function(data){
							if(data) {
								$("#editFormInner").html($(data).find("#editPersonModalForm"));
								$('a[href="#tabPersonLocale_'+locale+'"]').tab('show');
								$('.loading').hide();
							}else {
								$('.loading').hide();
								autoCloseMsg(1, 'Language is dont Added', 5000);
							}
						});
					}else {
						$('.loading').hide();
						autoCloseMsg(1, 'Language is dont Added', 5000);
					}					
				});				
			}			
		});				
	});
	//End	
});
</script>