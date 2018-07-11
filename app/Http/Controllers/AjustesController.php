<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;

use Vanguard\Http\Requests;

use Entrust;
use Vanguard\Oficina;
use Vanguard\Aporte;
use Vanguard\Deduccion;
use Vanguard\Acumulado;
use Vanguard\User;
use Vanguard\Pais;
use Vanguard\Campo;
use Vanguard\Cargo;
use Vanguard\Profesion;
use Vanguard\Motivo_permiso;
class AjustesController extends Controller
{	
	public function __construct()
    {
        $this->middleware('auth');
        
    }
   public function index($value='')
   {
   	$pais=auth()->user()->oficina->pais;
        //dd($pais->id);
       	$cargos=Cargo::orderBy('cargo')->get();
        $profesiones=Profesion::orderBy('profesion')->get();
        $motivos=Motivo_permiso::orderBy('motivo')->get();
        //dd($motivos);
        if(Entrust::hasRole('Administradora')){
            $paises=Pais::where('id',auth()->user()->oficina->pais->id)->get();      
            $oficinas=Oficina::where('id',auth()->user()->oficina->id)->get();      
        }else{
           	$paises=Pais::get();           
           	$oficinas=Oficina::get();           
        }

        return view('ajustes.ajustes',compact('paises','oficinas','cargos','profesiones','motivos'));
        //dd($Guatemala);
        
    }
    public function store(Request $requests)
    {
        
        $pais=auth()->user()->oficina->pais;
        $cargos=Cargo::get();;
        $profesiones=Profesion::get();
        $motivos=Motivo_permiso::get();
        if(Entrust::hasRole('Administradora')){
            $paises=Pais::where('id',auth()->user()->oficina->pais->id)->get();      
            $oficinas=Oficina::where('id',auth()->user()->oficina->id)->get();      
        }else{
           	$paises=Pais::get();           
           	$oficinas=Oficina::get();           
        }
        foreach ($paises as $pais) {          
           
  
            //Visibilidad de campo           
            if (isset($requests->pais[$pais->id]['campo_deducciones'])) {               
                $campo_deducciones=$requests->pais[$pais->id]['campo_deducciones'];
                $pais->campo_deducciones= implode(',', $campo_deducciones);
            } else{
                $pais->campo_deducciones="";
            }
            
            
              
            //Porcentajes
            $pais->moneda_simbolo=$requests->pais[$pais->id]['moneda_simbolo'];
            $pais->moneda_nombre=$requests->pais[$pais->id]['moneda_nombre'];
            $pais->porcentaje_pension=$requests->pais[$pais->id]['porcentaje_pension'];
            
            $pais->tipo_seguridad_social=$requests->pais[$pais->id]['tipo_seguridad_social'];
            $pais->porcentaje_seguridad_social=$requests->pais[$pais->id]['porcentaje_seguridad_social'];
            
            $pais->tipo_seguridad_social_p=$requests->pais[$pais->id]['tipo_seguridad_social_p'];
            $pais->seguridad_social_p=$requests->pais[$pais->id]['seguridad_social_p'];

            $pais->n_horas=$requests->pais[$pais->id]['n_horas'];
            $pais->n_dias=$requests->pais[$pais->id]['n_dias'];

            $pais->vacaciones=$requests->pais[$pais->id]['vacaciones'];

            $pais->save();

            //Nombre campos
            
            if ($pais->campo) {
                $campo=$pais->campo;
            }else{
                $campo=new Campo;
                $campo->pais()->associate($pais->id);
            }
            $campo->salario_base=$requests->campo[$pais->id]['salario_base'];
            $campo->ajustes=$requests->campo[$pais->id]['ajustes'];
            $campo->total_salario=$requests->campo[$pais->id]['total_salario'];
            $campo->catorceavo=$requests->campo[$pais->id]['catorceavo'];
            $campo->prestamo=$requests->campo[$pais->id]['prestamo'];
            $campo->interes=$requests->campo[$pais->id]['interes'];
            $campo->otras_deducciones=$requests->campo[$pais->id]['otras_deducciones'];
            $campo->impuestos=$requests->campo[$pais->id]['impuestos'];
            $campo->total_deducciones=$requests->campo[$pais->id]['total_deducciones'];
            $campo->seguridad_social=$requests->campo[$pais->id]['seguridad_social'];
            $campo->seguridad_social_patronal=$requests->campo[$pais->id]['seguridad_social_p'];
            $campo->liquido=$requests->campo[$pais->id]['liquido'];     
            $campo->acumulado_aguinaldo=$requests->campo[$pais->id]['acumulado_aguinaldo'];    
            $campo->acumulado_indemnizacion=$requests->campo[$pais->id]['acumulado_indemnizacion'];    
                   
            $campo->save();               

        }

        foreach ($oficinas as $oficina ) {
        	 $oficina->telf=$requests->oficina[$oficina->id]['telf'];	
        	 $oficina->nit=$requests->oficina[$oficina->id]['nit'];	
        	 $oficina->num_patronal=$requests->oficina[$oficina->id]['num_patronal'];	
        	 $oficina->direccion=$requests->oficina[$oficina->id]['direccion'];	
        	 $oficina->save();
        }
        
        foreach ($cargos as $cargo) {
        	 $cargo->cargo=$requests->cargo[$cargo->id]['cargo'];
			     $cargo->save();
        	
        }
        foreach ($profesiones as $profesion) {
        	 $profesion->profesion=$requests->profesion[$profesion->id]['profesion'];
			     $profesion->save();        	
        }

        foreach ($profesiones as $profesion) {
           $profesion->profesion=$requests->profesion[$profesion->id]['profesion'];
           $profesion->save();          
        }
        foreach ($motivos as $motivo) {
           $motivo->motivo=$requests->motivo_permiso[$motivo->id]['motivo'];
           $motivo->save();          
        }
        

        
        return redirect()->route('ajustes')
            ->withSuccess('Ajustes guardados con exito'); 
    } 
    public function delete_cargo($id)
    {
       	$cargo=Cargo::find($id);
       	
       	if ($id==21) {
       		return redirect()->route('ajustes')
            ->withErrors('No se puede eliminar el cargo: "Sin cargo" ');
       	}
       	
       	if ($cargo->user) {
       		
       		foreach ($cargo->user as $user) {
       			$user->cargo_id=21;
       			$user->save();       				
       		}
       	}
   		$cargo->delete();

		return redirect()->route('ajustes')
            ->withSuccess('Cargo eliminado con exito'); 
    }  
    public function delete_profesion($id)
    {
       	$profesion=Profesion::find($id);
       	
       	if ($id==21) {
       		return redirect()->route('ajustes')
            ->withErrors('No se puede eliminar la Profesion: "Sin profesion" ');
       	}
       	
       	if ($profesion->user) {
       		
       		foreach ($profesion->user as $user) {
       			$user->profesion_id=21;
       			$user->save();       				
       		}
       	}
   		  $profesion->delete();

		    return redirect()->route('ajustes')
            ->withSuccess('Profesion eliminada con exito'); 
    } 
    public function delete_motivo($id)
    {
        $motivo=Motivo_permiso::find($id)->delete();

        return redirect()->route('ajustes')
                ->withSuccess('Motivo de Permiso eliminada con exito'); 
    }
    function create_cargo_profesion(Request $requests)
    {
      	switch ($requests->tipo) {
      		case 'cargo':
      			$cargo=new Cargo;      			
      			$cargo->cargo=$requests->cargo;
      			$cargo->save();
            $mensaje="El Cargo $requests->cargo ";
      			break;
      		case 'profesion':
      			$profesion=new Profesion;
      			$profesion->profesion=$requests->profesion;
      			$profesion->save();
            $mensaje="La Profesion $requests->profesion ";
      			break;
          case 'motivo_permiso':
            $motivo=new Motivo_permiso;
            $motivo->motivo=$requests->motivo_permiso;
            $motivo->save();
            $mensaje="El motivo de permiso $requests->motivo_permiso ";
            break;       		
      	}
      	
      	return redirect()->route('ajustes')
            ->withSuccess("$mensaje a sido agregado con exito"); 
      	
    }  
}
