@if(isset($voucher))
    @foreach($voucher as $key => $val)
        <div class="panel vaucherBox">
            <div class="panel-body p-t-10">
                <div class="media-main">
                    <div class="pull-right" style="margin-top:-9px;margin-bottom:3px">
                        <button class="btn btn-primary btn-md getGiftVoucherEditor" data-id="{{ $key }}">
                            View
                        </button>
                    </div>
                    <div class="info col-md-3">
                        <p>
                            <b>{{ isset($val->title) ? $val->title : "" }}</b>
                        </p>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    @endforeach
@endif
<script>
    $(document).ready(function(){
        $('.getGiftVoucherEditor').click(function(){
            var vcbunchesID = $(this).data('id');
            autoCloseMsgHide();
            $.post('/store/giftVoucher/getGiftVoucherEditor', {vcbunchesID:vcbunchesID}, function(data){
                if(data.error == 0) {
                    $('#giftVoucherInfo').html(data.html);
                    $('#giftVoucherInfo').modal('show');
                }else
                    autoCloseMsg('1', data.message, 5000);
            });
        });
    });
</script>