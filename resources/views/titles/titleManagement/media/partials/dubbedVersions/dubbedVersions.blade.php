<div class="panel-heading">
    <h3 class="panel-title">Add New Dubbed Version</h3>
</div>
<div class="panel-body">
    <form id="dubbedVersionForm">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab-movie" data-toggle="tab">Movie</a>
            </li>
            <li>
                <a href="#tab-trailer" data-toggle="tab">Trailer</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-movie">
                <div class="media">
                    <div class="media-body">
                        <div class="form-group">
                            <select id="selectbox_movie" name="dubbedVersionsLanguages" data-placeholder="Choose Language" >
                                @if(isset($allLocales) && is_array($allLocales))
                                    <option selected="selected" value="0" >Choose Language</option>
                                    @foreach($allLocales as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <span class="fs12 dengerTxt language_info_movie"></span>
                            <div class="btn-group pull-right">
                                <button class="btn btn-default btn-sm" onclick="dubbedVersionsCreate('movie');">
                                    <span class="glyphicon glyphicon-plus"></span>&nbsp;Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"></div>
                </div>

                <div id="languages_movie">
                    <?php
                        $filmsMedia = $media['movie'];
                    ?>
                    @include('titles.titleManagement.media.partials.dubbedVersions.partials.movieAndTrailer', $filmsMedia)
                </div>

            </div>

            <div class="tab-pane fade" id="tab-trailer">
                <div class="media">
                    <div class="media-body">
                        <div class="form-group">
                            <select id="selectbox_trailer" name="dubbedVersionsLanguages" data-placeholder="Choose Language">
                                @if(isset($allLocales) && is_array($allLocales))
                                    <option selected="selected" value="0">Choose Language</option>
                                    @foreach($allLocales as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <span class="fs12 dengerTxt language_info_trailer"></span>
                            <div class="btn-group pull-right">
                                <button class="btn btn-default btn-sm" onclick="dubbedVersionsCreate('trailer');">
                                    <span class="glyphicon glyphicon-plus"></span>&nbsp;Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"></div>
                </div>
                <div class="miniwell" id="languages_trailer">
                    <?php
                         $filmsMedia = $media['trailer'];
                    ?>
                    @include('titles.titleManagement.media.partials.dubbedVersions.partials.movieAndTrailer', $filmsMedia)
                </div>
            </div>
        </div>
        <input type="hidden" name="filmId" value="{{ isset($film->id) ? $film->id : ''  }}">
    </form>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery("#selectbox_movie, #selectbox_trailer").select2({
            width: '100%',
        });
    });

    function dubbedVersionsCreate(type){
        $('.loading').show();
        var filmId = $('input[name="filmId"]').val();
        var locale = $('#selectbox_'+type+' option:selected').val();
        if(locale != 0){
            autoCloseMsgHide();
            $.post('{{url()}}/titles/media/dubbedVersions/dubbedVersionsCreate', {filmId:filmId, locale:locale, type:type}, function(response){
                if(response.error == 0){
                    $.each(window['player_setup_'+type], function( index, value ) {
                        if(value != undefined)
                            window['player_'+type][index].destroy();
                    });
                    window['player_setup_'+type] = [];
                    window['player_'+type] = [];
                    $('#languages_'+type).html(response.html);
                    $('#selectbox_'+type+' option[value=0]').prop('selected','selected').trigger('change');
                    $('.loading').hide();
                    autoCloseMsg(0, response.message, 5000);
                }
            });
        }else {
            $('.loading').hide();
            autoCloseMsg(1, 'Please Select Language From List', 5000);
        }

    }

    function dubbedVersionsRemove(movieId, language, filmId, type){
        autoCloseMsgHide();
        bootbox.confirm('Do you really want to delete '+language+' language ?', function(result) {
            if(result){
                $('.loading').show();
                $.post('{{url()}}/titles/media/dubbedVersions/dubbedVersionsRemove', {filmId:filmId, movieId:movieId , type:type}, function(response){
                    if(response.error == 0) {
                        $.each(window['player_setup_'+type], function( index, value ) {
                            if(value != undefined)
                                window['player_'+type][index].destroy();
                        });
                        window['player_setup_'+type] = [];
                        window['player_'+type] = [];
                        $('#languages_'+type).html(response.html);
                        $('#selectbox_'+type+' option[value=0]').prop('selected','selected').trigger('change');
                        $('.loading').hide();
                        autoCloseMsg(0, response.message, 5000);
                    }else {
                        $('.loading').hide();
                        autoCloseMsg(1, response.message, 5000);
                    }
                });
            }
        });
    }

</script>