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
                <th>
                    Photo
                </th>
                <th>
                    Name & Surname
                </th>
                <th>
                    Sex
                </th>
                <th>
                    E-mail
                </th>
                <th>
                    <a class="filter" data-order="bdate">Age
                        @if(!empty($order) && $order == 'bdate')
                            @if(!empty($orderType) && $orderType == 'desc')
                                <i class="ion-arrow-down-b"></i>
                            @else
                                <i class="ion-arrow-up-b"></i>
                            @endif
                        @endif
                    </a>
                </th>
                <th>
                    <a class="filter" data-order="geo_country">Country
                        @if(!empty($order) && $order == 'geo_country')
                            @if(!empty($orderType) && $orderType == 'desc')
                                <i class="ion-arrow-down-b"></i>
                            @else
                                <i class="ion-arrow-up-b"></i>
                            @endif
                        @endif
                    </a>
                </th>
                <th>
                    Channel
                </th>
                <th class="text-right">
                    Total Spend
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items->items() as $subscriber)
                <tr>
                    <td class="w-80">
                        <a>
                            <img src="http://cinecliq.assets.s3.amazonaws.com{{ isset($subscriber->u_avatar) ? $subscriber->u_avatar : "/i/n/nophoto_m.png" }}" width="40px" >
                        </a>
                    </td>
                    <td>
                        <a class="subscriberDetails" data-id="{{ isset($subscriber->accountsId) ? $subscriber->accountsId : "" }}">{{ isset($subscriber->u_fname) ? $subscriber->u_fname : "" }} {{ isset($subscriber->u_lname) ? $subscriber->u_lname : "" }}</a>
                    </td>
                    <td>
                        {{ isset($subscriber->u_gender) ? $subscriber->u_gender : "" }}
                    </td>
                    <td class="w-50">
                        @if(empty($subscriber->login_provider == 1))
                            <a class="mailIcon" data-toggle="tooltip" data-placement="right" animation="true"  title="{{ isset($subscriber->u_email) ? $subscriber->u_email : '' }}">
                                <i class="ion-email fa-2x"></i>
                            </a>
                        @else
                            <a href="http://www.facebook.com/{{ $subscriber->fb_id }}" class="fbIcon" target="blank">
                                <i class="ion-social-facebook fa-2x"></i>
                            </a>
                        @endif
                    </td>
                    <td>
                        @if($subscriber->u_bdate > 0)
                            {{ date('Y')-(date('Y', strtotime($subscriber->u_bdate))) }}
                        @endif
                    </td>
                    <td>
                        {{ isset($subscriber->geo_country) ? $subscriber->geo_country : "" }}
                    </td>
                    <td>
                        {{ isset($subscriber->subchannelTitle) ? $subscriber->subchannelTitle : "" }}
                    </td>
                    <td class="text-right">
                        {{ isset($subscriber->total) ? $subscriber->total : "" }}
                    </td>
                    <tr id="subscriberDetail_{{ isset($subscriber->accountsId) ? $subscriber->accountsId : "" }}" class="display-none"></tr>
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
                $.post('/store/subscribers/pager', 'page='+page+'&'+$('#subscribersFilter').serialize(), function(data){
                    $("#subscribersContainer").html(data);
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
            var orderType = ($('input[name="filter[orderType]"]').val() == "asc") ? "desc" : "asc";

            $('input[name="filter[order]"]').val(order);
            $('input[name="filter[orderType]"]').val(orderType);
            titlesFilter();
        });

        $('.mailIcon').tooltip('destroy');
        $('.mailIcon').hover(function(){
            $(this).tooltip('show');
        });

        $('.subscriberDetails').click(function() {
            var element = $(this);
            var subscriberID = $(this).data('id');

            $.post('/store/subscribers/getUserDetails', {subscriberID:subscriberID}, function(data){
                $('#subscriberDetail_'+subscriberID).html(data);
                $('#subscriberDetail_'+subscriberID).fadeToggle();
            });
        });
    });
</script>