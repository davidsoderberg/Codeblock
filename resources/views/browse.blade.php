@extends('master')

@section('css')

@stop

@section('content')
	<h2>Browse</h2>
	<div id="accordion" class="accordion">
		<ul>
			<li class="open">
				<a href="#">Categories</a>
				<div class="content">
					<a class="block" href="/posts/category/0">What is new?</a>
					@foreach ($categories as $category)
						<a class="block" href="/posts/category/{{ $category->id }}">{{ $category->name }}</a>
					@endforeach
				</div>
			</li>
			<li>
				<a href="">Tags</a>
				<div class="content" id="tagList">
					@foreach ($tags as $tag)
						<a class="block" href="/posts/tag/{{ $tag->id }}">{{ $tag->name }}</a>
					@endforeach
				</div>
			</li>
		</ul>
	</div>
	<div id="browseTabs" class="tabs">
		<ul class="clearfix">
			<li class="open"><a href="">Categories</a></li>
			<li><a href="">Tags</a></li>
		</ul>
		<ul>
			<li class="open">
				<a class="block" href="/posts/category/0">What is new?</a>
				@foreach ($categories as $category)
					<a class="block" href="/posts/category/{{ $category->id }}">{{ $category->name }}</a>
				@endforeach
				<div class="clear"></div>
			</li>
			<li>
				<div id="tagList">
					@foreach ($tags as $tag)
						<a class="block" href="/posts/tag/{{ $tag->id }}">{{ $tag->name }}</a>
					@endforeach
				</div>
			</li>
		</ul>
	</div>
@stop

@section('script')

@stop