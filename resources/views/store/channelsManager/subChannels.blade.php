<?php
$activeSubChannels = '';
$inActiveSubChannels = '';
?>
@if(isset($subChannels))
    @foreach($subChannels as $subChannelID => $subChannel)
        <?php
        if($subChannel->active == 1)
            $activeSubChannels .= '
                        <li class="list-group-item bg-primary">
                            <span class="glyphicon glyphicon-th-list"></span>
                            <span class="name w-80" style="display:inline-block">'.$subChannel->title.'</span>
                            <span class="w-160" style="display:inline-block"></span>
                            <span class="glyphicon glyphicon-user pos-icon">
                                '.$subChannel->source.'
                            </span>
                            <a href="/store/channelsManager/'.$subChannel->id.'" class="btn btn-default btn-sm">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            <button class="btn btn-danger btn-sm removeSubChannel" data-id="'.$subChannel->id.'" type="button">
                                <i class="fa fa-close"></i>
                            </button>
                        </li>
                     ';
        else
            $inActiveSubChannels .= '
                        <li class="list-group-item bg-primary">
                            <span class="glyphicon glyphicon-th-list"></span>
                            <span class="name w-80" style="display:inline-block">'.$subChannel->title.'</span>
                            <span class="w-160" style="display:inline-block"></span>
                            <span class="glyphicon glyphicon-user pos-icon">
                                 '.$subChannel->source.'
                            </span>
                            <a href="/store/channelsManager/'.$subChannel->id.'" class="btn btn-default btn-sm">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            <button  class="btn btn-danger btn-sm removeSubChannel" data-id="'.$subChannel->id.'" type="button">
                                <i class="fa fa-close"></i>
                            </button>
                        </li>
                     ';
        ?>
    @endforeach
@endif

<div class="panel panel-color ">
    <div class="panel-heading">Active Channels</div>
    <div class="panel-body">
        <form id="active-subchannels_web">
            <ul class="list-group channels_sort ui-sortable" rel="web">
                {!! $activeSubChannels !!}
            </ul>
        </form>
    </div>
</div>

<div class="panel panel-color ">
    <div class="panel-heading">Inactive Channels</div>
    <div class="panel-body">
        <form id="active-subchannels_web">
            <ul class="list-group channels_sort ui-sortable" rel="web">
                {!! $inActiveSubChannels !!}
            </ul>
        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
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