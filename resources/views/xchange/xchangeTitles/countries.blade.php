<td colspan="7">
    <div class="panel panel-color panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Available Countries</h3>
        </div>
        <div class="panel-body availableCountries">
            <table class="table">
                <tr>
                    <td nowrap="nowrap" colspan="2">Content Provider</td>
                    <td style="width:70%;">Countries</td>
                </tr>
                    @if(!empty($availableCountries))
                        @foreach($availableCountries as $companyID => $country)
                            <?php
                                $checkBoxView = '';
                                $colspan = 'colspan="2"';
                            ?>
                            @if(!$isFilmNotInStore)
                                <td>
                                    @if($country->delete_dt > 0)
                                        <input type="checkbox" disabled="disabled">
                                    @else
                                        <input data-companyid="{{ $companyID }}" data-filmid = '{{ $filmID }}' type="checkbox" {{ !empty($channelCompanies) ? 'checked="checked" class="disconnectCP2PL"' : 'class="connectCP2PL"' }}>
                                        @endif
                                </td>
                                <?php $colspan = '' ;?>
                            @endif
                            <tr>
                                <td nowrap {{ $colspan }}>
                                    {{ $country->info }} {{ ($country->delete_dt > 0) ? '<br><span class="dengerTxt">These territories will be deactivated for this title on <br>'.$country->delete_dt.'</span>' : '' }}
                                </td>
                                <td >
                                    {{ implode(', ', $country->countries) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
            </table>
        </div>
    </div>
</td>
<script>
$( document ).ready(function() {
    $( ".connectCP2PL" ).click(function(){
        autoCloseMsgHide();
        $(".loading").show();
        var companyID = $(this).attr("companyid");
        var filmID = $(this).data("filmid");

        $.post('/xchange/connectCP2PL', {filmID:filmID, companyID:companyID}, function(data){
            if(!data.error) {
                $( "#dtls"+filmID ).html(data);
                $(".loading").hide();
            }else
                autoCloseMsgHide(1, data.message, 5000);
        });

        return false;
    });

    $( ".disconnectCP2PL" ).click(function(){
        bootbox.confirm('You are about to deactivate the selected territories for this title in your your store. Are you sure? (Don’t worry, you can always activate them again later.)', function(result) {
            if(result) {
                autoCloseMsgHide();
                $(".loading").show();
                var companyID = $(this).attr("companyid");
                var filmID = $(this).data("filmid");

                $.post('/xchange/disconnectCP2PL', {filmID:filmID, companyID:companyID}, function(data){
                    if(!data.error) {
                        $( "#dtls"+filmID ).html(data);
                        $(".loading").hide();
                    }else
                        autoCloseMsgHide(1, data.message, 5000);
                });
            }
        });

        return false;
    });
});
</script>