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

	{{ Form::open(array('method' => 'POST', 'url' => url('/admin/login'))) }}

		<label>Email</label>
		{{ Form::text('email', Input::old('email', ''), array('placeholder' => 'Email')) }}

		<label>Password</label>
		{{ Form::password('password', array('placeholder' => 'Password')) }}

		@if($showCaptcha)
			{{ Form::captcha() }}
		@endif

		{{ Form::submit('Login') }}
		<a href="{{ url('/forgot') }}">Forgot password?</a>

	{{ Form::close() }}

@stop

@section('scripts')
	@if($showCaptcha)
		{{ Captcha::script() }}
	@endif
@stop