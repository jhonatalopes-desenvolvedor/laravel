<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\BarberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Auth\User;

class Barber extends User
{
    /** @use HasFactory<\Database\Factories\BarberFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'barbers';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'uuid',
        'is_active',
        'first_name',
        'last_name',
        'cpf',
        'phone_number',
        'email',
        'password',
        'current_status',
        'last_login_at',
    ];

    /**
     * Os atributos que devem ser ocultados durante a serialização.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'             => 'integer',
        'uuid'           => 'string',
        'company_id'     => 'integer',
        'is_active'      => 'boolean',
        'first_name'     => 'string',
        'last_name'      => 'string',
        'cpf'            => 'string',
        'phone_number'   => 'string',
        'email'          => 'string',
        'password'       => 'hashed',
        'current_status' => BarberStatus::class,
        'last_login_at'  => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Obtém a empresa à qual o barbeiro pertence.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtém as configurações de fila do barbeiro.
     *
     * @return HasOne<BarberQueueSetting, covariant $this>
     */
    public function queueSettings(): HasOne
    {
        return $this->hasOne(BarberQueueSetting::class);
    }

    /**
     * Obtém o endereço associado ao barbeiro.
     *
     * @return HasOne<BarberAddress, covariant $this>
     */
    public function address(): HasOne
    {
        return $this->hasOne(BarberAddress::class);
    }

    /**
     * Obtém os horários recorrentes do barbeiro.
     *
     * @return HasMany<BarberRecurringSchedule, covariant $this>
     */
    public function recurringSchedules(): HasMany
    {
        return $this->hasMany(BarberRecurringSchedule::class);
    }

    /**
     * Obtém os intervalos recorrentes do barbeiro.
     *
     * @return HasMany<BarberRecurringBreak, covariant $this>
     */
    public function recurringBreaks(): HasMany
    {
        return $this->hasMany(BarberRecurringBreak::class);
    }

    /**
     * Obtém os horários específicos do barbeiro.
     *
     * @return HasMany<BarberSpecificSchedule, covariant $this>
     */
    public function specificSchedules(): HasMany
    {
        return $this->hasMany(BarberSpecificSchedule::class);
    }

    /**
     * Obtém os intervalos específicos do barbeiro.
     *
     * @return HasMany<BarberSpecificBreak, covariant $this>
     */
    public function specificBreaks(): HasMany
    {
        return $this->hasMany(BarberSpecificBreak::class);
    }

    /**
     * Retorna os serviços que o barbeiro pode realizar.
     *
     * @return BelongsToMany<Service, covariant $this, Pivot>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'barber_service')
            ->withPivot('duration_minutes', 'price');
    }

    /**
     * Obtém os registros históricos de filas do barbeiro.
     *
     * @return HasMany<QueueEntry, covariant $this>
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Obtém as filas ativas do barbeiro.
     *
     * @return HasMany<LiveQueue, covariant $this>
     */
    public function liveQueues(): HasMany
    {
        return $this->hasMany(LiveQueue::class);
    }

    /**
     * Obtém os tokens de redefinição de senha do barbeiro.
     *
     * @return HasMany<BarberPasswordResetToken, covariant $this>
     */
    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(BarberPasswordResetToken::class, 'email', 'email');
    }
}
