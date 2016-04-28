@extends('layout')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Channels Manager</h3>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <button class="btn btn-primary brn-md" data-toggle="modal" data-target="#newChannelModal">+ Add New Channel</button>
        </div>
    </div>

    <div id="subChannelsContent">
        @include('store.channelsManager.subChannels')
    </div>

    <div class="modal fade" id="newChannelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Sub Channel</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="newChannelForm">
                        <div class="form-group">
                            <input type="text" name="channelTitle" class="form-control" id="channelTitle" placeholder="Channel Title">
                        </div>

                        <div class="form-group">
                            <select name="parentChannel" class="form-control">
                                <option value="0" selected="selected">Select Parent Channel</option>
                                @if(isset($allSubChannels))
                                    @foreach($allSubChannels as $allSubChannel)
                                        <option value="{{ $allSubChannel->id }}">{{ $allSubChannel->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="input-movieTitles">Movie Titles</label>
                            <input type="text" id="input-movieTitles" name="inputToken" value="" />
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#input-movieTitles").tokenInput("/store/channelsManager/getTokenMovieTitles", {
                                        theme: "facebook",
                                        tokenFormatter:function(item){ return '<li><input type="hidden" name="sorted['+item.id+']" /><p>' + item.title + '</p></li>' }
                                    });
                                });
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
                                        <option value="0">General</option>
                                        <option value="1">Subscription</option>
                                        <option value="2">Bundle</option>
                                    </select>
                                </div>

                                <div class="form-group channel_model channel_model_1 display-none">
                                    <label class="ff-label">Subscription</label>
                                    <select name="subscriptions_id" id="subscriptions_id" class="form-control">
                                        <option value="" selected="selected">Select Subscription</option>
                                        <option value="29">fd ?  EUR 2.99</option>
                                    </select>
                                </div>


                                <div class="form-group channel_model channel_model_2 display-none">
                                    <label class="ff-label">Bundle</label>
                                    <select name="bundles_id" id="bundles_id" class="form-control">
                                        <option value="" selected="selected">Select Bundle</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addNewChannel">Add & Continue to Edit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSubChannel" tabindex="-1" role="dialog"></div>
    <script>
        $(document).ready(function(){

            /* Import All Titles*/
            $('.importAllTitles').click(function(){
                $.post('/store/channelsManager/getAllTitlesForToken', function(data){
                    $.each(data,function(index, value){
                        $("#input-movieTitles").tokenInput("add", {id: index, title: "'"+value+"'"});
                    });
                });
            });

            /* Clear All Titles */
            $('.clearAllTitles').click(function(){
                $(".token-input-token-facebook").remove();
            });

            /* Add New Channel*/
            $('#addNewChannel').click(function(){

                var newChannelForm = $('#newChannelForm').serialize();

                $.post('/store/channelsManager/addChannel', newChannelForm, function(data){
                    $('#newChannelModal').modal('hide');
                    $('#subChannelsContent').html(data);
                    $('#newChannelForm')[0].reset();
                });

            });



            //initSubChannelHandlers();

        });
        function  showChannnelModel(){
            var model = $( "#channel_model" ).val();
            $('.channel_model').hide();
            $('.channel_model_'+model).show();
        }


        /*$(function() {
            $('.channels_sort').sortable({
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
        });*/

        /*
        $(document.body).on('hidden.bs.modal', function () {
            $('#subChannelsEditor').removeData('bs.modal')
        });

        function initSubChannelHandlers()
        {
            $('.active-sort').sortable({
                connectWith: '.inactive-sort',
                placeholder: "state-highlight",
                items: "li:not(.nonvisibleli)",
                update: function(event, ui) {
                    updateSubChannelOrder($(this).attr("rel"));
                }
            });

            $('.inactive-sort').sortable({
                connectWith: '.active-sort',
                placeholder: "state-highlight",
                items: "li:not(.nonvisibleli)"
            });

            $('.delete-subchannel').click(function(){
                var id = $(this).attr("rel");
                if(window.confirm("You are about to delete subchannel, Are you sure?"))
                {
                    var el = $(this);
                    $.ajax({
                        type: 'POST',
                        url: 'engine.php',
                        data: 'act=remove-subchannel&id='+id,
                        dataType: 'json',
                        success: function (data) {
                            if(data.success)
                                el.parent().remove();
                        }
                    });
                }
            });
        }
        function updateSubChannelOrder(device)
        {
            $.ajax({
                type: 'POST',
                url: 'engine.php',
                data: $('#active-subchannels_'+device).serialize()+'&act=save-subchannels-order&device='+device,
                dataType: 'json'
            });
        }

        function removeAllTitles(id)
        {
            $(".token-input-token-facebook").remove();
            $("#"+id).val('');
        }
        function post_modal_add_subchannel()
        {
            $("#titles-tokens").tokenInput("tokens.php?pid=titles", {theme: "facebook",onReady:function(){ $('.token-input-list-facebook').sortable(); },tokenFormatter:function(item){ return '<li><input type="hidden" name="sorted['+item.id+']" /><p>' + item.name + '</p></li>' }});
            $('.add-subchannel-genre').click(function(){
                $.ajax({
                    type: 'POST',
                    url: 'engine.php',
                    data: $('#form-add-subchannel-genre').serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if(data.success)
                        {
                            $('#form-add-subchannel-genre')[0].reset();
                            $('#subChannelsEditor').modal('hide');
                            $('#active-subchannels_'+data.device).load('engine.php?act=get-active-subchannels&device='+data.device,function(){initSubChannelHandlers();});
                        }
                    }
                });
            });

            $('.add-subchannel-custom').click(function(){
                $.ajax({
                    type: 'POST',
                    url: 'engine.php',
                    data: $('#form-add-subchannel-custom').serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if(data.success)
                        {
                            $('#form-add-subchannel-custom')[0].reset();
                            $('#subChannelsEditor').modal('hide');
                            $('#active-subchannels_'+data.device).load('engine.php?act=get-active-subchannels&device='+data.device,function(){initSubChannelHandlers();});
                        }
                    }
                });
            });
        }


        function deleteSubChannelLocale(id,locale){
            $.ajax({
                type: "POST",
                url: "engine.php",
                data: "act=deleteSubChannelLocale&id="+id+"&locale="+locale,
                dataType: "json",
                success: function (data) {
                    $.ajax({
                        type: "POST",
                        url: "engine.php",
                        data: "act=subChannels-editor-form-edit&id="+id+"",
                        dataType: "html",
                        success: function (data) {
                            $("#subChannelsEditor .modal-dialog .modal-content").html(data);
                        }
                    });
                }
            });
        }
        function post_modal_edit_subchannel(pre)
        {
            $("#edit-titles-tokens").tokenInput("tokens.php?pid=titles", {theme: "facebook",prePopulate:pre,onReady:function(){ $('.token-input-list-facebook').sortable(); },tokenFormatter:function(item){ return '<li><input type="hidden" name="sorted['+item.id+']" /><p>' + item.name + '</p></li>' }});
            $('.update-subchannel-custom').click(function(){
                $.ajax({
                    type: 'POST',
                    url: 'engine.php',
                    data: $('#form-update-subchannel-custom').serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if(data.success)
                        {
                            $('#form-update-subchannel-custom')[0].reset();
                            $('#subChannelsEditor').modal('hide');
                            $('#active-subchannels_'+data.device).load('engine.php?act=get-active-subchannels&device='+data.device,function(){initSubChannelHandlers();});
                            $('#inactive-subchannels_'+data.device).load('engine.php?act=get-inactive-subchannels&device='+data.device,function(){initSubChannelHandlers();});
                        }
                    }
                });
            });
        }*/

    </script>
@stop