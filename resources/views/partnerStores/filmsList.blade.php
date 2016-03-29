<div id="topPager" class="col-md-12 text-left clearfix">
	{!! $paginator->render() !!}
</div>

	<div class="row">
		@foreach($paginator->items() as $key => $val)
			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
				<div class="thumbnailBox text-center">
					<a href="/titles/metadata/{{ isset($key) ? $key : '' }}">
						<img  class="" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($val->cover) ? $val->cover : 'nocover.png' }}" title="" alt="">
					</a>
					<div class="text-center thumbnailCaption">
						<p>
							<a href="/titles/metadata/{{ isset($key) ? $key : '' }}">
								{{ isset($val->title) ? $val->title : '' }}
							</a>
						</p>
					</div>
				</div>
			</div>
		@endforeach
	</div>

<div id="bottomPager" class="col-md-12 text-left clearfix">
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
				$("#films").html(data);
				$('.loading').hide();
			});
			$('.pagination .active').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>