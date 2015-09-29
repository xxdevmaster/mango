@extends('layout')

@asaha('dddccvbcbcb')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">Titles Filter </h3></div>
                <div class="panel-body">

                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Content Providers</label>
                            <div class="col-sm-10">
                                <select class="form-control">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>

                            </div>
                        </div>
                    </form>
                </div> <!-- panel-body -->
            </div> <!-- panel -->
        </div> <!-- col -->

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Titles List</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
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
                                        <td><img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" style="width:50px;"></td>
                                        <td>{{ $film->id  }}</td>
                                        <td>{{ $film->title }}</td>
                                        <td><span class="badge bg-primary" data-html="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $film->companies->implode('title', '</br>')  }}">{{ $film->companies->count() }}</span></td>
                                        <td><span class="badge bg-primary" data-html="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $film->stores->implode('title', '</br>')  }}">{{ $film->stores->count() }}</span></td>
                                        <td> T  F </td>
                                        <td> Edit </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- End Row -->

@stop