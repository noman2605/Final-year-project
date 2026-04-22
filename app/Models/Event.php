<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'date',
        'location',
        'image',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function totalRevenue(): float
    {
        return (float) $this->tickets()
            ->where('payment_status', 'paid')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->sum('ticket_categories.price');
    }

    public function totalSold(): int
    {
        return $this->tickets()->where('payment_status', 'paid')->count();
    }

    /**
     * Always return a local static image URL. Any remote (http/https) value
     * stored in the database is ignored in favour of the bundled default,
     * so the project never renders dynamic/remote images.
     */
    public function getImageUrlAttribute(): string
    {
        $raw = $this->attributes['image'] ?? null;

        if ($raw && !Str::startsWith($raw, ['http://', 'https://', '//'])) {
            return asset(ltrim($raw, '/'));
        }

        return asset('images/events/default.jpg');
    }
}
