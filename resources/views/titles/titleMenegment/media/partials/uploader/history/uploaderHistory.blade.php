<table class="table">
    <tbody>
        <tr>
            <th>User</th>
            <th>Movie/Trailer</th>
            <th>Job Id</th>
            <th>Status</th>
            <th>Pass Id</th>
            <th>Submited at</th>
        </tr>
        @if(isset($bitjobs))
            @foreach($bitjobs as $value)
                <tr class="success">
                    <td>{{ isset($value->person) ? $value->person : '' }}</td>
                    <td>{{ isset($value->person) ? $value->person : '' }}</td>
                    <td>{{ isset($value->job_id) ? $value->job_id : '' }}</td>
                    <td>{{ isset($value->job_status) ? $value->job_status : '' }}</td>
                    <td>{{ isset($value->pass_id) ? $value->pass_id : '' }}</td>
                    <td>{{ isset($value->dt) ? $value->dt : '' }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>