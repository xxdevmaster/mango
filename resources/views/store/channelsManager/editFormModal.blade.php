<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Channel</h4>
        </div>
        <div class="modal-body">
            <form role="form" id="editChannelForm">
                <div id="subChannelTitles">
                    @include('store.channelsManager.titlesTab')
                </div>


                <div class="form-group m-t-10">
                    <label for="input-movieTitles">Movie Titles</label>
                    <input type="text" id="inputEdit-movieTitles" name="inputToken" value="" />
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $("#inputEdit-movieTitles").tokenInput("/store/channelsManager/getTokenMovieTitles", {
                                theme: "facebook",
                                tokenFormatter:function(item){ return '<li><input type="hidden" name="sorted['+item.id+']" /><p>' + item.title + '</p></li>' }
                            });
                        });
                        @if(isset($subChannelTitles))
                            @foreach($subChannelTitles as $subChannelTitle)
                                <?php echo '$("#inputEdit-movieTitles").tokenInput("add", {id: "'.$subChannelTitle->id.'", title: "'.$subChannelTitle->title.'"});';?>
                            @endforeach
                        @endif
                    </script>
                </div>
                <div class="form-group text-right">
                    <button class="btn btn-primary btn-sm importAllTitles" type="button">
                        <span class="glyphicon glyphicon-import" aria-hidden="true"></span> Import All Titles
                    </button>
                    <button class="btn btn-danger btn-sm clearAllTitles" type="button">
                        <span aria-hidden="true" class="glyphicon glyphicon-trash"></span> Clear All Titles
                    </button>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Channel Model</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <select id="channel_model" class="form-control" name="model" onchange="showChannnelModel();">
                                <option value="0" {{ ($subChannel->model == 0 ? 'selected' : '') }}>General</option>
                                <option value="1" {{ ($subChannel->model == 1 ? 'selected' : '') }}>Subscription</option>
                                <option value="2" {{ ($subChannel->model == 2 ? 'selected' : '') }}>Bundle</option>
                            </select>
                        </div>

                        <div class="form-group channel_model channel_model_1 {{ ($subChannel->model == 1 ? '' : 'display-none') }}">
                            <label class="ff-label">Subscription</label>
                            <select name="subscriptions_id" id="subscriptions_id" class="form-control">
                                <option value="" selected="selected">Select Subscription</option>
                                <option value="29">fd ?  EUR 2.99</option>
                            </select>
                        </div>


                        <div class="form-group channel_model channel_model_2 {{ ($subChannel->model == 2 ? '' : 'display-none') }}">
                            <label class="ff-label">Bundle</label>
                            <select name="bundles_id" id="bundles_id" class="form-control">
                                <option value="" selected="selected">Select Bundle</option>
                            </select>
                        </div>

                    </div>
                </div>
                <input type="hidden" name="subChannelID" value="{{ $subChannel->id }}">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="editChannel">Update & Continue to edit</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){

        /* Import All Titles*/
        $('.importAllTitles').click(function(){
            $.post('/store/channelsManager/getAllTitlesForToken', function(data){
                $.each(data,function(index, value){
                    $("#inputEdit-movieTitles").tokenInput("add", {id: index, title: "'"+value+"'"});
                });
            });
        });

        /* Clear All Titles */
        $('.clearAllTitles').click(function(){
            $(".token-input-token-facebook").remove();
        });

        // SubChannel Remove Titlte Language
        $('.removeBasicLocale').click(function() {
            autoCloseMsgHide();

            var title = $(this).data('title');
            var localeID = $(this).data('localeid');

            bootbox.confirm('Do you really want to delete '+title+' language ?', function(result) {
                if(result) {
                    $('.loading').show();
                    $.post('/titles/metadata/basic/localeRemove', {localeID:localeID},function(response){
                        if(!response.error) {
                            $('#basic').html(response.basic);
                            $("#images").html(response.images);
                            $('.loading').hide();
                        }else {
                            $('.loading').hide();
                            autoCloseMsg(1, response.message, 5000);
                        }
                    });
                }
            });
        });

        /* Edit Channel*/
        $('#editChannel').click(function(){
            var editChannelForm = $('#editChannelForm').serialize();

            $.post('/store/channelsManager/editChannel', editChannelForm, function(data){
                $('#editSubChannel').modal('hide');
                $('#subChannelsContent').html(data);
            });
        });
    });
</script>