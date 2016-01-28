<div id="topPager" class="text-right">
	{!! $paginator->render() !!}
</div>
<div class="text-center" id="titlesLoading">
	<i class="ion-loading-c fa-4x"></i>
</div>
<table id="datatable" class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>Poster</th>
        <th>ID</th>
        <th>Title</th>
        <th>Content Providers</th>
        <th>Stores</th>
        <th>Media</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($films as $film)
        <tr>
            <td>
                <a href="{{url()}}/titles/metadata/{{$film->id}}">
                    <img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" style="width:50px;">
                </a>
            </td>
            <td>{{ $film->id  }}</td>
            <td>{{ $film->title }}</td>
            <td>
                <span>{{ $film->companies->implode('title', '&nbsp;,&nbsp;')  }}</span>
            </td>
            <td>
                <span>{{ $film->stores->implode('title', '&nbsp;,&nbsp;')  }}</span>
            </td>
            <td> T  F </td>
            <td>
                <a href="{{url()}}/titles/metadata/{{$film->id}}">Edit</a>
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
			
			//All titles Pagination
            $('.pagination li').click(function(e){
                e.preventDefault();
				
				var page = $(this).children('a').attr('href');
				var page = page.split('=')[1];	

                $('#bottomPager').hide();
                $("#datatable").fadeOut(300, function(){
					$('#titlesLoading').show();
                    $.post('/titles/pager', {page:page}, function(response){
                        $("#allTitles").html(response);
                        $("#datatable").fadeIn(250);
                        $('body').animate({
                            scrollTop: $(".pagination").offset().top
                        });
                        $('#titlesLoading').hide();
                    });
                });
				
				$('.pagination .active').removeClass('active');
				$(this).addClass('active');
				
            });
			//End pagination
        });
</script>