<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'GateKeeper') | GateKeeper</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
@stack('head')
</head>
<body>

<header>
<nav class="navbar">
<h2 class="logo"><a href="{{ route('home') }}" style="text-decoration:none;color:inherit;">GateKeeper</a></h2>

<ul class="menu">
<li><a href="{{ route('home') }}">Home</a></li>
<li><a href="{{ route('events.index') }}">Events</a></li>
<li><a href="{{ route('about') }}">About</a></li>
<li><a href="{{ route('contact') }}">Contact</a></li>
@auth
<li><a href="{{ route('dashboard') }}">Dashboard</a></li>
@if(auth()->user()->isOrganizer() || auth()->user()->isAdmin())
<li><a href="{{ route('scanner.index') }}">Scanner</a></li>
@endif
@endauth
</ul>

@auth
<form method="POST" action="{{ route('logout') }}" style="display:inline;">
@csrf
<span style="margin-right:10px;font-size:13px;">{{ auth()->user()->name }}
<span class="role-badge role-{{ auth()->user()->role }}">{{ auth()->user()->role }}</span></span>
<button type="submit" class="nav-btn">Logout</button>
</form>
@else
<a href="{{ route('login') }}" class="nav-btn" style="text-decoration:none;display:inline-block;">Login</a>
@endauth
</nav>
</header>

@if(session('status'))
<div class="flash flash-success">{{ session('status') }}</div>
@endif
@if($errors->any())
<div class="flash flash-error">
@foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
</div>
@endif

@yield('content')

<footer>
<div class="footer-container">
<div class="footer-col">
<h3>GateKeeper</h3>
<p>Discover and book the best events across Bangladesh.</p>
</div>
<div class="footer-col">
<h3>Quick Links</h3>
<p><a href="{{ route('home') }}" style="color:white;text-decoration:none;">Home</a></p>
<p><a href="{{ route('events.index') }}" style="color:white;text-decoration:none;">Events</a></p>
<p><a href="{{ route('contact') }}" style="color:white;text-decoration:none;">Contact</a></p>
</div>
<div class="footer-col">
<h3>Contact</h3>
<p>Dhaka, Bangladesh</p>
<p>Email: support@gatekeeper.test</p>
<p>Phone: +880 1XXXX-XXXXXX</p>
</div>
</div>
<p class="copyright">&copy; {{ date('Y') }} GateKeeper Bangladesh</p>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
