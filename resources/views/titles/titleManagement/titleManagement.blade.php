@extends('layout')
@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="clearfix">
						<div class="pull-left">
							<h2 class="text-right">{{ $film->title }} / {{ $current_menu }}</h2>							
						</div>
						<div class="pull-right review">
                            <button class="btn btn-default btn-md">
                                Review
                            </button>
                        </div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="pull-left col-lg-2 col-md-3 col-sm-6 col-xs-5">
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" class="image-responsive" alt="" width="100%" height="auto">
							</div>
							<div class="pull-left col-lg-3 col-md-7">
								<div class="list-group no-border mail-list ">
                                  <a href="{{url()}}/titles/metadata/{{ $film->id }}" class="list-group-item {{ (Request::server('REQUEST_URI') == "/titles/metadata/$film->id") ? 'active' : '' }}">Metadata</a>
                                  <a href="{{url()}}/titles/media/{{ $film->id }}" class="list-group-item {{ (Request::server('REQUEST_URI') == "/titles/media/$film->id") ? 'active' : '' }}">Media</a>
                                  <a href="{{url()}}/titles/rights/{{ $film->id }}" class="list-group-item {{ (Request::server('REQUEST_URI') == "/titles/rights/$film->id") ? 'active' : '' }}">Rights</a>
                                  <a href="{{url()}}/titles/sales/{{ $film->id }}" class="list-group-item {{ (Request::server('REQUEST_URI') == "/titles/sales/$film->id") ? 'active' : '' }}">TVOD Sales</a>
                                </div>
							</div>
							@inject('helper', 'App\Libraries\CHhelper\Chhelper')
							@if($helper->isPublish($film))
								<?php $cheked="checked";?>
							@else
								<?php $cheked="";?>
							@endif
							<div class="pull-right onAndOff">
								<div class="onoffswitch">
									<input type="checkbox" name="onoffswitch" data-active="products" class="onoffswitch-checkbox" id="myonoffswitch0" {{ $cheked }}>
									<label class="onoffswitch-label" for="myonoffswitch0">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch">
											<i class="ion-checkmark"></i>
										</span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="m-h-50">
						@yield('titleManagement')
					</div>
				</div>
			</div>
			<input type="hidden" name="filmID" value="{{ $film->id }}">
		</div>		
	</div>
	<script>
		$(document).ready(function(){
			$('input[name="onoffswitch"').change(function(){
				var checked = $(this).prop('checked');
				if(checked){
					var confirmText = 'Do you realy want to published film?';
					var filmStatus = 1;
				}
				else{
					var confirmText = 'Do you really want to unpublish film?';
					var filmStatus = 0;
				}
				$.post('{{url()}}/titles/metadata/publishUnpublish', {filmStatus:filmStatus});
			});
		});
	</script>
@stop