<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contratos';

    protected $primaryKey = 'id';    
    
    protected $fillable = [
    	'n_contrato',
    	'fecha',    	
    	'consultoria',
    	'objetivo',
    	'alcance',
    	'actividades',
    	'metodologia',
    	'fecha_inicio',
    	'fecha_fin',
    	'monto_total',
        'monto_total_l',
        'cumplimiento',    	
    	'productos',
    	'fecha_contrato',
        'status',
        'aprobacion_coordinadora',
        'fecha_aprobacion_coordinadora',
        'aprobacion_directora',
    	'fecha_aprobacion_directora',
        'user_id',
        'oficina_id',
        'fecha_contrato',
    ];

    public function user()
    {
    	return $this->belongsTo('Vanguard\User');
    }

    public function oficina()
    {
    	return $this->belongsTo('Vanguard\Oficina');
    }
    public function pagos()
    {
        return $this->hasMany('Vanguard\Pagos_contrato');
    }
    public function documentos()
    {
        return $this->hasMany('Vanguard\Upload_documento');
    }
    public function adendas()
    {
        return $this->hasMany('Vanguard\Adenda');
    }
    public function coordinadora()
    {
        return $this->belongsTo('Vanguard\User');
    }

    public function directora()
    {
        return $this->belongsTo('Vanguard\User');
    }
}
