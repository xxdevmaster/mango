<div class="tab-pane active" id="tab-streaming">
    <div class="panel panel-default ">
        <div class="panel-body">
            <form id="streamingFilter">
                <div class="row form-group">
                    <div class="col-lg-4">
                        <select name="cp" id="cp" class="form-control filter_select"><option value="" selected="selected">Content Providers</option><option value="378">N1</option></select>
                    </div>
                    <div class="col-lg-4">
                        <select class="form-control filter_select" name="device">
                            <option value="">Device</option>
                            <option value="web">Web</option>
                            <option value="android">Android</option>
                            <option value="ipad">iPad</option>
                            <option value="samsung">Samsung</option>
                            <option value="roku">Roku</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <select class="form-control filter_select" name="type">
                            <option value="">Feature / Trailer</option>
                            <option value="movie">Feature</option>
                            <option value="trailer">Trailer</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div style="margin-left:173px;" class="pull-left">&nbsp;</div>
                    <div class="pull-left"><label>Start Date</label></div>
                    <div class="col-lg-3">
                        <input type="text" value="" name="dt-from" id="startDate-datepicker" placeholder="dd-mm-yyyy" class="dt form-control hasDatepicker">
                    </div>
                    <div class="pull-left"><label>End Date</label></div>
                    <div class="col-lg-3">
                        <input type="text" value="{{ $media['streaming']['dateNow']  }}" name="dt-till" id="endDate-datepicker" class="dt form-control hasDatepicker">
                    </div>
                    <div class="pull-left">
                        <button onclick="getStreamings();" type="button" class="btn btn-default btn-xs pull-right dt-filter" style="padding: 5px 12px 6px;">Filter</button>
                    </div>
                </div>
                <input type="hidden" name="act" value="getMediaStreamings">
                <input type="hidden" name="film_id" value="2505">
            </form>
        </div>
    </div>
    <div class="StreamingList" style="">

        <span class="pull-right text-primary text-uppercase">Total - 0 B</span>
        <div class="cl"></div>
        <div class="agreeSc">
            <table id="list-view-container" class="table">
                <thead>
                <tr>
                    <td class="imp_talign_left">Date</td>
                    <td>Content Provider</td>
                    <td>Type</td>
                    <td>Device</td>
                    <td style="text-align:right;">Stream</td>
                </tr>
                </thead>
                <tbody id="list-view" class="">

                </tbody>
            </table>
        </div>

    </div>
</div>
<script>
    function getStreamings() {
        var streamingFilter = $("#streamingFilter").serialize();
        $.post('/titles/media/streaming/getStreaming', {streamingFilter}, function(data){

        });
    }
</script>