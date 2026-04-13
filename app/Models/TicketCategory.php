<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'name', 'price', 'capacity'];

    protected $casts = [
        'price'    => 'decimal:2',
        'capacity' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    public function soldCount(): int
    {
        return $this->tickets()->where('payment_status', 'paid')->count();
    }

    public function remaining(): int
    {
        return max(0, $this->capacity - $this->soldCount());
    }

    public function isAvailable(): bool
    {
        return $this->remaining() > 0;
    }
}
