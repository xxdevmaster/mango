@if(isset($metadata['seo']['keywords']))
    @foreach($metadata['seo']['keywords'] as $locale => $value)
        <div class="panel personsPanel">
            <div class="panel-body p-t-10">
                <div class="media-main">
                    <div class="pull-right btn-group-sm" style="margin-top:-9px;margin-bottom:3px">
                        <button class="btn btn-default editKeywordsModalOpen" data-keywordid="{{  $value->id }}" data-toggle="modal" data-target="#editSeoItem">
                            <i class="fa fa-pencil-square-o"></i>
                        </button>
                        <button class="btn btn-danger removSeoItem" data-keywordid="{{ $value->id }}">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>
                    <div class="info col-md-3">
                        <h4 style="margin:0;padding:0;">
                            @if(array_key_exists($locale, $allLocales))
                                {{ $allLocales[$locale] }}
                            @endif
                        </h4>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    @endforeach
@endif

<script>
    $(document).ready(function(){
        /* Remove Seo Item*/
        $(".removSeoItem").click(function(){

            var keywordID = $(this).data('keywordid');
            bootbox.confirm('Do you really want delete Keyword', function(result) {
                if(result) {
                    $('.loading').show();
                    $.post('/titles/metadata/seo/removeSeoItem', {keywordID:keywordID}, function(data){
                        $('#seoContent').html(data);
                        $('.loading').hide();
                    });
                }
            });
        });

        /* Show Edit Modal Form*/
        $('.editKeywordsModalOpen').click(function(e){
            e.stopPropagation();
            var keywordID = $(this).data('keywordid')
            $.post('/titles/metadata/seo/showEditSeoItemForm', {keywordID:keywordID}, function(data){
                $('#editSeoItem').html(data);
                $('#editSeoItem').modal('show');
            });
        });
    });
</script>