@extends('layouts.main')
@section('title', 'New Event')

@section('content')
<section class="container">
<h1>Create Event</h1>

<form method="POST" action="{{ route('organizer.events.store') }}" class="form">
@csrf
<div class="row">
<label>Title</label>
<input type="text" name="title" value="{{ old('title') }}" required>
</div>
<div class="row">
<label>Description</label>
<textarea name="description" rows="5" required>{{ old('description') }}</textarea>
</div>
<div class="row">
<label>Date &amp; Time</label>
<input type="datetime-local" name="date" value="{{ old('date') }}" required>
</div>
<div class="row">
<label>Location</label>
<input type="text" name="location" value="{{ old('location') }}" required>
</div>
<div class="row">
<label>Image URL (optional)</label>
<input type="url" name="image" value="{{ old('image') }}">
</div>
<div class="row">
<label>Status</label>
<select name="status" required>
<option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
<option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
</select>
</div>

<h3 style="margin-top:25px;">Ticket Categories</h3>
<div id="cats">
<div class="cat-row" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:10px;">
<input type="text" name="categories[0][name]" placeholder="Category name (e.g. Regular)" required>
<input type="number" step="0.01" min="0" name="categories[0][price]" placeholder="Price" required>
<input type="number" min="1" name="categories[0][capacity]" placeholder="Capacity" required>
<button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">×</button>
</div>
</div>
<button type="button" class="btn btn-sm btn-secondary" onclick="addCat()">+ Add Category</button>

<div class="row" style="margin-top:25px;">
<button type="submit" class="btn">Create Event</button>
<a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>
</section>

@push('scripts')
<script>
let catIdx = 1;
function addCat(){
const wrap = document.getElementById('cats');
const div = document.createElement('div');
div.className = 'cat-row';
div.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:10px;';
div.innerHTML = `
<input type="text" name="categories[${catIdx}][name]" placeholder="Category name" required>
<input type="number" step="0.01" min="0" name="categories[${catIdx}][price]" placeholder="Price" required>
<input type="number" min="1" name="categories[${catIdx}][capacity]" placeholder="Capacity" required>
<button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">×</button>`;
wrap.appendChild(div);
catIdx++;
}
</script>
@endpush
@endsection
