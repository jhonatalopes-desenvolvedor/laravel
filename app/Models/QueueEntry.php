<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\QueueEntryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class QueueEntry extends Model
{
    /** @use HasFactory<\Database\Factories\QueueEntryFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'queue_entries';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'barber_id',
        'customer_profile_id',
        'status',
        'entered_at',
        'started_at',
        'finished_at',
        'total_amount_charged',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                   => 'integer',
        'company_id'           => 'integer',
        'barber_id'            => 'integer',
        'customer_profile_id'  => 'integer',
        'status'               => QueueEntryStatus::class,
        'entered_at'           => 'datetime',
        'started_at'           => 'datetime',
        'finished_at'          => 'datetime',
        'total_amount_charged' => 'float',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    /**
     * Obtém a empresa associada a este registro de fila.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtém o barbeiro associado a este registro de fila.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Obtém o perfil do cliente associado a este registro de fila.
     *
     * @return BelongsTo<CustomerProfile, covariant $this>
     */
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    /**
     * Os serviços solicitados neste registro de fila.
     *
     * @return BelongsToMany<Service, covariant $this, Pivot>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'queue_entry_service')
            ->withPivot('actual_duration_minutes', 'price_at_service');
    }

    /**
     * Obtém a fila ativa associada a este registro histórico.
     *
     * @return HasOne<LiveQueue, covariant $this>
     */
    public function liveQueue(): HasOne
    {
        return $this->hasOne(LiveQueue::class);
    }
}
