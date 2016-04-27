<ul class="nav nav-tabs">
	<li class="active">
		<a href="#tab_filmPosterImages" class="tab-level2" data-toggle="tab" aria-expanded="true">Film Poster Images</a>
	</li>	
	<li class="">
		<a href="#tab_filmSplashImages" class="tab-level2" data-toggle="tab" aria-expanded="true">Film Splash Image</a>
	</li>
</ul>
<div class="tab-content">
	<div class="tab-pane fade in active" id="tab_filmPosterImages">
<?php
$i = 0;
$tabsLi = '';
$tabPane = '';
$active = 'active';
$fade = 'fade in';
$filmPosterImage = 'nocover.png';
$uploadScript = '';

if(isset($metadata['images']['localeFilms']) && is_array($metadata['images']['localeFilms'])) {
	foreach ($metadata['images']['localeFilms'] as $locales) {
		if(!empty($locales['locale']) && array_key_exists($locales['locale'], $allLocales)){
			if ($i != 0){
				$active = '';
				$fade = 'fade';
			}
			
			if ($locales['cover'])
				$filmPosterImage = $locales['cover'];
			else
				$filmPosterImage = 'nocover.png';
			
			$tabsLi .= '
							<li class="'.$active.'  imagesLocalesTabs" data-locale="'.$locales['locale'].'">
								<a href="#tab_covers_locale_'.$locales['locale'].'" class="tab-level1" data-toggle="tab" aria-expanded="true">
									<span class="visible-xs">'.ucfirst(array_search($allLocales[$locales['locale']], $allLocales)).'</span> 
									<span class="hidden-xs">'.$allLocales[$locales['locale']].'</span>
								</a>
							</li>
						';
			
			$tabPane .= '
							<div class="tab-pane '.$fade.' '.$active.'" id="tab_covers_locale_'.$locales['locale'].'">
								<div class="media">
									<button class="pull-right btn btn-default btn-sm removePosterImage" data-locale="'.$locales['locale'].'" data-localeid="'.$locales['id'].'" aria-hidden="true" style="cursor:pointer">
										<i class="fa fa-close"></i> 
									</button>										
									<div class="col-sm-7 col-md-7">
										<img src="http://cinecliq.assets.s3.amazonaws.com/files/'.$filmPosterImage.'" alt="..." id="cover_imgview" class="cover_imgview_'.$locales['locale'].'" style="max-width:250px">
									</div>
									<div class="media-body">
										<div class="form-group">Poster image must be uploaded in the 2:3 aspect ratio. We strongly recommend the following format: 400x600px, JPG or PNG, 500KB maximum size.</div>
										<div class="form-group" id="cover_text"></div>
										<div class="form-group">												
											<div id="cover_img_'.$locales['locale'].'" class="uploadifive-button">Upload Image
												<input type="file" name="cover_img" style="display: none;" accept="image/*" />
												<input type="file" name="cover_img" style="font-size: 29px; opacity: 0; position: absolute; right: -3px; top: -3px; z-index: 999;" accept="image/*" />														
											</div>
										</div>										
									</div>
								</div>
							</div>
						';
						
			$uploadScript .= '
							CHUpload("'.url().'/titles/metadata/castAndCrew/posterImageUpload", "cover_img_'.$locales['locale'].'","Upload Image", {"filmID":"'.$film->id.'", "locale":"'.$locales['locale'].'", "_token":"'.csrf_token().'" }, function(data){
								var response = JSON.parse(data);
								if(!response.error){
									autoCloseMsg(0, "Poster Image was uploaded succesfully", 5000);
									$(".cover_imgview_'.$locales['locale'].'").attr("src", "http://cinecliq.assets.s3.amazonaws.com/files/"+response.message);			
								}			
								else {
									autoCloseMsg(1, response.message, 5000);
								}				
							});		
						';
			++$i;
		}
	}
}
?>
		<ul class="nav nav-tabs">
			{!! $tabsLi !!}
		</ul>
		<div class="tab-content">
			{!! $tabPane !!}
		</div>			
	</div>	
	<div class="tab-pane fade" id="tab_filmSplashImages">
				<div class="media">
					<button class="pull-right btn btn-default btn-sm" id="removeTSplashImage">
						<i class="fa fa-close"></i>  
					</button>
					<div class="col-sm-8 col-md-8">
						<?
							if(!empty($film->tsplash))
								$tsplash = $film->tsplash;
							else
								$tsplash = 'black.png';
						?>
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{ $tsplash }}" alt="" id="tsplash_imgview" width="250">
					</div>  
					<div class="media-body">
						<div class="form-group">Trailer Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<div class="form-group">
							<div id="uploadifive-tsplash_img" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/tsplashImageUpload">Upload Image
								<input type="file" id="tsplash_img" name="tsplash_img">
							</div>
						</div>
					</div>
				</div>
				<div class="media">
					<div class="col-md-12">
						<hr>
					</div>
				</div>
				<div class="media">
					<button class="pull-right btn btn-default btn-sm" id="removeFSplashImage">
						<i class="fa fa-close"></i>  
					</button>
					<div class="col-sm-8 col-md-8">
						<?
							if(!empty($film->fsplash))
								$fsplash = $film->fsplash;
							else
								$fsplash = 'black.png';
						?>					
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{ $fsplash }}" alt="..." id="fsplash_imgview" width="250">
					</div>  
					<div class="media-body">
						<div class="form-group">Film Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<div class="form-group">
							<div id="uploadifive-fsplash_img" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/fsplashImageUpload">Upload Image
								<input type="file" name="fsplash_img">
								<input type="file" multiple="multiple">
							</div>
						</div>
					</div>
				</div>		
	</div>
