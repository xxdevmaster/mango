<div class="text-right">
	<ul class="pagination">
		<?php 
			$total = floor($pager['total']/$pager['limit']);
			$offs = 0;

			if($pager['offset'] != 0)
				$offset = ceil((($pager['offset']+1)/$pager['limit']));
			else 
				$offset = 1;
		?>
	  @if(isset($pager))
		<li><a href="#">&laquo;</a></li>
			@for($i=1; $i < $total; ++$i)
				<?php
					if($offset == $i)
						$active = 'active';
					else
						$active = '';
				?>
				<li class="{{$active}}" data-pager="{{$offs}}">
					<a >{{ $i }}</a>
				</li>
				<?php $offs = $offs+$pager['limit'];?>
			@endfor
		<li><a href="#">&raquo;</a></li>
	  @endif
	</ul>
</div>