<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberAddress extends Model
{
    /** @use HasFactory<\Database\Factories\BarberAddressFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'barber_addresses';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barber_id',
        'postal_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'           => 'integer',
        'barber_id'    => 'integer',
        'postal_code'  => 'string',
        'street'       => 'string',
        'number'       => 'string',
        'complement'   => 'string',
        'neighborhood' => 'string',
        'city'         => 'string',
        'state'        => 'string',
        'country'      => 'string',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Obtém o barbeiro associado ao endereço.
     *
     * @return BelongsTo<Barber, covariant $this>
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
