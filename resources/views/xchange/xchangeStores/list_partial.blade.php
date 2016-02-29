<div id="topPager" class="text-right">
	{!! $paginator->render() !!}
</div>
<table class="table table-striped " id="platformsRows">
	<tbody>
		<tr class="info">
			<td>Logo</td>
			<td>Title</td>
			<td class="text-left">Description</td>
			<td class="text-right">Website</td>
		</tr>      
		@foreach($paginator->items() as $key => $val)
			<tr>
				<td>
					<a href="/xchange/stores/films/{{ isset($val->id) ? $val->id : '' }}" class="thumbnail listStoresThumbs">
						<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($val->logo) ? $val->logo : 'nologo.png' }}" width="100" style="mex-height:80px;">
					</a>
				</td>
				<td>
					<a href="/xchange/stores/films/{{ isset($val->id) ? $val->id : '' }}">{{ isset($val->title) ? $val->title : '' }}</a>
				</td>
				<td>
					<div class="listStoreBrief">
						{{ isset($val->brief) ? $val->brief : '' }}
					</div>
				</td>
				<td class="text-right">
					{{ isset($val->website) ? $val->website : '' }}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
<div id="bottomPager" class="text-right">
	{!! $paginator->render() !!}
</div>
<script>
$(document).ready(function(){
	$('.pagination li').click(function(e){
		e.preventDefault();	
		var searchWord = $("input[name='searchWord']").val();
		var page = $(this).children('a').attr('href');
		var page = page.split('=')[1];	
		$('.loading').show();
		$.post('/xchangeStores/pager', {page:page, searchWord:searchWord}, function(data){
			$("#PlatformsContainer").html(data);
			$('.loading').hide();
		});		
		$('.pagination .active').removeClass('active');
		$(this).addClass('active');
		
	});
});
</script>