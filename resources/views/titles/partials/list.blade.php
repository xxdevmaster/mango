<table id="datatable" class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>Poster</th>
        <th>ID</th>
        <th>Title</th>
        <th>Content Providers</th>
        <th>Stores</th>
        <th>Media</th>
        <th>Actions</th>
    </tr>
    </thead>


    <tbody>
    @foreach ($films as $film)
        <tr>
            <td>
                <a href="{{url()}}/titles/metadata/{{$film->id}}">
                    <img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" style="width:50px;">
                </a>
            </td>
            <td>{{ $film->id  }}</td>
            <td>{{ $film->title }}</td>
            <td>
                <span class="badge bg-primary" data-html="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $film->companies->implode('title', '</br>')  }}">{{ $film->companies->count() }}</span>
            </td>
            <td>
                <span class="badge bg-primary" data-html="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{-- $film->stores->implode('title', '</br>')  --}}">{{-- $film->stores->count() --}}</span>
            </td>
            <td> T  F </td>
            <td>
                <a href="{{url()}}/titles/metadata/{{$film->id}}">Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>