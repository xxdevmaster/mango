@extends('layout')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Channels Manager</h3>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <button class="btn btn-primary brn-md" id="showNewSubchannelModalForm">+ Add New Channel</button>
        </div>
    </div>

    <div id="subChannelsContent">
        @include('store.channelsManager.subChannels')
    </div>

    <div class="modal fade" id="newChannelModal" tabindex="-1" role="dialog"></div>
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

            /* Show New SubChannel Modal Form*/
            $('#showNewSubchannelModalForm').click(function(){
                $.post('/store/channelsManager/showNewSubchannelModalForm',function(data){
                    $('#newChannelModal').html(data);
                    $('#newChannelModal').modal('show');
                });
            })
        });

        function  showChannnelModel(){
            var model = $( "#channel_model" ).val();
            $('.channel_model').hide();
            $('.channel_model_'+model).show();
        }
    </script>
@stop