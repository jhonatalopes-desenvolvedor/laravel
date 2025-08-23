<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberSpecificBreak extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barber_specific_breaks';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barber_id',
        'break_start_at',
        'break_end_at',
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
        'break_start_at' => 'datetime',
        'break_end_at'   => 'datetime',
        'reason'         => 'string',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Get the barber that owns the specific break.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
