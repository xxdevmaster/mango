<?php
$activeSubChannels = '';
$inActiveSubChannels = '';
$subData = '';
?>
@if(isset($parentSubChannels))
    @foreach($parentSubChannels as $subChannelID => $subChannel)
        <?php
            if(array_key_exists($subChannel->id, $childrenSubChannels)) {
                $subData = '<ul class="childrenSubChannels">';
                foreach($childrenSubChannels[$subChannel->id] as $childrenSubChan) {
                    $subData .= '
                        <li class="list-group-item bg-primary">
                            <span class="glyphicon glyphicon-th-list"></span>
                            <span class="name w-80" style="display:inline-block">'.$childrenSubChan->title.'</span>
                            <div class="pull-right subChannelToolsButtonGroup">
                                <button class="btn btn-default btn-sm editSubChannelFormShowModal" data-id="'.$childrenSubChan->id.'" type="button" data-toggle="modal" data-target="#editSubChannel">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>
                                <button class="btn btn-danger btn-sm removeSubChannel" data-id="'.$childrenSubChan->id.'" type="button">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </li>
                    ';
                }
                $subData .= '</ul>';
            }else
                $subData = '';
        if($subChannel->active == 1)
            $activeSubChannels .= '
                        <li class="list-group-item bg-primary" data-id="'.$subChannel->id.'">
                            <span class="glyphicon glyphicon-th-list"></span>
                            <span class="name w-80" style="display:inline-block">'.$subChannel->title.'</span>
                            <div class="pull-right subChannelToolsButtonGroup">
                                <button class="btn btn-default btn-sm editSubChannelFormShowModal" data-id="'.$subChannel->id.'" type="button" data-toggle="modal" data-target="#editSubChannel">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>
                                <button class="btn btn-danger btn-sm removeSubChannel" data-id="'.$subChannel->id.'" type="button">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                            '.$subData.'
                        </li>
                        ';
        else
            $inActiveSubChannels .= '
                        <li class="list-group-item bg-primary" data-id="'.$subChannel->id.'">
                            <span class="glyphicon glyphicon-th-list"></span>
                            <span class="name w-80" style="display:inline-block">'.$subChannel->title.'</span>
                            <div class="pull-right">
                                <button class="btn btn-default btn-sm editSubChannelFormShowModal" data-id="'.$subChannel->id.'" type="button" data-toggle="modal" data-target="#editSubChannel">
                                    <i class="fa fa-pencil-square-o"></i>
                                </button>
                                <button class="btn btn-danger btn-sm removeSubChannel" data-id="'.$subChannel->id.'" type="button">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                            '.$subData.'
                        </li>
                     ';
        ?>
    @endforeach
@endif
<div id="draggable">
    <div class="panel panel-color ">
        <div class="panel-heading">Active Channels</div>
        <div class="panel-body">
            <form id="active-subchannels_web">
                <ul class="list-group" id="list1">
                    <li class="nonvisibleli"></li>
                    {!! $activeSubChannels !!}
                </ul>
            </form>
        </div>
    </div>

    <div class="panel panel-color ">
        <div class="panel-heading">Inactive Channels</div>
        <div class="panel-body">
            <form id="active-subchannels_web">
                <ul class="list-group" id="list2">
                    <li class="nonvisibleli"></li>
                    {!! $inActiveSubChannels !!}
                </ul>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){

        $('#list1').sortable({
            connectWith: '#list2',
            scroll: false,
            revert: true,
            items: "li:not(.nonvisibleli)",
            receive: function (event, ui) {
                var subChannelID  = ui.item.data('id');
                var active = 1;
                $.post('/store/channelsManager/activation', {subChannelID:subChannelID, active:active});
            },
            update: function (event, ui) {

            }
        });

        $('#list2').sortable({
            connectWith: '#list1',
            scroll: false,
            revert: true,
            items: "li:not(.nonvisibleli)",
            receive: function (event, ui) {
                var subChannelID  = ui.item.data('id');
                var active = 0;
                $.post('/store/channelsManager/activation', {subChannelID:subChannelID, active:active});
            },
            update: function (event, ui) {

            }
        });

        $('.editSubChannelFormShowModal').click(function(){
            var subChannelID = $(this).data('id');

            $.post('/store/channelsManager/editSubChannelFormShowModal', {subChannelID:subChannelID}, function(data){
                if(!data.error) {
                    $('#editSubChannel').html(data);
                    $('#editSubChannel').modal('show');
                }
            });
        })


        /* Remove SubChannel*/
        $('.removeSubChannel').click(function(){
            var subChannelID = $(this).data('id');

            bootbox.confirm('You are about to delete subchannel, Are you sure?', function(result) {
                if(result) {
                    $.post('/store/channelsManager/removeSubChannel', {subChannelID:subChannelID}, function(data){
                        $('#subChannelsContent').html(data);
                    });
                }
            });
        });

    });
</script>