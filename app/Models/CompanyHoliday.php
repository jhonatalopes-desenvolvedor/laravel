<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyHoliday extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyHolidayFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'company_holidays';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'date',
        'description',
        'is_closed',
        'open_time',
        'close_time',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'date'        => 'date',
        'description' => 'string',
        'is_closed'   => 'boolean',
        'open_time'   => 'datetime:H:i:s',
        'close_time'  => 'datetime:H:i:s',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Obtém a empresa associada ao feriado.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
