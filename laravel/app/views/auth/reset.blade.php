@extends('layouts.general')

@section('content')
	@if(isset($error) && $error == true)
		<p>{{ $error_message }}</p>
	@elseif(isset($success) && $success == true)
		<p>Password reseted! New password is sent to your email.</p>
	@endif
@stop