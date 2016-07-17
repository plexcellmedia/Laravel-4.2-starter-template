<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		<title>{{ $title }}</title>

		@yield('styles')
	</head>
	<body>
		<ul>
			<li><a href="{{ url('/users') }}">Members</a></li>
			<li><a href="{{ url('/users/password') }}">Change password</a></li>
			<li><a href="{{ url('/users/logout') }}">Logout</a></li>
		</ul>
		@yield('content')
		@yield('scripts')
	</body>
</html>