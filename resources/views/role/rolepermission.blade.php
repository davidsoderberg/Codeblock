@extends('master')

@section('css')

@stop

@section('content')
	<h2>Add permission to Role</h2>
	{{ Form::open(array('action' => array('RoleController@updateRolePermission'))) }}
		<table id="permissionTable">
			<thead>
				<tr>
					<th>Permission</th>
					@foreach ($roles as  $role => $value)
						<th>{{ $role }}</th>
					@endforeach
				</tr>
			</thead>
			<tbody>
				@if(!empty($permissions))
					@foreach ($permissions as $permission)
						<tr>
							<td>{{ $permission->name }}</td>
							@foreach ($roles as $role => $value)
								<td data-title="{{$role}}">
									{{ Form::checkbox(str_replace(' ', '', $role).'[]', $permission->id, $value[$permission->permission]) }}
								</td>
							@endforeach
						</tr>
					@endforeach
				@else
					<tr colspan="{{ count($roles) + 1 }}">
						<td colspan="{{ count($roles) + 1 }}" class="text-center">There is no permissions yet</td>
					</tr>
				@endif
			</tbody>
		</table>
		<div class="clear"></div>
		{{ Form::button('Save', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')

@stop