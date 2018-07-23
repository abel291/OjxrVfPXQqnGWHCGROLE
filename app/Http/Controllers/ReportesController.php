<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Entrust;
use Vanguard\Planilla;
use Vanguard\Aporte;
use Vanguard\Deduccion;
use Vanguard\Acumulado;
use Vanguard\User;
use Vanguard\Pais;
use Vanguard\Campo;
use Vanguard\Oficina;
use Vanguard\Empleado_planilla_normal;
use PDF;
use Vanguard\Vacaciones;
use Vanguard\Permiso;
use Vanguard\Contrato;

use Vanguard\Http\Requests;

class ReportesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');       
    }
    public function index()
    {
        if(Entrust::hasRole(['Directora','Coordinadora','Admin','Contralora'])){
            $oficinas=Oficina::get();
        }elseif (Entrust::hasRole('Administradora')) {
            $oficinas[]=auth()->user()->oficina;
        } 
        $paises=Pais::get();       
        
        return view('reportes.reporte',compact('oficinas','paises'));
    }

    public function reporte_planillas(Request $request)
    {          
        
        
        $planillas=Planilla::whereIn('oficina_id',$request->oficinas)->get();
       

        if ($request->confirmada) {
        
            $planillas=$planillas->where('confirmada','1');
                      
        }
        
        
        if (!$request->fecha_todas) {
            $mes_año=$this->fecha_reportes($request);
           $planillas=$planillas->whereIn('m_a', $mes_año); 
        }        
        
        $planillas->fecha_inicio="$request->fecha_inicio";
        $planillas->fecha_fin="$request->fecha_fin";         
        
        //si  no encuentra alguna planilla
        
        if ($planillas->count()==0){
            return redirect()->route('reportes')
            ->withErrors("Al parecer no se encontro ninguna planilla ");
        }
        
                
        if ($request->tipo=="empleados") {
            $empleados_oficinas=array_map('intval', $request->empleados_oficinas);
        }
        
        foreach ($planillas as $planilla) {

            if ($request->tipo=="empleados") {
                $planilla->empleados=$planilla->empleados->whereIn('user_id',$empleados_oficinas);
                
            }
            if (count($request->oficinas)==1) {
                $planilla->cambio_mensual=1;                        
            }else{
                $planilla->cambio_mensual=$planilla->cambio;
            }
        }
        
        
        $oficinas=Oficina::whereIn('id',array_map('intval', $request->oficinas))->get();        
        //dd($oficinas);
        
        //dd($planillas);
        $pdf = PDF::loadView('reportes.planillas.pdf_reporte_'.$request->tipo, compact('planillas','oficinas'))
        ->setPaper('a4', 'landscape');
        
        return $pdf->download("Reporte $request->tipo ".date("d-m-Y H:i:s").'.pdf');
        //return view('reportes.planillas.pdf_reporte_consolidado',['planillas' =>$planillas,'oficinas' =>$oficinas  ] );       
    }

    public function boleta_empleados(Request $request)
    {
       
        $datos=explode(',', $request->user);//0->user_id   1->oficina_id
        
        $m_a=$request->mes.'-'.$request->año;
        $planilla=planilla::where('m_a',"$m_a")->where('oficina_id',$datos[1])->first();
        //dd($planilla);
        if (is_null($planilla)){
            return redirect()->route('reportes')
            ->withErrors("Al parecer no se encontro ninguna planilla ");
        }               
        
        if ($datos[0]=='todos') {
            $empleados=$planilla->empleados;           
        }else{
            $empleados=$planilla->empleados->where('user_id',(integer)$datos[0]);
        }
        
        //dd($empleado);        
        
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        
        $fecha_hoy=$planilla->oficina->oficina." ".$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');
        $pais=auth()->user()->oficina->pais;
        
        //dd(str_contains($pais->campo_deducciones, 'fondo_pension'));

        $pdf = PDF::loadView('reportes.boleta_empleados.pdf_boleta_empleados', 
            compact('planilla','m_a','empleados','fecha_hoy','pais','campo'));
        
        return $pdf->download("Boleta de empleados $m_a.pdf");
        
        //return view('reportes.boleta_empleados.pdf_boleta_empleados', compact('planilla','m_a','empleados','fecha_hoy','pais','campo'));

    }
    public function reportes_vacaciones_permisos(Request $request)
    {
      
        $empleados_oficinas=$request->empleados_oficinas;     
        
        switch ($request->tipo) {
           
            case 'vacaciones':
               $reporte=Vacaciones::whereIn('oficina_id',$request->oficinas)->whereIn('user_id',$empleados_oficinas);
               $campo="aprobacion_directora";
              
               break;

            case 'permisos':
                $reporte=Permiso::whereIn('oficina_id',$request->oficinas)->whereIn('user_id',$empleados_oficinas);
                $campo="aprobacion_coordinadora";
                
               break;

            default:
               # code...
               break;
        }   
        if ($request->confirmada) {
        
            $reporte=$reporte->where($campo,"1");
                      
        }       
        
        if (!$request->fecha_todas) {

            $fecha_inicio=date('Y-m-d H:i:s',strtotime($request->fecha_inicio));
            $fecha_fin=date('Y-m-d H:i:s',strtotime($request->fecha_fin.'-31'));           
            $reporte=$reporte->whereBetween('created_at', [$fecha_inicio, $fecha_fin])->get();  

        } else{
            
            $fecha_inicio="Todos los meses";
            $fecha_fin="";
            $reporte=$reporte->get();

        }
        

        $reporte->fecha_inicio=$fecha_inicio;
        $reporte->fecha_fin=$fecha_fin;
        $oficinas=Oficina::whereIn('id',$request->oficinas)->get();  
        
        $pdf = PDF::loadView('reportes.permiso_vacaciones.pdf_'.$request->tipo,compact('reporte','oficinas'));
        
        return $pdf->download("Reporte $request->tipo ".date('d-m-Y').".pdf");
        
        return view('reportes.permiso_vacaciones.pdf_'.$request->tipo,compact('reporte','oficinas'));     
        
    }
   
    public function ajax_liquidacion(Request $request)
    {
        $datos=explode(',', $request->id);//0->user_id   1->oficina_id
        $id=$datos[0];
        $user=User::find($id);
        $user->cargo;
        $empleado=$user->planilla->last();
        $planilla=$empleado->planilla;
        $pais=$user->oficina->pais;
        $pension=$user->acumulado->sum('pension');
        $catorceavo=$user->acumulado->sum('catorceavo');
        $total_deducciones=$empleado->deduccion->total_deducciones;
       //dd($user->planilla->sortBy('planilla_id')->first());
        
        $user->fecha_ingreso=$this->fecha_string($user->created_at);
        $user->fecha_finalizacion=$this->fecha_string(date('Y-m-d'));
        echo json_encode([
            'user' => $user,
            'planilla' => $planilla,
            'empleado' => $empleado,
            'pais' => $pais,
            'pension' => $pension,
            'catorceavo' => $catorceavo,
            'total_deducciones' => $total_deducciones,

        ]);
    }
     public function boleta_liquidacion(Request $request)
    {
        $user=User::find($request->user); 
        $datos=$request;
        if (count($user->planilla)==0){
            return redirect()->route('reportes')
            ->withErrors("Este usuario no se encontro en ninguna palnilla");
        }
        $user->fecha_ingreso=$this->fecha_string($user->created_at);
        $user->fecha_finalizacion=$this->fecha_string(date('Y-m-d'));       
        

        $pdf = PDF::loadView('reportes.boleta_liquidacion.pdf_liquidacion',compact('user','datos'));

        return $pdf->download("Reporte de liquidacion $user->first_name $user->last_name ".date('d-m-Y').".pdf");

        return view('reportes.boleta_liquidacion.pdf_liquidacion',compact('user','datos'));
    
    }
    public function fecha_string($fecha)
    {
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha_string=date("d", strtotime($fecha))." de ".$meses[date("n", strtotime($fecha))-1]." del ".date("Y", strtotime($fecha));
        return $fecha_string;
         
    }
    public function fecha_reportes($request)
    {
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;
        
        $mes_año=[];
        $meses=[     
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',              
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ];
        if($fecha_inicio==$fecha_fin){
            $Fecha=strtotime($fecha_inicio);
            $mes=date("m",strtotime($fecha_inicio) );
            $mes=$meses[$mes-1];
            $año=date("Y",strtotime($fecha_inicio) );
            array_push($mes_año, $mes.'-'.$año);           
        }
        
        $fechaaamostar = $fecha_inicio;
        while(strtotime($fecha_fin) >= strtotime($fechaaamostar))
        {
            
                //echo "$fechaaamostar<br />";
                
                $mes=date("m",strtotime($fechaaamostar) );
                $mes=$meses[$mes-1];
                $año=date("Y",strtotime($fechaaamostar) );

                array_push($mes_año, $mes.'-'.$año);
                $fechaaamostar = date("Y-M", strtotime($fechaaamostar . " + 1 month"));
            
        }
        return $mes_año;
    }
    public function contratos(Request $request)
    {
        //dd($request->all());
        $contratos=Contrato::whereIn('user_id',$request->empleados_oficinas)
        ->whereIn('oficina_id',$request->oficinas)->orderBy('created_at','desc');   

        
        if ($request->status!="todos") {
            $contratos=$contratos->where('status',"$request->status");
        } 
       //dd($contratos->get())
       ;
        if (!$request->fecha_todas) {

            $fecha_inicio=date('Y-m-d H:i:s',strtotime($request->fecha_inicio));
            $fecha_fin=date('Y-m-d H:i:s',strtotime($request->fecha_fin));           
            $contratos=$contratos->whereBetween('created_at', [$fecha_inicio, $fecha_fin])->get();  
            $rango_fechas=$request->fecha_inicio."-".$request->fecha_fin;
        } else{

            $rango_fechas="Todos los meses";
            $contratos=$contratos->get();

        }
        if (count($contratos)==0){
            return redirect()->route('reportes')
            ->withErrors("Al parecer no se encontro ningun reporte ");
        }

        $contratos->rango_fechas=$rango_fechas;
        $oficinas=Oficina::whereIn('id',$request->oficinas)->get(); 
        //dd($contratos);

        $pdf = PDF::loadView('reportes.contratos.pdf_contratos',compact('contratos','oficinas'));
        
        return $pdf->download("Reporte Contratos ".date('d-m-Y').".pdf");
        
        return view('reportes.contratos.pdf_contratos',compact('contratos','oficinas'));
    }
}
