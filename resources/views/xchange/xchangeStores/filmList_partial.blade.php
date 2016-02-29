<div class="col-md-12 text-left clearfix">
	{!! $paginator->render() !!}
</div>
<div class="col-md-12 clearfix">
	<div class="row">
		@foreach($paginator->items() as $key => $val)
			<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
				<div class="thumbnailBox">
					<a href="#" class="thumbnail">
						<img  class="" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($val->cover) ? $val->cover : 'nocover.png' }}" title="" alt="">
					</a>
					<div class="text-center thumbnailCaption">
						<p>
							{{ isset($val->title) ? $val->title : '' }}
						</p>
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
			$.post('/xchange/stores/films/pager', {page:page, storeID:storeID}, function(data){
				$("#storeFilms").html(data);
				$('.loading').hide();
			});
			$('.pagination .active').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>