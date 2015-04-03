<ul class="float-left">
	<li class="divider"></li>
	{{HTML::actionlink($url = array('action' => 'MenuController@index'), '<i class="fa fa-home"></i>Home', array(), $before = '<li>', $after = '</li>')}}
	{{HTML::actionlink($url = array('action' => 'MenuController@browse'), '<i class="fa fa-folder-open"></i>Browse', array(), $before = '<li>', $after = '</li>')}}
	{{HTML::actionlink($url = array('action' => 'MenuController@contact'), '<i class="fa fa-phone"></i>Contact', array(), $before = '<li>', $after = '</li>')}}
</ul>
<ul class="float-right">
	@if(Auth::check())
		{{HTML::actionlink($url = array('action' => 'ForumController@listForums'), '<i class="fa fa-comments"></i>Forum', array(), $before = '<li>', $after = '</li>')}}
		@if(Auth::user()->role == 2)
			<li class="dropdown">
				{{HTML::actionlink($url = array('action' => 'UserController@index'), '<i class="fa fa-group"></i>Admin <i class="fa fa-bars only-small display-inline"></i>', array('class' => 'hideUl'))}}
				<ul>
					{{HTML::actionlink($url = array('action' => 'UserController@index'), 'Users', array(), $before = '<li>', $after = '</li>')}}
					{{HTML::actionlink($url = array('action' => 'PostController@index'), 'Codeblocks', array(), $before = '<li>', $after = '</li>')}}
					{{HTML::actionlink($url = array('action' => 'TagController@index'), 'Tags', array(), $before = '<li>', $after = '</li>')}}
					{{HTML::actionlink($url = array('action' => 'CategoryController@index'), 'Categories', array(), $before = '<li>', $after = '</li>')}}
					{{HTML::actionlink($url = array('action' => 'CommentController@index'), 'Comments', array(), $before = '<li>', $after = '</li>')}}
					{{HTML::actionlink($url = array('action' => 'ForumController@index'), 'Forums', array(), $before = '<li>', $after = '</li>')}}
					{{HTML::actionlink($url = array('action' => 'RoleController@index'), 'Roles', array(), $before = '<li>', $after = '</li>')}}
					{{HTML::actionlink($url = array('action' => 'PermissionController@index'), 'Permissions', array(), $before = '<li>', $after = '</li>')}}
				</ul>
			</li>
		@endif
		<li class="dropdown">
			{{HTML::actionlink($url = array('action' => 'UserController@listUserBlock'), '<i class="fa fa-code"></i>My Codeblocks <i class="fa fa-bars only-small display-inline"></i>', array('class' => 'hideUl'))}}
			<ul>
				{{HTML::actionlink($url = array('action' => 'UserController@listUserBlock'), 'List', array(), $before = '<li>', $after = '</li>')}}
				{{HTML::actionlink($url = array('action' => 'PostController@create'), 'Create', array(), $before = '<li>', $after = '</li>')}}
				<!-- <li><a href="/comments">Comments</a></li> -->
			</ul>
		</li>
		{{HTML::actionlink($url = array('action' => 'UserController@show'), '<i class="fa fa-user"></i>Profile', array(), $before = '<li>', $after = '</li>')}}
		{{HTML::actionlink($url = array('action' => 'UserController@logout'), '<i class="fa fa-sign-out"></i>Logout', array(), $before = '<li>', $after = '</li>')}}
	@else
		{{HTML::actionlink($url = array('action' => 'UserController@login'), '<i class="fa fa-sign-in"></i>Login / Sign Up', array(), $before = '<li>', $after = '</li>')}}
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