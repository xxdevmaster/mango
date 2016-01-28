<?php
	$tabClassActive = '';
	$basicLocaleNavTabs = '';
	$basicLocaleTabContents = '';
	
?>
@if(isset($filmLocales) && count($filmLocales) != 0)
	@foreach($filmLocales as $val)
			@if(!empty($val->locale) && array_key_exists($val->locale, $allLocales))
				@if($val->def === 1)
					<?php
						$tabClassActive = 'active';
						$fade = 'fade in';
						$defaultLocale = '
							<span class="text-success pull-right">
								<span class="glyphicon glyphicon-flag "></span> Default 
							</span>
						';					
					?>
				@else
					<?php
						$tabClassActive = '';
						$fade = 'fade';
						$defaultLocale = '';
					?>
				@endif
				<?php
				$basicLocaleNavTabs .= '
						<li class="'.$tabClassActive.'">
							<a href="#tabBasicLocale_'.$val->locale.'" class="tab-level1" data-toggle="tab" aria-expanded="false">
								<span class="visible-xs">'.ucfirst(array_search($allLocales[$val->locale], $allLocales)).'</span> 
								<span class="hidden-xs">'.$allLocales[$val->locale].'</span> 									
							</a>
						</li>
					';
					$basicLocaleTabContents .= '
						<div class="tab-pane '.$fade.' '.$tabClassActive.'" id="tabBasicLocale_'.$val->locale.'">
							<div class="form-group">
								'.$defaultLocale.'
								<label>Title</label>
								<span type="text" class="form-control readOnly">'.$val->title.'</span>
							</div>												
							<div class="form-group">
								<label>Synopsis</label>
								<span class="form-control readOnly" style="height:174px;">'.$val->synopsis.'</span>
							</div>																					
						</div>								
					';
				?>
			@endif
	@endforeach		
@endif

<div>
	<ul class="nav nav-tabs">
		{!! $basicLocaleNavTabs !!}
	</ul>
	<div class="tab-content">
		{!! $basicLocaleTabContents !!}	
	</div>
</div>