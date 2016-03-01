<div class="col-md-12 text-left clearfix">
	{!! $companyFilms->render() !!}
</div>
<div class="col-md-12 clearfix">
	<div class="row">
		@foreach($companyFilms->items() as $key => $val)
			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
				<div class="thumbnailBox text-center">
					<a href="#" class="">
						<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($val->cover) ? $val->cover : 'nocover.png' }}" title="" alt="">
					</a>
					<div class="isInVault">V</div>
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
<div class="col-md-12 text-left">
	{!! $companyFilms->render() !!}
</div>
<script>
	var companyID = {{ $company->first()->id }};
	$(document).ready(function(){
		$('.pagination li').click(function(e){
			e.preventDefault();
			var page = $(this).children('a').attr('href');
			var page = page.split('=')[1];
			$('.loading').show();
			$.post('/xchange/contentProviders/films/pager', {page:page, companyID:companyID}, function(data){
				$("#contentProvidersFilms").html(data);
				$('.loading').hide();
			});
			$('.pagination .active').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>