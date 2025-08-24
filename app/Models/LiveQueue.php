<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\CustomerArrivalStatus;
use App\Enums\LiveQueueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveQueue extends Model
{
    /** @use HasFactory<\Database\Factories\LiveQueueFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'live_queues';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'queue_entry_id',
        'barber_id',
        'company_id',
        'customer_profile_id',
        'current_status',
        'customer_arrival_status',
        'estimated_service_duration_minutes',
        'estimated_wait_time_minutes',
        'queue_order',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                                 => 'integer',
        'queue_entry_id'                     => 'integer',
        'barber_id'                          => 'integer',
        'company_id'                         => 'integer',
        'customer_profile_id'                => 'integer',
        'current_status'                     => LiveQueueStatus::class,
        'customer_arrival_status'            => CustomerArrivalStatus::class,
        'estimated_service_duration_minutes' => 'integer',
        'estimated_wait_time_minutes'        => 'integer',
        'queue_order'                        => 'integer',
        'created_at'                         => 'datetime',
        'updated_at'                         => 'datetime',
    ];

    /**
     * Obtém o registro histórico de fila associado a esta fila ativa.
     *
     * @return BelongsTo<QueueEntry, covariant $this>
     */
    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class);
    }

    /**
     * Obtém o barbeiro associado a esta fila ativa.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Obtém a empresa associada a esta fila ativa.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtém o perfil do cliente associado a esta fila ativa.
     *
     * @return BelongsTo<CustomerProfile, covariant $this>
     */
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }
}
