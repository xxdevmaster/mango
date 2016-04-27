<?php
	$tabClassActive = '';
	$basicLocaleNavTabs = '';
	$basicLocaleTabContents = '';
?>
@if(isset($metadata['basic']['filmLocales']) && count($metadata['basic']['filmLocales']) != 0)
	@foreach($metadata['basic']['filmLocales'] as $val)
			@if(!empty($val->locale) && array_key_exists($val->locale, $allLocales))
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
					<?php
						$tabClassActive = '';
						$fade = 'fade';
						$filmLocaleRemove = '
							<span class="pull-right removeBasicLocale" style="cursor:pointer" data-filmid="'.$film->id.'" data-localeid="'.$val->id.'" data-title="'.$allLocales[$val->locale].'">
								<i class="glyphicon glyphicon-remove-circle fa-lg"></i>  
							</span>				
						';	
						$defaultLocale = '
							<span class="text-primary  pull-right makeDefaultLocale" data-locale="'.$val->locale.'" data-localeid="'.$val->id.'" data-title="'.$allLocales[$val->locale].'" style="margin-right:15px;cursor:pointer">
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
			@endif
	@endforeach
@endif

<form id="basicForm" name="basicForm" action="" method="post" role="form">
	<ul class="nav nav-tabs">
		{!! $basicLocaleNavTabs !!}
	</ul>
	<div class="tab-content">
		{!! $basicLocaleTabContents !!}	
		<div class="form-group">
			<select class="selectBoxWithSearch" id="filmsNewLanguage" name="filmsNewLanguage" data-placeholder="+ Add New Metadata Language">
				@if(isset($metadata['basic']['allUniqueLocales']) && is_array($metadata['basic']['allUniqueLocales']))
						<option selected="selected" value="0">+ Add New Metadata Language</option>
					@foreach($metadata['basic']['allUniqueLocales'] as $key => $value)	
						<option value="{{ $key }}">{{ $value }}</option>
					@endforeach
				@endif
			</select>
		</div>
	</div>
	<input type="hidden" name="filmId" value="{{ $film->id }}">
</form>

<script>
jQuery(document).ready(function() {
	jQuery(".selectBoxWithSearch").select2({
		width: '100%',
	});	
});
$(document).ready(function(){
	//Tab Basic create new language
	$('#filmsNewLanguage').change(function() {
		autoCloseMsgHide();
		var title = $('#filmsNewLanguage option:selected').html();
		var selectedValue = $('#filmsNewLanguage option:selected').val();

		if(selectedValue != 0){	
			var locale = $('#filmsNewLanguage option:selected').val();
		
			bootbox.confirm('Please Confirm adding '+title+' translation', function(result) {
				if(result) {
					$('.loading').show();				
					$.post('/titles/metadata/basic/newLocale', {locale:locale},function(response){
						if(!response.error) {
							$('#basic').html(response.basic);
							$('a[href="#tabBasicLocale_'+locale+'"]').tab('show');
							$("#images").html(response.images);
							$('.loading').hide();
						}else {
							$('.loading').hide();
							autoCloseMsg(1, response.message, 5000);
						}					
					});
				}
			});	
		}
	});	
	
	//Tab Basic remove language
	$('.removeBasicLocale').click(function() {
		autoCloseMsgHide();
		
		var title = $(this).data('title');
		var localeID = $(this).data('localeid');
		
		bootbox.confirm('Do you really want to delete '+title+' language ?', function(result) {
			if(result) {
				$('.loading').show();
				$.post('/titles/metadata/basic/localeRemove', {localeID:localeID},function(response){
					if(!response.error) {
						$('#basic').html(response.basic);
						$("#images").html(response.images);
						$('.loading').hide();
					}else {
						$('.loading').hide();
						autoCloseMsg(1, response.message, 5000);
					}
				});
			}
		});			
	});	
	
	//Tab Basic make default language
	$('.makeDefaultLocale').click(function(){
		autoCloseMsgHide();
		
		var localeID = $(this).data('localeid');
		var locale = $(this).data('locale');
		var title = $(this).data('title');
		
		bootbox.confirm('Do you really want to make '+title+' language default?', function(result) {
			if(result) {
				$('.loading').show();
				$.post('/titles/metadata/basic/makeDefaultLocale', {locale:locale, localeID:localeID},function(response){
					if(!response.error) {
						$('#basic').html(response.basic);
						$("#images").html(response.images);
						$('.loading').hide();
					}else {
						$('.loading').hide();
						autoCloseMsg(1, response.message, 5000);
					}
				});
			}
		});	
	});	
	
});
</script>