<form id="basicForm" name="basicForm" action="" method="post" role="form">
	<ul class="nav nav-tabs">
		@if(isset($currenDefaultLanguages) && is_array($currenDefaultLanguages) && count($currenDefaultLanguages) != 0)
			@foreach($currenDefaultLanguages as $val)
					<li class="active"> 
						<a href="#tabBasicLocale_{{ $val['locale']}}" data-toggle="tab" aria-expanded="false"> 
							<span class="visible-xs"><i class="fa fa-cog"></i></span> 
							<span class="hidden-xs">{{ $allLocales[$val['locale']] }}</span> 
						</a> 
					</li>
			@endforeach
		@else	
					<li class="active"> 
						<a href="#tabBasicLocale_en" data-toggle="tab" aria-expanded="false"> 
							<span class="visible-xs"><i class="fa fa-cog"></i></span> 
							<span class="hidden-xs">{{ $allLocales['en'] }}</span> 
						</a> 
					</li>			
		@endif				
		@if(isset($currenLanguages) && is_array($currenLanguages))
			@foreach($currenLanguages as $val)	
<?

?>		
					<li class=""> 
						<a href="#tabBasicLocale_{{ $val['locale']}}" data-toggle="tab" aria-expanded="false"> 
							<span class="visible-xs"><i class="fa fa-cog"></i></span> 
							<span class="hidden-xs">{{ $allLocales[$val['locale']] }}</span> 
						</a> 
					</li>
			@endforeach
		@endif
	</ul>
	<div class="tab-content">
		@if(isset($currenDefaultLanguages) && is_array($currenDefaultLanguages) && count($currenDefaultLanguages) != 0)
			@foreach($currenDefaultLanguages as $val)
				<div class="tab-pane active" id="tabBasicLocale_{{ $val['locale']}}">
					<div class="form-group">
						<span class="text-success pull-right"><span class="glyphicon glyphicon-flag "></span> Default </span>
						<label for="title">Title</label>
						<input type="text" class="form-control" id="title" name="title" value="{{ $val['title'] }}">
					</div>												
					<div class="form-group">
						<label class="col-md-2 control-label">Synopsis</label>
						<textarea class="form-control" rows="8" name="synopsis" style="resize:none;">{{ $val['synopsis'] }}</textarea>
					</div>																					
				</div>		
			@endforeach
		@else
				<div class="tab-pane active" id="tabBasicLocale_en">
					<div class="form-group">
						<span class="text-success pull-right"><span class="glyphicon glyphicon-flag "></span> Default </span>
						<label for="title">Title</label>
						<input type="text" class="form-control" id="title" name="title" value="">
					</div>												
					<div class="form-group">
						<label class="col-md-2 control-label">Synopsis</label>
						<textarea class="form-control" rows="8" name="synopsis" style="resize:none;"></textarea>
					</div>																					
				</div>				
		@endif														
		@if(isset($currenLanguages) && is_array($currenLanguages))
			@foreach($currenLanguages as $val)												
				<div class="tab-pane" id="tabBasicLocale_{{ $val['locale']}}">
					<input type="hidden" name="filmsLocales[{{ $val['locale'] }}][localeId]" value="{{ $val['id'] }}">
					<span class="pull-right" id="removeBasicLocale" style="cursor:pointer" data-filmid="{{ $currentFilm[0]['id'] }}" data-localeid="{{ $val['id'] }}">
						<i class="glyphicon glyphicon-remove-circle text-danger fa-lg"></i>  
					</span>
					<div class="form-group">
						@if($val['def'] == 1)
							<span class="text-success pull-right"><span class="glyphicon glyphicon-flag "></span> Default </span>
						@endif
						<span class="text-primary  pull-right" id="makeDefaultLocale" data-locale="{{ $val['locale'] }}" data-localeid="{{ $val['id'] }}" style="margin-right:15px;cursor:pointer">
							<span class="glyphicon glyphicon-ok "></span> Make Default
						</span>													
						<label for="title">Title</label>
						<input type="text" class="form-control" id="title" name="filmsLocales[{{ $val['locale']}}][title]" value="{{ $val['title'] }}">
					</div>												
					<div class="form-group">
						<label class="col-md-2 control-label">Synopsis</label>
						<textarea class="form-control" rows="8" name="filmsLocales[{{ $val['locale']}}][synopsis]" style="resize:none;">{{ $val['synopsis'] }}</textarea>
					</div>																					
				</div>
			@endforeach
		@endif										
		<div class="form-group">
			<select class="form-control" id="filmsNewLanguage" name="filmsNewLanguage">
				<option selected="selected" value="">+ Add New Metadata Language</option>
				@if(isset($allLocales) && is_array($allLocales))
					@foreach($allLocales as $val => $key)														
						<option value="{{ $val }}">{{ $key }}</option>													
					@endforeach
				@endif
			</select>
		</div>
	</div>
	<input type="hidden" name="template" value="basic">
	<input type="hidden" name="filmId" value="{{ $currentFilm[0]['id'] }}">
</form>