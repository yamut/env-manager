<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EnvironmentVariable
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $environment_id
 * @property string $name
 * @property string $value
 * @property-read \App\Models\Environment|null $environment
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable query()
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable whereEnvironmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnvironmentVariable whereValue($value)
 * @mixin \Eloquent
 */
class EnvironmentVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'environment_id',
    ];

    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class);
    }

    public function getValueAttribute(): string
    {
        return $this->transformValue($this->attributes['value']);
    }

    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = $this->transformValue($value);
    }

    private function transformValue($value)
    {
        if (is_null($value)) {
            return '';
        } elseif ($value === true) {
            return 'true';
        }
        return strval($value);
    }
}
