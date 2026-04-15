<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookTicketRequest;
use App\Mail\TicketBookedMail;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    public function dashboard(): View
    {
        $tickets = auth()->user()
            ->tickets()
            ->with('event', 'category')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total'   => auth()->user()->tickets()->count(),
            'paid'    => auth()->user()->tickets()->where('payment_status', 'paid')->count(),
            'pending' => auth()->user()->tickets()->where('payment_status', 'pending')->count(),
        ];

        return view('attendee.dashboard', compact('tickets', 'stats'));
    }

    public function create(Event $event): View
    {
        abort_unless($event->isPublished(), 404);
        $event->load('categories');
        return view('attendee.book', compact('event'));
    }

    public function store(BookTicketRequest $request, Event $event): RedirectResponse
    {
        abort_unless($event->isPublished(), 404);

        $category = TicketCategory::findOrFail($request->validated('category_id'));
        abort_unless($category->event_id === $event->id, 422, 'Invalid category for this event.');

        if (!$category->isAvailable()) {
            return back()->withErrors(['category_id' => 'This category is sold out.']);
        }

        $ticket = Ticket::create([
            'user_id'        => auth()->id(),
            'event_id'       => $event->id,
            'category_id'    => $category->id,
            'payment_status' => Ticket::PAYMENT_PENDING,
        ]);

        return redirect()->route('attendee.tickets')
            ->with('status', "Ticket reserved! Code: {$ticket->unique_code}. Please complete payment.");
    }

    public function pay(Ticket $ticket): RedirectResponse
    {
        $this->authorizeTicket($ticket);

        if ($ticket->isPaid()) {
            return back()->with('status', 'Ticket is already paid.');
        }

        $ticket->update(['payment_status' => Ticket::PAYMENT_PAID]);

        try {
            Mail::to($ticket->user->email)->send(new TicketBookedMail($ticket->fresh(['event', 'category', 'user'])));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Payment successful! A confirmation email has been sent.');
    }

    public function tickets(): View
    {
        $tickets = auth()->user()
            ->tickets()
            ->with('event', 'category')
            ->latest()
            ->paginate(10);

        return view('attendee.tickets', compact('tickets'));
    }

    public function pdf(Ticket $ticket): Response
    {
        $this->authorizeTicket($ticket);
        $ticket->load('event', 'category', 'user');

        $qrPng = base64_encode(
            QrCode::format('png')->size(220)->margin(1)->generate($ticket->unique_code)
        );

        $pdf = Pdf::loadView('attendee.ticket_pdf', [
            'ticket' => $ticket,
            'qr'     => $qrPng,
        ])->setPaper('a5', 'portrait');

        return $pdf->download($ticket->unique_code.'.pdf');
    }

    protected function authorizeTicket(Ticket $ticket): void
    {
        abort_unless(
            auth()->user()->isAdmin() || $ticket->user_id === auth()->id(),
            403
        );
    }
}
