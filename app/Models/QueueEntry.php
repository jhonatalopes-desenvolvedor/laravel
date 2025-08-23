<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\QueueEntryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class QueueEntry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'queue_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'barber_id',
        'customer_profile_id',
        'status',
        'entered_at',
        'started_at',
        'finished_at',
        'total_amount_charged',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                   => 'integer',
        'company_id'           => 'integer',
        'barber_id'            => 'integer',
        'customer_profile_id'  => 'integer',
        'status'               => QueueEntryStatus::class,
        'entered_at'           => 'datetime',
        'started_at'           => 'datetime',
        'finished_at'          => 'datetime',
        'total_amount_charged' => 'float',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    /**
     * Get the company associated with the queue entry.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the barber associated with the queue entry.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Get the customer profile associated with the queue entry.
     *
     * @return BelongsTo<CustomerProfile, covariant $this>
     */
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    /**
     * The services requested in this queue entry.
     *
     * @return BelongsToMany<Service, covariant $this, Pivot>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'queue_entry_service')
            ->withPivot('actual_duration_minutes', 'price_at_service');
    }

    /**
     * Get the live queue entry associated with this historical entry.
     *
     * @return HasOne<LiveQueue, covariant $this>
     */
    public function liveQueue(): HasOne
    {
        return $this->hasOne(LiveQueue::class);
    }
}
