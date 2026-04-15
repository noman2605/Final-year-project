<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:64'],
        ]);

        $ticket = Ticket::with('event', 'user', 'category')
            ->where('unique_code', $data['code'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Ticket',
            ], 404);
        }

        if (!$ticket->isPaid()) {
            return response()->json([
                'status'  => 'pending',
                'message' => 'Payment Pending',
                'ticket'  => $this->ticketPayload($ticket),
            ], 402);
        }

        if ($ticket->is_used) {
            return response()->json([
                'status'  => 'used',
                'message' => 'Already Checked-in',
                'ticket'  => $this->ticketPayload($ticket),
                'checked_in_at' => $ticket->checked_in_at?->toDateTimeString(),
            ], 409);
        }

        $ticket->update([
            'is_used'       => true,
            'checked_in_at' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Check-in Successful',
            'ticket'  => $this->ticketPayload($ticket),
        ]);
    }

    protected function ticketPayload(Ticket $ticket): array
    {
        return [
            'code'     => $ticket->unique_code,
            'attendee' => $ticket->user->name ?? '—',
            'email'    => $ticket->user->email ?? '—',
            'event'    => $ticket->event->title ?? '—',
            'category' => $ticket->category->name ?? '—',
        ];
    }
}
