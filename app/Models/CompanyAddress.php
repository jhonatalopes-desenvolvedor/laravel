<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_addresses';

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be cast.
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
     * Get the company that owns the address.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
