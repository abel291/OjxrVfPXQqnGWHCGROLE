<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $primaryKey = 'id';

    protected $fillable = ['cargo'];
   
    public $timestamps = false;
   
    public function user()
    {
    	return $this->hasMany('Vanguard\User');
   	}

   	public function empleado_planilla_normal()	
   	{
   		return $this->belongsTo('App\Empleado_planilla_normal');
   	}
}
