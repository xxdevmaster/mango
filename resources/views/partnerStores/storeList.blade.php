<div id="topPager" class="text-left">
	{!! $stores->render() !!}
</div>
<div class="table-responsive">
	<table class="table">
		<tbody>
			<tr class="info">
				<td>
					Logo
				</td>
				<td>
					Title
				</td>
				<td class="text-right">Number of Titles</td>
			</tr>
			@foreach($stores->items() as $key => $val)
				<tr>
					<td>
						<a href="/partner/stores/films/{{ isset($val->id) ? $val->id : '' }}" class="thumbnail listStoresThumbs">
							<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($val->logo) ? $val->logo : 'nologo.png' }}" width="100">
						</a>
					</td>
					<td>
						<a href="/partner/stores/films/{{ isset($val->id) ? $val->id : '' }}">{{ isset($val->title) ? $val->title : '' }}</a>
					</td>
					<td class="text-right">
						{{ isset($val->titlesCount) ? $val->titlesCount : '' }}
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
<div id="bottomPager" class="text-left">
	{!! $stores->render() !!}
</div>
<script>
$(document).ready(function(){
	$('.pagination li').click(function(e){
		e.preventDefault();	
		var searchWord = $("input[name='searchWord']").val();

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

		$('.loading').show();
		$.post('/partner/stores/pager', {page:page, searchWord:searchWord}, function(data){
			$("#container").html(data);
			$('.loading').hide();
		});

	});
});
</script>