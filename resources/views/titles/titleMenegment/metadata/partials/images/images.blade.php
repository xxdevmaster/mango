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
								<ul class="list-group">
									<li class="list-group-item">
										<div class="media">
											<button class="pull-right btn btn-default btn-sm removePosterImage" data-locale="'.$locales['locale'].'" data-localeid="'.$locales['id'].'" aria-hidden="true" style="cursor:pointer">
												<i class="fa fa-close"></i> 
											</button>										
											<div class="col-sm-6 col-md-8">
												<img src="http://cinecliq.assets.s3.amazonaws.com/files/'.$filmPosterImage.'" alt="..." id="cover_imgview" class="cover_imgview_'.$locales['locale'].'" style="max-width:250px">
											</div>
											<h4 class="text-danger responseMessage_'.$locales['locale'].'"></h4>
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
									</li>
								</ul>
							</div>
						';
						
			$uploadScript .= '
							CHUpload("'.url().'/titles/metadata/castAndCrew/posterImageUpload", "cover_img_'.$locales['locale'].'", {"filmId":"'.$film->id.'", "locale":"'.$locales['locale'].'", "_token":"'.csrf_token().'" }, function(data){
								var response = JSON.parse(data);
								if(!response.error){
									$(".responseMessage_'.$locales['locale'].'").html("");
									$(".cover_imgview_'.$locales['locale'].'").attr("src", "http://cinecliq.assets.s3.amazonaws.com/files/"+response.message);			
								}			
								else {
									$(".responseMessage_'.$locales['locale'].'").html(response.message);
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
		<ul class="list-group">
			<li class="list-group-item">
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
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{ $tsplash }}" alt="..." id="tsplash_imgview" style="max-width:250px">			
					</div>  
					<div class="media-body">
						<div class="form-group">Trailer Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<h4 class="text-danger" id="responseMessage_tsplash_img"></h4>
						<div class="form-group">
							<div id="uploadifive-tsplash_img" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/tsplashImageUpload" style="height: 29px; line-height: 29px; overflow: hidden; position: relative; text-align: center; width: 129px;">Upload Image
								<input type="file" id="tsplash_img" name="tsplash_img" style="display: none;">
								<input type="file" style="font-size: 29px; opacity: 0; position: absolute; right: -3px; top: -3px; z-index: 999;" multiple="multiple">
							</div>
						</div>
					</div>
				</div>
			</li>
			<li class="list-group-item">
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
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{ $fsplash }}" alt="..." id="fsplash_imgview" style="max-width:250px">
					</div>  
					<div class="media-body">
						<div class="form-group">Film Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<h4 class="text-danger" id="responseMessage_fsplash_img"></h4>
						<div class="form-group">
							<div id="uploadifive-fsplash_img" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/fsplashImageUpload">Upload Image
								<input type="file" name="fsplash_img">
								<input type="file" multiple="multiple">
							</div>
						</div>
					</div>
				</div>
			</li>
		</ul>		
	</div>
</div>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<script>
$(document).ready(function(){
	{!! $uploadScript !!}
	
	var filmId = $('input[name="filmId"]').val();
	
	CHUpload("{{url()}}/titles/metadata/castAndCrew/tsplashImageUpload", "uploadifive-tsplash_img", {"filmId":filmId, "_token":"{{csrf_token()}}" }, function(data){
		var response = JSON.parse(data);
		if(!response.error){
			$("#responseMessage_tsplash_img").html("");
			$("#tsplash_imgview").attr("src", "http://cinecliq.assets.s3.amazonaws.com/splash/"+response.message);			
		}			
		else {
			$("#responseMessage_tsplash_img").html(response.message);
		}				
	});	
	
	CHUpload("{{url()}}/titles/metadata/castAndCrew/fsplashImageUpload", "uploadifive-fsplash_img", {"filmId":filmId, "_token":"{{csrf_token()}}" }, function(data){
		var response = JSON.parse(data);
		if(!response.error){
			$("#responseMessage_fsplash_img").html("");
			$("#fsplash_imgview").attr("src", "http://cinecliq.assets.s3.amazonaws.com/splash/"+response.message);			
		}			
		else {
			$("#responseMessage_fsplash_img").html(response.message);
		}				
	});
	
	$(".removePosterImage").click(function(){		
		var localeId = $(this).data('localeid');
		var locale = $(this).data('locale');
		
		bootbox.confirm('Do you realy want to delete Cover Image', function(result) {
			if(result){
				$.post('{{url()}}/titles/metadata/castAndCrew/posterImageRemove', { localeId:localeId }, function(data){
					if(data) {
						$('.cover_imgview_'+locale).attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/nocover.png');
					}
				});
			}
		});
	});	
	
	$("#removeTSplashImage").click(function(){
		bootbox.confirm('Do you realy want to delete trailer Splash', function(result) {
			if(result){
				$.post('{{url()}}/titles/metadata/castAndCrew/tsplashImageRemove', { filmId:filmId }, function(data){
					if(data) {
						$('#tsplash_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/splash/black.png');
					}
				});
			}
		});
	});

	$('#removeFSplashImage').click(function(){		
		bootbox.confirm('Do you realy want to delete film Splash', function(result) {
			if(result){
				$.post('{{url()}}/titles/metadata/castAndCrew/fsplashImageRemove', { filmId:filmId }, function(data){
					if(data) {
						$('#fsplash_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/splash/black.png');
					}
				});
			}
		});
	});
	
});
</script>