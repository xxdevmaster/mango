@if(!empty($items->items()))
	@foreach($items->items() as $key => $val)
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
						@foreach($filmStores as $k => $v)
							{{$k}}
							@if($k == $key)
								{{ $v }}
							@endif
						@endforeach
					</span>
				</td>
				<td class="text-right">
					@if(isset($val->V_ID))
						<button data-filmid="{{ isset($key) ? $key : '' }}" class="btn btn-danger btn-sm soloActDeleteFromStore cp">Remove from My Store</button>
					@else
						<button data-filmid="{{ isset($key) ? $key : '' }}" class="btn btn-primary btn-sm soloActAddToStore cp">Add to My Store</button>
					@endif
				</td>
			</tr>
	   @else
			<tr>
				<td style="width:20px;">
					@if(isset($val->V_ID))
						<input type="checkbox" name="filmsInVault[{{ isset($key) ? $key : '' }}]" class="itemCheckbox">
					@else
						<input type="checkbox" name="filmsNotInMyStore[{{ isset($key) ? $key : '' }}]" class="itemCheckbox">						
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
						@foreach($filmStores as $k => $v)
							@if($k == $key)
								{{ $v }}
							@endif
						@endforeach
					</span>
				</td>
				<td class="text-right">
					@if(isset($val->V_ID))
						<button data-filmid="{{ isset($key) ? $key : '' }}" class="btn btn-danger btn-sm soloActDeleteFromStore cp">Remove from My Store</button>
					@else	
						<button data-filmid="{{ isset($key) ? $key : '' }}" class="btn btn-primary btn-sm soloActAddToStore cp">Add to My Store</button>
					@endif
				</td>
			</tr>
		@endif
	@endforeach
@endif
<script>
$(document).ready(function(){
	$( ".soloActAddToStore" ).click(function(){
		var filmId = $(this).data("filmid");
		autoCloseMsgHide();
		$(".loading").show();
		$.post('/xchange/soloActAddToStore', {filmId:filmId}, function(data){
			//$('#listContent').html(data);
			$("#bulkActCheckbox").prop('checked', false);
			$(".loading").hide();
		});
	});


	$( ".soloActDeleteFromStore" ).click(function(){
		var filmId = $(this).data("filmid");
		autoCloseMsgHide();
		$(".loading").show();
		$.post('/xchange/soloActDeleteFromStore', {filmId:filmId}, function(data){
			//$('#listContent').html(data);
			$("#bulkActCheckbox").prop('checked', false);
			$(".loading").hide();
		});
	});

});
</script>