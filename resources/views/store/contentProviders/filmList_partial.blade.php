<div class="col-md-12 text-left clearfix">
	{!! $contentProviderFilms->render() !!}
</div>
<div class="col-md-12 clearfix">
	<div class="row">
		@foreach($contentProviderFilms->items() as $key => $val)
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
</div>
<div class="col-md-12 text-left clearfix">
	{!! $contentProviderFilms->render() !!}
</div>
<script>
	var contentProviderID = {{ $contentProvider->id }};
	$(document).ready(function(){
		$('.pagination li').click(function(e){
			e.preventDefault();
			var page = $(this).children('a').attr('href');
			if(page != undefined)
				var page = page.split('=')[1];
			else
				return false;
			$('.loading').show();
			$.post('/store/contentProviders/films/pager', {page:page, contentProviderID:contentProviderID}, function(data){
				$("#films").html(data);
				$('.loading').hide();
			});
			$('.pagination .active').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>