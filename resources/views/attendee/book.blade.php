@extends('layouts.main')
@section('title', 'Book Ticket')

@section('content')
<section class="booking-section">
<h1>Book Ticket — {{ $event->title }}</h1>
<p>📍 {{ $event->location }} · 📅 {{ $event->date->format('d M Y H:i') }}</p>

<form method="POST" action="{{ route('attendee.book.store', $event) }}" class="booking-form">
@csrf
<label>Select a ticket category:</label>
<select name="category_id" required>
@foreach($event->categories as $cat)
<option value="{{ $cat->id }}" {{ $cat->isAvailable() ? '' : 'disabled' }}>
{{ $cat->name }} — {{ number_format($cat->price, 2) }} BDT ({{ $cat->remaining() }} left)
</option>
@endforeach
</select>
<button type="submit" class="submit-btn">Reserve Ticket</button>
</form>
<p style="margin-top:20px;color:#666;">Payment will be completed on the next page.</p>
</section>
@endsection
