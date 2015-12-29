<div class="modal fade" id="editPersonModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Edit Cast/Crew Member</h4>
			</div>
			<div class="modal-body" id="editFormInner">
				<div>
					<div class="media-body">
						@if(isset($thisPerson[0]->img))
							<?php
								$personImg = $thisPerson[0]->img;
							?>
						@else
							<?php
								$personImg = 'nophoto.png';
							?>
						@endif
						<img src="http://cinecliq.assets.s3.amazonaws.com/persons/{{$personImg}}" class="person_image" style="width: 120px;margin:0 20px 14px 0; float:left;" id="person_image" />		
					</div>
					<div class="clearfix" style="clear:both">
						<ul class="nav nav-tabs ">										
									<li class="active"> 
										<a href="#tabPersonLocale_en" class="tab-level2" data-toggle="tab" aria-expanded="true"> 
											<span class="visible-xs"><?php echo ucfirst(array_search($allLocales['en'], $allLocales));?></span> 
											<span class="hidden-xs">
												@if(array_key_exists('en', $allLocales))
													{{$allLocales['en']}}
												@endif											
											</span> 
										</a> 
									</li>
							@if(isset($LocalePersons))
								@foreach($LocalePersons as $locale)
									<li class=""> 
										<a href="#tabPersonLocale_{{$locale->locale}}" class="tab-level2" data-toggle="tab" aria-expanded="true"> 
											<span class="visible-xs"><?php echo ucfirst(array_search($allLocales[$locale->locale], $allLocales));?></span> 
											<span class="hidden-xs">
												@if(array_key_exists($locale->locale, $allLocales))
													{{$allLocales[$locale->locale]}}
												@endif											
											</span> 
										</a> 
									</li>
								@endforeach
							@endif					
						</ul>
						<div class="tab-content">
							@if(isset($thisPerson))
								@foreach($thisPerson as $person)
									<div class="tab-pane active" id="tabPersonLocale_en">
										<div class="form-group">
											<label>Name</label>
											<span type="text" class="form-control">{{isset($person->title) ? $person->title : ''}}</span>
										</div>												
										<div class="form-group">
											<label>Bio</label>
											<span class="form-control readOnly" style="height:100px;overflow-y:auto;">{{$person->brief}}</span>
										</div>																					
									</div>
								@endforeach
							@endif								
							@if(isset($LocalePersons))
								@foreach($LocalePersons as $locale)
									<div class="tab-pane" id="tabPersonLocale_{{$locale->locale}}">
										<div class="form-group">							
											<label>Name</label>
											<span type="text" class="form-control readOnly">{{isset($locale->title) ? $locale->title : ''}}</span>
										</div>												
										<div class="form-group">
											<label>Bio</label>
											<span class="form-control readOnly" style="height:100px;overflow-y:auto;">{{isset($locale->brief) ? $locale->brief : ''}}</span>
										</div>																					
									</div>
								@endforeach
							@endif			
						</div>
					</div>
				</div>
			</div> 
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
  </div>
</div>