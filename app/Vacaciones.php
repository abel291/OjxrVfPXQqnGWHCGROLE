<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Vacaciones extends Model
{
    protected $table = 'vacaciones';    
    

    protected $fillable = ['fechas','num_dh','dh','aprobacion_directora','user_id','oficina_id',];

   	public function user()
    {
    	return $this->belongsTo('Vanguard\User');
    }

    public function oficina()
    {
    	return $this->belongsTo('Vanguard\Oficina');
    }
}
