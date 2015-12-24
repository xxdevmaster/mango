
    <input type="hidden" name="uploaderFilmId" id="uploaderFilmId" value="2505">
    <input type="hidden" name="uploaderAccountid" id="uploaderAccountid" value="227">
    <input type="hidden" name="uploaderUserid" id="uploaderUserid" value="400">
    <script src="/library/uploader/moment.js"></script>
    <script src="/library/uploader/exec.js"></script>

    <div class="miniwell">
        <div class="uploaderBlock">
            <div class="row">
                <div class="col-md-4">
                    <h4>Type Of Media</h4>
                    <div class="radio">
                        <label>
                            <input type="radio" name="bucket_type" value="trailer" checked="checked">
                            Trailer
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="bucket_type" value="movie"> Movie
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="bucket_type" value="bonus"> Bonus
                        </label>
                    </div>
                    <h4>Video Qaulity</h4>
                    <div class="radio">
                        <label>
                            <input type="radio" name="video_quality" value="hd" checked="checked">
                            HD
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="video_quality" value="sd"> SD
                        </label>
                    </div>




                </div>


                <div class="col-md-7">
                    <h4>Select Title</h4>
                    <div class="form-group">
                        <label class="radio-inline">
                            <input type="radio" name="filter" value="all" checked="checked"> All
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="filter" value="missing"> Missing
                        </label>
                    </div>
                    <div class="form-group">
                        <input id="serachbox" type="text" class="form-control" placeholder="Search">
                        <select id="titles" name="titles" size="5" class="form-control">
                            <option value="2505" data-locale="aa" data-movieid="" data-track="0">VaterMark - Afar</option><option value="2505" data-locale="aa" data-movieid="" data-track="0">VaterMark - Afar</option><option value="2505" data-locale="ab" data-movieid="" data-track="0">VaterMark - Abkhazian</option><option value="2505" data-locale="am" data-movieid="" data-track="0">VaterMark - Amharic</option>
                        </select>
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
            <span class="glyphicon glyphicon-refresh cp pull-right history_refresh"></span>
            <div style="clear:both;"></div>
            <div id="uploaderHistory"><table class="table"><tbody><tr>
                        <th>User</th>
                        <th>Movie/Trailer</th>
                        <th>Job Id</th>
                        <th>Status</th>
                        <th>Pass Id</th>
                        <th>Submited at</th>
                    </tr><tr class="success"><td>Gevorg Aznauryan</td><td>trailer</td><td>14271</td><td>Success</td><td>2437</td><td>2015-10-01 11:15:05</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>trailer</td><td>14285</td><td>Success</td><td>2439</td><td>2015-10-01 12:59:16</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>trailer</td><td>17678</td><td>Success</td><td>2976</td><td>2015-10-12 09:44:03</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>trailer</td><td>28510</td><td>Success</td><td>3782</td><td>2015-11-06 14:29:20</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>trailer</td><td>28511</td><td>Success</td><td>3783</td><td>2015-11-06 14:30:21</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>trailer</td><td>28512</td><td>Success</td><td>3784</td><td>2015-11-06 14:31:26</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>movie</td><td>28513</td><td>Success</td><td>3785</td><td>2015-11-06 14:32:52</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>movie</td><td>28514</td><td>Success</td><td>3786</td><td>2015-11-06 14:34:12</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>movie</td><td>28516</td><td>Success</td><td>3787</td><td>2015-11-06 14:37:47</td></tr><tr class="success"><td>Gevorg Aznauryan</td><td>trailer</td><td>28520</td><td>Success</td><td>3788</td><td>2015-11-06 14:52:33</td></tr></tbody></table></div>

        </div>
    </div>


