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
						<!--div class="pull-right">
							<h4>Invoice # <br>
								<strong>2015-04-23654789</strong>
							</h4>
						</div-->
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="pull-left col-lg-2 col-md-3 col-sm-6 col-xs-5">
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/{{ $film->cover }}" class="image-responsive" alt="" width="100%" height="auto">
							</div>
							<div class="pull-left col-lg-3 col-md-7">
								<div class="list-group no-border mail-list ">
                                  <a href="#" class="list-group-item active">Metadata</a>
                                  <a href="#" class="list-group-item">Media</a>
                                  <a href="#" class="list-group-item">Rights</a>
                                  <a href="#" class="list-group-item">TVOD Sales</a>
                                </div>
							</div>
						</div>
					</div>
					<hr>
					<div class="m-h-50">
						@yield('titleManagementReadOnly')
					</div>
				</div>
			</div>
			<input type="hidden" name="filmId" value="{{ $film->id }}">
		</div>		
	</div>		
@stop