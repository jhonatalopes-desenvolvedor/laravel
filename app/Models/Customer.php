<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'phone_number',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'           => 'integer',
        'company_id'   => 'integer',
        'phone_number' => 'string',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Obtém a empresa à qual o cliente pertence.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtém os perfis associados ao cliente.
     *
     * @return HasMany<CustomerProfile, covariant $this>
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(CustomerProfile::class);
    }
}
