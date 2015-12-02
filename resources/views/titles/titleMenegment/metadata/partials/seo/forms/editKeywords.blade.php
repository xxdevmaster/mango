<link rel="stylesheet" type="text/css" href="/assets/select2/select2.css" />

<div class="modal fade" id="editSeoItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Edit Keyword & Description</h4>
		</div>
		<form id="editKeywords" name="editKeywords" role="form">
			<div class="modal-body">		
				<div class="form-group">
					<select class="select2" name="countries" data-placeholder="Choose a Country...">
						@if(isset($allLocales) && is_array($allLocales))
							@foreach($allLocales as $key => $value)
								<option value="{{ $key }}">{{ $value }}</option>
							@endforeach
						@endif
					</select>
				</div>
				<div class="form-group">
					<label for="seoKeywords">Keyword</label>
					<textarea class="form-control" name="keywords" id="seoKeywords2"></textarea>				
				</div>			
				<div class="form-group">
					<label for="seoDescription">Description</label>
					<textarea class="form-control" name="description" id="seoDescription"></textarea>				
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="editSeoItemButton" data-dismiss="modal">Add</button>
				<button type="button" class="btn btn-primary">Close</button>
			</div>
			<input type="hidden" name="keywordsId" value="">
		</form>
    </div>
  </div>
</div>
		
		<script type="text/javascript" src="/assets/jquery-multi-select/jquery.multi-select.js"></script>
		<script type="text/javascript" src="/assets/spinner/spinner.min.js"></script>
		<script src="/assets/select2/select2.min.js" type="text/javascript"></script>

<script>
	$(document).ready(function(){
		$(document).on('click', '#editSeoItemButton', function(){
			
			var editKeywords = $('#editKeywords').serialize();
			
			$.post('{{url()}}/titles/metadata/castAndCrew/editSeoItem', editKeywords, function(data){
				
			});
		})
	});
</script>		
		
        <script>
            jQuery(document).ready(function() {
                    


                //multiselect start

                $('#my_multi_select1').multiSelect();
                $('#my_multi_select2').multiSelect({
                    selectableOptgroup: true
                });

                $('#my_multi_select3').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                    afterInit: function (ms) {
                        var that = this,
                            $selectableSearch = that.$selectableUl.prev(),
                            $selectionSearch = that.$selectionUl.prev(),
                            selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                            selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                            .on('keydown', function (e) {
                                if (e.which === 40) {
                                    that.$selectableUl.focus();
                                    return false;
                                }
                            });

                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                            .on('keydown', function (e) {
                                if (e.which == 40) {
                                    that.$selectionUl.focus();
                                    return false;
                                }
                            });
                    },
                    afterSelect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    },
                    afterDeselect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    }
                });

                //spinner start
                $('#spinner1').spinner();
                $('#spinner2').spinner({disabled: true});
                $('#spinner3').spinner({value:0, min: 0, max: 10});
                $('#spinner4').spinner({value:0, step: 5, min: 0, max: 200});
                //spinner end

                // Select2
                jQuery(".select2").select2({
                    width: '100%'
                });
            });
        </script>