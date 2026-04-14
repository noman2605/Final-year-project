@extends('layouts.main')
@section('title', $event->title)

@section('content')
<section class="container">
<div class="event-detail">
<img src="{{ $event->image ?: 'https://picsum.photos/1200/400?'.$event->id }}">
<div class="body">
<h1>{{ $event->title }}</h1>
<div class="meta">
📍 {{ $event->location }} &nbsp; · &nbsp;
📅 {{ $event->date->format('l, d F Y · H:i') }} &nbsp; · &nbsp;
🎤 Organized by {{ $event->organizer->name ?? 'GateKeeper' }}
</div>
<div class="desc">{!! nl2br(e($event->description)) !!}</div>

<h3 style="margin-top:20px;">Ticket Categories</h3>
@if($event->categories->isEmpty())
<p>No ticket categories available yet.</p>
@else
@auth
@if(auth()->user()->isAttendee())
<form method="POST" action="{{ route('attendee.book.store', $event) }}">
@csrf
<div class="cat-pick">
@foreach($event->categories as $cat)
<label>
<input type="radio" name="category_id" value="{{ $cat->id }}" {{ $loop->first ? 'checked' : '' }} {{ $cat->isAvailable() ? '' : 'disabled' }}>
<span><strong>{{ $cat->name }}</strong></span><br>
<span class="price">{{ number_format($cat->price, 2) }} BDT</span><br>
<span class="left">{{ $cat->remaining() }} / {{ $cat->capacity }} available</span>
</label>
@endforeach
</div>
<button type="submit" class="btn" style="margin-top:20px;">Reserve Ticket</button>
</form>
@else
<div class="flash flash-error" style="margin:20px 0;">Only attendee accounts can book tickets.</div>
@endif
@else
<table class="table" style="margin-top:10px;">
<thead><tr><th>Category</th><th>Price</th><th>Availability</th></tr></thead>
<tbody>
@foreach($event->categories as $cat)
<tr>
<td>{{ $cat->name }}</td>
<td>{{ number_format($cat->price, 2) }} BDT</td>
<td>{{ $cat->remaining() }} / {{ $cat->capacity }}</td>
</tr>
@endforeach
</tbody>
</table>
<a href="{{ route('login') }}" class="btn">Login to Book</a>
@endauth
@endif
</div>
</div>
</section>
@endsection
