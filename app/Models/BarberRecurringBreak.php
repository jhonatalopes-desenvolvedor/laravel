<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberRecurringBreak extends Model
{
    /** @use HasFactory<\Database\Factories\BarberRecurringBreakFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'barber_recurring_breaks';

    /**
     * Os atributos que podem ser preenchidos em massa.
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
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'               => 'integer',
        'barber_id'        => 'integer',
        'day_of_week'      => 'integer',
        'break_start_time' => 'datetime:H:i:s',
        'break_end_time'   => 'datetime:H:i:s',
        'reason'           => 'string',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Obtém o barbeiro associado ao intervalo recorrente.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
