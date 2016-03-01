<div class="table-responsive">
    <table class="table">
        <tbody>
            <tr>
                <td style="width:56px">
                    Photo
                </td>
                <td style="width:209px;">
                    Name &amp; Surname
                </td>
                <td style="width:65px;">
                    Sex
                </td>
                <td style="width:65px;">
                    E-mail
                </td>
                <td style="width:60px;"><a class="cp pull-left orderDESC" rel="bdate">
                        Age
                    </a>
                </td>
                <td style="width:145px;">
                    <a class="cp pull-left orderDESC" rel="geo_country">Country</a>
                </td>
                <td style="width:105px;">
                    <a class="cp pull-left orderASC" rel="u_regdate">Joined&nbsp;On</a>
                    <span class=" pull-left AscDescIcon iconDESC"></span>
                </td>
                <td align="right" style="width:110px;">
                    <a class="cp orderDESC" style="display:inline-block;float:right" rel="uamount">Total&nbsp;Spend</a>
                </td>
            </tr>
            @if(isset($users))
                @foreach($users as $key => $val)
                    <?php
                    $curYear = date('Y');
                    if($val->bdate > 0)
                        $uYear = $curYear - $val->bdate;
                    ?>
                    <tr>
                        <td style="width:56px">
                            <img src="http://cinecliq.assets.s3.amazonaws.com{{ isset($val->u_avatar) ? $val->u_avatar : "" }}" width="50">
                        </td>
                        <td style="width:209px;">
                            {{ isset($val->u_fname) ? $val->u_fname : "" }} {{ isset($val->u_name) ? $val->u_name : "" }}
                        </td>
                        <td style="width:65px;">
                            {{ isset($val->u_gender) ? $val->u_gender : "" }}
                        </td>
                        <td style="width:65px;">
                            <?php
                                !empty($val->login_provider) ? '<a href="http://www.facebook.com/'.$val->fb_id.'" target="blank" class="icon_fb"></a>'
                                  : "<a></a>";
                            ?>
                        </td>
                        <td style="width:60px;">
                            <a class="cp pull-left orderDESC" rel="bdate">
                                {{ isset($uYear) ? $uYear : "" }}
                            </a>
                        </td>
                        <td style="width:145px;">
                            {{ isset($val->geo_country) ? $val->geo_country : "" }}
                        </td>
                        <td style="width:105px;">
                            {{ strftime('%d/%m/%Y', $val->u_regdate) }}
                        </td>
                        <td align="right" style="width:110px;">
                            {{ isset($val->uamount) ? $val->uamount : "" }}
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>