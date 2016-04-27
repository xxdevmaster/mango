<div class="miniwell countries">
	<form name="seriesForm" id="seriesForm">
		<div class="form-group">
			<label>Title type</label>
			<select class="form-control" id="title_type" name="filmType">
				@if(isset($film))
					@if($film->series_parent == 0)
						<option value="0" selected="selected">Feature</option>
						<option value="-1">Series</option>
						<option value="-2">Episode</option>
						<?php $display = 'display:none';?>
					@elseif($film->series_parent == -1)
						<option value="0">Feature</option>
						<option value="-1" selected="selected">Series</option>
						<option value="-2">Episode</option>
						<?php $display = 'display:none';?>
					@else
						<option value="0">Feature</option>
						<option value="-1">Series</option>
						<option value="-2" selected="selected">Episode</option>
						<?php $display = 'display:block';?>
					@endif
				@endif
			</select>
		</div>	
		<div id="seriesChiled" style="{{ $display }}">
			<div class="form-group">
				<label for="form-title">Series</label>
				<input type="text" class="hidden" style="z-index: 1000;" id="input-series_parent">
				<script type="text/javascript">
					$(document).ready(function() {
						$("#input-series_parent").tokenInput("{{url()}}/titles/metadata/series/getTokenSeries", {
							theme: "facebook",
							tokenFormatter:function(item){ return '<li><input type="hidden" name="series_parent"  value="'+item.id+'"/><p>' + item.title + '</p></li>' },
							tokenLimit:1
						});
						@if(isset($metadata['series']['parentFilm']))
							<?php
								echo '$("#input-series_parent").tokenInput("add", {id: "'.$metadata['series']['parentFilm']->id.'", title: "'.$metadata['series']['parentFilm']->title.'"});';
							?>
						@endif						
					});
				</script>				
			</div>			
			<div class="form-group">
				<label for="form-title">Episode Number</label>
				<div id="spinner1">
					<div class="input-group input-small col-md-3">
						<input type="text" class="spinner-input form-control" name="series_num" maxlength="5" value="{{ isset($film->series_num) ? $film->series_num : 0 }}">
						<div class="spinner-buttons input-group-btn btn-group-vertical">
							<button type="button" class="btn spinner-up btn-xs btn-default">
								<i class="fa fa-angle-up"></i>
							</button>
							<button type="button" class="btn spinner-down btn-xs btn-default">
								<i class="fa fa-angle-down"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>		
	</form>
</div>

<script>
$(document).ready(function(){
	$("#title_type").change(function () {	  
		var selected = $("#title_type option:selected").val();
		if(selected == 0 || selected =="-1")
			$("#seriesChiled").hide();
		else if(selected == "-2")
			$("#seriesChiled").show();			
	});

	$('#spinner1').spinner();
});
</script>