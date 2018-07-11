<?php

namespace Vanguard\Http\Controllers;
use Illuminate\Http\Request;
use Vanguard\Http\Requests;
use Entrust;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Worksheet_PageSetup;
use Vanguard\Empleado_planilla_normal;
use Vanguard\Planilla;
use Vanguard\Aporte;
use Vanguard\Deduccion;
use Vanguard\Acumulado;
use Vanguard\User;
use Vanguard\Pais;
use Vanguard\Campo;

class PlanillaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');       
    }
    public function index($value='')
    {   
        if(Entrust::hasRole(['Directora', 'Coordinadora','Admin','Contralora'])){
             $planillas=Planilla::get();
        }
        elseif (Entrust::hasRole('Administradora')) {
             $planillas=Planilla::where('oficina_id',auth()->user()->oficina_id)
            ->get();
        }        
        //dd($planillas->first()->oficina_id);
        
        return view('planillas.list',compact('planillas'));
    }

    public function crear(Request $requests)
    {           

        $administradora=Auth()->user();
        $oficina=Auth()->user()->oficina;
        $pais=Auth()->user()->oficina->pais;
        $edit=false;
        $year=date('Y');
        $fecha=Planilla::where('m_a',$requests->fecha)->where('oficina_id',$oficina->id)->first();
        if ($fecha) {
            return redirect()->route('planilla.normal')
            ->withErrors("Al parecer la planilla del mes: ( $fecha->m_a ) ya fue elaborada");            
        }
        $fecha=$requests->fecha;        
        $users=User::where('oficina_id',$oficina->id)->where('status',1)->whereHas('roles', function($q){
            $q->whereNotIn('name',['Admin','Directora']);
        }
        )->get();
        //dd(count($users));
        
        $meses_catorceavo=[                    
                    'Junio-'.($year-1),
                    'Julio-'.($year-1),
                    'Agosto-'.($year-1),
                    'Septiembre-'.($year-1),
                    'Octubre-'.($year-1),
                    'Noviembre-'.($year-1),
                    'Diciembre-'.($year-1),
                    'Enero-'.$year,
                    'Febrero-'.$year,
                    'Marzo-'.$year,
                    'Abril-'.$year,
                    'Mayo-'.$year,
            ];
        $meses_acumulados=[         
                    'Enero-'.$year,
                    'Febrero-'.$year,
                    'Marzo-'.$year,
                    'Abril-'.$year,
                    'Mayo-'.$year,
                    'Junio-'.$year,
                    'Julio-'.$year,
                    'Agosto-'.$year,
                    'Septiembre-'.$year,
                    'Octubre-'.$year,
                    'Noviembre-'.$year,
                    'Diciembre-'.$year                    
        ];
        //verifico que sea la planilla de julio para hacer el calculo del acumulado
        
                      
        $planilla_enero=Planilla::where('oficina_id',$oficina->id)->where('m_a','like',"%Enero%")->get();
        
        $planilla_empleados=count($planilla_enero)?$planilla_enero->last()->empleados:NULL;        
        //dd($planilla_empleados);
        foreach ($users as $user) {
            
            $acumulado=$user->acumulado;

           
            if (str_contains($requests->fecha, 'Junio')) {
                $catorcevo_total=$acumulado->where('oficina_id',$oficina->id)->whereIn('m_a',$meses_catorceavo);                           
                $user->catorceavo_total=$catorcevo_total->sum('catorceavo');
            }

            if (str_contains($requests->fecha, 'Diciembre')) {   
                $acumulado_total=$acumulado->where('oficina_id',$oficina->id)->whereIn('m_a',$meses_acumulados);               
            }   
            if($pais->id==6){
                    //aguinaldo exento
                    //$aex=503.40;
                    //aguinaldo agravado
                    //$aga=$user->salario_base-$aex;  
                    //$user->ag=$aex+$aga-$impuestos);
                $user->ag=0;
            }else{       
                $user->ag=number_format($user->salario_base*(8.33/100),2,'.','');
            }
            $user->pen=number_format($user->salario_base*((float)$pais->porcentaje_pension/100),2,'.','');
            $user->inde=number_format($user->salario_base*(8.33/100),2,'.','');

            if ($pais->tipo_seguridad_social_p=='porcentaje') {

                $user->seguridad_social_patronal=number_format($user->salario_base*((float)$pais->seguridad_social_p/100),2,'.','');
                //dd($user->seguridad_social_patronal);
            }
            else{
                
                $user->seguridad_social_patronal=number_format(((float)$pais->seguridad_social_p*1),2,'.','');
            }
            
            if ($pais->tipo_seguridad_social=='porcentaje') {
                $user->seguridad_social=number_format($user->salario_base*((float)$pais->porcentaje_seguridad_social/100),2,'.','');
            }
            else{

                $user->seguridad_social=number_format((float)$pais->porcentaje_seguridad_social*1,2,'.','');

            }
            
            $usuario=$planilla_empleados?$planilla_empleados->where('user_id',$user->id)->first():null;
            
            $user->impuesto_renta=$usuario?(float)$planilla_empleados->where('user_id',$user->id)->first()->deduccion->impuesto_renta:0;                     
            $user->prestamo=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->deduccion->prestamo:0;         
            $user->interes=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->deduccion->interes:0;         
            $user->otras_deducciones=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->deduccion->otras_deducciones:0;         
            
            switch ($pais->id) {
                case 1:
                //aportes planilla enero
                $user->bonificacion_incentivo=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->aporte->bonificacion_incentivo:0; 
                $user->bonificacion_docto_37_2001=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->aporte->bonificacion_docto_37_2001:0; 
                $user->reintegros=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->aporte->reintegros:0; 
                               
                    break;
                case 2://BOLIVIA                   

                    //deducciones                    
                    $user->rc_iva=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->deduccion->rc_iva:0;                    
                    $user->cta_ind=$user->salario_base*0.10; 
                    $user->riesgo=round($user->salario_base*0.0171,2);
                    $user->com_afp=round($user->salario_base*0.005,2); 
                    $user->afp_aporte_solidario=round($user->salario_base*0.005,2);
                    $user->afp_aporte_nacional_solidario=round( (($user->salario_base>13000)?($user->salario_base-13000)*0.01 : 0),2);
                    
                    //aportes patronales
                    $user->seguro_universitario       =round( $user->salario_base*(10/100),2);
                    $user->afp_prevision              =round( $user->salario_base*(1.71/100),2);
                    $user->afp_prevision_pnvs         =round( $user->salario_base*(2/100),2);
                    $user->aporte_afp_aporte_solidario       =round( $user->salario_base*(3/100),2);

                    break;
                case 3://NICARAGUA
                    //DEDUCCIINES PLANILLAENERO
                    $user->deduccion_1='';
                    $user->deduccion_2='';
                    
                    $user->deduccion_1=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->deduccion->deduccion_1:0; 
                    $user->deduccion_2=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->deduccion->deduccion_2:0;
                    
                    //aportes patronal planilla enero                    /
                    $user->INATEC=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->aporte->INATEC:0;
                    break;
                case 4://HONDURAS 
                    //deducciones planilla enero
                    $user->seguro_medico=$usuario?$planilla_empleados->where('user_id',$user->id)->first()->deduccion->seguro_medico:0;
                    $user->rap=round( $user->salario_base*0.015,2);
                    //aportes patronales
                    $user->rap_patronal=round( $user->salario_base*0.015,2);
                    break;
                case 5:
    
                    break;
                case 6:
                    //deduccion
                    $user->afp=round($user->salario_base*0.0625,2);
                    break;

            }  


        }        
        
        return view('planillas.nueva_normal',compact('users','oficina','fecha','pais','administradora','edit','year'));
    }
    public function store(Request $requests)
    {   
        
        if ($requests->id_planilla) {
           
           $planilla=Planilla::find($requests->id_planilla);
           $planilla->confirmada=$requests->confirmada;

           $planilla->save();
           $empleados_planilla=$planilla->empleados;
           $edit=true;
       
        }else{            
           
            if (Planilla::where('m_a',$requests->m_a)->where('oficina_id',auth()->user()->oficina_id)->first()) {
                return redirect()->route('planilla.normal')
                ->withErrors("Al parecer la planilla del mes: ( $requests->m_a ) ya fue elaborada");
                
            }
            
            $planilla= (new Planilla)->fill($requests->all());

            $planilla->save();
            $empleados_planilla=$requests->planilla;
            $edit=false;
        }
        


        foreach ($empleados_planilla as  $empleado) {          
            
            //SALARIO
            if ($edit) {
                $p_e=$empleado;
                $aporte=$p_e->aporte;
                $deducciones=$p_e->deduccion;
                $acumulados=$p_e->acumulado;
                $id=$p_e->id;
                $empleado=$requests->planilla[$id];    
                
            }else{
                $p_e=new Empleado_planilla_normal;
                $aporte= new Aporte;
                $deducciones=new Deduccion;
                $acumulados= new Acumulado;    
            }
            
            $p_e->nombre=$empleado['nombre'];
            $p_e->n_contrato=$empleado['n_contrato'];
            $p_e->fecha_inicio=$empleado['fecha_inicio'];
            $p_e->documento=$empleado['documento'];
            $p_e->cargo=$empleado['cargo'];
            //dd($requests->all());
            $p_e->dias_trabajados=$requests->oficina_id!=1?$empleado['dias_trabajados']:30;
            $p_e->salario_base=$empleado['salario_base'];
            $p_e->ajuste=$empleado['ajuste'];
            $p_e->total_salario=$empleado['salario_base']+$empleado['ajuste'];
            $p_e->user_id=$empleado['user_id'];
            $p_e->liquido_recibir=$empleado['liquido'];            
            
            if (str_contains($requests->m_a, 'Diciembre')) {
                           
                $acumualado_meses=array_keys($empleado['aguinaldo_meses']);
                $acumualado_meses=Acumulado::where('user_id',$empleado['user_id'])->whereIn('m_a',$acumualado_meses)->get();
                 
                foreach ($acumualado_meses as $mes) {
                   
                    $mes->aguinaldo=$empleado['aguinaldo_meses'][$mes->m_a];
                    
                    ///
                    if($requests->pago_indemnizacion=="anual") {
                       $mes->indemnizacion=$empleado['indemnizacion_meses'][$mes->m_a];
                    }
                    
                    ///
                    if($requests->pago_pension=="anual"){
                        $mes->pension=$empleado['pension_meses'][$mes->m_a];
                    }
                    
                    $mes->save();
                }                
                

                $p_e->total_aguinaldo=$empleado['total_aguinaldo'];

                
                if($requests->pago_indemnizacion=="anual") {         

                    $p_e->total_indemnizacion=$empleado['total_indemnizacion'];
                }
                
                if($requests->pago_pension=="anual"){
                    $p_e->total_pension=$empleado['total_pension'];
                }
            }

            //APORTES /////////////////////////////////////////////
            $aporte->bonificacion_14=array_key_exists('bonificacion_14', $empleado)?  $empleado['bonificacion_14']: 0;
            $aporte->seguridad_social_patronal=array_key_exists('seguridad_social_patronal', $empleado)?  $empleado['seguridad_social_patronal']: 0;

            $aporte->total_aportes=array_key_exists('total_aportes', $empleado)?  $empleado['total_aportes']: 0;


            $aporte->total_carga_patronal=0;
            $aporte->total_carga_patronal+=$aporte->seguridad_social_patronal;               
            switch ($requests->pais_id) {
                
                case 1://APORTES  DE GUATEMALA
                    $aporte->bonificacion_incentivo=$empleado['bonificacion_incentivo'];
                    $aporte->bonificacion_docto_37_2001=$empleado['bonificacion_docto_37_2001'];
                    $aporte->reintegros=$empleado['reintegros']; 
                    
                    break;
                
                case 2://APORTES PATRONALES DE BOLIVIA
                    $aporte->seguro_universitario       =$empleado['seguro_universitario'];
                    $aporte->afp_prevision              =$empleado['afp_prevision'];
                    $aporte->afp_prevision_pnvs         =$empleado['afp_prevision_pnvs'];
                    $aporte->afp_aporte_solidario       =$empleado['aporte_afp_aporte_solidario']  ;                  
                    
                    $aporte->total_carga_patronal+=
                    
                    $aporte->seguro_universitario+
                    $aporte->afp_prevision+
                    $aporte->afp_prevision_pnvs+
                    $aporte->afp_aporte_solidario;

                    break;

                case 3://APORTES PATRONALES DE NICARAGUA
                    $aporte->INATEC              =$empleado['INATEC'];
                    $aporte->total_carga_patronal+=$aporte->INATEC;
                    
                    break;

                case 4://APORTES PATRONALES DE HONDURAS 
                    $aporte->rap=$empleado['rap_patronal'];
                    $aporte->total_carga_patronal+=$aporte->rap;                  
                    
                    break;

                case 5://APORTES PATRONALES DE PARAGUAY
                    $aporte->total_aporte_25_5   =$p_e->total_salario*(25.5/100);
                    $aporte->total_carga_patronal+=$aporte->total_aporte_25_5;
                    
                    break;

                case 6://APORTES PATRONALES DE SALVADOR
                    $aporte->afp_6_75            =$p_e->total_salario*(6.75/100);
                    $aporte->total_carga_patronal+=$aporte->afp_6_75;
                    
                    break;                
                default:
                    
                    break;
            }            

            //DEDUCCIONES /////////////////////////////////////////////
            
            $deducciones->prestamo=array_key_exists('prestamo', $empleado)?  $empleado['prestamo']: 0;          
            $deducciones->interes=array_key_exists('interes', $empleado)?  $empleado['interes']: 0 ;
            $deducciones->otras_deducciones=array_key_exists('otras_deducciones',$empleado)? $empleado['otras_deducciones']: 0 ;
            $deducciones->impuesto_renta=array_key_exists('impuesto_renta', $empleado)?  $empleado['impuesto_renta']: 0 ;            
            $deducciones->seguridad_social=array_key_exists('seguridad_social', $empleado)?  $empleado['seguridad_social']: 0 ;
            //dd($deducciones);
            switch ($requests->pais_id) {                
                
                case 2://DEDUCCIONES DE BOLIVIA
                    $deducciones->cta_ind=$empleado['cta_ind'];
                    $deducciones->riesgo=$empleado['riesgo'] ;
                    $deducciones->com_afp=$empleado['com_afp'] ;
                    $deducciones->afp_aporte_solidario=$empleado['afp_aporte_solidario'];
                    $deducciones->afp_aporte_nacional_solidario=$empleado['afp_aporte_nacional_solidario'];
                    $deducciones->rc_iva=$empleado['rc_iva'] ;
                    break;
                
                case 6://DEDUCCIONES DE SALVADOR
                    $deducciones->afp=$empleado['afp'];
                    break;
                case 4://DEDUCCIONES DE HONDURAS
                    $deducciones->rap=$empleado['rap'];
                    $deducciones->seguro_medico=$empleado['seguro_medico'];
                    break;
                case 3://DEDUCCIONES DE NICARAGUA                    
                    $deducciones->deduccion_1=$empleado['deduccion_1'];
                    $deducciones->deduccion_2=$empleado['deduccion_2'];
                    break;
                
                default:
                    # code...
                    break;
            }

            //sumo toda las deducciones
           /* $sum=0;
            $total_d=$edit ? $deducciones->total_deducciones :0;
            foreach ($deducciones->toArray() as $key => $value) {
                $sum+=$value;
            }*/            
            $deducciones->total_deducciones=$empleado['total_deducciones'];         
                       
            //ACUMULADOS/////////////////////////////////////////////           
            //pension
            if (array_key_exists('pension_meses', $empleado)) {                
               $acumulados->pension=$empleado['pension_meses']["$requests->m_a"];

            }
            
            //aguinaldo
            $acumulados->aguinaldo=$empleado['aguinaldo_meses']["$requests->m_a"];          

            //indemnisacion            
            $acumulados->indemnizacion=$empleado['indemnizacion_meses']["$requests->m_a"];    
            
            //catorceavo mes            
            $acumulados->catorceavo=$empleado['catorceavo'];
            $acumulados->total_salario=$p_e->total_salario;       
            $acumulados->m_a=$requests->m_a;

            if ($edit) {
                $p_e->save();
                $aporte->save();
                $deducciones->save();                
                $acumulados->save();
                $mensaje="Planilla Actualizada con exito";

            }else{

                $p_e->planilla()->associate($planilla->id);
                $p_e->save();

                $aporte->empleado()->associate($p_e->id);
                $aporte->planilla()->associate($planilla->id);
                $aporte->save();
                $p_e->save();

                $deducciones->empleado()->associate($p_e->id);
                $deducciones->planilla()->associate($planilla->id);
                $deducciones->save();

                $acumulados->empleado()->associate($p_e->id);      
                $acumulados->user()->associate($p_e->user_id);      
                $acumulados->oficina()->associate($planilla->oficina_id);      
                $acumulados->planilla()->associate($planilla->id); 
                $acumulados->save();

                $mensaje="Planilla creada con exito";                

            }         
            
       }
        
        return redirect()->route('planilla.normal')
            ->withSuccess($mensaje);      
    }
    public function edit($id)
    {
        $planilla=Planilla::find($id);

        
            $users=$planilla->empleados;
            $vista='planillas.nueva_normal';
       
             
        $fecha=$planilla->m_a;

        $oficina=$planilla->oficina;
        $pais=$oficina->pais;
        $administradora=$planilla->administradora;
        $edit=true; 
        if (str_contains($fecha, 'Diciembre')) {
                $year=str_replace("Diciembre-", "", $fecha);           
        }else{
            $year=date('Y');
        }                 
         
        return  view($vista,compact('planilla','users','oficina','fecha','pais','edit','administradora','year'));
    }
    
    public function delete($id)
    {       
        $planilla=Planilla::find($id);        
        $m_a=$planilla->m_a;
        $oficina=$planilla->oficina->oficina;
        $planilla->delete();
        return redirect()->route('planilla.normal')
            ->withSuccess("Planilla $oficina $m_a Borrada con exito");
    }
    
    public function aprobacion($id)
    {
        $planilla=Planilla::find($id);
              
        $id=auth()->user()->id;
        if (Entrust::hasRole(['Directora'])) {
            $planilla->aprobacion_directora=1;
            $planilla->fecha_aprobacion_directora=date('Y-m-d');
            $planilla->directora()->associate($id);
        }
        elseif (Entrust::hasRole(['Coordinadora','Contralora'])) {
            $planilla->aprobacion_coordinadora=1;
            $planilla->fecha_aprobacion_coordinadora=date('Y-m-d');
            $planilla->coordinadora()->associate($id);          

        }       

        if (!$planilla->confirmada) {
                return redirect()->route('planilla.normal')
            ->withErrors("Esta planilla aun sigue en revision debe esperar a ser confirmada");    
        }  

        $planilla->save();
        return redirect()->route('planilla.normal')
            ->withSuccess("Aprobada la planilla $planilla->m_a"); 
    }

    function ajustes($value='')
    { 
        
        $pais=auth()->user()->oficina->pais;
        //dd($pais->id);
       
        if(Entrust::hasRole('Administradora')){
            $paises=Pais::where('id',$pais->id)->get();      
        }else{
           $paises=Pais::get();           
        }
        return view('planillas.ajustes',compact('paises'));
        //dd($Guatemala);
        
    }
    public function guardar_ajustes(Request $requests)
    {
        
        $pais=auth()->user()->oficina->pais;
        if(Entrust::hasRole('Administradora')){
            $paises=Pais::where('id',$pais->id)->get();           
        }else{
            $paises=Pais::get();
        }
        foreach ($paises as $pais) {          
           
            
            //Visibilidad de campo           
            if (isset($requests->pais[$pais->id]['campo_deducciones'])) {               
                $campo_deducciones=$requests->pais[$pais->id]['campo_deducciones'];
                $pais->campo_deducciones= implode(',', $campo_deducciones);
            }           
            //Porcentajes
            $pais->moneda_simbolo=$requests->pais[$pais->id]['moneda_simbolo'];
            $pais->moneda_nombre=$requests->pais[$pais->id]['moneda_nombre'];
            $pais->porcentaje_seguridad_social=$requests->pais[$pais->id]['porcentaje_seguridad_social'];
            $pais->porcentaje_pension=$requests->pais[$pais->id]['porcentaje_pension'];
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
            $campo->seguridad_social=$requests->campo[$pais->id]['seguridad_social_p'];
            $campo->seguridad_social_patronal=$requests->campo[$pais->id]['seguridad_social'];
            $campo->liquido=$requests->campo[$pais->id]['liquido'];     
            $campo->acumulado_aguinaldo=$requests->campo[$pais->id]['acumulado_aguinaldo'];    
            $campo->acumulado_indemnizacion=$requests->campo[$pais->id]['acumulado_indemnizacion'];    
                   
            $campo->save();               

        }
        
        return redirect()->route('planilla.ajustes')
            ->withSuccess('Ajustes guardados con exito'); 
    }    

    public function descargar_planilla($id)
    {       
        $planilla=Planilla::find($id);         

        $planilla->firma_administradora=$this->value_firma($planilla->confirmada,$planilla->administradora);        
        $planilla->firma_coordinadora=$this->value_firma($planilla->aprobacion_coordinadora,$planilla->coordinadora);        
        $planilla->firma_directora=$this->value_firma($planilla->aprobacion_directora,$planilla->directora);

        $oficina=$planilla->oficina;
        $pais=$oficina->pais;
        $fecha = $planilla->m_a;
        $campo=$pais->campo;

        switch ($pais->id) {
            case 1://GUATEMALA
                $planilla->catorceavo_mes=(str_contains($planilla->m_a, 'Junio'))?'Junio':'Julio';              
                $planilla->cell_aportes_patronales=2;
                $planilla->cell_aportes=5;
                $planilla->cell_deducciones=count(explode(',', $planilla->campo_deducciones));
                
                break;
            case 2://BOLIVIA
                $planilla->cell_aportes_patronales=5;
                $planilla->cell_aportes=0;
                $planilla->cell_deducciones=count(explode(',', $planilla->campo_deducciones))+6;
                break;
            case 3://NICARAGUA
                $planilla->cell_aportes_patronales=3;
                $planilla->cell_aportes=0;
                $planilla->cell_deducciones=count(explode(',', $planilla->campo_deducciones))+2;
                break;
            case 4://HONDURAS
                $planilla->cell_aportes_patronales=3;
                $planilla->cell_aportes=0;
                $planilla->cell_deducciones=count(explode(',', $planilla->campo_deducciones))+2;
                break;
            case 5://PARAGUAY
                $planilla->cell_aportes_patronales=3;
                $planilla->cell_aportes=0;
                $planilla->cell_deducciones=count(explode(',', $planilla->campo_deducciones));              
                break;
            case 6://SALVADOR
                $planilla->cell_aportes_patronales=3;
                $planilla->cell_aportes=0;
                $planilla->cell_deducciones=count(explode(',', $planilla->campo_deducciones))+1;
                break;                   
            }  

        $pdf = PDF::loadView('pdf.pdf_main_planilla',
            [
                'planilla' =>$planilla, 
                'oficina' =>$oficina,
                'campo' =>$campo,
                'pais' =>$pais,
            ]    
        )->setPaper('a4', 'landscape');
        return $pdf->download("Planilla $planilla->m_a.pdf");
                
        return view('pdf.pdf_main_planilla',[
                    'planilla' =>$planilla, 
                    'oficina' =>$oficina,
                    'campo' =>$campo,
                    'pais' =>$pais,
                ] );


             
        
               

        Excel::create("Planilla $oficina->oficina $planilla->m_a" , function($excel) use ($fecha,$planilla,$oficina,$campo,$pais) 
        {   
            
            
            /*$excel->sheet('Planilla', function($sheet) use ($planilla,$oficina,$campo,$pais){

               
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo-p1.png')); //your image path  
                $objDrawing->setCoordinates('A2');              
                $objDrawing->setWorksheet($sheet);
  
                $sheet->protect('password');
                $sheet->loadView('excel.planilla.totales',
                    array(  'planilla' =>$planilla, 
                            'oficina' =>$oficina,
                            'campo' =>$campo,
                            'pais' =>$pais,
                            
                    )
                )->with('no_asset', true);
                
                
            });
            
            $excel->sheet('Aportes Patronales', function($sheet) use ($planilla,$oficina,$campo,$pais){

                
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('img/logo-p1.png')); //your image path  
                $objDrawing->setCoordinates('A2');              
                $objDrawing->setWorksheet($sheet);                
                
                $sheet->loadView('excel.planilla.aportes_patronales',
                    array(  'planilla' =>$planilla, 
                            'oficina' =>$oficina,
                            'campo' =>$campo,
                            'pais' =>$pais,
                            
                    )
                )->with('no_asset', true);
                
            });*/
            if (str_contains($planilla->m_a, 'Diciembre'))
            {
                $excel->sheet('Aguinaldos', function($sheet) use ($fecha,$planilla,$oficina,$campo,$pais){

                
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('img/logo-p1.png')); //your image path  
                    $objDrawing->setCoordinates('A2');              
                    $objDrawing->setWorksheet($sheet);
      

                    $sheet->loadView('excel.planilla.aguinaldo',
                        array(  'fecha' => $fecha,  
                                'planilla' =>$planilla, 
                                'oficina' =>$oficina,
                                'campo' =>$campo,
                                'pais' =>$pais,
                                
                        )
                    )->with('no_asset', true)
                    ->setStyle(
                        array(
                            'font' => array(
                                'name'      =>  'Calibri',                    
                            )
                        )   
                    );
                });

                if(!str_contains($oficina, 'Salvador')){
                    $excel->sheet('Indemnización', function($sheet) use ($fecha,$planilla,$oficina,$campo,$pais){

                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(public_path('img/logo-p1.png')); //your image path  
                        $objDrawing->setCoordinates('A2');              
                        $objDrawing->setWorksheet($sheet);
          

                        $sheet->loadView('excel.planilla.indemnizacion',
                            array(  'fecha' => $fecha,  
                                    'planilla' =>$planilla, 
                                    'oficina' =>$oficina,
                                    'campo' =>$campo,
                                    'pais' =>$pais,
                                    
                            )
                        )->with('no_asset', true)
                        ->setStyle(
                            array(
                                'font' => array(
                                    'name'      =>  'Calibri',                    
                                )
                            )   
                        );
                    });

                    $excel->sheet('Pensión', function($sheet) use ($fecha,$planilla,$oficina,$campo,$pais){

                    
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(public_path('img/logo-p1.png')); //your image path  
                        $objDrawing->setCoordinates('A2');              
                        $objDrawing->setWorksheet($sheet);
          

                        $sheet->loadView('excel.planilla.pension',
                            array(  'fecha' => $fecha,  
                                    'planilla' =>$planilla, 
                                    'oficina' =>$oficina,
                                    'campo' =>$campo,
                                    'pais' =>$pais,
                                    
                            )
                        )->with('no_asset', true)
                        ->setStyle(
                            array(
                                'font' => array(
                                    'name'      =>  'Calibri',                    
                                )
                            )   
                        );
                    });
                }
            }

            $excel->setActiveSheetIndex(0)->download('pdf');        

        });
    }
    public function value_firma($confirmacion,$img)
    {       
            
            if($confirmacion==1){
                if($img->firma && file_exists("upload/users/$img->firma")){
                    $firma="<img src='upload/users/$img->firma'><br><br>
                            <label style='background: #4caf50 ;'>CONFIRMADA</label><br>";  
                }else{
                    $firma="<label style='background: #4caf50 ;'>CONFIRMADA</label><br>";
                }
            }else{
                $firma="<label style='background: #ffeb3b;'>EN REVISION</label><br>";
            }           
            return $firma;
        
    }
}
