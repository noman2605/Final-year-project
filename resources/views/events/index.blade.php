@extends('layouts.main')
@section('title', 'Events')

@section('content')
<section class="events-page">
<h1>All Events</h1>

<form method="GET" action="{{ route('events.index') }}" style="margin:20px 0;">
<input type="text" name="q" value="{{ request('q') }}" placeholder="Search events or location..."
style="padding:10px;width:300px;border:1px solid #ccc;border-radius:5px;">
<button type="submit" class="btn">Search</button>
</form>

<div class="event-container">
@forelse($events as $event)
<div class="card">
<img src="{{ $event->image_url }}" alt="{{ $event->title }}">
<h3>{{ $event->title }}</h3>
<p>📍 {{ $event->location }}</p>
<p>📅 {{ $event->date->format('d M Y') }}</p>
@if($event->categories->isNotEmpty())
<p class="price">From {{ number_format($event->categories->min('price'), 0) }} BDT</p>
@endif
<a href="{{ route('events.show', $event) }}" class="book-btn" style="text-decoration:none;display:inline-block;">View Details</a>
</div>
@empty
<p>No events match your search.</p>
@endforelse
</div>

<div class="pagination-wrap">
{{ $events->links() }}
</div>
</section>
@endsection
