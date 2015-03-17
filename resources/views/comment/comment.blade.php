@if($comments->count > 0)
	@if($comments->count == 1)
		<h2>1 comment</h2>
	@else
		<h2>{{ $comments->count }} comments</h2>
	@endif
@else
	<h2>No comments</h2>
@endif
@if($comments)
	<ol>
		@foreach($comments as $comment)
			<li id="comment-{{ $comment->id }}">
				<div class="comment_meta clerfix">
					<i class="by">Av: {{ link_to('/user/'.$comment->user_id, $comment->user->username) }}</i>
					<small><b>{{ $comment->created_at }}</b></small>
				</div>
				<div class="comment">{{ $comment->comment }}</div>
			</li>
		@endforeach
	</ol>
@endif
{{ $comments->links() }}