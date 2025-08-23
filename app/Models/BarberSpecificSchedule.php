<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberSpecificSchedule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barber_specific_schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barber_id',
        'date',
        'start_time',
        'end_time',
        'is_working_day',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'             => 'integer',
        'barber_id'      => 'integer',
        'date'           => 'date',
        'start_time'     => 'time',
        'end_time'       => 'time',
        'is_working_day' => 'boolean',
        'reason'         => 'string',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Get the barber that owns the specific schedule.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
