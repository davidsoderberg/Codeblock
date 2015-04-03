<ul class="float-left">
	<li class="divider"></li>
	{{HTML::menulink($url = array('action' => 'MenuController@index'), '<i class="fa fa-home"></i>Home')}}
	{{HTML::menulink($url = array('action' => 'MenuController@browse'), '<i class="fa fa-folder-open"></i>Browse')}}
	{{HTML::menulink($url = array('action' => 'MenuController@contact'), '<i class="fa fa-phone"></i>Contact')}}
</ul>
<ul class="float-right">
	@if(Auth::check())
		{{HTML::menulink($url = array('action' => 'ForumController@listForums'), '<i class="fa fa-comments"></i>Forum')}}
		{{HTML::adminmenu(
			'<i class="fa fa-group"></i>Admin <i class="fa fa-bars only-small display-inline"></i>',
			array(
				array(array('action' => 'UserController@index'), 'Users'),
				array(array('action' => 'PostController@index'), 'Codeblocks'),
				array(array('action' => 'TagController@index'), 'Tags'),
				array(array('action' => 'CategoryController@index'), 'Categories'),
				array(array('action' => 'CommentController@index'), 'Comments'),
				array(array('action' => 'ForumController@index'), 'Forums'),
				array(array('action' => 'RoleController@index'), 'Roles'),
				array(array('action' => 'PermissionController@index'), 'Permissions')
			)
		)}}
		<li class="dropdown">
			{{HTML::actionlink($url = array('action' => 'UserController@listUserBlock'), '<i class="fa fa-code"></i>My Codeblocks <i class="fa fa-bars only-small display-inline"></i>', array('class' => 'hideUl'))}}
			<ul>
				{{HTML::menulink($url = array('action' => 'UserController@listUserBlock'), 'List')}}
				{{HTML::menulink($url = array('action' => 'PostController@create'), 'Create')}}
				<!--{{HTML::menulink($url = array('action' => 'CommentController@listComments'), 'Comments')}}-->
			</ul>
		</li>
		{{HTML::menulink($url = array('action' => 'UserController@show'), '<i class="fa fa-user"></i>Profile')}}
		{{HTML::menulink($url = array('action' => 'UserController@logout'), '<i class="fa fa-sign-out"></i>Logout')}}
	@else
		{{HTML::menulink($url = array('action' => 'UserController@login'), '<i class="fa fa-sign-in"></i>Login / Sign Up')}}
	@endif
	<li class="divider"></li>
	<li class="form">
		<a class="search" href="#">
			<i class="fa fa-search"></i>
		</a>
		{{ Form::open(array('action' => 'PostController@search')) }}
		{{ Form::text('term', null, array('placeholder' => 'Search')) }}
		{{ Form::close() }}
	</li>
</ul>