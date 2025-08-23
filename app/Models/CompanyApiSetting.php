<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyApiSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_api_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'whatsapp_token_access',
        'whatsapp_token_verify',
        'whatsapp_phone_number_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'                       => 'integer',
        'company_id'               => 'integer',
        'whatsapp_token_access'    => 'string',
        'whatsapp_token_verify'    => 'string',
        'whatsapp_phone_number_id' => 'string',
        'created_at'               => 'datetime',
        'updated_at'               => 'datetime',
    ];

    /**
     * Get the company that owns the API settings.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
