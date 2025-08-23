<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyOperatingHour extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_operating_hours';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'day_of_week' => 'integer',
        'open_time'   => 'time',
        'close_time'  => 'time',
        'is_closed'   => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Get the company that owns the operating hour.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
