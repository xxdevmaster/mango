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
											<div class="col-sm-6 col-md-8">
												<img src="http://cinecliq.assets.s3.amazonaws.com/files/'.$filmPosterImage.'" alt="..." style="max-width:250px">
											</div>
										</div>
									</li>
								</ul>
							</div>
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
					<div class="col-sm-8 col-md-8">
						<?
							if(!empty($film->tsplash))
								$tsplash = $film->tsplash;
							else
								$tsplash = 'black.png';
						?>
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{ $tsplash }}" alt="..." style="max-width:250px">			
					</div>
				</div>
			</li>
			<li class="list-group-item">
				<div class="media">
					<div class="col-sm-8 col-md-8">
						<?
							if(!empty($film->fsplash))
								$fsplash = $film->fsplash;
							else
								$fsplash = 'black.png';
						?>					
						<img src="http://cinecliq.assets.s3.amazonaws.com/splash/{{ $fsplash }}" alt="..." style="max-width:250px">
					</div>
				</div>
			</li>
		</ul>		
	</div>
</div>