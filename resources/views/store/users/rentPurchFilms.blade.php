<td colspan="8">
    <div class="panel panel-color panel-primary row" >
        <div class="panel-heading">
            <h3 class="panel-title">Rented / Purchased Films</h3>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <td>Title</td>
                        <td>Date</td>
                        <td>Price ($)</td>
                        <td class="text-right">Type</td>
                    </tr>
                </thead>
                <tbody>
                    @if(!$renPurchFilms->isEmpty())
                        @foreach($renPurchFilms as $key =>$val)
                            <tr>
                                <td>{{ isset($val->title) ? $val->title : "" }}</td>
                                <td>{{ isset($val->dt) ? $val->dt : "" }}</td>
                                <td>{{ isset($val->amount) ? $val->amount : "" }}</td>
                                <td class="text-right">
                                    {{ ($val->order_type == 0) ? 'Rented' : 'Purchased' }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</td>