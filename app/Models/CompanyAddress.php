<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAddress extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyAddressFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'company_addresses';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'postal_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'google_maps_url',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'              => 'integer',
        'company_id'      => 'integer',
        'postal_code'     => 'string',
        'street'          => 'string',
        'number'          => 'string',
        'complement'      => 'string',
        'neighborhood'    => 'string',
        'city'            => 'string',
        'state'           => 'string',
        'country'         => 'string',
        'google_maps_url' => 'string',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Obtém a empresa associada ao endereço.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
