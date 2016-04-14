<div id="topPager" class="text-right">
	{!! $films->render() !!}
</div>
<div class="text-center" id="titlesLoading">
	<i class="ion-loading-c fa-4x"></i>
</div>
<form id="vaultCPBulkForm" onsubmit="return false" role="form">
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
							<li role="presentation"><a class=" cp" tabindex="-1" role="menuitem" rollapp-href="" id="bulkActAddToVault">Add to Xchange</a></li>
							<li role="presentation"><a class=" cp" tabindex="-1" role="menuitem" rollapp-href="" id="bulkActDeleteFromVault">Remove from Xchange</a></li>
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
				<td>
					Xchange
				</td>
				<td></td>
			</tr>
			<tbody>
			@if(isset($films))
				@foreach($films as $key => $val)
					@if(isset($val->delete_dt))
						<tr>
							<td>
								<input type="checkbox"  disabled="disabled">
							</td>
							<td>
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($val->cover) ? $val->cover : '' }}" style="width:50px;">
							</td>
							<td>{{ isset($key) ? $key : '' }}</td>
							<td>
								<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">{{ isset($val->title) ? $val->title : '' }}</a>
								<br>
								<span class="dengerTxt text-danger">This title will be removed from Xchange on {{ isset($val->delete_dt) ? $val->delete_dt : '' }}</span>
							</td>
							<td>
						<span>
							<span>{{ implode(' , ', $filmStores[$key]) }}</span>
						</span>
							</td>
							<td>

							</td>
							<td>
								<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">Edit</a>
							</td>
						</tr>
					@else
						<tr>
							<td>
								@if(isset($val->vaultID))
									<input type="checkbox" name="filmsInVault[{{ isset($key) ? $key : '' }}]" class="itemCheckbox">
								@else
									<input type="checkbox" name="filmsNotInVault[{{ isset($key) ? $key : '' }}]" class="itemCheckbox">
								@endif
							</td>
							<td class="w-100">
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($val->cover) ? $val->cover : '' }}" width="50" height="auto" alt="">
							</td>
							<td>{{ isset($key) ? $key : '' }}</td>
							<td>
								<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">{{ isset($val->title) ? $val->title : '' }}</a>
							</td>
							<td>
						<span>
							<span>{{ implode(' , ', $filmStores[$key]) }}</span>
						</span>
							</td>
							<td>
								@if(isset($val->vaultID))
									<button data-id="{{ isset($key) ? $key : '' }}" class="btn btn-danger btn-sm soloActDeleteFromVault cp">Remove from Xchange</button>
								@else
									<button data-id="{{ isset($key) ? $key : '' }}" class="btn btn-primary btn-sm soloActAddToVault cp">Add to Xchange</button>
								@endif
							</td>
							<td>
								<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">Edit</a>
							</td>
						</tr>
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

	/* Add films to vault */
	$( ".soloActAddToVault" ).click(function(){
		var filmID = $(this).data("id");
		autoCloseMsgHide();
		$(".loading").show();
		$.post('/CPTitles/soloActAddToVault', 'filmID='+filmID+'&'+$('#titlesFilter').serialize(), function(data){
			$('#CPTitles').html(data);
			$("#bulkActCheckbox").prop('checked', false);
			$(".loading").hide();			
		});
	});

	/* Delete films from vault */
	$( ".soloActDeleteFromVault" ).click(function(){
		var filmID = $(this).data("id");
		autoCloseMsgHide();
		$(".loading").show();		
		$.post('/CPTitles/soloActDeleteFromVault', 'filmID='+filmID+'&'+$('#titlesFilter').serialize(), function(data){
			$('#CPTitles').html(data);
			$("#bulkActCheckbox").prop('checked', false);
			$(".loading").hide();			
		});				
	});

	/* Add films to vault which the checked*/
	$("#bulkActAddToVault").click(function(){
		autoCloseMsgHide();
		$(".loading").show();
		$.post('/CPTitles/bulkActAddToVault', $("#vaultCPBulkForm").serialize()+'&'+$('#titlesFilter').serialize(), function(data){
			$('#CPTitles').html(data);
			$("#bulkActCheckbox").prop('checked', false);
			$(".loading").hide();
		});
	});

	/* Delete films from vault which the checked */
	$("#bulkActDeleteFromVault").click(function(){
		bootbox.confirm('You are about to remove the selected titles from vault. Are you sure? ', function(result) {
			if(result) {
				autoCloseMsgHide();
				$(".loading").show();
				$.post('/CPTitles/bulkActDeleteFromVault', $("#vaultCPBulkForm").serialize()+'&'+$('#titlesFilter').serialize(), function(data){
					$('#CPTitles').html(data);
					$("#bulkActCheckbox").prop('checked', false);
					$(".loading").hide();
				});
			}
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
			$.post('/CPTitles/pager', 'page='+page+'&'+$('#titlesFilter').serialize(), function(response){
				$("#CPTitles").html(response);
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