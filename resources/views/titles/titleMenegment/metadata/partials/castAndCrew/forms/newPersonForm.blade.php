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
<script>
$(document).ready(function(){
	
	$('#addNewPersonModalClose').click(function(){
		autoCloseMsgHide();
		$('#addNewPersonModal').modal('hide');
		$('html').css({'overflow-y':'auto'});
	});
		
	$('#addNewPersonModal').on('hidden.bs.modal', function (e) {
		$('html').css({'overflow-y':'auto'});
		$("#newPersonFormDiv").html('');
	});	

	// add job actors
	$('#actorPosition').click(function(){
		$("#input-jobs").tokenInput("clear");
		$("#input-jobs").tokenInput("add", {id: "28", title: "Actors / Actor"});
	});	
	
	// add job director
	$('#directorPosition').click(function(){
		$("#input-jobs").tokenInput("clear");
		$("#input-jobs").tokenInput("add", {id: "22", title: "Directing / Director"});
	});

	//Add New Person
	$('#addNewPerson').click(function(){
		
		var personName = $('input[name="persons"]').val();
		var jobId = $('input[name="jobs"]').val();
		
		if(personName === undefined) {
			autoCloseMsg(1, 'Field Person is empty', 5000);
			return false;
		}		
		if(jobId === undefined) {
			autoCloseMsg(1, 'Field Position is empty', 5000);
			return false;
		}		
		autoCloseMsgHide();
		
		$('#addNewPersonModal').modal('hide');
		$('html').css({'overflow-y':'auto'});
		
		var newPersonForm = $("#newPersonForm").serialize();
		var filmId = $("input[name='filmId']").val();
		$('.loading').show();
		
		$.post('{{url()}}/titles/metadata/castAndCrew/personCreate', newPersonForm, function(response){
			if(response.error == 0) {
				$.post('{{url()}}/titles/metadata/basic/getTemplate', {filmId:filmId, template:'castAndCrew'}, function(data){							
					if(data) {
						$('#castAndCrew').html(data);
						$('.loading').hide();
						autoCloseMsg(0, response.message, 5000);
					}							
				});						
			}else {
				$('.loading').hide();
				autoCloseMsg(response.error, response.message, 5000);
			}
		});
		
	});	
	// End
});
</script>
</div>