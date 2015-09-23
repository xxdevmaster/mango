@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">Titles Filter</h3></div>
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
                    <h3 class="panel-title">Datatable</h3>
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
                                    @foreach ($store_films as $store_film)
                                    <tr>
                                        <td><img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $store_film->films->cover }}" style="width:50px;"></td>
                                        <td>{{ $store_film->films->id  }}</td>
                                        <td>{{ $store_film->films->title }}</td>
                                        <td>61</td>
                                        <td>2011/04/25</td>
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