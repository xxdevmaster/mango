@if(isset($subscriptions))
    @foreach($subscriptions as $subscriptionID => $subscription)
        <div class="panel vaucherBox">
            <div class="panel-body p-t-10">
                <div class="media-main">
                    <div class="pull-right" style="margin-top:-9px;margin-bottom:3px">
                        <button class="btn btn-primary btn-md subscriptionEditor" data-id="{{ $subscriptionID }}">
                            View
                        </button>
                    </div>
                    <div class="info col-md-3">
                        <p>
                            <b>{{ isset($subscription->title) ? $subscription->title : "" }}</b>
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
        $('.subscriptionEditor').click(function(){
            var subscriptionID = $(this).data('id');
            autoCloseMsgHide();
            $.post('/store/subscriptions/getSubscriptionEditor', {subscriptionID:subscriptionID}, function(data){
                if(data.error == 0) {
                    $('#subscriptionEditor').html(data.html);
                    $('#subscriptionEditor').modal('show');
                }else
                    autoCloseMsg('1', data.message, 5000);
            });
        });
    });
</script>