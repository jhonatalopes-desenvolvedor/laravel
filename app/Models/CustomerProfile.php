<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\CustomerArrivalStatus;
use App\Enums\CustomerRelationshipType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProfile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'profile_name',
        'relationship_type',
        'arrival_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                => 'integer',
        'customer_id'       => 'integer',
        'profile_name'      => 'string',
        'relationship_type' => CustomerRelationshipType::class,
        'arrival_status'    => CustomerArrivalStatus::class,
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /**
     * Get the customer that owns the profile.
     *
     * @return BelongsTo<Customer, covariant $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the queue entries associated with this customer profile.
     *
     * @return HasMany<QueueEntry, covariant $this>
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Get the live queue entries associated with this customer profile.
     *
     * @return HasMany<LiveQueue, covariant $this>
     */
    public function liveQueues(): HasMany
    {
        return $this->hasMany(LiveQueue::class);
    }
}
