<div id="topPager" class="text-right">
	{!! $films->render() !!}
</div>
<form id="vaultBulkForm" onsubmit="return false" role="form">
	<div class="table-responsive">
		<table class="table table-striped table-bordered" id="datatable">
			<tr>
				<td class="bulkAct">
					<div class="dropdown pull-left">
						<input type="checkbox" id="bulkActCheckbox">
						<a id="bulkActPopup" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
							<span class="caret"></span>
						</a>
						<ul aria-labelledby="bulkActPopup" role="menu" class="dropdown-menu">
							<li role="presentation"><a class=" cp" tabindex="-1" role="menuitem" rollapp-href="" id="bulkActAddToStore">Add to My Store</a></li>
							<li role="presentation"><a class=" cp" tabindex="-1" role="menuitem" rollapp-href="" id="bulkActDeleteFromStore">Remove from My Store</a></li>
						</ul>
					</div>
				</td>
				<td>
					Poster
				</td>
				<td>
					<a class="filter text-primary" data-order="id">ID
						@if(!empty($orderBy) && $orderBy == 'id')
							@if(!empty($orderType) && $orderType == 'desc')
								<i class="ion-arrow-down-b"></i>
							@else
								<i class="ion-arrow-up-b"></i>
							@endif
						@endif
					</a>
				</td>
				<td>
					<a class="filter text-primary" data-order="title">Title
						@if(!empty($orderBy) && $orderBy == 'title')
							@if(!empty($orderType) && $orderType == 'desc')
								<i class="ion-arrow-down-b"></i>
							@else
								<i class="ion-arrow-up-b"></i>
							@endif
						@endif
					</a>
				</td>
				<td>
					Stores
				</td>
				<td class="text-right">
					Add / Remove
				</td>
			</tr>
			<tbody>
			@if(!empty($films->items()))
				@foreach($films->items() as $filmID => $film)
					@if($ownerFilmIDS->search($filmID))
						<tr>
							<td>

							</td>
							<td>
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($film->cover) ? $film->cover : '' }}" width="50" alt="">
							</td>
							<td>
								{{ $filmID }}
							</td>
							<td>
								<a href="/titles/metadata/{{ $filmID }}" class="view-link">{{ isset($film->title) ? $film->title : '' }}</a>
							</td>
							<td>
								@if(array_key_exists($filmID, $filmStores))
									<span>{{ implode(' , ', $filmStores[$filmID]) }}</span>
								@endif
							</td>
							<td class="text-right">
								You're the owner of this title.
							</td>
						</tr>
					@else
						@if(array_key_exists($filmID, $deletedFilmsForXchange['InSomeTeritories'])))
							@if((!empty($deletedFilmsForXchange['InSomeTeritories'][$filmID])) > 0)
								<?php $isDelete = true ;?>
							@elseif((!empty($deletedFilmsForXchange['AllTeritories'][$filmID])) > 0)
								<?php $isDelete = true ;?>
							@else
								<?php $isDelete = false ;?>
							@endif
						@endif

						@if(!empty($isDelete))
							<tr>
								<td>
									@if((!empty($deletedFilmsForXchange['InSomeTeritories'][$filmID])) > 0)
										<input type="checkbox" name="{{ ($film->channelContractID > 0) ? "filmsInMyStore[$filmID]" : "filmsNotInMyStore[$filmID]"  }}" class="itemCheckbox">
									@else
										<input type="checkbox"  disabled="disabled">
									@endif
								</td>
								<td>
									<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($film->cover) ? $film->cover : '' }}" width="50" alt="">
								</td>
								<td>
									{{ $filmID }}
								</td>
								<td>
									<a href="/review/{{ $filmID }}" class="view-link">
										{{ isset($film->title) ? $film->title : '' }}
									</a>
									@if($deletedFilmsForXchange['InSomeTeritories'][$filmID] > 0)
										<span class="dengerTxt">
											This titles will soon be removed from Xchange in some territories.
										</span><br>
										<a class="showCountries cp" data-filmid="{{ $filmID }}">Available Countries</a>
									@else
										<span class="dengerTxt">This title will be removed from Xchange on  {{ $deletedFilmsForXchange['InSomeTeritories'][$filmID] }}.</span>
									@endif
								</td>
								<td>
									@if(array_key_exists($filmID, $filmStores))
										<span>{{ implode(' , ', $filmStores[$filmID]) }}</span>
									@endif
								</td>
								<td class="text-center">
									@if($film->channelContractID > 0)
										<span data-filmid="{{ $filmID }}" class="btn btn-primary btn-xs soloActDeleteFromStore cp">Remove</span>
									@else
										<span data-filmid="{{ $filmID }}" class="btn btn-primary btn-xs soloActAddToStore cp">Add</span>
									@endif
								</td>
							</tr>
						@else
							<tr>
								<td>
									<input type="checkbox"  name="{{ ($film->channelContractID > 0) ? "filmsInMyStore[$filmID]" : "filmsNotInMyStore[$filmID]"  }}" class="itemCheckbox">
								</td>
								<td>
									<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($film->cover) ? $film->cover : '' }}" width="50" alt="">
								</td>
								<td>{{ $filmID }}</td>
								<td>
									<a href="/review/{{ $filmID }}" class="view-link">
										{{ isset($film->title) ? $film->title : '' }}
									</a><br>
									<a class="showCountries cp" data-filmid="{{ $filmID }}">Available Countries</a>
								</td>
								<td>
									@if(array_key_exists($filmID, $filmStores))
										<span>{{ implode(' , ', $filmStores[$filmID]) }}</span>
									@endif
								</td>
								<td class="text-right">
									@if($film->channelContractID > 0)
										<span data-filmid="{{ $filmID }}" class="btn btn-primary btn-md soloActDeleteFromStore cp">Remove from My Store</span>
									@else
										<span data-filmid="{{ $filmID }}" class="btn btn-primary btn-md soloActAddToStore cp">Add to My Store</span>
									@endif
								</td>
								<tr id="availableCountries_{{ $filmID }}" class="display-none"></tr>
							</tr>
						@endif
					@endif
				@endforeach
			@endif
			</tbody>
		</table>
	</div>
