@extends('layouts.general')

@section('content')
	@if(isset($error) && $error == true)
		<p>{{ $error_message }}</p>
	@elseif(isset($success) && $success == true)
		<p>Account activated! Login <a href="{{ url('/login') }}">here.</a></p>
	@endif
@stop