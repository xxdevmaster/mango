<ul class="nav nav-tabs">
	<li class="active">
		<a href="#tab_filmPosterImages"  data-toggle="tab" aria-expanded="true">Film Poster Images</a>
	</li>	
	<li class="">
		<a href="#tab_filmSplashImages"  data-toggle="tab" aria-expanded="true">Film Splash Image</a>
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
						<li class="'.$active.'">
							<a href="#tab_covers_locale_'.$locales['locale'].'"  data-toggle="tab" aria-expanded="true">'.$allLocales[$locales['locale']].'</a>
						</li>
					';
		
		$tabPane .= '
						<div class="tab-pane '.$active.'" id="tab_covers_locale_'.$locales['locale'].'">
							<ul class="list-group " id="slider-list-holder">
								<li class="list-group-item">
									<div class="media">
										<span rel="179" class="delete-collection cp  pull-right" aria-hidden="true"><span class="glyphicon glyphicon-remove "></span>  </span>
										<div class="col-sm-6 col-md-4">
											<output id="list">
												<img src="http://cinecliq.assets.s3.amazonaws.com/files/'.$filmPosterImage.'" alt="..." id="cover_imgview" class="cover_imgview" style="max-width:250px">
											</output>
										</div>
										<div class="media-body">
											<div class="form-group">Poster image must be uploaded in the 2:3 aspect ratio. We strongly recommend the following format: 400x600px, JPG or PNG, 500KB maximum size.</div>
											<div class="form-group" id="cover_text"></div>
											<div class="form-group">
												<form action="" method="post" enctype="multipart/form-data" role="form"">
												<div id="cover_img_'.$locales['locale'].'" class="uploadifive-button" style="height: 29px; line-height: 29px; overflow: hidden; position: relative; text-align: center; width: 129px;">Upload Image
													<input type="file" name="cover_img" style="display: none;">
													<input type="file" name="cover_img" style="font-size: 29px; opacity: 0; position: absolute; right: -3px; top: -3px; z-index: 999;" multiple="multiple">														
												</div>	
												</form>
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
					<button type="button" rel="179" class="delete-collection close" aria-hidden="true" onclick="if (confirm('Do you really want to delete trailer splash?')) { FilmImagesDelete('tsplash');}"> X </button>
					<div class="col-sm-6 col-md-4">
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{$film->fsplash}}" alt="..." id="tsplash_imgview" style="max-width:250px">			
					</div>  
					<div class="media-body">
						<div class="form-group">Trailer Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<div class="form-group" id="tsplash_text"></div>
						<div class="form-group"><div id="uploadifive-tsplash_img" class="uploadifive-button" style="height: 29px; line-height: 29px; overflow: hidden; position: relative; text-align: center; width: 129px;">Upload Image<input type="file" id="tsplash_img" name="tsplash_img" style="display: none;"><input type="file" style="font-size: 29px; opacity: 0; position: absolute; right: -3px; top: -3px; z-index: 999;" multiple="multiple"></div><div id="uploadifive-tsplash_img-queue" class="uploadifive-queue"></div></div>
						<!--div class="form-group"><button type="button" class="btn btn-large btn-danger" onclick="if (confirm('Do you really want to delete trailer splash?')) { FilmImagesDelete('tsplash');} ">Delete</button></div-->
						<div class="row">
							<form action="#" class="dropzone" id="dropzone">
							  <div class="fallback">
								<input name="file" type="file" multiple />
							  </div>
							</form>
						</div>					
					</div>
				</div>
			</li>
			<li class="list-group-item">
				<div class="media">
					<button type="button" rel="179" class="delete-collection close" aria-hidden="true" onclick="if (confirm('Do you really want to delete film splash?')) { FilmImagesDelete('fsplash');}"> X </button>
					<div class="col-sm-6 col-md-4">
							<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{$film->tsplash}}" alt="..." id="fsplash_imgview" style="max-width:250px">
					</div>  
					<div class="media-body">
						<div class="form-group">Film Splash Image [1920x1080px, JPG or PNG, 500KB max size]</div>
						<div class="form-group" id="fsplash_text"></div>
						<div class="form-group"><div id="uploadifive-fsplash_img" class="uploadifive-button" style="height: 29px; line-height: 29px; overflow: hidden; position: relative; text-align: center; width: 129px;">Upload Image<input type="file" id="fsplash_img" name="fsplash_img" style="display: none;"><input type="file" style="font-size: 29px; opacity: 0; position: absolute; right: -3px; top: -3px; z-index: 999;" multiple="multiple"></div><div id="uploadifive-fsplash_img-queue" class="uploadifive-queue"></div></div>
						<!--div class="form-group"><button type="button" class="btn btn-large btn-danger" onclick="if (confirm('Do you really want to delete film splash?')) { FilmImagesDelete('fsplash');} ">Delete</button></div-->
						<div class="row">
							<form action="#" class="dropzone" id="dropzone">
							  <div class="fallback">
								<input name="file" type="file" multiple />
							  </div>
							</form>
						</div>					
					</div>
				</div>
			</li>
		</ul>		
	</div>
</div>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<script>
$(function() {
    var locale = 'ru';
    var filmId = 1671;
	var _token = $('input[name="_token"]').val();
	$('#cover_img_'+locale).uploadifive({
		'buttonText' 	  : 'Upload Image',
		'auto'            : true,
		'queueID' 		  : false,
		'removeCompleted' : true,
		'removeTimeout' :0,
		'itemTemplate': '',
		'width' : '129',
		'height' : '29',
	    'scriptData': {},
	    'formData'         : {'folder'     : '/files/','imageField':'cover', 'film_id':filmId, 'locale':locale, _token:_token },
	    'uploadScript'     : '{{url()}}/titles/metadata/castAndCrew/posterImageUpload',
		'onUploadComplete' : function(file, data) { 
				response = JSON.parse(data);
				//console.log(response);
				if(response.file!=false)
					$("#cover_imgview_"+locale).attr('src','http://cinecliq.assets.s3.amazonaws.com/files/'+response.file);
				$("#cover_text_"+locale).html(response.text);
		}
	});
});
</script>