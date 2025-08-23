<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberRecurringSchedule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barber_recurring_schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barber_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_working_day',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'             => 'integer',
        'barber_id'      => 'integer',
        'day_of_week'    => 'integer',
        'start_time'     => 'time',
        'end_time'       => 'time',
        'is_working_day' => 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Get the barber that owns the recurring schedule.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
