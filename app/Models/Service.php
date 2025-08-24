<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'services';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'is_active',
        'name',
        'description',
        'duration_minutes',
        'price',
    ];

    /**
     * Os atributos que devem ter tipo definido (cast).
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'is_active'        => 'boolean',
        'name'             => 'string',
        'description'      => 'string',
        'duration_minutes' => 'integer',
        'price'            => 'float',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Obtém a empresa que oferece este serviço.
     *
     * @return BelongsTo<Company, covariant $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtém os barbeiros que podem realizar este serviço.
     *
     * @return BelongsToMany<Barber, covariant $this, Pivot>
     */
    public function barbers(): BelongsToMany
    {
        return $this->belongsToMany(Barber::class, 'barber_service')
            ->withPivot('duration_minutes', 'price');
    }

    /**
     * Obtém os registros de fila em que este serviço foi solicitado.
     *
     * @return BelongsToMany<QueueEntry, covariant $this, Pivot>
     */
    public function queueEntries(): BelongsToMany
    {
        return $this->belongsToMany(QueueEntry::class, 'queue_entry_service')
            ->withPivot('actual_duration_minutes', 'price_at_service');
    }
}
