<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Aporte extends Model
{
    protected $table = 'aportes';
    protected $fillable = [     
        'bonificacion_incentivo',
        'bonificacion_docto_37_2001',
        'reintegros',
        'seguro_universitario',
        'afp_prevision',
        'afp_prevision_pnvs',
        'afp_aporte_solidario',
        'provision_aguinaldo',
        'prevision_indemnizacion',
        'afp_6_75',
        'INATEC',
        'seguridad_social_patronal',
        'total_aporte_25_5',
        'total_carga_patronal',
        'total_aportes',
        'empleado_id',
        'planilla_id'
    ];
    
    public $timestamps = false;

    public function empleado()
    {
        return $this->belongsTo('Vanguard\Empleado_planilla_normal');
    }
     public function planilla()
    {
        return $this->belongsTo('Vanguard\Planilla');
    }
}
