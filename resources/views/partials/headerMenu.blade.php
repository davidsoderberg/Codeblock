<ul class="float-left">
	<li class="divider"></li>
	@if(!Auth::check())
		{{HTML::menulink($url = array('action' => 'MenuController@index'), '<i class="fa fa-home"></i>Home')}}
	@endif
	{{HTML::menulink($url = array('action' => 'MenuController@browse'), '<i class="fa fa-folder-open"></i>Browse')}}
	{{HTML::menulink($url = array('action' => 'ArticleController@index'), '<i class="fa fa-info-circle"></i>Blog')}}
	{{HTML::menulink($url = array('action' => 'MenuController@contact'), '<i class="fa fa-phone"></i>Contact')}}
</ul>
<ul class="float-right">
	@if(Auth::check())
		{{HTML::menulink($url = array('action' => 'ForumController@listForums'), '<i class="fa fa-comments"></i>Forum')}}
		{{HTML::submenu(
			'<i class="fa fa-group"></i>Admin <i class="fa fa-bars only-small display-inline"></i>',
			array(
				array(array('action' => 'UserController@index'), 'Users'),
				array(array('action' => 'PostController@index'), 'Codeblocks'),
				array(array('action' => 'TagController@index'), 'Tags'),
				array(array('action' => 'CategoryController@index'), 'Categories'),
				array(array('action' => 'CommentController@index'), 'Comments'),
				array(array('action' => 'ForumController@index'), 'Forums'),
				array(array('action' => 'RoleController@index'), 'Roles'),
				array(array('action' => 'RoleController@editRolePermission'), 'Permissions')
			)
		)}}
		<li class="dropdown">
			{{HTML::actionlink($url = array('action' => 'UserController@listUserBlock'), '<i class="fa fa-code"></i>My Codeblocks <i class="fa fa-bars only-small display-inline"></i>', array('class' => 'hideUl'))}}
			<ul>
				{{HTML::menulink($url = array('action' => 'UserController@listUserBlock'), '<i class="fa fa-list"></i>List')}}
				{{HTML::menulink($url = array('action' => 'PostController@create'), '<i class="fa fa-pencil"></i>Create')}}
				{{HTML::menulink($url = array('action' => 'CommentController@listComments'), '<i class="fa fa-comment"></i>Comments')}}
				{{HTML::menulink($url = array('action' => 'UserController@backup'), '<i class="fa fa-save"></i>Backup')}}
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