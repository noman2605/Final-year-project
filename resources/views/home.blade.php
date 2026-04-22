@extends('layouts.main')
@section('title', 'Home')

@section('content')

<section class="hero">
<div class="hero-content">
<h1>Discover &amp; Book Amazing Events</h1>
<p>Concerts, Tech Conferences, Cultural Programs and Workshops across Bangladesh.</p>
<a href="{{ route('events.index') }}" class="hero-btn">Explore Events</a>
</div>
</section>

<section class="success">
<h2>Our Successful Events</h2>
<div class="success-container">
<div class="success-card">
<img src="{{ asset('images/home/tech-summit.jpg') }}" alt="Dhaka Tech Summit 2025">
<h3>Dhaka Tech Summit 2025</h3>
<p>Over 1500 participants joined the biggest tech event in Dhaka.</p>
</div>
<div class="success-card">
<img src="{{ asset('images/home/startup-meetup.jpg') }}" alt="Daffodil Startup Meetup">
<h3>Daffodil Startup Meetup</h3>
<p>Entrepreneurs and investors gathered to discuss new ideas.</p>
</div>
<div class="success-card">
<img src="{{ asset('images/home/music-festival.jpg') }}" alt="Bangladesh Music Festival">
<h3>Bangladesh Music Festival</h3>
<p>A cultural night with famous Bangladeshi artists.</p>
</div>
</div>
</section>

<section class="organizers">
<h2>Our Hardworking Organizers</h2>
<div class="organizer-container">
<div class="organizer-card">
<img src="{{ asset('images/team/rahim.jpg') }}" alt="Rahim Ahmed">
<h3>Rahim Ahmed</h3>
<p>Lead Event Organizer</p>
</div>
<div class="organizer-card">
<img src="{{ asset('images/team/nusrat.jpg') }}" alt="Nusrat Jahan">
<h3>Nusrat Jahan</h3>
<p>Event Manager</p>
</div>
<div class="organizer-card">
<img src="{{ asset('images/team/tanvir.jpg') }}" alt="Tanvir Hasan">
<h3>Tanvir Hasan</h3>
<p>Technical Coordinator</p>
</div>
</div>
</section>

<section class="events">
<h2>Popular Events</h2>
<div class="event-container">
@forelse($popular as $event)
<div class="card">
<img src="{{ $event->image_url }}" alt="{{ $event->title }}">
<h3>{{ $event->title }}</h3>
<p>📍 {{ $event->location }}</p>
<p>📅 {{ $event->date->format('d M Y') }}</p>
@if($event->categories->isNotEmpty())
<p class="price">From {{ number_format($event->categories->min('price'), 0) }} BDT</p>
@endif
<a href="{{ route('events.show', $event) }}" class="book-btn" style="text-decoration:none;display:inline-block;">View &amp; Book</a>
</div>
@empty
<p>No popular events yet. Check back soon!</p>
@endforelse
</div>
</section>

<section class="upcoming">
<h2>Upcoming Events</h2>
<div class="upcoming-container">
@forelse($upcoming as $event)
<div class="upcoming-card">
<img src="{{ $event->image_url }}" alt="{{ $event->title }}">
<h3>{{ $event->title }}</h3>
<p>📍 {{ $event->location }}</p>
<p>📅 {{ $event->date->format('d M Y') }}</p>
<a href="{{ route('events.show', $event) }}" class="book-btn" style="text-decoration:none;display:inline-block;">Book Ticket</a>
</div>
@empty
<p style="color:white;">No upcoming events scheduled.</p>
@endforelse
</div>
</section>

<section class="faq-section">
<h2>Frequently Asked Questions</h2>
<div class="faq">
<div class="faq-item">
<button class="faq-question">How can I book tickets for an event?</button>
<div class="faq-answer"><p>Browse the Events page, pick an event, choose a ticket category and complete the simulated payment.</p></div>
</div>
<div class="faq-item">
<button class="faq-question">Do I need an account to buy tickets?</button>
<div class="faq-answer"><p>Yes, please register an account first.</p></div>
</div>
<div class="faq-item">
<button class="faq-question">What payment methods are accepted?</button>
<div class="faq-answer"><p>This is a demo platform — payment is simulated with a single "Mark as Paid" button.</p></div>
</div>
<div class="faq-item">
<button class="faq-question">Can I cancel my ticket?</button>
<div class="faq-answer"><p>Refund / cancellation depends on the event organizer policy.</p></div>
</div>
</div>
</section>

<section class="cta">
<h2>Want to Join an Event?</h2>
<p>Book your ticket today and secure your seat.</p>
<a href="{{ route('events.index') }}" class="book-cta" style="text-decoration:none;display:inline-block;">Browse Events</a>
</section>

@endsection
