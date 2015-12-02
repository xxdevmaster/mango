@extends('layout')

{{--@asaha('dddccvbcbcb')--}}

@section('content')
	<link href="/assets/nestable/jquery.nestable.css" rel="stylesheet" />	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="clearfix">
						<div class="pull-left">
							<h4 class="text-right">{{ $film->title }}</h4>							
						</div>
						<div class="pull-right">
							<h4>Invoice # <br>
								<strong>2015-04-23654789</strong>
							</h4>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="pull-left col-md-2">
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" class="image-responsive" alt="" width="100%" height="auto">
							</div>
							<div class="pull-left col-md-3">
								<div class="list-group no-border mail-list ">
                                  <a href="#" class="list-group-item active"><i class="fa fa-download m-r-5"></i>Inbox <b>(8)</b></a>
                                  <a href="#" class="list-group-item"><i class="fa fa-star-o m-r-5"></i>Starred</a>
                                  <a href="#" class="list-group-item"><i class="fa fa-file-text-o m-r-5"></i>Draft <b>(20)</b></a>
                                  <a href="#" class="list-group-item"><i class="fa fa-paper-plane-o m-r-5"></i>Sent Mail</a>
                                  <a href="#" class="list-group-item"><i class="fa fa-trash-o m-r-5"></i>Trash <b>(354)</b></a>
                                </div>
							</div>
						</div>
					</div>
					<hr>
					<div class="m-h-50">
						<div class="col-lg-12"> 
							<ul class="nav nav-tabs"> 
								<li class="active"> 
									<a href="#basic" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-home"></i></span> 
										<span class="hidden-xs">Basic</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#advanced" data-toggle="tab" class="tab-level0" aria-expanded="true"> 
										<span class="visible-xs"><i class="fa fa-user"></i></span> 
										<span class="hidden-xs">Advanced</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#castAndCrew" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-envelope-o"></i></span> 
										<span class="hidden-xs">Cast & Crew</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#images" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-cog"></i></span> 
										<span class="hidden-xs">Images</span> 
									</a> 
								</li>								
								<li class=""> 
									<a href="#subtitles" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-home"></i></span> 
										<span class="hidden-xs">Subtitles</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#ageRates" data-toggle="tab" class="tab-level0" aria-expanded="true"> 
										<span class="visible-xs"><i class="fa fa-user"></i></span> 
										<span class="hidden-xs">Age Ratings</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#series" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-envelope-o"></i></span> 
										<span class="hidden-xs">Series</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#seo" data-toggle="tab" class="tab-level0" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-cog"></i></span> 
										<span class="hidden-xs">Seo</span> 
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
				</div>
			</div>

		</div>
		<input type="hidden" name="filmId" value="{{ $film->id }}">
	</div>	
	
	
<link href="/assets/dropzone/dropzone.css" rel="stylesheet" type="text/css" />
<script src="/assets/dropzone/dropzone.min.js"></script>	

