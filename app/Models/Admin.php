<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;

class Admin extends User
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'admins';

    /**
     * Os atributos que podem ser preenchidos em massa.
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
     * Obtém a empresa à qual o administrador pertence.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtém os tokens de redefinição de senha do administrador.
     *
     * @return HasMany<AdminPasswordResetToken, covariant $this>
     */
    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(AdminPasswordResetToken::class, 'email', 'email');
    }
}
