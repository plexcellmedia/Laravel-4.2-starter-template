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

	{{ Form::open(array('method' => 'POST', 'url' => url('/forgot'))) }}

		<label>Email</label>
		{{ Form::text('email', Input::old('email', '')) }}
		{{ Form::captcha() }}
		{{ Form::submit('Recover account') }}

	{{ Form::close() }}
@stop

@section('scripts')
	{{ Captcha::script() }}
@stop