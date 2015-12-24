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
                            <select class="movieLanguages" id="movieLanguages" name="dubbedVersionsLanguages" data-placeholder="Choose Language">
                                @if(isset($allLocales) && is_array($allLocales))
                                    <option selected="selected" value="0">Choose Language</option>
                                    @foreach($allLocales as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <span class="fs12 dengerTxt language_info_movie"></span>
                            <div class="btn-group pull-right">
                                <button class="btn btn-default btn-sm" id="addNewMovieLanguage">
                                    <span class="glyphicon glyphicon-plus"></span>&nbsp;Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"></div>
                </div>

                <div id="languages_movie">
                    @include('titles.titleMenegment.media.partials.dubbedVersions.partials.movie')
                </div>

            </div>

            <div class="tab-pane fade" id="tab-trailer">
                <div class="media">
                    <div class="media-body">
                        <div class="form-group">
                            <select class="trailerLanguages" id="dubbedVersionsLanguages" name="dubbedVersionsLanguages" data-placeholder="Choose Language">
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
                                <button class="btn btn-default btn-sm" id="addNewTrailerLanguage">
                                    <span class="glyphicon glyphicon-plus"></span>&nbsp;Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"></div>
                </div>
                <div class="miniwell" id="languages_trailer">
                    @include('titles.titleMenegment.media.partials.dubbedVersions.partials.trailer')
                </div>
            </div>
        </div>
        <input type="hidden" name="filmId" value="{{ isset($film->id) ? $film->id : ''  }}">
    </form>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery(".movieLanguages, .trailerLanguages").select2({
            width: '100%',
        });
    });
    $(document).ready(function(){
        var filmId = $('input[name="filmId"]').val();

        $("#addNewMovieLanguage").click(function(){
            autoCloseMsgHide();
            $('.loading').show();
            var selectedValue = $('.movieLanguages option:selected').val();
            if(selectedValue != 0){
                $.post('{{url()}}/titles/media/dubbedVersions/movieCreate', {filmId:filmId, locale:selectedValue, type:'movie'}, function(response){
                    if(response.error == 0){
                        $.post('http://pro.cinehost-back.loc/titles/media/getTemplate', {filmId:filmId, template:'movie'}, function(data){
                            if(data){
                                $.each(player_setup_movie, function( index, value ) {
                                    if(value != undefined)
                                        player_movie[index].destroy();

                                });
                                player_setup_movie = [];
                                player_movie = [];
                                $('#languages_movie').html(data);
                                $('#movieLanguages option[value="0"]').trigger("change");
                                $('.loading').hide();
                                autoCloseMsg(0, response.message, 5000);
                            }else
                                $('.loading').hide();
                        });
                    }else{
                        $('.loading').hide();
                        autoCloseMsg(1, response.message, 5000);
                    }
                });
            }else{
                $('.loading').hide();
                autoCloseMsg(1, 'Please Select Language From List', 5000);
            }
        });

        $("#addNewTrailerLanguage").click(function(){
            autoCloseMsgHide();
            $('.loading').show();
            var selectedValue = $('.trailerLanguages option:selected').val();
            if(selectedValue != 0){
                $.post('{{url()}}/titles/media/dubbedVersions/movieCreate', {filmId:filmId, locale:selectedValue, type:'trailer'}, function(response){
                    if(response.error == 0){
                        $.post('http://pro.cinehost-back.loc/titles/media/getTemplate', {filmId:filmId, template:'trailer'}, function(data){
                            if(data){

                                $.each(player_setup_trailer, function( index, value ) {
                                    if(value != undefined)
                                        player_trailer[index].destroy();

                                });
                                player_setup_trailer = [];
                                player_trailer = [];
                                $('#languages_trailer').html(data);
                                $('.trailerLanguages option[value="0"]').trigger("change");
                                $('.loading').hide();
                                autoCloseMsg(0, response.message, 5000);
                            }else
                                $('.loading').hide();
                        });
                    }else{
                        $('.loading').hide();
                        autoCloseMsg(1, response.message, 5000);
                    }
                });
            }else{
                $('.loading').hide();
                autoCloseMsg(1, 'Please Select Language From List', 5000);
            }
        });

    });
</script>