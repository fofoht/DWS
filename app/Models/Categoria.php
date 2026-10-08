<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
class Categoria extends Model
{
      protected $table = 'categorias';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'descripcion',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function empresa(): BelongsTo
{
    return $this->belongsTo(Empresa::class);
}
}
