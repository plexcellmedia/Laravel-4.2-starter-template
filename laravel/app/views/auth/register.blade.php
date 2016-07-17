@extends('layouts.general')

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

	{{ Form::open(array('method' => 'POST', 'url' => url('/register'))) }}
	
		<label>Email</label>
		{{ Form::text('email', Input::old('email', '')) }}

		<label>Password</label>
		{{ Form::password('password') }}

		<label>Confirm password</label>
		{{ Form::password('password_confirm') }}

		{{ Form::captcha() }}

		{{ Form::submit('Register') }}

	{{ Form::close() }}
@stop

@section('scripts')
	{{ Captcha::script() }}
@stop