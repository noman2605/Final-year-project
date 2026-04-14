@extends('layouts.main')
@section('title', 'About')

@section('content')
<section class="about-hero">
<div class="hero-content">
<h1>About GateKeeper</h1>
<p>Connecting people with amazing events across Bangladesh</p>
</div>
</section>

<section class="about-info">
<div class="about-container">
<div class="about-text">
<h2>Who We Are</h2>
<p>GateKeeper is an event management and ticket booking platform designed for people across Bangladesh. Our platform helps users discover and book tickets for concerts, conferences, workshops, cultural events and business meetups. We aim to support event organizers and make ticket booking simple and accessible for everyone in cities like Dhaka, Rajshahi, Chattogram, Khulna and Sylhet.</p>
</div>
<div class="about-image">
<img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678">
</div>
</div>
</section>

<section class="about-video">
<h2>Event Highlights</h2>
<p>Watch highlights from our successful events.</p>
<div class="video-container">
<iframe width="560" height="315" src="https://www.youtube.com/embed/ScMzIvxBSi4" title="Event Video" frameborder="0" allowfullscreen></iframe>
</div>
</section>

<section class="team">
<h2>Our Hardworking Organizers</h2>
<div class="team-container">
<div class="team-card"><img src="https://randomuser.me/api/portraits/men/21.jpg"><h3>Rahim Ahmed</h3><p>Lead Event Organizer</p></div>
<div class="team-card"><img src="https://randomuser.me/api/portraits/women/44.jpg"><h3>Nusrat Jahan</h3><p>Event Manager</p></div>
<div class="team-card"><img src="https://randomuser.me/api/portraits/men/55.jpg"><h3>Tanvir Hasan</h3><p>Technical Coordinator</p></div>
</div>
</section>
@endsection
