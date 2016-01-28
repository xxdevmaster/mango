<div class="modal fade" id="addNewTitle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="addNewTitleModalLabel">Add New Title</h4>
            </div>
            <form id="addNewTitleForm" role="form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="title">
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="text" class="form-control" name="year" id="year"></input>
                    </div>
                    <div class="form-group">
                        <select class="selectBoxWithSearch" name="newTitleLanguage" data-placeholder="+ Add New Metadata Language">
                            @if(isset($allLocales) && is_array($allLocales))
                                @foreach($allLocales as $key => $value)
                                    @if($key == 'en')
                                        <option selected="selected" value="{{ $key }}">{{ $value }}</option>
                                    @else
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="input-genre">Content Provider</label>
                        <input type="text" id="input-genre" name="inputToken" value="" />
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $("#input-genre").tokenInput("/titles/getCP", {
                                    theme: "facebook",
                                    tokenFormatter:function(item){ return '<li><input type="hidden" name="cp['+item.id+']" /><p>' + item.title + '</p></li>' },
                                });
                            });
                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm addClose">Add</button>
                    <button type="button" class="btn btn-primary btn-sm addEdit">Add &amp; Continue to Edit</button>
                </div>
                <input type="hidden" name="filmId" value="{{-- $film->id --}}">
            </form>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery(".selectBoxWithSearch").select2({
            width: '100%',
        });
    });
</script>