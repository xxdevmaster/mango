<div id="topPager" class="text-right">
	{!! $items->render() !!}
</div>
<div class="text-center" id="titlesLoading">
	<i class="ion-loading-c fa-4x"></i>
</div>
<div class="table-responsive">
    <table id="datatable" class="table table-striped table-bordered">
        <thead>
        <tr>
            <th class="w-80">
                Poster
            </th>
            <th class="w-160">
                <a class="filter" data-order="id">ID
                    @if(!empty($orderBy) && $orderBy == 'id')
                        @if(!empty($orderType) && $orderType == 'desc')
                            <i class="ion-arrow-down-b"></i>
                        @else
                            <i class="ion-arrow-up-b"></i>
                        @endif
                    @endif
                </a>
            </th>
            <th class="w-160">
                <a class="filter" data-order="title">Title
                    @if(!empty($orderBy) && $orderBy == 'title')
                        @if(!empty($orderType) && $orderType == 'desc')
                            <i class="ion-arrow-down-b"></i>
                        @else
                            <i class="ion-arrow-up-b"></i>
                        @endif
                    @endif
                </a>
            </th>
            <th class="w-450">
                Content Providers
            </th>
            <th class="w-450">
                Stores
            </th>
            <th>Media</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items->items() as $film)
            <tr>
                <td class="w-80">
                    <a href="{{url()}}/titles/metadata/{{$film->id}}">
                        <img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" style="width:50px;">
                    </a>
                </td>
                <td class="w-160">
                    {{ $film->id  }}
                </td>
                <td class="w-160">
                    {{ $film->title }}
                </td>
                <td class="w-450">
                    <span>{{ implode(' , ', $filmCP[$film->id])  }}</span>
                </td>
                <td class="w-450">
                    <span>{{ implode(' , ', $filmStores[$film->id]) }}</span>
                </td>
                <td> T  F </td>
                <td>
                    <a href="{{url()}}/titles/metadata/{{$film->id}}">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div id="bottomPager" class="text-right">
	{!! $items->render() !!}
</div>

<script>
    $(document).ready(function(){
        //All titles Pagination
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
                $.post('/titles/pager', 'page='+page+'&'+$('#titlesFilter').serialize(), function(response){
                    $("#allTitles").html(response);
                    $("#datatable").fadeIn(250);
                    $('body').animate({
                        scrollTop: $(".pagination").offset().top
                    });
                    $('#titlesLoading').hide();
                });
            });
        });
        //End pagination

        $('.filter').click(function(){
            var order = $(this).attr('data-order');
            var orderType = ($('input[name="filter[orderType]"]').val() == "asc")?"desc":"asc";

            $('input[name="filter[order]"]').val(order);
            $('input[name="filter[orderType]"]').val(orderType);
            titlesFilter();
        });

    });
</script>