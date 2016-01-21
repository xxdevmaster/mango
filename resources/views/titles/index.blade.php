@extends('layout')
@section('content')
    <div-- class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">All Titles </h3></div>
                <div class="panel-body">

                    <!--form class="form-horizontal" role="form">
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
                    </form-->
                </div> <!-- panel-body -->
            </div> <!-- panel -->
        </div> <!-- col -->

    </div-->

    <div-- class="row">
        <div class="col-md-12">
            <div class="panel panel-color panel-inverse">
                <div class="panel-heading">
                    <h3 class="panel-title">Titles Filter </h3>
                </div>
                <div class="panel-body">
                    <form id="titlesFilter" onsubmit="return false">
                        <div class="form-group row">
                            <!--div class="col-lg-8">
                                <input type="text" class="dt form-control" id="dt-from" name="filter[search_word]" value="" placeholder="User Name or E-mail" />
                            </div-->

                            <div class="col-lg-12">
                                <div class="input-group">

                                    <input type="text" class="form-control" placeholder="Title" value="" name="filter[search_word]">
                                     <span class="input-group-btn">
                                        <button type="button" class="btn btn-effect-ripple btn-primary" id="titleSearch">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">

                                <div class="col-lg-6">
                                    @if(isset($companies))
                                        <select name="filter[cp]" id="filter[cp]" class="form-control filter_select">
                                            <option value="" selected="selected">Content Providers</option>
                                            @foreach($companies as $val => $key)
                                                <option value="{{$key->id}}">{{ $key->title  }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="col-lg-6">
                                    @if(isset($stores))
                                        <select name="filter[pl]" id="filter[pl]" class="form-control filter_select">
                                            <option value="" selected="selected">Stores</option>
                                            @foreach($stores as $val => $key)
                                                <option value="{{$key->id}}">{{ $key->title  }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                           </div>
                        <input type="hidden" value="ASC" id="ordertype" name="filter[ordertype]">
                        <input type="hidden" value="title" id="order" name="filter[order]">
                    </form>
                </div>
            </div> <!-- panel -->
        </div> <!-- col -->

    </div-->
    <div class="col-md-12">
        <div class="panel panel-default">
            <div>
                @include('pager.pager')
            </div>
            <div id="allTitles" class="panel-body">
                @include('titles.partials.list')
            </div>
            <div>
                @include('pager.pager')
            </div>
        </div>
    </div>

    <script>

         function titlesFilter(){
             $('.loading').show();
             $("#ordertype").val("ASC");
             $.post('/titles/titlesFilter',$("#titlesFilter").serialize(), function(response){
                 if(response.error)
                     $('#allTitles').html('<h3 class="text-center text-danger">' + response.message + '</h3>');
                 else
                     $('#allTitles').html(response);
                 $('.loading').hide();
             });
         }

        $('#titleSearch').click(function(){
            titlesFilter();
        });

        $(".filter_select").change(function(){
            titlesFilter();

        });

        $(document).ready(function(){
            $(document).on('click', '.pagination li', function(){
                $('.loading').show();
                $('.pagination .active').removeClass('active');
                $(this).addClass('active');
                $("#allTitles").fadeOut(500);
                var pager = $(this).data('pager');

                $.post('/titles/pager', {pager:pager}, function(response){

                    $("#allTitles").html(response);
                    $('.loading').hide();
                    $("#allTitles").fadeIn('slow');
                });
            });
        });
    </script>
@stop