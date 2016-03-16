@extends('layout')
@section('content')
<?php
$navTabs = '';
$navContent = '';
$storeFilms = '';
?>
    @if(isset($films))
        @foreach($films as $key => $val)
            <?php $storeFilms .= '<option value="'.$key.'">'.$val->title.'</option>';?>
        @endforeach
    @endif
    @if(isset($sliderItems))
        @foreach($sliderItems as $key => $val)
            @if($slider->count() > 1)
                <?php $navTabs .= '<li class="active"><a href="#slider_'.$key.'" data-toggle="tab">'.$val->title.'</a></li>'; ?>
            @endif

            <?php
            $imageSrc = $val->url ? $val->url : 'http://cinecliq.assets.s3.amazonaws.com/wls/'.$storeID.'/'.$val->filename;
            $navContent .=
                    ' <div class="tab-pane active" id="slider_'.$key.'">
                        <ul class="list-group sortable ui-sortable" id="slider-list-holder_49">
                            <li class="list-group-item" id="item-686">
                                <div class="media">
                                    <div class="col-sm-6 col-md-3">
                                        <a href="#" class="thumbnail">
                                            <img data-src="holder.js/100x100" src="'.$imageSrc.'" alt="...">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <div class="form-group"><input name="title['.$key.']" type="text" class="form-control" placeholder="Title" value="'.$val->title.'"></div>
                                        <div class="form-group"><input name="brief['.$key.']" type="text" class="form-control" placeholder="Description" value="'.$val->brief.'"></div>
                                        <div class="form-group"><input name="url['.$key.']" type="text" class="form-control" placeholder="URL" value="'.$val->url.'"></div>
                                        <div class="form-group">
                                            <select name="film['.$key.']" class="selectpicker form-control">
                                                <option value="">Select Film</option>
                                                '.$storeFilms.'
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger">Delete</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>';
            ?>
        @endforeach
    @endif
<div class="col-lg-12">
    <div class="row">
        @if(!empty($navTabs))
            <ul class="nav nav-tabs">
                {!!  $navTabs !!}
            </ul>
        @endif

        <div class="tab-content">
            {!!  $navContent !!}
        </div>
        <!--div class="col-lg-12">
            <button class="btn btn-success" id="saveChanges">Save Changes</button>
        </div-->
    </div>
</div>
@stop
