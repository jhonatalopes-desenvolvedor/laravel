<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyApiSetting extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyApiSettingFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'company_api_settings';

    /**
     * Os atributos que podem ser preenchidos em massa.
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
     * Os atributos que devem ter tipo definido (cast).
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
     * Obtém a empresa associada às configurações de API.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
