<?php

if ($contractShareInfo->share_type == 0){
	$shareSelected = 'selected="selected"';
	$staticDisabled = 'style="display:none;"';
	$shareDisabled = '';

}
else if ($contractShareInfo->share_type == 1){
	$staticSelected = 'selected="selected"';
	$staticDisabled = '';
	$shareDisabled = 'style="display:none;"';

}
else if ($contractShareInfo->share_type == 2){
	$mixedSelected = 'selected="selected"';
	$shareDisabled = '';
	$staticDisabled = '';

}
?>
<div class="miniwell">
	<form id="channelDeal">
		<div class="panel-body">
			<div class="form-group row">
				<label class="col-lg-2" for="">Model</label>
				<div class="col-lg-3">
					<select class="form-control" name="cp_share_type" id="cp_share_type" onchange="changeCPShareType();">
						<option value="0" '{{ isset($shareSelected) ? $shareSelected : '' }}' >Share (%)</option>
						<option  value="1" '{{ isset($staticSelected) ? $staticSelected : '' }}' >Static Fee ($)</option>
						<option value="2" '{{ isset($mixedSelected) ? $mixedSelected : '' }}' >Mixed ($ + %)</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<table id="list-view-container" class="table">
					<thead>
					<tr>
						<td style="width:30%;"><a>Model</a></td>
						<td style="width:20%;"><a>Content Provider</a></td>
						<td style="width:20%;"><a>Store</a></td>
						<td style="width:20%;"><a>Cinehost</a></td>
					</tr>
					</thead>
					<tbody id="list-view">
					<tr class="cp_static"  '{{ isset($staticDisabled) ? $staticDisabled : '' }}'>
					<td><a>Static Fee ($)</a></td>
					<td><input type="text" id="cp_share_fee" name="share_fee" class="form-control"  value="{{$contractShareInfo->share_fee}}"></td>
					<td><input type="text" class="form-control" disabled="disabled"></td>
					<td><input type="text" class="form-control" disabled="disabled"></td>
					</tr>
					<tr class="cp_share" '{{ isset($shareDisabled) ? $shareDisabled : '' }}'>
					<td><a>Share (%)</a></td>
					<td><input type="text" id="cp_share_cp" name="share_cp" class="form-control cpplkey"  value="{{ isset($contractShareInfo->share_cp) ? $contractShareInfo->share_cp : '' }}"></td>
					<td><input type="text" id="cp_share_pl" name="share_pl" class="form-control cpplkey"  value="{{ isset($contractShareInfo->share_pl) ? $contractShareInfo->share_pl : '' }}"></td>
					<td><input type="text" id="cp_share_ch" readonly name="share_ch" class="form-control"  value="{{ isset($contractShareInfo->share_ch) ? $contractShareInfo->share_ch : '' }}"></td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<input type="hidden" name="share_contract_id" value="{{ isset($contractShareInfo->id) ? $contractShareInfo->id : '' }}">
	</form>
</div>
<script>
function changeCPShareType(){
	var type = $( "#cp_share_type option:selected" ).val();
	if (type == 0){
		$(".cp_static").hide();
		$(".cp_share").show();
	}else if (type == 1){
		$(".cp_static").show();
		$(".cp_share").hide();
	}else if(type == 2){
		$(".cp_static").show();
		$(".cp_share").show();
	}
	return false;
}
$(".cpplkey").keyup(function(){
	$("#cp_share_pl").val($("#cp_share_pl").val().replace(/[^\d\.]/g, ""));
	$("#cp_share_cp").val($("#cp_share_cp").val().replace(/[^\d\.]/g, ""));
	var plval = parseFloat($("#cp_share_pl").val());
	plval = (isNaN(plval))?0:plval;
	var cpval = parseFloat($("#cp_share_cp").val());
	cpval = (isNaN(cpval))?0:cpval;
	var remaining = 100-cpval-plval;
	remaining = (isNaN(remaining))?0:remaining;
	if (remaining>100 || remaining<0)
		$("#cp_share_ch").val("ERROR");
	else
		$("#cp_share_ch").val(remaining);
	return false;
});
$("#cp_share_fee").keyup(function(){
	$("#cp_share_fee").val($("#cp_share_fee").val().replace(/[^\d\.]/g, ""));
	return false;
});
</script>