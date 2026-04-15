@extends('layouts.main')
@section('title', 'My Tickets')

@section('content')
<section class="container">
<h1>My Tickets</h1>

@forelse($tickets as $ticket)
<div class="ticket-card">
<div class="info">
<h3>{{ $ticket->event->title }}</h3>
<p>📍 {{ $ticket->event->location }} · 📅 {{ $ticket->event->date->format('d M Y H:i') }}</p>
<p>🎟 {{ $ticket->category->name }} — {{ number_format($ticket->category->price, 2) }} BDT</p>
<p>Code: <span class="code">{{ $ticket->unique_code }}</span></p>
<p>
<span class="status-badge status-{{ $ticket->payment_status }}">{{ strtoupper($ticket->payment_status) }}</span>
@if($ticket->is_used)
<span class="status-badge status-used">CHECKED IN</span>
@endif
</p>
</div>
<div class="actions">
@if(!$ticket->isPaid())
<form method="POST" action="{{ route('attendee.tickets.pay', $ticket) }}">
@csrf
<button type="submit" class="btn">Mark as Paid</button>
</form>
@else
<a href="{{ route('attendee.tickets.pdf', $ticket) }}" class="btn">Download PDF</a>
@endif
</div>
</div>
@empty
<p>You haven't booked any tickets yet. <a href="{{ route('events.index') }}" class="auth-link">Browse events</a>.</p>
@endforelse

<div class="pagination-wrap">{{ $tickets->links() }}</div>
</section>
@endsection
