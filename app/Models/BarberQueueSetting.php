<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\BarberQueueState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberQueueSetting extends Model
{
    /** @use HasFactory<\Database\Factories\BarberQueueSettingFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'barber_queues_settings';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barber_id',
        'queue_state',
        'max_capacity',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
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
     * Obtém o barbeiro associado às configurações da fila.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
