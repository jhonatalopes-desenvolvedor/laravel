<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\CompanyOperationalStatus;
use App\Enums\CompanySaaSStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    /**
     * Summary of table
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be cast.
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
     * Get the address associated with the company.
     *
     * @return HasOne<CompanyAddress, covariant $this>
     */
    public function address(): HasOne
    {
        return $this->hasOne(CompanyAddress::class);
    }

    /**
     * Get the API settings associated with the company.
     *
     * @return HasOne<CompanyApiSetting, covariant $this>
     */
    public function apiSettings(): HasOne
    {
        return $this->hasOne(CompanyApiSetting::class);
    }

    /**
     * Get the operating hours for the company.
     *
     * @return HasMany<CompanyOperatingHour, covariant $this>
     */
    public function operatingHours(): HasMany
    {
        return $this->hasMany(CompanyOperatingHour::class);
    }

    /**
     * Get the holidays for the company.
     *
     * @return HasMany<CompanyHoliday, covariant $this>
     */
    public function holidays(): HasMany
    {
        return $this->hasMany(CompanyHoliday::class);
    }

    /**
     * Get the admins for the company.
     *
     * @return HasMany<Admin, covariant $this>
     */
    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    /**
     * Get the services offered by the company.
     *
     * @return HasMany<Service, covariant $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the barbers associated with the company.
     *
     * @return HasMany<Barber, covariant $this>
     */
    public function barbers(): HasMany
    {
        return $this->hasMany(Barber::class);
    }

    /**
     * Get the customers associated with the company.
     *
     * @return HasMany<Customer, covariant $this>
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the queue entries for the company.
     *
     * @return HasMany<QueueEntry, covariant $this>
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Get the live queue entries for the company.
     *
     * @return HasMany<LiveQueue, covariant $this>
     */
    public function liveQueues(): HasMany
    {
        return $this->hasMany(LiveQueue::class);
    }
}