</form>
<div id="bottomPager" class="text-right">
	{!! $films->render() !!}
</div>
<script>
	$(document).ready(function(){

		/* Add film from xchange to store */
		$( ".soloActAddToStore" ).click(function(){
			var filmID = $(this).data("filmid");
			autoCloseMsgHide();
			$(".loading").show();
			$.post('/xchange/soloActAddToStore', {filmID:filmID}, function(data){
				$('#xchangeTitles').html(data);
				$("#bulkActCheckbox").prop('checked', false);
				$(".loading").hide();
			});
		});

		/* Delete the film from store */
		$( ".soloActDeleteFromStore" ).click(function(){
			var filmID = $(this).data("filmid");
			autoCloseMsgHide();
			$(".loading").show();
			$.post('/xchange/soloActDeleteFromStore', {filmID:filmID}, function(data){
				$('#xchangeTitles').html(data);
				$("#bulkActCheckbox").prop('checked', false);
				$(".loading").hide();
			});
		});


		/* Add the films from xchange to store. */
		$( "#bulkActAddToStore" ).click(function(){
			autoCloseMsgHide();
			$(".loading").show();
			$.post('/xchange/bulkActAddToStore', $("#vaultBulkForm").serialize(), function(data){
				$('#xchangeTitles').html(data);
				$("#bulkActCheckbox").prop('checked', false);
				$(".loading").hide();
			});
		});

		/* Delete the films from store. */
		$( "#bulkActDeleteFromStore" ).click(function(){
			bootbox.confirm('You are about to remove the selected titles from your store. Are you sure?', function(result) {
				if(result) {
					autoCloseMsgHide();
					$(".loading").show();
					$.post('/xchange/bulkActDeleteFromStore', $("#vaultBulkForm").serialize(), function(data){
						$('#xchangeTitles').html(data);
						$("#bulkActCheckbox").prop('checked', false);
						$(".loading").hide();
					});
				}
			});
		});

		/* Show Available Countries*/
		$('.showCountries').click(function () {
			autoCloseMsgHide();
			var filmID = $(this).data("filmid");

			$.post('/xchange/drawAvailableCountries', {filmID:filmID}, function(data){
				$('#availableCountries_'+filmID).html(data);
				$('#availableCountries_'+filmID).fadeToggle();
			});

		});

		/* Xchange cp titles Pagination */
		$('.pagination li').click(function(e){
			e.preventDefault();

			var page = $(this).children('a').attr('href');
			var rel = $(this).children('a').attr('rel');

			if(page != undefined)
				var page = page.split('=')[1];
			else
				return false;

			if(rel == 'prev')
			{
				var active = $('.pagination li[class="active"]');
				$('.pagination .active').removeClass('active');
				$(active).prev('li').addClass('active');
			}
			else if(rel == 'next')
			{
				var active = $('.pagination li[class="active"]');
				$('.pagination .active').removeClass('active');
				$(active).next('li').addClass('active');
			}
			else
			{
				$('.pagination .active').removeClass('active');
				$(this).addClass('active');
			}

			$('#bottomPager').hide();
			$("#datatable").fadeOut(300, function(){
				$('#titlesLoading').show();
				$.post('/xchange/pager', 'page='+page+'&'+$('#titlesFilter').serialize(), function(response){
					$("#xchangeTitles").html(response);
					$("#datatable").fadeIn(250);
					$('body').animate({
						scrollTop: $(".pagination").offset().top
					});
					$('#titlesLoading').hide();
				});
			});
		});

		/* filter  ordering */
		$('.filter').click(function(){
			var order = $(this).attr('data-order');
			var orderType = ($('input[name="filter[orderType]"]').val() == "asc")?"desc":"asc";

			$('input[name="filter[order]"]').val(order);
			$('input[name="filter[orderType]"]').val(orderType);
			titlesFilter();
		});

		/* Cheked  films */
		$("#bulkActCheckbox").click(function(){
			var actType = $(this).prop("checked");
			$(".itemCheckbox").prop("checked",actType);
		});

	});
</script>