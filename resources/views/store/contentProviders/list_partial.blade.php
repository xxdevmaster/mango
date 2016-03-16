<div id="topPager" class="text-right">
    {!! $contentProviders->render() !!}
</div>
<table class="table table-striped ">
    <thead>
        <tr>
            <th class="text-left">Logo</th>
            <th>Title</th>
            <th>Website</th>
            <th>Number of Titles</th>
            <th class="text-right">Edit</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($contentProviders->items()))
            @foreach($contentProviders->items() as $key => $val)
                <tr>
                    <td class="text-left">
                        <a href="/store/contentProviders/films/{{ isset($val->id) ? $val->id : ''}}" class="thumbnail listStoresThumbs">
                            <img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($val->logo) ? $val->logo : "nologo.png" }}" width="100" height="auto">
                        </a>
                    </td>
                    <td>
                        <a href="/store/contentProviders/films/{{ isset($val->id) ? $val->id : ''}}">
                            {{ isset($val->title) ? $val->title : "" }}
                        </a>
                    </td>
                    <td>{{ isset($val->website) ? $val->website : "" }}</td>
                    <td>{{ isset($val->titlesCount) ? $val->titlesCount : "0" }}</td>
                    <td class="text-right">
                        <button class="btn btn-default btn-sm openModal" data-id="{{ isset($val->id) ? $val->id : ''}}">Edit</button>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<div id="bottomPager" class="text-right">
    {!! $contentProviders->render() !!}
</div>
<script>
    $(document).ready(function(){
        $('.pagination li').click(function(e){
            e.preventDefault();
            if($(this).attr('class') == 'disabled')
                return false;
            var searchWord = $("input[name='searchWord']").val();
            var page = $(this).children('a').attr('href');
            var page = page.split('=')[1];
            $('.loading').show();
            $.post('/store/contentProviders/pager', {page:page, searchWord:searchWord}, function(data){
                $("#container").html(data);
                $('.loading').hide();
            });
            $('.pagination .active').removeClass('active');
            $(this).addClass('active');
        });

        $('.openModal').click(function(){
            var contentProviderID = $(this).data('id');

            if(contentProviderID != ''){
                $.post('/store/contentProviders/getContentProviderInfo', {contentProviderID:contentProviderID}, function(data){
                    $("#editContentProvider").html(data);
                    $('#editContentProvider').modal('show');
                });
            }
        });

    });
</script>