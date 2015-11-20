<div class="modal fade" id="addNewPersonModal" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add person</h4>
      </div>
      <div class="modal-body">
		<div class="form-group" style="z-index:100000000">
			<label for="input-person">Person</label>	
			<input type="text" id="input-person" name="inputToken" value="" />
			<script type="text/javascript">
			$(document).ready(function() {
				$("#input-person").tokenInput("{{url()}}/titles/metadata/castAndCrew/getTokenPerson", {
					tokenLimit: 1,
					preventDuplicates: true,
					theme: "facebook",
					noResultsText : '',
					tokenValue: true,
					tokenFormatter:function(item){ return '<li><input type="hidden" name="persons['+item.id+']" /><p>' + item.title + '</p></li>' },
				});
			});
			</script>
		</div>
		<div class="form-group">
			<label class="ff-label">Quick add position: </label>
			<button type="button" class="btn btn-default btn-sm actor">Actor</button>
			<button type="button" class="btn btn-default btn-sm director">Director</button>
			<label class="ff-label">or find one below</label>
		</div>
		<div class="form-group">
			<label for="input-position">Position</label>	
			<input type="text" id="input-position" name="inputToken" value="" />
			<script type="text/javascript">
			$(document).ready(function() {
				$("#input-position").tokenInput("{{url()}}/titles/metadata/castAndCrew/getTokenJobs", {
					tokenLimit: 1,
					theme: "facebook",
					tokenFormatter:function(item){ return '<li><input type="hidden" name="jobs['+item.id+']" /><p>' + item.title + '</p></li>' }
				});
			});
			</script>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
	
	$(document).on('click', '.actor', function(){
		$(".token-input-token-facebook").tokenInput("clear");
		$("#input-position").tokenInput("add", {id: "", title: "Actors / Actor"});
	});	
	
	$(document).on('click', '.director', function(){
		$(".token-input-token-facebook").tokenInput("clear");
		$("#input-position").tokenInput("add", {id: "", title: "Directing / Director"});
	});
	
});
</script>