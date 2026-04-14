<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function home(): View
    {
        $popular = Event::published()
            ->withCount(['tickets as sold' => fn ($q) => $q->where('payment_status', 'paid')])
            ->orderByDesc('sold')
            ->take(3)
            ->get();

        $upcoming = Event::published()
            ->where('date', '>=', now())
            ->orderBy('date')
            ->take(3)
            ->get();

        return view('home', compact('popular', 'upcoming'));
    }

    public function index(Request $request): View
    {
        $query = Event::published()->with('categories')->orderBy('date');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(6)->withQueryString();
        return view('events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->isPublished(), 404);
        $event->load('categories', 'organizer');
        return view('events.show', compact('event'));
    }

    public function organizerDashboard(): View
    {
        $events = auth()->user()->events()
            ->withCount(['tickets as sold' => fn ($q) => $q->where('payment_status', 'paid')])
            ->latest()
            ->paginate(8);

        $totals = [
            'events'      => auth()->user()->events()->count(),
            'tickets_sold'=> DB::table('tickets')
                ->join('events', 'events.id', '=', 'tickets.event_id')
                ->where('events.organizer_id', auth()->id())
                ->where('tickets.payment_status', 'paid')
                ->count(),
            'revenue'     => (float) DB::table('tickets')
                ->join('events', 'events.id', '=', 'tickets.event_id')
                ->join('ticket_categories', 'ticket_categories.id', '=', 'tickets.category_id')
                ->where('events.organizer_id', auth()->id())
                ->where('tickets.payment_status', 'paid')
                ->sum('ticket_categories.price'),
        ];

        return view('organizer.dashboard', compact('events', 'totals'));
    }

    public function create(): View
    {
        return view('organizer.events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $categories = $data['categories'];
        unset($data['categories']);
        $data['organizer_id'] = auth()->id();

        DB::transaction(function () use ($data, $categories) {
            $event = Event::create($data);
            foreach ($categories as $c) {
                $event->categories()->create($c);
            }
        });

        return redirect()->route('organizer.dashboard')
            ->with('status', 'Event created successfully.');
    }

    public function edit(Event $event): View
    {
        $this->authorizeEvent($event);
        $event->load('categories');
        return view('organizer.events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        DB::transaction(function () use ($event, $data, $categories) {
            $event->update($data);

            $keepIds = [];
            foreach ($categories as $c) {
                if (!empty($c['id'])) {
                    $cat = TicketCategory::where('event_id', $event->id)->find($c['id']);
                    if ($cat) {
                        $cat->update(['name' => $c['name'], 'price' => $c['price'], 'capacity' => $c['capacity']]);
                        $keepIds[] = $cat->id;
                    }
                } else {
                    $new = $event->categories()->create([
                        'name'     => $c['name'],
                        'price'    => $c['price'],
                        'capacity' => $c['capacity'],
                    ]);
                    $keepIds[] = $new->id;
                }
            }
            $event->categories()->whereNotIn('id', $keepIds)->delete();
        });

        return redirect()->route('organizer.dashboard')
            ->with('status', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);
        $event->delete();
        return back()->with('status', 'Event deleted.');
    }

    public function toggleStatus(Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);
        $event->update([
            'status' => $event->isPublished() ? Event::STATUS_DRAFT : Event::STATUS_PUBLISHED,
        ]);
        return back()->with('status', 'Event status updated.');
    }

    public function analytics(Event $event): View
    {
        $this->authorizeEvent($event);
        $event->load('categories.tickets');

        $stats = [
            'total_sold' => $event->totalSold(),
            'revenue'    => $event->totalRevenue(),
            'checked_in' => $event->tickets()->where('is_used', true)->count(),
            'pending'    => $event->tickets()->where('payment_status', 'pending')->count(),
        ];

        return view('organizer.events.analytics', compact('event', 'stats'));
    }

    public function exportAttendees(Event $event): StreamedResponse
    {
        $this->authorizeEvent($event);

        $filename = 'attendees-'.$event->id.'-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($event) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Ticket Code', 'Attendee Name', 'Email', 'Category', 'Payment', 'Checked In', 'Booked At']);

            $event->tickets()->with('user', 'category')->chunk(200, function ($tickets) use ($out) {
                foreach ($tickets as $t) {
                    fputcsv($out, [
                        $t->unique_code,
                        $t->user->name ?? '',
                        $t->user->email ?? '',
                        $t->category->name ?? '',
                        $t->payment_status,
                        $t->is_used ? 'Yes' : 'No',
                        $t->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function authorizeEvent(Event $event): void
    {
        abort_unless(
            auth()->user()->isAdmin() || $event->organizer_id === auth()->id(),
            403
        );
    }
}
