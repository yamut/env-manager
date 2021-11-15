<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Environment
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EnvironmentVariable[] $environmentVariables
 * @property-read int|null $environment_variables_count
 * @method static \Illuminate\Database\Eloquent\Builder|Environment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Environment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Environment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Environment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Environment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Environment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Environment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Environment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function environmentVariables(): HasMany
    {
        return $this->hasMany(EnvironmentVariable::class);
    }
}
