<div class="miniwell countries">
	<div>
		<div class="form-group">
			<label>Title type</label>
			<div class="form-control readOnly">
				@if(isset($film))
					@if($film->series_parent === 0)
						<span>Feature</span>
						<?php $displayNone = 'style=display:none';?>
					@elseif($film->series_parent === -1)
						<span>Series</span>
						<?php $displayNone = 'style=display:none';?>
					@else
						<span>Episode</span>
						<?php $displayNone = '';?>
					@endif
				@endif
			</div>
		</div>	
		<div {{$displayNone}}>
			<div class="form-group">
				<label>Production companies</label>
				<span class="form-control readOnly">
					@if(isset($metadata['series']['parentFilm']))
						@foreach($metadata['series']['parentFilm'] as $key)		
							<li class="token-input-token-facebook" style="list-style:none;">{{isset($key->title) ? $key->title : $key->title}}</button>
						@endforeach
					@endif					
				</span>
			</div>			
			<div class="form-group">
				<label>Episode Number</label>
				<div id="spinner1">
					<div class="input-group input-small col-md-3">
						<span class="spinner-input form-control">{{ isset($film->series_num) ? $film->series_num : 0 }}</span>
					</div>
				</div>
			</div>
		</div>		
	</div>
</div>