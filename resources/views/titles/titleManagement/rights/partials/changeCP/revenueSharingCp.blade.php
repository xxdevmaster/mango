<?php
if(!empty($contractShareInfo)){
	$staticDisabled = 'disabled="disabled"';
	$shareDisabled = '';
	if ($contractShareInfo->share_type==0){
		$shareSelected = 'selected="selected"';
		$staticDisabled = 'disabled="disabled"';
		$shareDisabled = '';

	}
	else if ($contractShareInfo->share_type==1){
		$staticSelected = 'selected="selected"';
		$staticDisabled = '';
		$shareDisabled = 'disabled="disabled"';

	}
	else if ($contractShareInfo->share_type==2){
		$mixedSelected = 'selected="selected"';
		$shareDisabled = '';
		$staticDisabled = '';

	}
}
?>
<div class="miniwell">
	<form id="channelDeal">
		<div class="panel panel-default">
			<h1 class="h2">Revenue Sharing</h1>
			<div class="panel-body">
				<div class="form-group row">
					<label class="col-lg-2" for="bprice">Model</label>
					<div class="col-lg-3">
						<select class="form-control" name="share_type" id="share_type" onchange="changeShareType();">
							<option value="0" '{{ isset($shareSelected) ? $shareSelected : '' }}' >Share (%)</option>
							<option  value="1" '{{ isset($staticSelected) ? $staticSelected : '' }}' >Static Fee ($)</option>
							<option value="2" '{{ isset($mixedSelected)? $mixedSelected : '' }}' >Mixed ($ + %)</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-lg-2" for="bprice">My Share (%)</label>
					<div class="col-lg-3"><input type="text" id="share_cp" name="share_cp" class="form-control" '{{isset($shareDisabled)?$shareDisabled:''}}'  value="{{$contractShareInfo->share_cp}}"></div>
				</div>
				<div class="form-group row">
					<label class="col-lg-2" for="static">Static Fee ($)</label>
					<div class="col-lg-3"><input type="text" id="share_fee" name="share_fee" class="form-control" '{{isset($staticDisabled)?$staticDisabled :''}}' value="{{  $contractShareInfo->share_fee }}"></div>
				</div>
			</div>
		</div>

		<div class="row"></div>

		<input type="hidden" name="type" value="Cp">
	</form>
</div>
<script>
	function changeShareType(){
		var type = $( "#share_type option:selected" ).val();
		if (type == 0){
			$("#share_fee").attr({ "disabled": "disabled" });
			$("#share_cp").removeAttr("disabled");
		}else if (type == 1){
			$("#share_fee").removeAttr("disabled");
			$("#share_cp").attr({ "disabled": "disabled" });
		}else if(type == 2){
			$("#share_fee").removeAttr("disabled");
			$("#share_cp").removeAttr("disabled");
		}
		return false;
	}
</script>
