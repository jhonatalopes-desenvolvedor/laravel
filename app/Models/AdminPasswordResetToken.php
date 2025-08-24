<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPasswordResetToken extends Model
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'admin_password_reset_tokens';

    /**
     * Indica se o modelo deve ter timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Os atributos que podem ser preenchidos em massa.
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
     * Os atributos que devem ter tipo definido (cast).
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
     * Obtém a empresa associada ao token.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtém o administrador ao qual o token pertence.
     *
     * @return BelongsTo<Admin, covariant $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'email', 'email');
    }
}
