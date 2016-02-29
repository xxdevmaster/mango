<div class="col-md-12 text-left clearfix">
	{!! $paginator->render() !!}
</div>
<div class="col-md-12 clearfix">
	<div class="row">
		@foreach($paginator->items() as $key => $val)
			<div class="col-sm-2">
				<div class="thumbnail storeFilmsThumbs">
					<a href="/titles/metadata/{{ isset($key) ? $key : '' }}">
						<img  class="" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($val->cover) ? $val->cover : 'nocover.png' }}" title="" alt="">
					</a>
					<div class="caption text-center">
						<h5 class="h5">
							<a href="/titles/metadata/{{ isset($key) ? $key : '' }}">
								{{ isset($val->title) ? $val->title : '' }}
							</a>
						</h5>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</div>
<div class="col-md-12 text-left clearfix">
	{!! $paginator->render() !!}
</div>
<script>
	var storeID = {{ $store->id }};
	$(document).ready(function(){
		$('.pagination li').click(function(e){
			e.preventDefault();
			var page = $(this).children('a').attr('href');
			var page = page.split('=')[1];
			$('.loading').show();
			$.post('/partner/stores/films/pager', {page:page, storeID:storeID}, function(data){
				$("#storeFilms").html(data);
				$('.loading').hide();
			});
			$('.pagination .active').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>