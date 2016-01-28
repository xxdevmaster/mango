@extends('layout')
@section('content')
    <div-- class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{isset($current_menu) ? $current_menu : ''}} </h3>
                </div>
            </div>
        </div>
    </div-->
    <div-- class="row">
        <div class="col-md-12">
            <div class="panel panel-color panel-inverse">
                <div class="panel-heading">
                    <h3 class="panel-title">Titles Filter </h3>
                </div>
                <div class="panel-body">
                    <form id="titlesFilter">
                        <div class="form-group row">
                            <!--div class="col-lg-8">
                                <input type="text" class="dt form-control" id="dt-from" name="filter[search_word]" value="" placeholder="User Name or E-mail" />
                            </div-->

                            <div class="col-lg-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Title" value="" name="filter[searchWord]">
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
                                <select name="filter[pl]" id="filter[pl]" class="form-control filter_select">
                                    <option value="" selected="selected">Stores</option>
                                    <option value="31">Arthouse.ru</option>
                                    <option value="32">City of Film</option>
                                    <option value="42">MillenniumOnDemand</option>
                                    <option value="48">Cinecliq</option>
                                    <option value="89">Neovod</option>
                                    <option value="217">N1</option>
                                    <option value="230">HerFlix</option>
                                    <option value="234">Edgarsss55</option>
                                    <option value="235">Kinogo</option>
                                    <option value="238">Herflix</option>
                                    <option value="239">Robbie Little</option>
                                    <option value="242">ojocorto</option>
                                </select>
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
                    </form>
                </div>
            </div>
        </div>

    </div-->
    <div class="col-md-12">
        <div class="panel panel-default">
            <div id="allTitles" class="panel-body">
                @include('titles.partials.list')
            </div>
        </div>
    </div>
    @include('titles.partials.newTitle')
    <script>


        function titlesFilter(){
            $('.loading').show();
            $("#ordertype").val("ASC");

            var titlesFilter = $('#titlesFilter').serialize();

            $.post('/titles/titlesFilter', titlesFilter, function(response){
                if(response.error)
                    $('#allTitles').html('<h3 class="text-center text-danger">' + response.message + '</h3>');
                else
                    $('#allTitles').html(response);
                $('.loading').hide();
            });
        }

        $('#titlesFilter').submit(function(e){
            e.preventDefault();
            titlesFilter();
        });

        $('#titleSearch').click(function(){
            titlesFilter();
        });

        $(".filter_select").change(function(){
            titlesFilter();
        });
    </script>
@stop