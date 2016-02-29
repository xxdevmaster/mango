@extends('layout')
@section('content')
<div class="title">
	<h1 class="h1">
		Store / {{ isset($store->title) ? $store->title : ""}}
	</h1>
</div>
<hr class="hrLine">
<div class="col-md-9">
	<div class="row">
		<div class="col-xs-6 col-md-4">
			<a href="#" class="thumbnail">
				<img width="173" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($store->logo) ? $store->logo : 'nologo.png' }}" title="" alt="" >
			</a>
		</div>
		<h3 class="h3" style="margin:0;">{{ isset($store->title) ? $store->title : '' }}</h3>
		<h5 class="h5 text-info">
			{{ isset($store->brief) ? $store->brief : '' }}
		</h5>
		<h5 class="h5">
			<a href="http://<?=str_replace(['http://', 'HTTP://'], '', $store->website);?>" class="text-primary" target="blank">
				{{ isset($store->website) ? $store->website : '' }}
			</a>
		</h5>
	</div>
</div>
<div id="storeFilms">
	@include('partnerStores.filmList_partial')
</div>
@stop