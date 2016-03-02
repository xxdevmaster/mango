<div class="row text-left clearfix">
    {!! $users->render() !!}
</div>
<div class="table-responsive">
    <table class="table table-striped ">
        <thead>
            <tr>
                <th>
                    Photo
                </th>
                <th>
                    Name &amp; Surname
                </th>
                <th>
                    Sex
                </th>
                <th>
                    E-mail
                </th>
                <th>
                    <a class="filter" data-order="bdate" data-orderType="Asc">Age
                        <i class="ion-arrow-down-b hidden"></i>
                    </a>
                </th>
                <th>
                    <a class="filter" data-order="country" data-orderType="Asc">Country
                        <i class="ion-arrow-down-b hidden"></i>
                    </a>
                </th>
                <th>
                    <a class="filter" data-order="u_regdate" data-orderType="Asc">Joined On
                        <i class="ion-arrow-down-b"></i>
                    </a>
                </th>
                <th class="text-right">
                    <a class="filter" data-order="uamount" data-orderType="Asc">Total Spend
                        <i class="ion-arrow-down-b hidden"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($users->items()))
                @foreach($users->items() as $key => $val)
                    <?php
                    $curYear = date('Y');
                    if($val->bdate > 0)
                        $uYear = $curYear - $val->bdate;
                    ?>
                    <tr>
                        <td>
                            <img src="http://cinecliq.assets.s3.amazonaws.com{{ isset($val->u_avatar) ? $val->u_avatar : "" }}" width="50">
                        </td>
                        <td>
                            {{ isset($val->u_fname) ? $val->u_fname : "" }} {{ isset($val->u_name) ? $val->u_name : "" }}
                        </td>
                        <td>
                            {{ isset($val->u_gender) ? $val->u_gender : "" }}
                        </td>
                        <td>
                            @if(empty($val->login_provider == 1))
                                <a class="fbIcon" data-toggle="tooltip" data-placement="right" animation="true"  title="{{ isset($val->u_email) ? $val->u_email : '' }}">
                                    <i class="ion-email fa-2x"></i>
                                </a>
                            @else
                                <a href="http://www.facebook.com/{{ $val->fb_id }}" target="blank">
                                    <i class="ion-social-facebook fa-2x"></i>
                                </a>
                            @endif
                        </td>
                        <td>
                            <p>
                                {{ isset($uYear) ? $uYear : "" }}
                            </p>
                        </td>
                        <td>
                            {{ isset($val->geo_country) ? $val->geo_country : "" }}
                        </td>
                        <td>
                            {{ strftime('%d/%m/%Y', $val->u_regdate) }}
                        </td>
                        <td class="text-right">
                            {{ isset($val->uamount) ? $val->uamount : "" }}
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div id="bottomPager" class="row text-left">
    {!! $users->render() !!}
</div>
<script>
$(document).ready(function(){
    $('.pagination li').click(function(e){
        e.preventDefault();
        var usersFilter = $("#usersFilter").serialize();
        var page = $(this).children('a').attr('href');
        if(page != undefined)
            var page = page.split('=')[1];
        else
            return false;
        $('.loading').show();
        $.post('/store/usersManagement/pager', 'page='+page+"&"+usersFilter, function(data){
            $("#usersContainer").html(data);
            $('.loading').hide();
        });
        $('.pagination .active').removeClass('active');
        $(this).addClass('active');

    });


});
</script>