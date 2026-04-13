<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID    = 'paid';

    protected $fillable = [
        'user_id',
        'event_id',
        'category_id',
        'unique_code',
        'payment_status',
        'is_used',
        'checked_in_at',
    ];

    protected $casts = [
        'is_used'       => 'boolean',
        'checked_in_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->unique_code)) {
                $ticket->unique_code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'GK-' . strtoupper(Str::random(8));
        } while (static::where('unique_code', $code)->exists());

        return $code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }
}
