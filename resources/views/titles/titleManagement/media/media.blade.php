@extends('titles.titleManagement.titleManagement')
@section('titleManagement')
	<div class="m-h-50">
		<div class="col-lg-12">
			<ul class="nav nav-tabs">
				<li class="active" data-placement="top" data-toggle="tooltip" data-original-title="Streaming">
					<a href="#tab-streaming" data-toggle="tab" class="tab-level0" aria-expanded="false">
						<span class="visible-sm visible-xs"><i class="fa fa-home"></i></span>
						<span class="hidden-sm hidden-xs">Streaming</span>
					</a>
				</li>
				<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Storage">
					<a href="#tab-storage" data-toggle="tab" class="tab-level0" aria-expanded="true">
						<span class="visible-sm visible-xs"><i class="fa fa-info-circle"></i></span>
						<span class="hidden-sm hidden-xs">Storage</span>
					</a>
				</li>
				<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Dubbed Versions">
					<a href="#tab-dubbedVersions" data-toggle="tab" class="tab-level0" aria-expanded="false">
						<span class="visible-sm visible-xs"><i class="fa fa-user"></i></span>
						<span class="hidden-sm hidden-xs">Dubbed Versions</span>
					</a>
				</li>
				<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Extras">
					<a href="#tab-extras" data-toggle="tab" class="tab-level0" aria-expanded="false">
						<span class="visible-sm visible-xs" ><i class="fa fa-picture-o"></i></span>
						<span class="hidden-sm hidden-xs">Extras</span>
					</a>
				</li>
				<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Vimeo">
					<a href="#tab-vimeo" data-toggle="tab" class="tab-level0" aria-expanded="false">
						<span class="visible-sm visible-xs"><i class="fa fa-newspaper-o"></i></span>
						<span class="hidden-sm hidden-xs">Vimeo</span>
					</a>
				</li>
				<li class="" data-placement="top" data-toggle="tooltip" data-original-title="Uploader">
					<a href="#tab-uploader" data-toggle="tab" class="tab-level0" aria-expanded="true">
						<span class="visible-sm visible-xs"><i class="fa fa-flag-o"></i></span>
						<span class="hidden-sm hidden-xs">Uploader</span>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="tab-streaming">
					@include('titles.titleManagement.media.partials.streaming.streaming')
				</div>
				<div class="tab-pane fade" id="tab-storage">
					@include('titles.titleManagement.media.partials.storage.storage')
				</div>
				<div class="tab-pane fade" id="tab-dubbedVersions">
					@include('titles.titleManagement.media.partials.dubbedVersions.dubbedVersions')
				</div>
				<div class="tab-pane fade" id="tab-extras">
					@include('titles.titleManagement.media.partials.extras.extras')
				</div>
				<div class="tab-pane fade" id="tab-vimeo">
					@include('titles.titleManagement.media.partials.vimeo.vimeo')
				</div>
				<div class="tab-pane fade" id="tab-uploader">
					@include('titles.titleManagement.media.partials.uploader.uploader')
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<button class="btn btn-success" id="saveChanges">Save Changes</button>
	</div>
	<script>
		$(document).ready(function(){

				$(document).on('click', '#saveChanges', function(){
				autoCloseMsgHide();
				var thisEllement = $(this);
				thisEllement.html('Saving...');

				var vimeoForm = $('#vimeoForm').serialize();
				var dubbedVersionForm = $('#dubbedVersionForm').serialize();

				$('.loading').show();

				$.when(
						$.ajax({
							type: 'POST',
							url: '{{ url() }}/titles/media/vimeo/saveChangesVimeo',
							data: vimeoForm,
						}),
						$.ajax({
							type: 'POST',
							url: '{{ url() }}/titles/media/dubbedVersions/saveChanges',
							data: dubbedVersionForm,
						})
				).done(function(){
							$('.loading').hide();
							thisEllement.html('Save Changes');
						}).fail(function(){
							$('.loading').hide();
							thisEllement.html('Save Changes');
							autoCloseMsg(1,'Bad Request',5000);  //show error message
						});
			});

			$('form').submit(function(){
				return false;
			});
		});
	</script>
@stop