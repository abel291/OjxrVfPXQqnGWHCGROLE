<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;

use Vanguard\Http\Requests;
use Entrust;
use Mail;
use Vanguard\Permiso;
use Vanguard\Oficina;
use Vanguard\User;
use Vanguard\Motivo_permiso;

class PermisosAusenciasController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');       
    }
    public function index()
    {    	

    	$permisos=Permiso::orderBy('id','desc')->get();
    	$users="";
    	$oficina_id=auth()->user()->oficina_id;
    	
        if (Entrust::hasRole('Administradora')) {
            $users=User::orderBy('first_name','asc')->where('oficina_id',$oficina_id)->get();
            //dd($users->get());
    	 	$permisos=Permiso::orderBy('id','desc')->where('oficina_id',$oficina_id)->get();

    	}elseif (Entrust::hasRole(['Coordinadora','Directora','Contralora','Admin'])) {

            $permisos=Permiso::orderBy('id','desc')->get();          

        }elseif (Entrust::hasRole('Colega')) {

            $permisos=Permiso::orderBy('id','desc')->where('user_id',auth()->user()->id)->get();        

        }
       // dd($permisos->first()->created_at);
    	return view('permisos.list',compact('permisos','users'));
    }

    public function create(Request $requests)
    {
        $motivo_permiso=Motivo_permiso::get();
        $edit=false;

        if (Entrust::hasRole('Administradora')) {

            $user=User::find($requests->id);

        }else{

            $user=auth()->user();

        }
        $aprobacion_coordinadora=0;
        return view('permisos.create',compact('user','edit','motivo_permiso','aprobacion_coordinadora'));   	
    	
    }
    public function store(Request $requests)
    {
        if($requests->permiso_id) {
            $permiso=Permiso::find($requests->permiso_id);
            if ($permiso->aprobacion_coordinadora!=0) {
               return redirect()->route('permisos.list')
                 ->withErrors("Los permisos ya aprobados no pueden ser editados"); 
            }
            $permiso= $permiso->fill($requests->all());
            $mensaje="Solicitud de permiso o ausencia modificada con exito";
        }else{
            $permiso= (new Permiso)->fill($requests->all());
            $mensaje="Solicitud de Permiso creada con exito";
        }
                
        //dd($permiso);
        $permiso->save();
         return redirect()->route('permisos.list')
            ->withSuccess($mensaje);    
        
    }
    public function edit($id)
    {
        $permiso=Permiso::find($id);        
        $user=$permiso->user;
        $motivo_permiso=Motivo_permiso::get();
        $edit=true;
        $aprobacion_coordinadora=$permiso->aprobacion_coordinadora;
        return view('permisos.create',compact('permiso','edit','user','motivo_permiso','aprobacion_coordinadora')); 

    }
    public function delete($id)
    {
       $permiso=Permiso::find($id);
       if ($permiso->aprobacion_coordinadora!=0) {
            return redirect()->route('permisos.list')
            ->withErrors("Los permisos ya aprobados no pueden ser eliminados");           
       }
       $permiso->delete();
       return redirect()->route('permisos.list')
            ->withSuccess("Permiso borrado con exito"); 

    }
    public function aprobacion(Request $requests, $id)
    {
        
        $permiso=Permiso::find($id);
        $user = $permiso->user;        
        
        $permiso->aprobacion_coordinadora=$requests->aprobacion;

        if ($requests->aprobacion==1) {
            $status="<label style='color: green;'><b>Su solicitud de permiso ha sido aprobada con exito </b></label>";   
        }elseif ($requests->aprobacion==2) {
           $status="<label style='color: red;'><b>Su solicitud de permiso ha sido rechazada</b></label>";
        } 

        $data = array(                
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'contrato' => $user->n_contrato,
            'cargo' => $user->cargo->cargo,
            'oficina' => $user->oficina->oficina,
            'tipo_permiso' => $permiso->tipo,
            'motivo' => $permiso->motivo,
            'fecha_inicio' => $permiso->fecha_inicio,
            'fecha_fin' => $permiso->fecha_fin,
            'num_dh' => $permiso->num_dh,
            'dh' => $permiso->dh,
            'tipo' => 'permiso',
            'status' => $status
        );        

        //return view('emails.aprobacion.vacaciones_permiso',$data);
        Mail::send('emails.aprobacion.vacaciones_permiso', $data, function ($message) use($user) {
            $message->from('notificacion@weeffect-podeeir.org', "WE EFFECT");
            $message->subject('Solicitud de Permiso');
             $message->to($user->email,"$user->first_name $user->last_name");
        });   
        
        $permiso->save();        

        if ($requests->aprobacion==1) {

            return redirect()->route('permisos.list')
            ->withSuccess("Aprobado El permiso de  ".$user->first_name." ".$user->last_name);   
        
        }elseif ($requests->aprobacion==2) {

           return redirect()->route('permisos.list')
            ->withErrors("Rechazado el permiso de  ".$user->first_name." ".$user->last_name); 
        
        } 
       
    
    }

}
