<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\BarberQueueState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberQueueSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barber_queues_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barber_id',
        'queue_state',
        'max_capacity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'           => 'integer',
        'barber_id'    => 'integer',
        'queue_state'  => BarberQueueState::class,
        'max_capacity' => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Get the barber that owns the queue settings.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
