<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    use HasFactory;

    protected $table = 'mascotas';

    protected $fillable = [
        'user_id',
        'nombre',
        'especie',
        'raza',
        'edad_meses',
        'peso',
        'foto_url',
    ];

    public function dueno()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'Usuario Desconocido'
        ]);
    }
}