</div>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<script>
$(document).ready(function(){
	{!! $uploadScript !!}
	
	var filmID = $('input[name="filmID"]').val();
	
	CHUpload("{{url()}}/titles/metadata/castAndCrew/tsplashImageUpload", "uploadifive-tsplash_img", 'Upload Image', {"filmID":filmID, "_token":"{{csrf_token()}}" }, function(data){
		var response = JSON.parse(data);
		if(!response.error){
			autoCloseMsg(0, "Trailer Splash Image was uploaded succesfully", 5000);
			$("#tsplash_imgview").attr("src", "http://cinecliq.assets.s3.amazonaws.com/splash/"+response.message);			
		}			
		else {
			autoCloseMsg(1, response.message, 5000);
		}				
	});	
	
	CHUpload("{{url()}}/titles/metadata/castAndCrew/fsplashImageUpload", "uploadifive-fsplash_img", 'Upload Image', {"filmID":filmID, "_token":"{{csrf_token()}}" }, function(data){
		var response = JSON.parse(data);
		if(!response.error){
			autoCloseMsg(0, "Film Splash Image was uploaded succesfully", 5000);
			$("#fsplash_imgview").attr("src", "http://cinecliq.assets.s3.amazonaws.com/splash/"+response.message);			
		}			
		else {
			autoCloseMsg(1, response.message, 5000);
		}				
	});
	
	$(".removePosterImage").click(function(){	
		autoCloseMsgHide();
		var localeID = $(this).data('localeid');
		var locale = $(this).data('locale');
		
		bootbox.confirm('Do you realy want to delete Cover Image', function(result) {
			if(result){
				$.post('{{url()}}/titles/metadata/castAndCrew/posterImageRemove', {localeID:localeID }, function(){
					$('.cover_imgview_'+locale).attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/nocover.png');
				});
			}
		});
	});	
	
	$("#removeTSplashImage").click(function(){
		autoCloseMsgHide();
		bootbox.confirm('Do you realy want to delete trailer Splash', function(result) {
			if(result){
				$.post('{{url()}}/titles/metadata/castAndCrew/tsplashImageRemove', function(){
					$('#tsplash_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/splash/black.png');
				});
			}
		});
	});

	$('#removeFSplashImage').click(function(){		
		autoCloseMsgHide();
		bootbox.confirm('Do you realy want to delete film Splash', function(result) {
			if(result){
				$.post('{{url()}}/titles/metadata/castAndCrew/fsplashImageRemove', function(){
					$('#fsplash_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/splash/black.png');
				});
			}
		});
	});
	
});
</script>