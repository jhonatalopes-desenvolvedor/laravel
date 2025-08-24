<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\CompanyOperationalStatus;
use App\Enums\CompanySaaSStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'saas_status',
        'operational_status',
        'name',
        'domain',
        'email',
        'language_code',
        'timezone',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                 => 'integer',
        'saas_status'        => CompanySaaSStatus::class,
        'operational_status' => CompanyOperationalStatus::class,
        'name'               => 'string',
        'domain'             => 'string',
        'email'              => 'string',
        'language_code'      => 'string',
        'timezone'           => 'string',
    ];

    /**
     * Obtém o endereço associado à empresa.
     *
     * @return HasOne<CompanyAddress, covariant $this>
     */
    public function address(): HasOne
    {
        return $this->hasOne(CompanyAddress::class);
    }

    /**
     * Obtém as configurações de API associadas à empresa.
     *
     * @return HasOne<CompanyApiSetting, covariant $this>
     */
    public function apiSettings(): HasOne
    {
        return $this->hasOne(CompanyApiSetting::class);
    }

    /**
     * Obtém os horários de funcionamento da empresa.
     *
     * @return HasMany<CompanyOperatingHour, covariant $this>
     */
    public function operatingHours(): HasMany
    {
        return $this->hasMany(CompanyOperatingHour::class);
    }

    /**
     * Obtém os feriados da empresa.
     *
     * @return HasMany<CompanyHoliday, covariant $this>
     */
    public function holidays(): HasMany
    {
        return $this->hasMany(CompanyHoliday::class);
    }

    /**
     * Obtém os administradores da empresa.
     *
     * @return HasMany<Admin, covariant $this>
     */
    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    /**
     * Obtém os serviços oferecidos pela empresa.
     *
     * @return HasMany<Service, covariant $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Obtém os barbeiros associados à empresa.
     *
     * @return HasMany<Barber, covariant $this>
     */
    public function barbers(): HasMany
    {
        return $this->hasMany(Barber::class);
    }

    /**
     * Obtém os clientes associados à empresa.
     *
     * @return HasMany<Customer, covariant $this>
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Obtém os registros de filas da empresa.
     *
     * @return HasMany<QueueEntry, covariant $this>
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Obtém as filas ativas da empresa.
     *
     * @return HasMany<LiveQueue, covariant $this>
     */
    public function liveQueues(): HasMany
    {
        return $this->hasMany(LiveQueue::class);
    }
}
