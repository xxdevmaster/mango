@if(isset($films))
	@foreach($films as $key => $val)
		@if(isset($val->delete_dt))
			<tr>
				<td style="width:20px;">
					<input type="checkbox"  disabled="disabled">
				</td>
				<td>
					<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($val->cover) ? $val->cover : '' }}" style="width:50px;">
				</td>
				<td>{{ isset($key) ? $key : '' }}</td>
				<td>
					<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">{{ isset($val->title) ? $val->title : '' }}</a>
					<br>
					<span class="dengerTxt text-danger">This title will be removed from Xchange on {{ isset($val->delete_dt) ? $val->delete_dt : '' }}</span>
				</td>
				<td>
					<span>
						<span>{{ $stores->implode('title', '&nbsp;,&nbsp;')  }}</span>
					</span>
				</td>
				<td>

				</td>
				<td>
					<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">Edit</a>
				</td>
			</tr>
	   @else
			<tr>
				<td style="width:20px;">
					@if(isset($val->V_ID))
						<input type="checkbox" name="filmsInVault[{{ isset($key) ? $key : '' }}]" class="itemCheckbox">
					@else
						<input type="checkbox" name="filmsNotInVault[{{ isset($key) ? $key : '' }}]" class="itemCheckbox">						
					@endif	
				</td>
				<td style="width:150px;">
					<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($val->cover) ? $val->cover : '' }}" width="50" height="auto" alt="">
				</td>
				<td style="width:20px;">{{ isset($key) ? $key : '' }}</td>
				<td>
					<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">{{ isset($val->title) ? $val->title : '' }}</a>
				</td>
				<td>
					<span>
						<span>{{ $stores->implode('title', '&nbsp;,&nbsp;')  }}</span>
					</span>
				</td>
				<td>
					@if(isset($val->V_ID))
						<button data-filmid="{{ isset($key) ? $key : '' }}" class="btn btn-danger btn-sm soloActDeleteFromVault cp">Remove from Xchange</button>		
					@else	
					<button data-filmid="{{ isset($key) ? $key : '' }}" class="btn btn-primary btn-sm soloActAddToVault cp">Add to Xchange</button>
					@endif
				</td>
				<td>
					<a href="/titles/metadata/{{ $key  }}" class="view-link text-primary">Edit</a>
				</td>
			</tr>
		@endif
	@endforeach
@endif
<script>
$(document).ready(function(){
	$( ".soloActAddToVault" ).click(function(){
		var filmId = $(this).data("filmid");
		autoCloseMsgHide();
		$(".loading").show();
		$.post('/CPTitles/soloActAddToVault', {filmId:filmId}, function(data){
			$('#listContent').html(data);
			$("#bulkActCheckbox").prop('checked', false);
			$(".loading").hide();			
		});
	});
	
	$( ".soloActDeleteFromVault" ).click(function(){
		var filmId = $(this).data("filmid");
		
		autoCloseMsgHide();
		$(".loading").show();		
		$.post('/CPTitles/soloActDeleteFromVault', {filmId:filmId}, function(data){
			$('#listContent').html(data);
			$("#bulkActCheckbox").prop('checked', false);
			$(".loading").hide();			
		});				
	});	
});
</script>