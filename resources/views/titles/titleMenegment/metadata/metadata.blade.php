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
							<h4 class="text-right">{{ $currentFilm[0]['title'] }}</h4>							
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
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $currentFilm[0]['cover'] }}" class="image-responsive" alt="" width="100%" height="auto">
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
									<a href="#basic" data-toggle="tab" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-home"></i></span> 
										<span class="hidden-xs">Basic</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#advanced" data-toggle="tab" aria-expanded="true"> 
										<span class="visible-xs"><i class="fa fa-user"></i></span> 
										<span class="hidden-xs">Advanced</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#castAndCrew" data-toggle="tab" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-envelope-o"></i></span> 
										<span class="hidden-xs">Cast & Crew</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#images" data-toggle="tab" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-cog"></i></span> 
										<span class="hidden-xs">Images</span> 
									</a> 
								</li>								
								<li class=""> 
									<a href="#subtitles" data-toggle="tab" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-home"></i></span> 
										<span class="hidden-xs">Subtitles</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#ageRatings" data-toggle="tab" aria-expanded="true"> 
										<span class="visible-xs"><i class="fa fa-user"></i></span> 
										<span class="hidden-xs">Age Ratings</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#series" data-toggle="tab" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-envelope-o"></i></span> 
										<span class="hidden-xs">Series</span> 
									</a> 
								</li> 
								<li class=""> 
									<a href="#seo" data-toggle="tab" aria-expanded="false"> 
										<span class="visible-xs"><i class="fa fa-cog"></i></span> 
										<span class="hidden-xs">Seo</span> 
									</a> 
								</li> 
							</ul> 
							<div class="tab-content">
								<div class="tab-pane active" id="basic">
									@include('titles.titleMenegment.metadata.partials.basic.basic')
								</div>
								<div class="tab-pane" id="advanced"> 
									@include('titles.titleMenegment.metadata.partials.advanced.advanced')
								</div> 
								<div class="tab-pane" id="castAndCrew"> 
									@include('titles.titleMenegment.metadata.partials.castAndCrew.castAndCrew')
								</div> 
								<div class="tab-pane" id="images"> 
									@include('titles.titleMenegment.metadata.partials.images.images')
								</div> 								
								<div class="tab-pane" id="subtitles"> 
									
								</div> 
								<div class="tab-pane" id="ageRatings"> 
									 
								</div> 
								<div class="tab-pane" id="series"> 
									
								</div> 
								<div class="tab-pane" id="seo"> 
									
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
		<input type="hidden" name="filmId" value="{{ $currentFilm[0]['id'] }}">
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

	//Create person new locale
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
					if(data != 0) {
						xhr('{{url()}}/titles/metadata/castAndCrew/getPersonEditForm','POST',{personId:personId},function(data){							
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
@stop