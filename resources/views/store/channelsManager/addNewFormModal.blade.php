<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Add New Sub Channel</h4>
        </div>
        <div class="modal-body">
            <form role="form" id="newChannelForm">
                <div class="form-group">
                    <input type="text" name="channelTitle" class="form-control" id="channelTitle" placeholder="Channel Title">
                </div>

                <div class="form-group">
                    <select name="parentChannel" id="parentChannel" class="form-control">
                        <option value="0" selected="selected">Select Parent Channel</option>
                        @if(isset($parentSubChannels))
                        @foreach($parentSubChannels as $subChannel)
                        <option value="{{ $subChannel->id }}">{{ $subChannel->title }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="input-movieTitles">Movie Titles</label>
                    <input type="text" id="input-movieTitles" name="inputToken" value="" />
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $("#input-movieTitles").tokenInput("/store/channelsManager/getTokenMovieTitles", {
                                theme: "facebook",
                                tokenFormatter:function(item){ return '<li><input type="hidden" name="sorted['+item.id+']" /><p>' + item.title + '</p></li>' }
                            });
                        });
                    </script>
                </div>
                <div class="form-group text-right">
                    <button class="btn btn-primary btn-sm importAllTitles" type="button">
                        <span class="glyphicon glyphicon-import" aria-hidden="true"></span> Import All Titles
                    </button>
                    <button class="btn btn-danger btn-sm clearAllTitles" type="button">
                        <span aria-hidden="true" class="glyphicon glyphicon-trash"></span> Clear All Titles
                    </button>
                </div>
                <div class="panel panel-default" id="model">
                    <div class="panel-heading">Channel Model</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <select id="channel_model" class="form-control" name="model" onchange="showChannnelModel();">
                                <option value="0">General</option>
                                <option value="1">Subscription</option>
                                <option value="2">Bundle</option>
                            </select>
                        </div>

                        <div class="form-group channel_model channel_model_1 display-none">
                            <label class="ff-label">Subscription</label>
                            <select name="subscriptions_id" id="subscriptions_id" class="form-control">
                                <option value="" selected="selected">Select Subscription</option>
                                @if(isset($subscriptions))
                                @foreach($subscriptions as $subscription)
                                @if($subscription->currency == 'EUR')
                                <option value="{{ $subscription->id }}">{{ $subscription->title }} ? {{ $euroPlans->search($subscription->plan_id) }}</option>
                                @else
                                <option value="{{ $subscription->id }}">{{ $subscription->title }} ? Every {{ $subscription->regular_frequency }} {{ ($subscription->regular_period == 'day' ? 'Day(s)' : 'Month(s)' ) }} ? {{ $subscription->currency }} {{ $subscription->regular_amount }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                        </div>


                        <div class="form-group channel_model channel_model_2 display-none">
                            <label class="ff-label">Bundle</label>
                            <select name="bundles_id" id="bundles_id" class="form-control">
                                <option value="" selected="selected">Select Bundle</option>
                                @if(isset($bundles))
                                @foreach($bundles as $bundleID => $bundle)
                                <option value="{{ $bundleID }}">{{ $bundle }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="addNewChannel">Add & Continue to Edit</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        /* Add New Channel*/
        $('#addNewChannel').click(function(){

            var newChannelForm = $('#newChannelForm').serialize();

            $.post('/store/channelsManager/addSubChannel', newChannelForm, function(data){
                $('#newChannelModal').modal('hide');
                $('#subChannelsContent').html(data);
                $('#newChannelForm')[0].reset();
            });

        });

        $('#parentChannel').change(function(){
            var value = $(this).val();

            if(value != 0)
                $('#model').hide();
            else
                $('#model').show();
        });
    });
</script>