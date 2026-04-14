@extends('layouts.main')
@section('title', 'Organizer Dashboard')

@section('content')
<section class="container">
<h1>Organizer Dashboard</h1>

<div class="stats">
<div class="stat-card"><span class="value">{{ $totals['events'] }}</span><span class="label">Events</span></div>
<div class="stat-card"><span class="value">{{ $totals['tickets_sold'] }}</span><span class="label">Tickets Sold</span></div>
<div class="stat-card"><span class="value">{{ number_format($totals['revenue'], 2) }}</span><span class="label">Revenue (BDT)</span></div>
</div>

<div style="margin:20px 0;">
<a href="{{ route('organizer.events.create') }}" class="btn">+ New Event</a>
</div>

<table class="table">
<thead>
<tr><th>Title</th><th>Date</th><th>Status</th><th>Sold</th><th>Actions</th></tr>
</thead>
<tbody>
@forelse($events as $event)
<tr>
<td>{{ $event->title }}</td>
<td>{{ $event->date->format('d M Y') }}</td>
<td><span class="status-badge status-{{ $event->status }}">{{ strtoupper($event->status) }}</span></td>
<td>{{ $event->sold }}</td>
<td>
<a href="{{ route('organizer.events.analytics', $event) }}" class="btn btn-sm">Stats</a>
<a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-sm btn-warn">Edit</a>
<a href="{{ route('organizer.events.attendees', $event) }}" class="btn btn-sm btn-secondary">CSV</a>
<form method="POST" action="{{ route('organizer.events.toggle', $event) }}" style="display:inline;">@csrf
<button class="btn btn-sm">{{ $event->isPublished() ? 'Unpublish' : 'Publish' }}</button>
</form>
<form method="POST" action="{{ route('organizer.events.destroy', $event) }}" style="display:inline;"
onsubmit="return confirm('Delete this event?');">
@csrf @method('DELETE')
<button class="btn btn-sm btn-danger">Delete</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="5">No events yet. Create your first one!</td></tr>
@endforelse
</tbody>
</table>

<div class="pagination-wrap">{{ $events->links() }}</div>
</section>
@endsection
