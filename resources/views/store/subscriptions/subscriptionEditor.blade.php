<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close clear-form" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Edit Subscription</h4>
        </div>
        <div class="modal-body">
            <form id="sub-edit-form" rell="form">
                <div class="miniwell">
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label class="ff-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ $subscriptions->title }}">
                    </div>
                    <div class="form-group" style="width:100px;float:left;">
                        <select name="currency" id="currency" class="form-control currency">
                            @if(isset($currencies))
                                @foreach($currencies as $currenciesCode => $currency)
                                    @if($currenciesCode == 'USD')
                                        <option value="{{ $currenciesCode }}" selected="selected">{{ $currency }}</option>
                                    @else
                                        <option value="{{ $currenciesCode }}">{{ $currency }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group TEUR" style="width:145px;display:none;float:left;margin-left:20px;">
                        <select name="plan_id" id="plan_id" class="form-control euroPlans">
                            <option value="" selected="selected">Select Euro Plan</option>
                            @if(isset($euroPlans))
                                @foreach($euroPlans as $euroPlanCode => $euroPlan)
                                    <option value="{{ $euroPlanCode }}">{{ $euroPlan }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="cl"></div>
                    <div class="form-group subLeft row">
                        <div class="ff-label clearfix">Trial Period&nbsp;
                            <span aria-hidden="true" class="glyphicon qs glyphicon-question-sign cp" data-toggle="popover" data-content="Trial periods run from the time of signup for the duration indicated. After the trial, normal billing begins. Trials need not be free, but the most common settings are &quot;$0 lasting 1 month&quot;, i.e. a one month free trial." data-original-title="" title=""></span>
                        </div>
                        <div class="pull-left ">
                            <div class="input-group" style="width:150px;float:left;">
                                <div class="input-group-addon curText">USD</div>
                                <input type="text" class="form-control" id="trial_amount" placeholder="Free" name="trial_amount" readonly="readonly">
                            </div>
                        </div>
                        <span class="pull-left">lasting</span>
                        <div class="pull-left">
                            <input type="text" name="trial_frequency" style="z-index:20000" class="form-control" id="trial_frequency" value="">
                        </div>
                        <div class="pull-left " style="width:100px;">
                            <select class="form-control" name="trial_period">
                                <option value="0">Day(s)</option>
                                <option value="1">Month(s)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group subLeft clearfix row">
                        <div class="ff-label clearfix">Recurring Period and Price&nbsp;
                            <span aria-hidden="true" class="glyphicon qs glyphicon-question-sign cp" data-toggle="popover" data-content="Charged every period as a base price, beginning after any applicable trial period. Recurring billing continues until cancelled." data-original-title="" title=""></span>
                        </div>
                        <div class="pull-left ">
                            <div class="input-group" style="width:150px;float:left;">
                                <div class="input-group-addon curText">USD</div>
                                <input type="text" class="form-control" id="regular_amount" placeholder="Price" name="regular_amount">
                            </div>
                        </div>
                        <span class="pull-left">&nbsp;every&nbsp;</span>
                        <div class="pull-left">
                            <input type="text" name="regular_frequency" style="z-index:20000" class="form-control" id="regular_frequency" value="">
                        </div>
                        <div class="pull-left " style="width:100px;">
                            <select class="form-control" name="regular_period">
                                <option value="0">Day(s)</option>
                                <option value="1">Month(s)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="subscriptionID" value="{{ isset($subscriptions->id) ? $subscriptions->id : '' }}">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-md clear-form" data-dismiss="modal" aria-hidden="true">Close</button>
            <button type="button" class="btn btn-primary btn-md" aria-hidden="true" data-dismiss="modal" id="updateSubscription">Save</button>
        </div>
        <script>
            $(function () {
                $('[data-toggle="popover"]').popover()
            });
            $(document).ready(function(){
                $("#currency").change(function () {
                    currencyChange();
                });
            });

            $(document).ready(function(){
                $('#updateSubscription').click(function(){
                    $('.loading').show();
                    $.post('/store/subscriptions/updateSubscription', $("#sub-edit-form").serialize(), function(data){
                        if(data.error != '1') {
                            $("#subscriptionsContainer").html(data);
                        }else{
                            autoCloseMsg('1', data.message, 5000);
                        }
                        $('.loading').hide();
                    });
                });
            });
        </script>
    </div>
</div>