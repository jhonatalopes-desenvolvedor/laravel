<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberSpecificBreak extends Model
{
    /** @use HasFactory<\Database\Factories\BarberSpecificBreakFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'barber_specific_breaks';

    /**
     * Os atributos que podem ser preenchidos em massa.
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
     * Os atributos que devem ter tipo definido (cast).
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
     * Obtém o barbeiro associado ao intervalo específico.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
