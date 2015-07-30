@if(!empty($comment->children[0]))
	@foreach($comment->children as $comment)
		<div class="comment" id="comment-{{ $comment->id}}">
			<hr class="margin-bottom-one">
			<div>
				@if(Auth::check() && Auth::user()->id != $comment->user_id)
					@if ($rate->check($comment->id) == '+')
						{{ $rate->calc($comment->id) }}
						{{HTML::actionlink($url = array('action' => 'RateController@minus', 'params' => array($comment->id)), '<i class="fa fa-caret-down"></i>')}}
					@elseif($rate->check($comment->id) == '-')
						{{HTML::actionlink($url = array('action' => 'RateController@plus', 'params' => array($comment->id)), '<i class="fa fa-caret-up"></i>')}}
						{{ $rate->calc($comment->id) }}
					@else
						{{HTML::actionlink($url = array('action' => 'RateController@plus', 'params' => array($comment->id)), '<i class="fa fa-caret-up"></i>')}}

						{{ $rate->calc($comment->id) }}
						{{HTML::actionlink($url = array('action' => 'RateController@minus', 'params' => array($comment->id)), '<i class="fa fa-caret-down"></i>')}}
					@endif
				@else
					{{ $rate->calc($comment->id) }}
				@endif
			</div>
			<div>
				<b>{{ date('Y-m-d', strtotime($comment->created_at)) }}</b> - {{HTML::actionlink($url = array('action' => 'UserController@show', 'params' => array($comment->user_id)), $comment->user->username)}}
				@if(Auth::check())
					<span class="pull-right">
						@if($comment->user_id == Auth::user()->id || HTML::hasPermission('CommentController@edit'))
							<a href="{{URL::action('PostController@show', $comment->slug)}}/{{$comment->id}}"><i class="fa fa-pencil"></i></a>
						@endif
						@if(Auth::user()->id == $comment->user_id || HTML::hasPermission('CommentController@delete'))
							<a href="{{URL::action('CommentController@delete', $comment->id)}}"><i class="fa fa-trash-o"></i></a>
						@endif
					</span>
				@endif
				<p>{{ HTML::mention(HTML::markdown($comment->comment)) }}</p>
				<a class="reply" href="#comment-{{$comment->id}}">Reply</a>
				@include('comment.child')
			</div>
		</div>
	@endforeach
@endif