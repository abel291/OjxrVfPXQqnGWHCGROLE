<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'pais',
        'color',
        'moneda_simbolo',
        'moneda_nombre',
        'porcentaje_seguridad_social',
        'tipo_seguridad_social',
        'seguridad_social_p',
        'tipo_seguridad_social_p',
        'n_horas',
        'n_dias',
        'vacaciones',
        'porcentaje_pension',
        'pago_indemnizacion',
        'pago_pension',
        'campo_deducciones',
    ];
    public function oficina()
    {
    	return $this->belongsTo('Vanguard\Oficina');
    }

    public function campo()  
    {
        return $this->hasOne('Vanguard\Campo');
    }

     public function feriados()
    {
        return $this->hasMany('App\Feriado');
    }
}
