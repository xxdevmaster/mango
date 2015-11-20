<form id="editPersonForm_" name="editPersonForm_" action="" method="post" role="form">
<div id="editPersonForm">	
	<div class="modal fade" id="editPersonModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Edit Cast/Crew Member</h4>
		  </div>
		  <div class="modal-body">
			<div class="form-group">
				<output id="list">
					<img src="http://cinecliq.assets.s3.amazonaws.com/persons/nophoto.png" class="person_image" style="width: 120px;margin:0 20px 14px 0; float:left;" id="person_image_">
				</output>			
				<p>We strongly recommend the following format: 375x375px, JPG or PNG, 500KB maximum size.</p>
				<button type="button" class="btn btn-primary" id="uploadImage" style="position:relative;">Upload Image
					<input type="file" id="files" name="personImage" style="position:absolute;top:0;left:0;height:33px;width:112px;opacity:0;" />
				</button>			
			</div> 
			<div class="clearfix" style="clear:both">
				<ul class="nav nav-tabs ">										
							<li class="active"> 
								<a href="#tabPersonLocale_en" data-toggle="tab" aria-expanded="true"> 
									<span class="visible-xs"><i class="fa fa-cog"></i></span> 
									<span class="hidden-xs">{{$allLocales['en']}}</span> 
								</a> 
							</li>
					@if(isset($LocalePersons))
						@foreach($LocalePersons as $locale)
							<li class=""> 
								<a href="#tabPersonLocale_{{$locale->locale}}" data-toggle="tab" aria-expanded="true"> 
									<span class="visible-xs"><i class="fa fa-cog"></i></span> 
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
							<input type="hidden" name="person[{{$person->locale}}][localeId]" value="{{$person->id}}">
							<div class="tab-pane active" id="tabPersonLocale_en">
								<div class="form-group">
									<label for="title">Name</label>
									<input type="text" class="form-control" id="title" name="persons[en][title]" value="{{$person->title}}">
								</div>												
								<div class="form-group">
									<label class="col-md-2 control-label">Bio</label>
									<textarea class="form-control" rows="4" name="persons[en][brief]" style="resize:none;">{{$person->breif}}</textarea>
								</div>																					
							</div>
						@endforeach
					@endif								
					@if(isset($LocalePersons))
						@foreach($LocalePersons as $locale)
							<div class="tab-pane" id="tabPersonLocale_{{$locale->locale}}">
								<div class="form-group">
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
		  </div>
		<input type="hidden" name="template" value="basic"> 
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary" id="personEdit_">Add</button>
		</div>
		</div>
	  </div>
	</div>
</div>
</form>
<script>
	$(document).on('click', '#personEdit_', function() {
		autoCloseMsgHide();
		
		var editPersonForm = $("#editPersonForm_").serialize();
		console.log(editPersonForm);
		
		
		var filmId = $('input[name="filmId"]').val();
		//$('.loading').show();				
		xhr('{{url()}}/titles/metadata/castAndCrew/personEdit','POST', editPersonForm ,function(data){					
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
	});		
function handleFileSelect(evt) {
	var files = evt.target.files;

	for (var i = 0, f; f = files[i]; i++) {
	  if (!f.type.match('image.*')) {
		continue;
	  }
	  var reader = new FileReader();
	  reader.onload = (function(theFile) {
		return function(e) {
			$('.person_image').attr('src', e.target.result);
			$('.person_image').attr('title', escape(theFile.name));
		};
	  })(f);
	  reader.readAsDataURL(f);
	}
}

document.getElementById('files').addEventListener('change', handleFileSelect, false);
</script>