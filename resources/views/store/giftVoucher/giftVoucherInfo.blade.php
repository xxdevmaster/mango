<div class="modal-dialog">
    <div class="modal-content ">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="label">Gift Voucher Codes</h4>
        </div>
        <div class="modal-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p class="panel-title">Available Codes</p>
                </div>
                <div class="panel-body">
                    @if(isset($vouchers['free']))
                        @foreach($vouchers['free'] as $val)
                            <h3 class="h3 pull-left codes" data-toggle="tooltip" data-placement="top" title="Click to copy" role="tooltip" >
                                <button class="btn btn-primary" onclick="copy(this);return false;" data-toggle="tooltip" data-placement="top" title="Copied!" role="tooltip">{{ $val }}</button>
                                <span>{{ $val }}</span>
                            </h3>

                        @endforeach
                    @endif
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Used Codes</div>
                <div class="panel-body">
                    @if(isset($vouchers['used']))
                        @foreach($vouchers['used'] as $val)
                            <h3 class="h3 pull-left codes" data-toggle="tooltip" data-placement="top" title="Click to copy" role="tooltip" >
                                <button class="btn btn-inverse" onclick="copy(this);return false;" data-toggle="tooltip" data-placement="top" title="Copied!" role="tooltip">{{ $val }}</button>
                                <span>{{ $val }}</span>
                            </h3>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
    $('.codes').tooltip();
    function copy(target) {
        $('.codes').attr('data-original-title', 'Click to copy');
        $('.codes').children('span').attr('id','');
        $(target).parent('.codes').attr('data-original-title', 'Copied!').tooltip('show');
        $(target).parent().children('span').attr('id','currentCopyText');
        var rng, sel;
        if (document.createRange) {
            rng = document.createRange();
            rng.selectNodeContents(document.getElementById('currentCopyText'));
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(rng);
        } else {
            var rng = document.body.createTextRange();
            rng.moveToElementText(document.getElementById('currentCopyText'));
            rng.select();
        }
        document.execCommand('copy');
    }
</script>