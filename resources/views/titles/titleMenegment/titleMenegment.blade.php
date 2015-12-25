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

					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="pull-left col-lg-2 col-md-3 col-sm-6 col-xs-5">
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" class="image-responsive" alt="" width="100%" height="auto">
							</div>
							<div class="pull-left col-lg-3 col-md-7">
								<div class="list-group no-border mail-list ">
									<?php


									?>
                                  <a href="{{url()}}/titles/metadata/{{$id}}" class="list-group-item">Metadata</a>
                                  <a href="{{url()}}/titles/media/{{$id}}" class="list-group-item">Media</a>
                                  <a href="{{url()}}/titles/rights/{{$id}}" class="list-group-item">Rights</a>
                                  <a href="{{url()}}/titles/sales/{{$id}}" class="list-group-item">TVOD Sales</a>
                                </div>
							</div>
							<div class="pull-right onAndOff">
								<div class="onoffswitch">
									<input type="checkbox" name="onoffswitch" data-item="79" data-active="products" class="onoffswitch-checkbox" id="myonoffswitch0" checked="">
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
						@yield('titleMenegment')
					</div>
				</div>
			</div>
			<input type="hidden" name="filmId" value="{{ $film->id }}">
		</div>		
	</div>		
@stop