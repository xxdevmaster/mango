@if(isset($subscriptions))
    @foreach($subscriptions as $subscriptionID => $subscription)
        <div class="panel vaucherBox">
            <div class="panel-body p-t-10">
                <div class="media-main">
                    <div class="pull-right btn-group-sm">
                        <button class="btn btn-primary btn-md subscriptionEditor" data-id="{{ $subscriptionID }}">
                            View
                        </button>
                        <button class="btn btn-danger removeSubscription" data-id="{{ $subscriptionID }}" data-placement="top" data-toggle="tooltip" data-original-title="Delete">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>
                    <div class="info col-md-3">
                        <p>
                            <span class="name">{{ isset($subscription->title) ? $subscription->title : "" }} - Every {{ isset($subscription->regular_frequency) ? $subscription->regular_frequency : "" }} {{ isset($subscription->regular_period) && $subscription->regular_period == 'day' ? 'Day(s)' : 'Month(s)' }} - {{ isset($subscription->currency) ? $subscription->currency : "" }} {{ isset($subscription->regular_amount) ? $subscription->regular_amount : "" }}</span>
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


        $(".removeSubscription").click(function(){
            autoCloseMsgHide();
            var subscriptionID = $(this).data('id');

            bootbox.confirm('Do you really want to delete this item?', function(result) {
                if(result) {
                    $('.loading').show();
                    $.post('/store/subscriptions/removeSubscription', {subscriptionID:subscriptionID}, function(data){
                        if(data.error != '1') {
                            $("#subscriptionsContainer").html(data.html);
                        }else{
                            autoCloseMsg('1', data.message, 5000);
                        }
                        $('.loading').hide();
                    });
                }
            });
        });

        /** Get Subscription Editor*/
        $('.subscriptionEditor').click(function(){
            autoCloseMsgHide();
            var subscriptionID = $(this).data('id');

            $.post('/store/subscriptions/getSubscriptionEditor', {subscriptionID:subscriptionID}, function(data){
                if(data.error != 1) {
                    $('#subscriptionEditor').html(data);
                    $('#subscriptionEditor').modal('show');
                }else
                    autoCloseMsg('1', data.message, 5000);
            });
        });

    });
</script>