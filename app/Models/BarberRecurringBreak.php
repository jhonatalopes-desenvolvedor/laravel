<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberRecurringBreak extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barber_recurring_breaks';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barber_id',
        'day_of_week',
        'break_start_time',
        'break_end_time',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'               => 'integer',
        'barber_id'        => 'integer',
        'day_of_week'      => 'integer',
        'break_start_time' => 'time',
        'break_end_time'   => 'time',
        'reason'           => 'string',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Get the barber that owns the recurring break.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
