<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\CustomerArrivalStatus;
use App\Enums\LiveQueueStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveQueue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'live_queues';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'queue_entry_id',
        'barber_id',
        'company_id',
        'customer_profile_id',
        'current_status',
        'customer_arrival_status',
        'estimated_service_duration_minutes',
        'estimated_wait_time_minutes',
        'queue_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                                 => 'integer',
        'queue_entry_id'                     => 'integer',
        'barber_id'                          => 'integer',
        'company_id'                         => 'integer',
        'customer_profile_id'                => 'integer',
        'current_status'                     => LiveQueueStatus::class,
        'customer_arrival_status'            => CustomerArrivalStatus::class,
        'estimated_service_duration_minutes' => 'integer',
        'estimated_wait_time_minutes'        => 'integer',
        'queue_order'                        => 'integer',
        'created_at'                         => 'datetime',
        'updated_at'                         => 'datetime',
    ];

    /**
     * Get the historical queue entry associated with this live queue.
     *
     * @return BelongsTo<QueueEntry, covariant $this>
     */
    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class);
    }

    /**
     * Get the barber associated with this live queue entry.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Get the company associated with this live queue entry.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the customer profile associated with this live queue entry.
     *
     * @return BelongsTo<CustomerProfile, covariant $this>
     */
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }
}
