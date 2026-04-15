@extends('layouts.main')
@section('title', 'Attendee Dashboard')

@section('content')
<section class="container">
<h1>Welcome, {{ auth()->user()->name }}</h1>

<div class="stats">
<div class="stat-card"><span class="value">{{ $stats['total'] }}</span><span class="label">Total Tickets</span></div>
<div class="stat-card"><span class="value">{{ $stats['paid'] }}</span><span class="label">Paid</span></div>
<div class="stat-card"><span class="value">{{ $stats['pending'] }}</span><span class="label">Pending</span></div>
</div>

<h2>Recent Tickets</h2>
@forelse($tickets as $ticket)
<div class="ticket-card">
<div class="info">
<h3>{{ $ticket->event->title }}</h3>
<p>📅 {{ $ticket->event->date->format('d M Y') }} · 🎟 {{ $ticket->category->name }}</p>
<p>Code: <span class="code">{{ $ticket->unique_code }}</span> ·
<span class="status-badge status-{{ $ticket->payment_status }}">{{ strtoupper($ticket->payment_status) }}</span></p>
</div>
<div class="actions">
@if(!$ticket->isPaid())
<form method="POST" action="{{ route('attendee.tickets.pay', $ticket) }}">@csrf
<button class="btn">Mark as Paid</button></form>
@else
<a href="{{ route('attendee.tickets.pdf', $ticket) }}" class="btn">PDF</a>
@endif
</div>
</div>
@empty
<p>No tickets yet. <a href="{{ route('events.index') }}" class="auth-link">Find an event</a>.</p>
@endforelse

<a href="{{ route('attendee.tickets') }}" class="btn">View All Tickets</a>
</section>
@endsection
