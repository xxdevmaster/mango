<?
	$tabClassActive = '';
	$basicLocaleNavTabs = '';
	$basicLocaleTabContents = '';
?>
@if(isset($filmLocales) && count($filmLocales) != 0)
	@foreach($filmLocales as $val)
			@if($val->def === 1)
				<?php
					$tabClassActive = 'active';
					$fade = 'fade in';
					$filmLocaleRemove = '';
					$defaultLocale = '
						<span class="text-success pull-right"><span class="glyphicon glyphicon-flag "></span> Default </span>
					';
				?>
			@else
				<?
					$tabClassActive = '';
					$fade = 'fade';
					$filmLocaleRemove = '
						<span class="pull-right" id="removeBasicLocale" style="cursor:pointer" data-filmid="'.$film->id.'" data-localeid="'.$val->id.'">
							<i class="glyphicon glyphicon-remove-circle fa-lg"></i>  
						</span>				
					';	
					$defaultLocale = '
						<span class="text-primary  pull-right" id="makeDefaultLocale" data-locale="'.$val->locale.'" data-localeid="'.$val->id.'" style="margin-right:15px;cursor:pointer">
							<span class="glyphicon glyphicon-ok "></span> Make Default
						</span>					
					';
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
						'.$filmLocaleRemove.'
						<input type="hidden" name="filmsLocales['.$val->locale.'][localeId]" value="'.$val->id.'">
						<input type="hidden" name="filmsLocales['.$val->locale.'][def]" value="'.$val->def.'">
						<div class="form-group">
							'.$defaultLocale.'
							<label for="title">Title</label>
							<input type="text" class="form-control" id="title" name="filmsLocales['.$val->locale.'][title]" value="'.$val->title.'">
						</div>												
						<div class="form-group">
							<label class="col-md-2 control-label">Synopsis</label>
							<textarea class="form-control" rows="8" name="filmsLocales['.$val->locale.'][synopsis]" style="resize:none;">'.$val->synopsis.'</textarea>
						</div>																					
					</div>								
				';
			?>
	@endforeach
@else	
	<?php
		$basicLocaleNavTabs = '
			<li class="active"> 
				<a href="#tabBasicLocale_en" class="tab-level1" data-toggle="tab" aria-expanded="false"> 
					<span class="visible-xs"><i class="fa fa-cog"></i></span> 
					<span class="hidden-xs">'.$allLocales["en"].'<span> 
				</a> 
			</li>				
		';
		$basicLocaleTabContents = '
			<div class="tab-pane active" id="tabBasicLocale_en">
				<div class="form-group">
					<input type="hidden" name="filmsLocales["en"][localeId]" value="'.$val->id.'">
					<span class="text-success pull-right"><span class="glyphicon glyphicon-flag "></span> Default </span>
					<label for="title">Title</label>
					<input type="text" class="form-control" id="title" name="title" value="">
				</div>												
				<div class="form-group">
					<label class="col-md-2 control-label">Synopsis</label>
					<textarea class="form-control" rows="8" name="synopsis" style="resize:none;"></textarea>
				</div>																					
			</div>					
		';
	?>			
@endif

<form id="basicForm" name="basicForm" action="" method="post" role="form">
	<ul class="nav nav-tabs">
		{!! $basicLocaleNavTabs !!}
	</ul>
	<div class="tab-content">
		{!! $basicLocaleTabContents !!}	
		<div class="form-group">
			<select class="form-control" id="filmsNewLanguage" name="filmsNewLanguage">
				<option selected="selected" value="">+ Add New Metadata Language</option>
				@if(isset($allLocales) && is_array($allLocales))
					@foreach($allLocales as $key => $value)														
						<option value="{{ $key }}">{{ $value }}</option>													
					@endforeach
				@endif
			</select>
		</div>
	</div>
	<input type="hidden" name="template" value="basic">
	<input type="hidden" name="filmId" value="{{ $film->id }}">
</form>