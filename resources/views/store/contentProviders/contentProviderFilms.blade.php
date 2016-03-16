@extends('layout')
@section('content')
	<div class="title">
		<h1 class="h1">
			<a href="/store/contentProviders" class="text-info">Content Providers </a> / {{ isset($contentProvider->title) ? $contentProvider->title : ""}}
		</h1>
	</div>
	<hr>
	<div class="col-md-9">
		<div class="row">
			<div class="col-xs-6 col-md-4">
				<a href="http://<?=str_replace(['http://', 'HTTP://'], '', $contentProvider->website);?>" class="thumbnail" target="blank">
					<img width="173" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($contentProvider->logo) ? $contentProvider->logo : 'nologo.png' }}" title="" alt="" >
				</a>
			</div>
			<h3 class="h3" style="margin:0;">{{ isset($contentProvider->title) ? $contentProvider->title : '' }}</h3>
			<p class="h5">
				{{ isset($contentProvider->brief) ? $contentProvider->brief : '' }}
			</p>
			<p>
				<a href="http://<?=str_replace(['http://', 'HTTP://'], '', $contentProvider->website);?>" class="text-primary" target="blank">
					{{ isset($contentProvider->website) ? $contentProvider->website : '' }}
				</a>
			</p>
		</div>
	</div>
	<div id="films">
		@include('store.contentProviders.filmList_partial')
	</div>
@stop