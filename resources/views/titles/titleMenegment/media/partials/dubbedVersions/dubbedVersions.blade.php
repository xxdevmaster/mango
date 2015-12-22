<div class="panel panel-default ">
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
                                <select class="selectBoxWithSearch" id="dubbedVersionsLanguages" name="dubbedVersionsLanguages" data-placeholder="Choose Language">
                                    @if(isset($allLocales) && is_array($allLocales))
                                        <option selected="selected" value="0">+ Add New Metadata Language</option>
                                        @foreach($allLocales as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <span class="fs12 dengerTxt language_info_movie"></span>
                                <div class="btn-group pull-right">
                                    <span class="btn-xs" id="addNewMovieLanguage">
                                        <span class="glyphicon glyphicon-plus"></span>&nbsp;Add
                                    </span>
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
                                <select class="selectBoxWithSearch" id="dubbedVersionsLanguages" name="dubbedVersionsLanguages" data-placeholder="Choose Language">
                                    @if(isset($allLocales) && is_array($allLocales))
                                        <option selected="selected" value="0">+ Add New Metadata Language</option>
                                        @foreach($allLocales as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <span class="fs12 dengerTxt language_info_trailer"></span>
                                <div class="btn-group pull-right">
                                    <span class="btn-xs cp addNewTrailerLanguageBtn"><span class="glyphicon glyphicon-plus"></span>&nbsp;Add</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group"></div>
                    </div>
                    <div class="miniwell" id="mediaLanguages_trailer">
                        @include('titles.titleMenegment.media.partials.dubbedVersions.partials.trailer')
                    </div>
                </div>
            </div>
            <input type="hidden" name="filmId" value="{{ isset($film->id) ? $film->id : ''  }}">
        </form>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery(".selectBoxWithSearch").select2({
            width: '100%',
        });
    });
    $(document).ready(function(){
        var filmId = $('input[name="filmId"]').val();

        $("#addNewMovieLanguage").click(function(){
            autoCloseMsgHide();
            $('.loading').show();
            var selectedValue = $('.selectBoxWithSearch option:selected').val();
            if(selectedValue != 0){
                $.post('{{url()}}/titles/media/dubbedVersions/movieCreate', {filmId:filmId, locale:selectedValue}, function(response){
                    if(response.error == 0){
                        $.post('http://pro.cinehost-back.loc/titles/media/getTemplate', {filmId:filmId, template:'movie'}, function(data){
                            if(data){
                                $('#languages_movie').html(data);
                                $('.loading').hide();
                                autoCloseMsg('0', response.message, 5000);
                            }else
                                $('.loading').hide();
                        });
                    }else{
                        $('.loading').hide();
                        autoCloseMsg('1',response.message, 5000);
                    }
                });
            }else{
                $('.loading').hide();
                autoCloseMsg('1', 'Please Select Language From List', 5000);
            }
        });
    });
</script>