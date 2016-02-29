@extends('layout')
@section('content')
<div class="title">
	<h1 class="h1">
		<a href="/xchangeTitles" class="text-primary" >Xchange </a> / <a href="/xchange/contentProviders" class="text-primary" >Content Providers </a> / {{ isset($company->first()->title) ? $company->first()->title : ""}}
	</h1>
</div>
<hr>

<div class="col-md-9">
	<div class="row">
		<div class="col-xs-6 col-md-4">
			<a href="#" class="thumbnail">
				<img width="173" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ isset($company->logo) ? $company->logo : 'nologo.png' }}" title="" alt="" >
			</a>
		</div>
		<p>{{ isset($company->first()->title) ? $company->first()->title : '' }}</p>
		<p>
			{{ isset($company->first()->brief) ? $company->first()->brief : '' }}
		</p>
		<div>
			<a href="http://<?=str_replace(['http://', 'HTTP://'], '', $company->first()->website);?>" class="text-primary" target="blank">
				{{ isset($company->first()->website) ? $company->first()->website : '' }}
			</a>
		</div>
	</div>
</div>
<div id="contentProvidersFilms">
	@include('xchange.xchangeContentproviders.filmList_partial')
</div>
@stop