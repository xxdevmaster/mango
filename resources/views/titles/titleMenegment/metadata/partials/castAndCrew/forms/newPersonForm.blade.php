<div class="modal fade" id="addNewPersonModal" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add person</h4>
      </div>
      <div class="modal-body">
		<form id="newPersonForm" role="form">
			<div class="form-group" style="z-index:100000000">
				<label for="input-person">Person</label>	
				<input type="text" id="input-person" name="inputToken" value="" />
				<script type="text/javascript">
				$(document).ready(function() {
					$("#input-person").tokenInput("{{url()}}/titles/metadata/castAndCrew/getTokenPerson", {
						tokenLimit: 1,
						theme: "facebook",
						tokenFormatter:function(item){ return '<li><input type="hidden" name="persons" value="'+item.title+'"/><p>' + item.title + '</p></li>' },
					});
				});
				</script>
			</div>
			<div class="form-group">
				<label class="ff-label">Quick add position: </label>
				<button type="button" class="btn btn-default btn-sm" id="actorPosition">Actor</button>
				<button type="button" class="btn btn-default btn-sm" id="directorPosition">Director</button>
				<label class="ff-label">or find one below</label>
			</div>
			<div class="form-group">
				<label for="input-position">Position</label>	
				<input type="text" id="input-jobs" name="inputToken" value="" />
				<script type="text/javascript">
				$(document).ready(function() {
					$("#input-jobs").tokenInput("{{url()}}/titles/metadata/castAndCrew/getTokenJobs", {
						tokenLimit: 1,
						theme: "facebook",
						tokenFormatter:function(item){ return '<li><p><input type="hidden" name="jobs" value="'+item.id+'" />' + item.title + '</p></li>' }
					});
				});
				</script>
			</div>
			<input type="hidden" name="filmId" value="{{ $film->id }}">
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="addNewPersonModalClose">Close</button>
        <button type="button" class="btn btn-primary" id="addNewPerson">Add</button>
      </div>
    </div>
  </div>
</div>