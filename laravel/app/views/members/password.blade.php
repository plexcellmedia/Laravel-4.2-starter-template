@extends('layouts.members')

@section('content')
	
	@if(Session::has('message'))
		{{ Session::get('message') }}
	@endif

	@if(Session::has('error'))
		{{ Session::get('error') }}
	@endif

	@if ($errors->has())
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	@endif

	{{ Form::open(array('method' => 'POST', 'url' => url('/users/password'))) }}

		<label>New password</label>
		{{ Form::password('new_password') }}

		<label>Confirm password</label>
		{{ Form::password('new_password_confirm') }}

		<label>Current password</label>
		{{ Form::password('password') }}

		{{ Form::submit('Change password') }}

	{{ Form::close() }}
@stop