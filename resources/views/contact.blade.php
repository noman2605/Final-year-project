@extends('layouts.main')
@section('title', 'Contact')

@section('content')
<section class="contact-hero">
<h1>Contact Us</h1>
<p>We would love to hear from you</p>
</section>

<section class="contact-section">
<div class="contact-container">
<div class="contact-form">
<h2>Send Us a Message</h2>
<form id="contactForm">
<input type="text" placeholder="Your Name" required>
<input type="email" placeholder="Email Address" required>
<input type="tel" placeholder="Phone Number">
<textarea placeholder="Your Message" rows="5" required></textarea>
<button type="submit" class="submit-btn">Send Message</button>
<p id="contactSuccess"></p>
</form>
</div>
<div class="contact-info">
<h2>Contact Information</h2>
<p>📍 Dhaka, Bangladesh</p>
<p>📧 support@gatekeeper.test</p>
<p>📞 +880 1XXXX-XXXXXX</p>
<p>🕒 Office Hours: 9AM - 6PM</p>
</div>
</div>
</section>

<section class="map-section">
<h2>Our Location</h2>
<div class="map-container">
<iframe src="https://maps.google.com/maps?q=dhaka&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0"></iframe>
</div>
</section>
@endsection
