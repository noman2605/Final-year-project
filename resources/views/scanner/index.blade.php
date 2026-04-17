@extends('layouts.main')
@section('title', 'QR Scanner')

@section('content')
<section class="container">
<div class="scanner-wrap">
<h1>Ticket QR Scanner</h1>
<p>Point your webcam at a ticket QR code. Each code is processed once every 3 seconds.</p>

<div id="reader" style="width:400px;max-width:100%;"></div>

<div id="result" class="scan-result">Awaiting scan…</div>

<div style="margin-top:15px;font-size:13px;color:#666;">
Camera access required. If permission is denied, refresh and allow.
</div>
</div>
</section>

@push('head')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endpush

@push('scripts')
<script>
const result = document.getElementById('result');
let lastCode = null, lastTime = 0, busy = false;

function show(message, status, ticket){
result.classList.remove('success','error','warn');
if (status === 'success') result.classList.add('success');
else if (status === 'pending' || status === 'used') result.classList.add('warn');
else result.classList.add('error');

let html = `<strong style="font-size:18px;">${message}</strong>`;
if (ticket) {
html += `<div style="margin-top:8px;font-size:14px;">
👤 ${ticket.attendee} (${ticket.email})<br>
🎫 ${ticket.event} — ${ticket.category}<br>
🔑 <code>${ticket.code}</code>
</div>`;
}
result.innerHTML = html;
}

function beep(ok){
try {
const ctx = new (window.AudioContext || window.webkitAudioContext)();
const o = ctx.createOscillator(); const g = ctx.createGain();
o.connect(g); g.connect(ctx.destination);
o.frequency.value = ok ? 880 : 220;
g.gain.value = 0.1;
o.start(); setTimeout(()=>{o.stop();ctx.close();}, ok ? 150 : 350);
} catch(e) {}
}

async function onScanSuccess(decodedText) {
const now = Date.now();
if (busy) return;
if (decodedText === lastCode && now - lastTime < 3000) return;
lastCode = decodedText; lastTime = now; busy = true;

try {
const r = await fetch("{{ route('ticket.verify') }}", {
method: 'POST',
headers: {
'Content-Type': 'application/json',
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
'Accept': 'application/json',
'X-Requested-With': 'XMLHttpRequest',
},
body: JSON.stringify({ code: decodedText }),
});
const data = await r.json();
show(data.message, data.status, data.ticket);
beep(data.status === 'success');
} catch (e) {
show('Network error: '+e.message, 'error');
beep(false);
} finally {
setTimeout(()=>{ busy = false; }, 1200);
}
}

new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 }, false)
.render(onScanSuccess);
</script>
@endpush
@endsection
