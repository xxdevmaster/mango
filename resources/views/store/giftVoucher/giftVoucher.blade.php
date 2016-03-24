@extends('layout')
@section('content')
    <div class="col-md-12">
        <div class="row">
            <div class="title">
                <h1 class="h1">Gift Vouchers</h1>
            </div>
            <hr>
            <div class="form-group cont_block">
                <button class="btn btn-primary btn-md" data-target="#addNewGiftVoucher" data-toggle="modal">+ Add New</button>
            </div>
            <hr>

            <div id="voucherContainer">
                @include('store.giftVoucher.list')
            </div>

        </div>
    </div>

    <div class="modal fade" id="addNewGiftVoucher" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="label">Add New Gift Voucher</h4>
                </div>
                <div class="modal-body">
                    <form id="newVaucherForm" autocomplete="off">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="vaucherTitle" class="form-control" id="vaucherTitle" value="">
                        </div>
                        <div class="form-group">
                            <label for="totalCount">Number of Vouchers (max 20)</label>
                            <input type="number" name="totalCount" class="form-control" id="totalCount" min="1" max="20">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="attache">Add</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="giftVoucherInfo" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true"></div>
    <script>
        $('#attache').click(function(){
            var newVaucherForm = $('#newVaucherForm').serialize();

            var title = $('#vaucherTitle');
            var totalCount = $('#totalCount');

            if(title.val() != '') {

                if(totalCount.val() > 0 && totalCount.val() <= 20) {
                    $('#addNewGiftVoucher').modal('hide');
                    $('.loading').show();
                    $.post('/store/giftVoucher/attacheVoucher', newVaucherForm, function(data){
                        title.val('');
                        totalCount.val('');
                        $('#vaucherTitle').parent('.form-group').removeClass('has-error');
                        $('#totalCount').parent('.form-group').removeClass('has-error');
                        $('#voucherContainer').html(data);
                        $('.loading').hide();
                    });
                }else
                    $('#totalCount').parent('.form-group').addClass('has-error');
            }else
                $('#vaucherTitle').parent('.form-group').addClass('has-error');
        });
    </script>
@stop
