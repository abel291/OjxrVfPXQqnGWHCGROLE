<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;

use Vanguard\Http\Requests;
use Entrust;
use Mail;
use Vanguard\User;
use Vanguard\Vacaciones;

class VacacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');       
    }
    public function index()
    {
    	$vacaiones=Vacaciones::orderBy('id','desc')->get();
    	$users="";
    	$oficina_id=auth()->user()->oficina_id;
    	
        if (Entrust::hasRole('Administradora')) {
            $users=User::orderBy('first_name','asc')->where('oficina_id',$oficina_id)->get();
            //dd($users->get());
    	 	$vacaciones=Vacaciones::orderBy('id','desc')->where('oficina_id',$oficina_id)->get();

    	}elseif (Entrust::hasRole(['Coordinadora','Directora','Contralora','Admin'])) {

            $vacaciones=Vacaciones::orderBy('id','desc')->get();          

        }elseif (Entrust::hasRole('Colega')) {

            $vacaciones=Vacaciones::orderBy('id','desc')->where('user_id',auth()->user()->id)->get();        

        }
       // dd($permisos->first()->created_at);
    	return view('vacaciones.list',compact('vacaciones','users'));
    }
    public function create(Request $requests)
    {       
       
        $edit=false;

        if (Entrust::hasRole('Administradora')) {

            $user=User::find($requests->id);

        }else{

            $user=auth()->user();

        }

        $pais=$user->oficina->pais;

        
        $user->acumulado_vacaciones=round( ($user->acumulado_vacaciones+count($user->planilla))*$pais->vacaciones,2);
        
        $solicitudes_vacaciones=Vacaciones::whereYear('created_at','=',date('Y'))->where('user_id',$user->id)->get();
        
        if (count($solicitudes_vacaciones)) {
            
          $user->acumulado_vacaciones=round($user->acumulado_vacaciones-$solicitudes_vacaciones->sum('num_dh'),2);
          
          if($user->acumulado_vacaciones<=0)
          return redirect()->route('vacaciones.list')
            ->withErrors("Al parecer al empleado $user->first_name $user->last_name no le quedan mas dias de vacaciones por es te año");         
        }
        $aprobacion_directora=0;      

        return view('vacaciones.create',compact('user','edit','aprobacion_directora'));   	
    	
    }
    public function store(Request $requests)
    {
    	if($requests->vacaciones_id) {
            $vacaciones=Vacaciones::find($requests->vacaciones_id);
             if ($vacaciones->aprobacion_directora!=0) {
               return redirect()->route('vacaciones.list')
                 ->withErrors("Las solicitudes de vacaciones ya aprobadas no pueden ser editadas"); 
            }
            $vacaciones= $vacaciones->fill($requests->all());
            $mensaje='Solicitud de vacaciones modificada con exito';
        }else{
            $vacaciones=(new Vacaciones)->fill($requests->all());
            $mensaje='Solicitud de vacaciones creada con exito';

        }
        //$vacaciones->dh='dias';
        //$vacaciones->num_dh=count(explode(",", $requests->fechas));        
    	
    	$vacaciones->save();

    	return redirect()->route('vacaciones.list')
            ->withSuccess($mensaje);    
    	
    }

    public function edit($id)
    {
        $vacaciones=Vacaciones::find($id);        
        $user=$vacaciones->user;        
        $edit=true;  
        $pais=$user->oficina->pais;    
        $user->acumulado_vacaciones=round(( $user->acumulado_vacaciones+count($user->planilla) )*$pais->vacaciones,2);
        $aprobacion_directora=$vacaciones->aprobacion_directora;
        return view('vacaciones.create',compact('edit','user','vacaciones','aprobacion_directora')); 

    }
    public function delete($id)
    {
        $vacaciones=Vacaciones::find($id);
        if ($vacaciones->aprobacion_directora!=0) {
            return redirect()->route('vacaciones.list')
            ->withErrors("Las solicitudes de vacaciones ya aprobadas no pueden ser eliminados"); 
           
        }

        $vacaciones->delete();
        return redirect()->route('vacaciones.list')
        ->withSuccess("Solicitud de vacaciones borrada con exito"); 

    }
    public function aprobacion(Request $requests, $id)
    {
        
        $vacaciones=Vacaciones::find($id);
        $user = $vacaciones->user;
       
        $vacaciones->aprobacion_directora=$requests->aprobacion;

        if ($requests->aprobacion==1) {
            $status="<label style='color: green;'><b>Su solicitud de vacaciones ha sido aprobada con exito </b></label>";   
        }elseif ($requests->aprobacion==2) {
           $status="<label style='color: red;'><b>Su solicitud de vacaciones ha sido rechazada</b></label>";
        }
        
        $data = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'contrato' => $user->n_contrato,
            'cargo' => $user->cargo->cargo,
            'oficina' => $user->oficina->oficina,
            'fecha_vacaciones' => explode(',', $vacaciones->fechas),
            'tiempo_vacaciones' => $vacaciones->num_dh,
            'dh' => $vacaciones->dh,
            'status'=>$status,
            'tipo' => 'vacaciones',
        );

        //return view('emails.aprobacion.vacaciones_permiso',$data);
        Mail::send('emails.aprobacion.vacaciones_permiso', $data, function ($message) use ($user) {
            $message->from('notificacion@weeffect-podeeir.org', "WE EFFECT");
            $message->subject('Solicitud de Vacaciones');
            $message->to($user->email,"$user->first_name $user->last_name");
        });    
       
        $vacaciones->save();        
        
        if ($requests->aprobacion==1) {
           return redirect()->route('vacaciones.list')
            ->withSuccess("Aprobada la solicitud de vacaciones de:  ".$vacaciones->user->first_name." ".$vacaciones->user->last_name);   
        }elseif ($requests->aprobacion==2) {
           return redirect()->route('vacaciones.list')
            ->withErrors("Rechazada la solicitud de vacaciones de:  ".$vacaciones->user->first_name." ".$vacaciones->user->last_name); 
        }
        
    
    }
}
