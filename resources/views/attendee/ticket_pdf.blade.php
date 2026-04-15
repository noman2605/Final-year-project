<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ticket {{ $ticket->unique_code }}</title>
<style>
body{font-family:DejaVu Sans, sans-serif;margin:0;padding:0;color:#222;}
.wrap{padding:30px;border:3px dashed #0aa;}
.brand{font-size:28px;color:#0aa;font-weight:bold;text-align:center;margin-bottom:20px;border-bottom:2px solid #0aa;padding-bottom:10px;}
.title{font-size:22px;margin-bottom:15px;}
.row{margin:8px 0;font-size:14px;}
.label{color:#666;font-size:12px;text-transform:uppercase;}
.value{font-size:16px;font-weight:bold;color:#222;}
.code{font-family:monospace;font-size:18px;background:#f0f0f0;padding:8px 14px;border-radius:6px;}
.qr{text-align:center;margin-top:20px;}
.foot{text-align:center;margin-top:25px;font-size:11px;color:#888;}
</style>
</head>
<body>
<div class="wrap">
<div class="brand">GateKeeper Ticket</div>
<div class="title">{{ $ticket->event->title }}</div>

<div class="row"><span class="label">Attendee:</span> <span class="value">{{ $ticket->user->name }}</span></div>
<div class="row"><span class="label">Email:</span> {{ $ticket->user->email }}</div>
<div class="row"><span class="label">Category:</span> <span class="value">{{ $ticket->category->name }}</span></div>
<div class="row"><span class="label">Price:</span> {{ number_format($ticket->category->price, 2) }} BDT</div>
<div class="row"><span class="label">Date:</span> {{ $ticket->event->date->format('l, d F Y · H:i') }}</div>
<div class="row"><span class="label">Location:</span> {{ $ticket->event->location }}</div>
<div class="row"><span class="label">Ticket Code:</span><span class="code">{{ $ticket->unique_code }}</span></div>

<div class="qr">
<img src="data:image/png;base64,{{ $qr }}" alt="QR" style="width:200px;height:200px;">
<div style="font-size:12px;color:#666;margin-top:5px;">Scan at the gate</div>
</div>

<div class="foot">© {{ date('Y') }} GateKeeper Bangladesh — Issued {{ $ticket->created_at->format('d M Y') }}</div>
</div>
</body>
</html>
