@if($post->revisionHistory->count() > 0)
	<div class="modal">
		<div class="wrapper">
			<a href="" class="toogleModal float-right">X</a>
			<h2>History for {{ $post->name }}</h2>
			<ul>
				@foreach($post->revisionHistory->sortByDesc('created_at')->take(100) as $history )
					<li class="margin-bottom-half">{{$history->created_at->format('Y-m-d')}} - {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($history->userResponsible()->id)), $history->userResponsible()->username)}} changed <strong>{{ $history->fieldName() }}</strong> from <code>{{ $history->oldValue() }}</code> to <code>{{ $history->newValue() }}</code>.
						@if(Auth::check() && Auth::user()->id == $post->user_id)
							{{HTML::actionlink($url = array('action' => 'PostController@undo', 'params' => array($history->id)), '<i class="fa fa-undo"></i>', array('class' => 'float-right'))}}
						@endif
					</li>
				@endforeach
				@if($post->revisionHistory->count() <= 99)
					<li class="margin-bottom-half">{{$post->created_at->format('Y-m-d')}} - {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($post->user_id)), $post->user->username)}} created this codeblock.</li>
				@endif
			</ul>
		</div>
	</div>
@endif