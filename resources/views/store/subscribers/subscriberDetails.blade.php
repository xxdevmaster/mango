<td colspan="8">
    <div class="panel panel-color panel-primary row" >
        <div class="panel-heading">
            <h3 class="panel-title">Subscriber info</h3>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                <tr>
                    <td>Channel</td>
                    <td>Start Date</td>
                    <td>End date</td>
                    <td class="text-right">Months Count</td>
                    <td class="text-right">Price Per Month</td>
                    <td class="text-right">Status</td>
                </tr>
                </thead>
                <tbody>
                @if(!$subscriberDetails->isEmpty())
                    @foreach($subscriberDetails as $subscriberDetail)
                        <tr>
                            <td>
                                {{ isset($subscriberDetail->subchannelTitle) ? $subscriberDetail->subchannelTitle : '' }}
                            </td>
                            <td >
                                {{ isset($subscriberDetail->start_date) ? substr($subscriberDetail->start_date,0,10) : '' }}
                            </td>
                            <td >
                                {{ isset($subscriberDetail->end_date) ? substr($subscriberDetail->end_date,0,10) : '' }}
                            </td>
                            <td >
                                {{ isset($subscriberDetail->totalmonth) ? $subscriberDetail->totalmonth : '' }}
                            </td>
                            <td >
                                {{ isset($subscriberDetail->currency) ? $subscriberDetail->currency : '' }} {{ isset($subscriberDetail->amount) ? $subscriberDetail->amount : '' }}
                            </td>
                            <td class="text-right">
                                {{ isset($subscriberDetail->end_date) || $subscriberDetail->end_date == '0000-00-00 00:00:00' ? 'Active' : 'Inactive' }}
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</td>