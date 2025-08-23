<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\BarberStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Barber extends Authenticatable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barbers';

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
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
     * Get the company that the barber belongs to.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the queue settings for the barber.
     *
     * @return HasOne<BarberQueueSetting, covariant $this>
     */
    public function queueSettings(): HasOne
    {
        return $this->hasOne(BarberQueueSetting::class);
    }

    /**
     * Get the address associated with the barber.
     *
     * @return HasOne<BarberAddress, covariant $this>
     */
    public function address(): HasOne
    {
        return $this->hasOne(BarberAddress::class);
    }

    /**
     * Get the recurring schedules for the barber.
     *
     * @return HasMany<BarberRecurringSchedule, covariant $this>
     */
    public function recurringSchedules(): HasMany
    {
        return $this->hasMany(BarberRecurringSchedule::class);
    }

    /**
     * Get the recurring breaks for the barber.
     *
     * @return HasMany<BarberRecurringBreak, covariant $this>
     */
    public function recurringBreaks(): HasMany
    {
        return $this->hasMany(BarberRecurringBreak::class);
    }

    /**
     * Get the specific schedules for the barber.
     *
     * @return HasMany<BarberSpecificSchedule, covariant $this>
     */
    public function specificSchedules(): HasMany
    {
        return $this->hasMany(BarberSpecificSchedule::class);
    }

    /**
     * Get the specific breaks for the barber.
     *
     * @return HasMany<BarberSpecificBreak, covariant $this>
     */
    public function specificBreaks(): HasMany
    {
        return $this->hasMany(BarberSpecificBreak::class);
    }

    /**
     * The services that the barber can perform.
     *
     * @return BelongsToMany<Service, covariant $this, Pivot>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'barber_service')
            ->withPivot('duration_minutes', 'price');
    }

    /**
     * Get the historical queue entries for this barber.
     *
     * @return HasMany<QueueEntry, covariant $this>
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Get the live queue entries for this barber.
     *
     * @return HasMany<LiveQueue, covariant $this>
     */
    public function liveQueues(): HasMany
    {
        return $this->hasMany(LiveQueue::class);
    }

    /**
     * Get the password reset tokens for the barber.
     *
     * @return HasMany<BarberPasswordResetToken, covariant $this>
     */
    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(BarberPasswordResetToken::class, 'email', 'email');
    }
}
