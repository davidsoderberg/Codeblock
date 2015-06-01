<div class="item">
	{{HTML::actionlink($url = array('action' => 'UserController@showByUsername', 'params' => array($reply->user->username)), '<img alt="Avatar for '.$reply->user->username.'" src="'.HTML::avatar($reply->user->id).'">', array('class' => 'avatar'))}}
	<div class="reply">
		<p class="font-bold">
			{{HTML::actionlink($url = array('action' => 'UserController@showByUsername', 'params' => array($reply->user->username)), $reply->user->username)}},
			{{$reply->created_at->diffForHumans()}}
			@if(Auth::check())
				<span class="float-right">
					@if(Auth::user()->id == $reply->user_id || HTML::hasPermission('TopicController@createOrUpdate'))
						{{HTML::actionlink($url = array('action' => 'TopicController@show', 'params' => array($reply->topic->id, $reply->id)), '<i class="fa fa-pencil"></i>')}}
					@endif
					@if($reply->topic->replies->first()->id != $reply->id)
						@if(Auth::user()->id == $reply->user_id || HTML::hasPermission('ReplyController@delete'))
							{{HTML::actionlink($url = array('action' => 'ReplyController@delete', 'params' => array($reply->id)), '<i class="fa fa-trash-o"></i>')}}
						@endif
					@endif
				</span>
			@endif
		</p>
		<p>{{HTML::mention(HTML::markdown($reply->reply))}}</p>
	</div>
</div>