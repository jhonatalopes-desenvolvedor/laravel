<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\CustomerArrivalStatus;
use App\Enums\CustomerRelationshipType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProfile extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerProfileFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'customer_profiles';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'profile_name',
        'relationship_type',
        'arrival_status',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                => 'integer',
        'customer_id'       => 'integer',
        'profile_name'      => 'string',
        'relationship_type' => CustomerRelationshipType::class,
        'arrival_status'    => CustomerArrivalStatus::class,
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /**
     * Obtém o cliente associado ao perfil.
     *
     * @return BelongsTo<Customer, covariant $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Obtém os registros de fila associados a este perfil de cliente.
     *
     * @return HasMany<QueueEntry, covariant $this>
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Obtém as filas ativas associadas a este perfil de cliente.
     *
     * @return HasMany<LiveQueue, covariant $this>
     */
    public function liveQueues(): HasMany
    {
        return $this->hasMany(LiveQueue::class);
    }
}
