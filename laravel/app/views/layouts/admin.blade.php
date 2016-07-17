<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		<title>{{ $title }}</title>

		@yield('styles')
	</head>
	<body>
		<ul>
			<li><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			<li><a href="{{ url('/admin/logout') }}">Logout</a></li>
		</ul>
		@yield('content')
		@yield('scripts')
	</body>
</html>