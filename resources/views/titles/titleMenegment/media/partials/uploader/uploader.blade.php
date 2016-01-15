
    <input type="hidden" name="uploaderFilmId" id="FilmId" value="{{ isset($film->id) ? $film->id : ''}}">
    <script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.13.min.js"></script>
    <script src="/library/uploader/moment.js"></script>
    <script src="/library/uploader/exec.js"></script>

    <div class="miniwell">
        <div class="uploaderBlock">
            <div class="row">
                <div class="col-md-4">
                    <h4>Type Of Media</h4>
                    <div class="radio">
                        <label class="cr-styled" for="radio-trailer">
                            <input type="radio" name="bucket_type" value="trailer" checked="checked" id="radio-trailer" >
                            <i class="fa"></i>
                            Trailer
                        </label>
                    </div>
                    <div class="radio">
                        <label class="cr-styled" for="radio-movie">
                            <input type="radio" name="bucket_type" value="movie" id="radio-movie">
                            <i class="fa"></i>
                            Movie
                        </label>
                    </div>
                    <div class="radio">
                        <label class="cr-styled" for="radio-bonus">
                            <input type="radio" name="bucket_type" value="bonus" id="radio-bonus">
                            <i class="fa"></i>
                            Bonus
                        </label>
                    </div>
                    <h4>Video Qaulity</h4>
                    <div class="radio">
                        <label class="cr-styled" for="radio-hd">
                            <input type="radio" name="video_quality" value="hd" checked="checked" id="radio-hd">
                            <i class="fa"></i>
                            HD
                        </label>
                    </div>
                    <div class="radio">
                        <label class="cr-styled" for="radio-sd">
                            <input type="radio" name="video_quality" value="sd" id="radio-sd">
                            <i class="fa"></i>
                            SD
                        </label>
                    </div>
                </div>


                <div class="col-md-7">
                    <h4>Select Title</h4>
                    <div class="form-group">
                        <div class="radio-inline">
                            <label class="cr-styled" for="radio-all">
                                <input type="radio" name="filter" value="all" checked="checked" id="radio-all" >
                                <i class="fa"></i>
                                All
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label class="cr-styled" for="radio-missing">
                                <input type="radio" name="filter" value="missing" id="radio-missing">
                                <i class="fa"></i>
                                Missing
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="titlesBlock">
                        @include('titles.titleMenegment.media.partials.uploader.partials.mediaUploader')
                    </div>
                </div>
            </div>
            <div class="miniwell">
                <div class="form-group">
                    <h4>Select File</h4>
                    <input type="file" id="file">
                    <span id="errMsg"></span>
                </div>
                <div class="form-group">
                    <button id="upload-button" class="btn btn-primary">Upload</button>
                    <div class="progress" style="float: right; width: 580px; margin-top: 5px;border:0px;background:none;box-shadow:none;">
                        <div style="width: 0%;background:#8090bd;border:0px" aria-valuemax="100" aria-valuemin="0" aria-valuenow="1" role="progressbar" class="progress-bar progress-bar-success " id="uploading_progress"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="line"></div>
        <div class="miniwell">
            <h4 class="pull-left">Uploader History</h4>
            <button class="btn btn-default btn-sm pull-right history_refresh">
                <i class="fa fa-refresh"></i>
            </button>

            <div id="uploaderHistory">
                {!! $media['uploadHistory']['html'] !!}
            </div>
        </div>
    </div>


