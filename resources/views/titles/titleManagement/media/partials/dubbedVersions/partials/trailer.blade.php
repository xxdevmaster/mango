<table class="table">
    @if(isset($media['trailer']))
        @foreach($media['trailer'] as $key => $value)
            <tr>
                @if(array_key_exists($value->locale, $allLocales))
                    <td style="width:250px;">
                        <?php
                        $source = '';
                        if($value->source != ''){
                            $source = json_decode($value->source)->dash;
                            $source =  str_replace(array('s3://cinehost.streamer.s3.amazonaws.com/','s3://cinehost.streamer.s3-website-us-east-1.amazonaws.com'),'https://s3.amazonaws.com/cinehost.streamer/',$source->playlist);
                        }

                        if($source == '')
                            echo '<img src="http://cinecliq.assets.s3.amazonaws.com/files/NoVideo.png" class="img-responsive" style="max-width:250px;">';
                        else
                            echo  '
                               <div id="'.$value->type.'_'.$value->id.'" style="width:350px;"></div>
                               <script>
                                    $(document).ready(function(){
                                       if(typeof player_setup_'.$value->type.' !== "undefined"){
                                          if(player_setup_'.$value->type.'['.$value->id.'] != undefined)
                                              player_'.$value->type.'['.$value->id.'].destroy();
                                       }
                                       player_setup_'.$value->type.'['.$value->id.'] = "setup" ;
                                       player_'.$value->type.'['.$value->id.'] = bitdash("'.$value->type.'_'.$value->id.'");
                                       player_'.$value->type.'['.$value->id.'].setup({
                                           key:              "ea4f0aad-4aba-41d2-8015-8c11f182e859",
                                           source: {
                                               dash:            "'.$source.'",
                                           },
                                           playback: {
                                               autoplay: false
                                           },
                                           style: {
                                               width:            "100%",
                                               aspectratio:      "16:9",
                                               controls:         true,
                                               autohidecontrols: true
                                           }
                                         }).then(function(playerObject) {

                                        }, function(error) {

                                        });

                                    });
                               </script>
                           ';
                        ?>
                    </td>
                    <td style="width:90%">
                        <div class="form-group">
                            <select class="selectBoxWithSearchTrailer" name="language[{{$value->type}}][{{$value->id}}]" data-placeholder="Choose Language">
                                @if(isset($allLocales) && is_array($allLocales))
                                    <option selected="selected" value="0">Choose Language</option>
                                    @foreach($allLocales as $key => $val)
                                        @if($key === $value->locale)
                                            <option value="{{ $key }}" selected>{{ $val }}</option>
                                        @else
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </td>
                    <td></td>
                    <td>
                        <button class="pull-right btn btn-default btn-sm" onclick="return trailerRemove({{$value->id}}, '{{ $allLocales[$value->locale] }}', '{{ $value->films_id }}');" data-id="{{ $value->id }}">
                            <i class="fa fa-close"></i>
                        </button>
                    </td>
            </tr>
            @endif
        @endforeach
    @endif
</table>
<script>
    jQuery(document).ready(function() {
        jQuery(".selectBoxWithSearchTrailer").select2({
            width: '100%',
        });
    });
    function trailerRemove(movieId, language, filmId){
        autoCloseMsgHide();
        bootbox.confirm('Do you really want to delete '+language+' language ?', function(result) {
            if(result){
                $('.loading').show();
                $.post('http://pro.cinehost-back.loc/titles/media/dubbedVersions/movieRemove', {filmId:filmId, movieId:movieId }, function(response){
                    if(response.error == 0) {
                        $.post('http://pro.cinehost-back.loc/titles/media/getTemplate', {filmId:filmId, template:'trailer'}, function(data){
                            if(data){
                                $.each(player_setup_trailer, function( index, value ) {
                                    if(value != undefined){
                                        console.log(player_trailer[index]);
                                        player_trailer[index].destroy();
                                    }
                                });
                                player_setup_trailer = [];
                                player_trailer = [];
                                $('#languages_trailer').html(data);
                                $('.loading').hide();
                                autoCloseMsg(0, response.message, 5000);
                            }else
                                $('.loading').hide();
                        });
                    }else {
                        $('.loading').hide();
                        autoCloseMsg(1, response.message, 5000);
                    }
                });
            }
        });
    }
</script>