<div class="form-group text-right">
    <select name="subChannelNewLanguage" id="subChannelNewLanguage" class="form-control">
        <option value="0" selected="selected">+ Add New Section Language</option>
        @if(isset($allUniqueLanguages))
            @foreach($allUniqueLanguages as $code => $title)
                <option value="{{ $code }}" >{{ $title }}</option>
            @endforeach
        @endif
    </select>
</div><hr>
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="#en" aria-controls="home" role="tab" data-toggle="tab">English</a>
    </li>
    @if(isset($subChannelLanguages))
        @foreach($subChannelLanguages as $subChannelLanguage)
            <li role="presentation">
                <a href="#{{ $subChannelLanguage->locale }}" aria-controls="{{ $subChannelLanguage->locale }}" role="tab" data-toggle="tab">{{ $allLanguages[$subChannelLanguage->locale] }}</a>
            </li>
        @endforeach
    @endif
</ul>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="en">
        <div class="form-group">
            <label for="channelTitle">Channel Title</label>
            <input type="text" name="channelTitle" class="form-control" id="channelTitle" placeholder="Channel Title">
        </div>
    </div>
    @if(isset($subChannelLanguages))
        @foreach($subChannelLanguages as $subChannelLanguage)
            <div role="tabpanel" class="tab-pane" id="{{ $subChannelLanguage->locale }}">
                <div class="form-group">
                    <button class="btn btn-danger btn-sm pull-right removeSubChannelLanguage" data-id="{{ $subChannelLanguage->id }}" type="button">
                        <i class="fa fa-close"></i>
                    </button>
                    <label for="channelTitle">Channel Title</label>
                    <input type="text" name="channelTitle" class="form-control" id="channelTitle" placeholder="Channel Title">
                </div>
            </div>
        @endforeach
    @endif
</div>
<script>
    $(document).ready(function(){

        // SubChannel Add New Titlte Language
        $('#subChannelNewLanguage').change(function() {
            autoCloseMsgHide();
            var title = $('#subChannelNewLanguage option:selected').html();
            var selectedValue = $('#subChannelNewLanguage option:selected').val();

            if(selectedValue != 0){
                var locale = $('#subChannelNewLanguage option:selected').val();

                bootbox.confirm('Please Confirm adding '+title+' translation', function(result) {
                    if(result) {
                        $.post('/store/channelsManager/newLocale', {locale:locale, subChannelID:{{ $subChannel->id }}},function(data){
                            if(!data.error) {
                                $('#subChannelTitles').html(data);
                                $('a[href="#'+locale+'"]').tab('show');
                            }
                        });
                    }
                });
            }
        });

        /* SubChannel Remove Titlte Language */
       $('.removeSubChannelLanguage').click(function(){
           var subChannellanguageID = $(this).data('id');

           $.post('/store/channelsManager/removeLanguage', {subChannellanguageID:subChannellanguageID, subChannelID:{{ $subChannel->id }}}, function(data){
               if(!data.error) {
                   $('#subChannelTitles').html(data);
               }
           });
       });
    });
</script>