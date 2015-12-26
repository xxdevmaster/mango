<table class="table">
    <?php $type = '';?>
        @if(isset($filmsMedia))
            @foreach($filmsMedia as $key => $value)
            <tr>
                @if(array_key_exists($value->locale, $allLocales))
                    <td>
                        <?php
                            $type = $value->type;
                            $source = '';
                        if($value->source != ''){
                            $source = json_decode($value->source)->dash;
                            $source =  str_replace(array('s3://cinehost.streamer.s3.amazonaws.com/','s3://cinehost.streamer.s3-website-us-east-1.amazonaws.com'),'https://s3.amazonaws.com/cinehost.streamer/',$source->playlist);
                            $out = '
                               <div id="'.$value->type.'_'.$value->id.'" style="width:350px;"></div>
                               <script>
                                    $(document).ready(function(){
                                       if( typeof player_setup_'.$value->type.' !== "undefined"){
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
                        }else
                           $out =  '<img src="http://cinecliq.assets.s3.amazonaws.com/files/NoVideo.png" class="img-responsive noVideoPng">';
                        ?>
                       {!! $out !!}
                    </td>
                    <td class="col-md-12">
                        <div class="form-group">
                            <select class="selectBoxWithSearch_<?php echo $type;?>" name="language[{{$value->type}}][{{$value->id}}]" data-placeholder="Choose Language">
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
                        <button class="pull-right btn btn-default btn-sm" onclick="dubbedVersionsRemove({{$value->id}}, '{{ $allLocales[$value->locale] }}', '{{ $value->films_id }}', '{{ $value->type }}');">
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
        jQuery(".selectBoxWithSearch_<?php echo $type;?>").select2({
            width: '100%',
        });
    });
</script>