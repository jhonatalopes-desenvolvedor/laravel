<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPasswordResetToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_password_reset_tokens';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'company_id',
        'token',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'         => 'integer',
        'company_id' => 'integer',
        'token'      => 'string',
        'created_at' => 'datetime',
    ];

    /**
     * Get the company associated with the token.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the admin that the token belongs to.
     *
     * @return BelongsTo<Admin, covariant $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'email', 'email');
    }
}
