@extends('layouts.main')
@section('title', 'Event Analytics')

@section('content')
<section class="container">
<h1>{{ $event->title }} — Analytics</h1>
<p>📅 {{ $event->date->format('d M Y H:i') }} · 📍 {{ $event->location }}</p>

<div class="stats">
<div class="stat-card"><span class="value">{{ $stats['total_sold'] }}</span><span class="label">Tickets Sold</span></div>
<div class="stat-card"><span class="value">{{ number_format($stats['revenue'], 2) }}</span><span class="label">Revenue (BDT)</span></div>
<div class="stat-card"><span class="value">{{ $stats['checked_in'] }}</span><span class="label">Checked-in</span></div>
<div class="stat-card"><span class="value">{{ $stats['pending'] }}</span><span class="label">Pending</span></div>
</div>

<h2>Per-Category Breakdown</h2>
<table class="table">
<thead><tr><th>Category</th><th>Price</th><th>Capacity</th><th>Sold</th><th>Remaining</th><th>Revenue</th></tr></thead>
<tbody>
@foreach($event->categories as $cat)
@php
$sold = $cat->soldCount();
$revenue = $sold * $cat->price;
@endphp
<tr>
<td>{{ $cat->name }}</td>
<td>{{ number_format($cat->price, 2) }} BDT</td>
<td>{{ $cat->capacity }}</td>
<td>{{ $sold }}</td>
<td>{{ $cat->remaining() }}</td>
<td>{{ number_format($revenue, 2) }} BDT</td>
</tr>
@endforeach
</tbody>
</table>

<div style="margin-top:20px;">
<a href="{{ route('organizer.events.attendees', $event) }}" class="btn">⬇ Download Attendees CSV</a>
<a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">Back</a>
</div>
</section>
@endsection
