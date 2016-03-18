<?php
$navTabs = '';
$navContent = '';

$active = 'active';
$fade = 'fade in';
$i = 0;

?>
@if(isset($slider))
    @foreach($slider as $sliderID => $slide)
        <?php
        if($i > 0) {
            $active = '';
            $fade = 'fade ';
        }
        $navTabs .= '<li class="'.$active.'"><a href="#slider_'.$sliderID.'" data-toggle="tab">'.$slide->title.'</a></li>';
        $sliderInfo = 'Please upload images for your front page slider. Required file specifications: '.$slide->width.'x'.$slide->height.'px ('.($slide->width*2).'x'.($slide->height*2).'px for retina displays), JPG or PNG, 3Mb max size.';
        ?>
        @inject('sliderController', 'App\Http\Controllers\Store\SliderController')
        <?php $item ='';?>

        @foreach($sliderController->getImageitems($sliderID) as $key => $val)
            @if(isset($films))
                <?php $storeFilms = '';?>
                @foreach($films as $k => $v)
                    @if($k == $val->films_id)
                        <?php $storeFilms .= '<option selected="selected" value="'.$k.'">'.$v->title.'</option>';?>
                    @else
                        <?php $storeFilms .= '<option value="'.$k.'">'.$v->title.'</option>';?>
                    @endif
                @endforeach
            @endif
            <?php
            $imageSrc = $val->url ? $val->url : 'http://cinecliq.assets.s3.amazonaws.com/wls/'.$storeID.'/'.$val->filename;
            $item .=
                    '
                         <li class="list-group-item" data-item="'.$val->position.'">
                            <input type="hidden" class="position" name="slider['.$key.'][position]" value="">
                            <div class="media">
                                <div class="col-sm-6 col-md-3">
                                    <a href="#" class="thumbnail">
                                        <img data-src="holder.js/100x100" src="'.$imageSrc.'" alt="...">
                                    </a>
                                </div>
                                <div class="media-body">
                                    <div class="form-group"><input name="slider['.$key.'][title]" type="text" class="form-control" placeholder="Title" value="'.$val->title.'"></div>
                                    <div class="form-group"><input name="slider['.$key.'][brief]" type="text" class="form-control" placeholder="Description" value="'.$val->brief.'"></div>
                                    <div class="form-group"><input name="slider['.$key.'][url]" type="text" class="form-control" placeholder="URL" value="'.$val->url.'"></div>
                                    <div class="form-group">
                                        <select name="slider['.$key.'][filmsID]" class="selectpicker form-control">
                                            <option value="">Select Film</option>
                                            '.$storeFilms.'
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger removeSlide" data-id="'.$key.'">Delete</button>
                                </div>
                            </div>
                        </li>';
            ?>
        @endforeach


        <?php
        $navContent .=
                ' <div class="tab-pane '.$fade.' '.$active.'" id="slider_'.$sliderID.'">
                        <div class="panel panel-default ML20">
                            <div class=" panel-body ">
                                '.$sliderInfo.'
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group col-md-6" id="uploadifive-img_'.$sliderID.'" style="width:180px">
                                    <input type="file" id="uploadifive-img_'.$sliderID.'" name="img" />
                                </div>
                                <script>
                                    $(document).ready(function(){
                                        CHUpload("/store/slider/uploadImage", "uploadifive-img_'.$sliderID.'", "+ Add New Slider Image", {"_token":"'.csrf_token().'" , "sliderID": "'.$sliderID.'"}, function(data){
                                            var response = JSON.parse(data);
                                            if(!response.error){
                                                $("#sliderContainer").html(response.html);
                                            }
                                            else {
                                                autoCloseMsg(1, response.message, 5000);
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        <hr>
                        <ul class="list-group sortable ui-sortable" id="slider-list-holder_49">
                            '.$item.'
                        </ul>
                    </div>';




        ?>
        <?php ++$i;?>
    @endforeach
@endif
<form id="sliderForm" role="form">
    <div class="title">
        <h1 class="h1">Slider Manager</h1>
    </div>
    @if(!empty($navTabs))
        <ul class="nav nav-tabs">
            {!!  $navTabs !!}
        </ul>
    @endif
    <div class="tab-content sliderContent">
        {!!  $navContent !!}
        <div class="row" style="margin-top:25px;">
            <div class="col-lg-12">
                <button class="btn btn-success" type="button" id="save">Save</button>
            </div>
        </div>
    </div>
</form>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
    $(function() {
        $('.sliderContent').sortable({
            items: ' li' ,
            update: function (event,ui){
                var arr = new Array();
                var i = 0;
                $(".list-group-item").each(function(){
                    $(this).children('.position').val(i);
                    ++i;
                });
            }
        });
    });

    $(document).ready(function(){
        $('#save').click(function(){
            $('.loading').show();
            var element = $(this);
            $(this).text('Saving..');
            var sliderForm = $('#sliderForm').serialize();

            $.post('/store/slider/save', sliderForm, function(data){
                $('.loading').hide();
                element.text('Save');
            });
        });

        $('.removeSlide').click(function(){
            $('.loading').show();
            var slideID = $(this).data('id');

            $.post('/store/slider/removeSlide', {slideID:slideID}, function(data){
                $('#sliderContainer').html(data);
                $('.loading').hide();
            });
        });
    });
</script>