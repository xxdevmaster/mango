<ul class="nav nav-tabs">
	<li class="active">
		<a href="#tab_filmPosterImages" class="tab-level2" data-toggle="tab" aria-expanded="true">Film Poster Images</a>
	</li>	
	<li class="">
		<a href="#tab_filmSplashImages" class="tab-level2" data-toggle="tab" aria-expanded="true">Film Splash Image</a>
	</li>
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="tab_filmPosterImages">
		<!--form name="movie-form-translations" id="movie-form-translations" role="form"-->
<?
$i = 0;
$tabsLi = '';
$tabPane = '';
$active = 'active';
$filmPosterImage = 'nocover.png';

if(isset($images['localeFilms']) && is_array($images['localeFilms'])) {
	foreach ($images['localeFilms'] as $locales) {
		
		if ($i != 0)
			$active = '';
		
		if ($locales['cover'])
			$filmPosterImage = $locales['cover'];
		else
			$filmPosterImage = 'nocover.png';
		
		$tabsLi .= '
						<li class="'.$active.'" data-locale="'.$locales['locale'].'">
							<a href="#tab_covers_locale_'.$locales['locale'].'" class="tab-level1" data-toggle="tab" aria-expanded="true">
								<span class="visible-xs">'.ucfirst(array_search($allLocales[$locales['locale']], $allLocales)).'</span> 
								<span class="hidden-xs">'.$allLocales[$locales['locale']].'</span>
							</a>
						</li>
					';
		
		$tabPane .= '
						<div class="tab-pane '.$active.'" id="tab_covers_locale_'.$locales['locale'].'">
							<ul class="list-group " id="slider-list-holder">
								<li class="list-group-item">
									<div class="media">
										<span class="pull-right" id="removePosterImage" data-localeid="'.$locales['id'].'" aria-hidden="true" style="cursor:pointer">
											<span class="glyphicon glyphicon-remove "></span>  
										</span>
										<div class="col-sm-6 col-md-4">
											<output id="list">
												<img src="http://cinecliq.assets.s3.amazonaws.com/files/'.$filmPosterImage.'" alt="..." id="cover_imgview" class="cover_imgview" style="max-width:250px">
											</output>
										</div>
										<div class="media-body">
											<div class="form-group">Poster image must be uploaded in the 2:3 aspect ratio. We strongly recommend the following format: 400x600px, JPG or PNG, 500KB maximum size.</div>
											<div class="form-group" id="cover_text"></div>
											<div class="form-group">												
												<div id="cover_img_'.$locales['locale'].'" class="uploadifive-button" data-url="'.url().'/titles/metadata/castAndCrew/posterImageUpload" data-locale="'.$locales['locale'].'" style="height: 29px; line-height: 29px; overflow: hidden; position: relative; text-align: center; width: 129px;">Upload Image
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
		++$i;
	}
}
?>
		<ul class="nav nav-tabs">
			{!! $tabsLi !!}
		</ul>
		<div class="tab-content">
			{!! $tabPane !!}
		</div>
		<!--/form-->			
	</div>	
	<div class="tab-pane" id="tab_filmSplashImages">
		<ul class="list-group " id="slider-list-holder">
			<li class="list-group-item">
				<div class="media">
					<span class="pull-right" id="removeTSplashImage" aria-hidden="true" style="cursor:pointer">
						<span class="glyphicon glyphicon-remove "></span>  
					</span>
					<div class="col-sm-6 col-md-4">
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{$film->tsplash}}" alt="..." id="tsplash_imgview" style="max-width:250px">			
					</div>  
					<div class="media-body">
						<div class="form-group">Trailer Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<div class="form-group" id="tsplash_text"></div>
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
					<span class="pull-right" id="removeFSplashImage" aria-hidden="true" style="cursor:pointer">
						<span class="glyphicon glyphicon-remove "></span>  
					</span>
					<div class="col-sm-6 col-md-4">
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{$film->fsplash}}" alt="..." id="fsplash_imgview" style="max-width:250px">
					</div>  
					<div class="media-body">
						<div class="form-group">Film Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<div class="form-group" id="fsplash_text"></div>
						<div class="form-group">
							<div id="uploadifive-fsplash_img" class="uploadifive-button" data-url="{{url()}}/titles/metadata/castAndCrew/fsplashImageUpload" style="height: 29px; line-height: 29px; overflow: hidden; position: relative; text-align: center; width: 129px;">Upload Image
								<input type="file" id="fsplash_img" name="fsplash_img" style="display: none;">
								<input type="file" style="font-size: 29px; opacity: 0; position: absolute; right: -3px; top: -3px; z-index: 999;" multiple="multiple">
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

	$('.uploadifive-button').click(function(){
		var this_ = $(this);
		var locale = this_.data('locale');
		var filmId = $('input[name="filmId"]').val();
		var _token = $('input[name="_token"]').val();
		var url = this_.data('url');
		CHUpload(url, 'uploadifive-button', {'filmId':filmId, 'locale':locale, '_token':_token }, function(data){
			var response = JSON.parse(data);
			if(!response.error)
				$('#cover_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/'+response.message);
			else {
				$(this_).parent().find('.media-body').find('.responseMessage').remove();
				$(this_).parent().find('.media-body').prepend('<h3 class="text-danger responseMessage">'+response.message+'</h3>')
			}				
		});	
	});
	
	$(document).on('click', '#removePosterImage', function(){
		
		var localeId = $(this).data('localeid');
		var confirmText = 'Do you realy want to delete Cover Image';
		
		bootbox.confirm(confirmText, function(result) {
			
			$.post('{{url()}}/titles/metadata/castAndCrew/posterImageRemove', { localeId:localeId }, function(data){
				if(data) {
					$('#cover_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/files/nocover.png');
				}
			});
			
		});
	});	
	
	$(document).on('click', '#removeTSplashImage', function(){
		
		var filmId = $('input[name="filmId"]').val();
		var confirmText = 'Do you realy want to delete trailer Splash';
		
		bootbox.confirm(confirmText, function(result) {
			
			$.post('{{url()}}/titles/metadata/castAndCrew/tsplashImageRemove', { filmId:filmId }, function(data){
				if(data) {
					$('#tsplash_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/splash/black.png');
				}
			});
			
		});
	});

	$(document).on('click', '#removeFSplashImage', function(){
		
		var filmId = $('input[name="filmId"]').val();
		var confirmText = 'Do you realy want to delete film Splash';
		
		bootbox.confirm(confirmText, function(result) {
			
			$.post('{{url()}}/titles/metadata/castAndCrew/fsplashImageRemove', { filmId:filmId }, function(data){
				if(data) {
					$('#fsplash_imgview').attr('src', 'http://cinecliq.assets.s3.amazonaws.com/splash/black.png');
				}
			});
			
		});
	});
</script>