<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyHoliday extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_holidays';

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'date'        => 'date',
        'description' => 'string',
        'is_closed'   => 'boolean',
        'open_time'   => 'time',
        'close_time'  => 'time',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Get the company that owns the holiday.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
