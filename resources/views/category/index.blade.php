@extends('master')

@section('css')

@stop

@section('content')
    <h2>Categories</h2>
    <div class="verticalRule">
        <div class="float-left">
            <h3>List of categories</h3>
            {{ HTML::table(array('name'), $categories, 'categories', array('View' => false, 'Pagination' => 10), 'There are no categories right now.') }}
        </div>
        <div class="float-right">
            @if(isset($category->id))
                {{ Form::model($category, array('action' => array('CategoryController@createOrUpdate', $category->id))) }}
            @else
                {{ Form::model($category, array('action' => 'CategoryController@createOrUpdate')) }}
            @endif
            <h3>Make/update</h3>
            {{ HTML::flash() }}
            {{ Form::label('Name', 'Name:') }}
            {{ Form::text('name', Input::old('name'), array('id' => 'Name', 'placeholder' => 'Name of category', 'data-validator' => 'required|min:3')) }}
            {{ $errors->first('name', '<div class="alert error">:message</div>') }}
            {{ Form::button('Send', array('type' => 'submit')) }}
            {{ Form::close() }}
        </div>
    </div>
@stop

@section('script')

@stop