<script>
$(document).ready(function(){
	
	//Create film new locale
	$(document).on('change', '#filmsNewLanguage', function() {
		autoCloseMsgHide();
		var title = $('#filmsNewLanguage option:selected').html();
		var locale = $('#filmsNewLanguage option:selected').val();
		var filmId = $('input[name="filmId"]').val();
		var confirmText = 'Please Confirm adding '+title+' translation';
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();				
				xhr('{{url()}}/titles/metadata/basic/newLocale','POST',{filmId:filmId,locale:locale},function(data){					
					if(data != 0) {
						xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId,template:'basic'},function(data){							
							if(data) {
								$('#basic').html(data);
								$('a[href="#tabBasicLocale_'+locale+'"]').tab('show');
								autoCloseMsg(0, 'Language is added', 5000);	
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
	
	$(document).on('click', '#removeBasicLocale', function() {
		autoCloseMsgHide();
		var filmId = $('input[name="filmId"]').val();
		var localeId = $(this).data('localeid');
		var confirmText = 'Do you really want to delete : language?';
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();
				xhr('{{url()}}/titles/metadata/basic/localeRemove','POST',{localeId:localeId},function(data){
					if(data != 0) {
						xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId,template:'basic'},function(data){							
							if(data) {
								$('#basic').html(data);
								autoCloseMsg(0, 'Language is Deleted', 5000);	
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
	
	$(document).on('click', '#makeDefaultLocale', function(){
		autoCloseMsgHide();
		var filmId = $('input[name="filmId"]').val();
		var localeId = $(this).data('localeid');
		var locale = $(this).data('locale');
		var title;
		var confirmText = 'Do you really want : language default?';
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();
				xhr('{{url()}}/titles/metadata/basic/makeDefaultLocale','POST',{locale:locale, localeId:localeId, filmId:filmId},function(data){
					if(data != 0) {
						xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId,template:'basic'},function(data){							
							if(data) {
								$('#basic').html(data);
								autoCloseMsg(0, 'Language is Deleted', 5000);	
								$('.loading').hide();
							}							
						});	
					}else {
						autoCloseMsg(1, 'Defualt Language is maked', 5000);
					}
				});
			}
		});	
	});	
	
	$(document).on('click', '#saveChanges', function(){
		autoCloseMsgHide();
		var thisEllement = $(this);
		thisEllement.html('Saving...');	
		var basicForm = $('#basicForm').serialize();
		var advancedForm = $('#advancedForm').serialize();
		var seriesForm = $('#seriesForm').serialize();
		$('.loading').show();

            $.when(
                $.ajax({
                    type: 'POST',
                    url: '{{ url()}}/titles/metadata/basicSaveChanges',
                    data: basicForm,
                }),
                $.ajax({
                    type: 'POST',
                    url: '{{ url()}}/titles/metadata/advancedSaveChanges',
                    data: advancedForm,
                }),
                $.ajax({
                    type: 'POST',
                    url: '{{ url()}}/titles/metadata/seriesSaveChanges',
                    data: seriesForm,
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
	
});
</script>	



<script>
// Cast And Crew
$(document).ready(function(){
	
	$(document).on('click', '#addNewPersonModalOpen', function(){
		$('#addNewPersonModal').modal('show');
		$('html').css({'overflow-y':'hidden'});
	});	
	
	$(document).on('click', '#addNewPersonModalClose', function(){
		$('#addNewPersonModal').modal('hide');
		$('html').css({'overflow-y':'auto'});
	});
	

	
	
	$('#addNewPersonModal').on('hidden.bs.modal', function (e) {
		$('html').css({'overflow-y':'auto'});
	});	

	
	
	
	// Add New Person
	$(document).on('click', '#actorPosition', function(){
		$("#input-jobs").tokenInput("clear");
		$("#input-jobs").tokenInput("add", {id: "28", title: "Actors / Actor"});
	});	
	
	$(document).on('click', '#directorPosition', function(){
		$("#input-jobs").tokenInput("clear");
		$("#input-jobs").tokenInput("add", {id: "22", title: "Directing / Director"});
	});
	
	$(document).on('click', '#addNewPerson', function(){
		autoCloseMsgHide();
		$('#addNewPersonModal').modal('hide');
		$('html').css({'overflow-y':'auto'});
		
		var newPersonForm = $("#newPersonForm").serialize();
		var filmId = $("input[name='filmId']").val();
		$('.loading').show();
		
		$.post('{{url()}}/titles/metadata/castAndCrew/personCreate', newPersonForm, function(data){
			if(data != 0) {
				xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId, template:'castAndCrew'},function(data){							
					if(data) {
						$('#castAndCrew').html(data);
						$('.loading').hide();
						autoCloseMsg(0, 'Person is added', 5000);
					}							
				});						
			}else {
				$('.loading').hide();
				autoCloseMsg(1, 'Person is dont added', 5000);
			}
		});
		
	});	
	// End
	
	//Open person edit form modal
	$(document).on('click', '.editPerson', function(e){
		e.stopPropagation();
		
		var personId = $(this).data('personid');
		
		$.post('{{url()}}/titles/metadata/castAndCrew/getPersonEditForm', {personId:personId}, function(data){
			$('#editPersonForm').html(data);
			$('#editPersonModal').modal('show');
		});
		
	});
	//End


	
	//edit person
	$(document).on('click', '#personEdit', function() {
		autoCloseMsgHide();
		
		var editPersonModalForm = $("#editPersonModalForm").serialize();			
		var filmId = $('input[name="filmId"]').val();	
		$("#editPersonModal").modal('hide');
		xhr('{{url()}}/titles/metadata/castAndCrew/personEdit','POST', editPersonModalForm, function(data){					
			if(data) {
				xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId, template:'castAndCrew'},function(data){							
					if(data) {
						$('#castAndCrew').html(data);
						autoCloseMsg(0, 'Person is updated', 5000);
					}							
				});						
			}else {
				autoCloseMsg(1, 'Person is dont updates', 5000);
			}					
		});
	});	
	//End
	
	//Remove Person
	$(document).on('click', '.removePerson', function() {
		autoCloseMsgHide();
		var filmId = $("input[name='filmId']").val();
		var personId = $(this).data('personid');
		var confirmText = 'Do you really want to delete : Person?';
		
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();				
				xhr('{{url()}}/titles/metadata/castAndCrew/personRemove','POST',{personId:personId},function(data){					
					if(data != 0) {
						xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId, personId:personId, template:'castAndCrew'},function(data){							
							if(data) {
								$('#castAndCrew').html(data);
								$('.loading').hide();
								autoCloseMsg(0, 'Person  is Removed', 5000);	
							}							
						});						
					}else {
						$('.loading').hide();
						autoCloseMsg(1, 'Person  is dont Removed', 5000);
					}					
				});
			}
		});			
	});

	//Person Image Upload
	$(document).on('click', '#uploadifive-person', function(){
		var this_ = $(this);
		var personId = $('input[name="personId"]').val();
		var _token = $('input[name="_token"]').val();
		var url = this_.data('url');
		CHUpload(url, 'uploadifive-button', {'personId':personId, '_token':_token }, function(data){
			var response = JSON.parse(data);
			if(!response.error) {
				$('#person_image').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/persons/'+response.message);
				xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId, personId:personId, template:'castAndCrew'},function(data){							
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
	});	
	//End

	//Remove person locale
	$(document).on('click', '#removePersonLocale', function(){
		var localeId = $(this).data("localeid");
		var personId = $('input[name="personId"]').val();
		confirmText = 'Do you realy want to delete person locale';
		
		bootbox.confirm(confirmText, function(result) {
			if(result) {
				$('.loading').show();				
				xhr('{{url()}}/titles/metadata/castAndCrew/removePersonLocale','POST',{localeId:localeId},function(data){					
					if(data) {
						xhr('{{url()}}/titles/metadata/castAndCrew/getPersonEditForm','POST',{personId:personId},function(data){							
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
	
	//Remove person Image
	$(document).on('click', '#removePersonImage', function(){
		var personId = $('input[name="personId"]').val();
		$.post('{{url()}}/titles/metadata/castAndCrew/removePersonImage', {personId:personId}, function(data){
			if(data){
				xhr('{{url()}}/titles/metadata/basic/getTemplate','POST',{filmId:filmId, personId:personId, template:'castAndCrew'},function(data){							
					if(data) {
						$('#person_image').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/persons/nophoto.png');
						$('#castAndCrew').html(data);
					}							
				});						
			}
		});
	});
	//End
	
});
</script>
@stop