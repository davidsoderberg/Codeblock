@if(!empty($comment->children[0]))
	@foreach($comment->children as $comment)
		<div class="comment" id="comment-{{ $comment->id}}">
			<hr class="margin-bottom-one">
			<div>
				@if(Auth::check() && Auth::user()->id != $comment->user_id)
					@if ($rate->check($comment->id) == '+')
						{{ $rate->calc($comment->id) }}
						<a href="/rate/minus/{{$comment->id}}"><i class="fa fa-caret-down"></i></a>
					@elseif($rate->check($comment->id) == '-')
						<a href="/rate/plus/{{$comment->id}}"><i class="fa fa-caret-up"></i></a>
						{{ $rate->calc($comment->id) }}
					@else
						<a href="/rate/plus/{{$comment->id}}"><i class="fa fa-caret-up"></i></a>
						{{ $rate->calc($comment->id) }}
						<a href="/rate/minus/{{$comment->id}}"><i class="fa fa-caret-down"></i></a>
					@endif
				@else
					{{ $rate->calc($comment->id) }}
				@endif
			</div>
			<div>
				<b>{{ date('Y-m-d', strtotime($comment->created_at)) }}</b> - <a href="/user/{{ $comment->user_id }}">{{ $comment->user->username }}</a>
				<p>{{ HTML::mention($comment->comment) }}</p>
				<a class="reply" href="#comment-{{$comment->id}}">Reply</a>
				@include('comment.child')
			</div>
		</div>
	@endforeach
@endif