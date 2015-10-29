@extends('layout')


@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel-group" id="accordion-test-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-2" aria-expanded="false" class="collapsed">
                            Basic
                        </a>
                    </h4>
                </div>
                <div id="collapseOne-2" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                    <div class="panel-body">
                        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h4 class="modal-title">Edit Feature</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="field-3" class="control-label">Name</label>
                                                    <input type="text" class="form-control" id="field-3" placeholder="Address">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group no-margin">
                                                    <label for="field-7" class="control-label">Description</label>
                                                    <textarea class="form-control autogrow" id="field-7" placeholder="Write something about yourself" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;">                                                        </textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="m-b-30">
                                                    <button id="addToTable" class="btn btn-primary waves-effect waves-light">Add <i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-striped" id="datatable-editable">
                                            <thead>
                                                <tr>
                                                    <th>name</th>
                                                    <th>value</th>
                                                    <th>visible</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="gradeX">
                                                    <td>amount</td>
                                                    <td>15</td>
                                                    <td><input type="checkbox" /></td>
                                                    <td class="actions">
                                                        <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                                                        <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                                                        <a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
                                                        <a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-info">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal -->
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Decription</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($features as $feature)
                                                @if($feature->feature_level == 1)
                                                <tr>
                                                    <td>{{ $feature->feature_name  }}</td>
                                                    <td>{{ $feature->feature_description }}</td>
                                                    <td><a data-toggle="modal" data-target="#con-close-modal"> Edit </a></td>
                                                </tr>
                                                @endif
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseTwo-2" class="" aria-expanded="true">
                            Enterprise
                        </a>
                    </h4>
                </div>
                <div id="collapseTwo-2" class="panel-collapse collapse in" aria-expanded="flase">
                    <div class="panel-body">
                        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h4 class="modal-title">Edit Feature</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="field-3" class="control-label">Name</label>
                                                    <input type="text" class="form-control" id="field-3" placeholder="Address">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group no-margin">
                                                    <label for="field-7" class="control-label">Description</label>
                                                    <textarea class="form-control autogrow" id="field-7" placeholder="Write something about yourself" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;">                                                        </textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="m-b-30">
                                                    <button id="addToTable" class="btn btn-primary waves-effect waves-light">Add <i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-striped" id="datatable-editable">
                                            <thead>
                                                <tr>
                                                    <th>name</th>
                                                    <th>value</th>
                                                    <th>visible</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="gradeX">
                                                    <td>amount</td>
                                                    <td>15</td>
                                                    <td><input type="checkbox" /></td>
                                                    <td class="actions">
                                                        <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                                                        <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                                                        <a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
                                                        <a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-info">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal -->
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Decription</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($features as $feature)
                                                @if($feature->feature_level == 2)
                                                <tr>
                                                    <td>{{ $feature->feature_name  }}</td>
                                                    <td>{{ $feature->feature_description }}</td>
                                                    <td><a data-toggle="modal" data-target="#con-close-modal"> Edit </a></td>
                                                </tr>
                                                @endif
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseThree-2" class="collapsed" aria-expanded="false">
                            Studio
                        </a>
                    </h4>
                </div>
                <div id="collapseThree-2" class="panel-collapse collapse" aria-expanded="false">
                    <div class="panel-body">
                        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h4 class="modal-title">Edit Feature</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="field-3" class="control-label">Name</label>
                                                    <input type="text" class="form-control" id="field-3" placeholder="Address">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group no-margin">
                                                    <label for="field-7" class="control-label">Description</label>
                                                    <textarea class="form-control autogrow" id="field-7" placeholder="Write something about yourself" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;">                                                        </textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="m-b-30">
                                                    <button id="addToTable" class="btn btn-primary waves-effect waves-light">Add <i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-striped" id="datatable-editable">
                                            <thead>
                                                <tr>
                                                    <th>name</th>
                                                    <th>value</th>
                                                    <th>visible</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="gradeX">
                                                    <td>amount</td>
                                                    <td>15</td>
                                                    <td><input type="checkbox" /></td>
                                                    <td class="actions">
                                                        <a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>
                                                        <a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>
                                                        <a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>
                                                        <a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-info">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.modal -->
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Decription</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($features as $feature)
                                                @if($feature->feature_level == 3)
                                                <tr>
                                                    <td>{{ $feature->feature_name  }}</td>
                                                    <td>{{ $feature->feature_description }}</td>
                                                    <td><a data-toggle="modal" data-target="#con-close-modal"> Edit </a></td>
                                                </tr>
                                                @endif
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('footer')
<script src="/assets/jquery-datatables-editable/jquery.dataTables.js"></script>
<script src="/assets/datatables/dataTables.bootstrap.js"></script>
<script src="/assets/jquery-datatables-editable/datatables.editable.init.js"></script>

<script>
            jQuery(document).ready(function() {

                $('#datatable-basic').dataTable({
                    bFilter: false, bInfo: false
                });
            });
        </script>
@stop