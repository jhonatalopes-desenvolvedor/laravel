<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'uuid',
        'language_code',
        'first_name',
        'last_name',
        'email',
        'password',
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
        'id'            => 'integer',
        'uuid'          => 'string',
        'language_code' => 'string',
        'first_name'    => 'string',
        'last_name'     => 'string',
        'email'         => 'string',
        'password'      => 'hashed',
        'last_login_at' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Get the company that the admin belongs to.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the password reset tokens for the admin.
     *
     * @return HasMany<AdminPasswordResetToken, covariant $this>
     */
    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(AdminPasswordResetToken::class, 'email', 'email');
    }
}
