<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Storage;
use Entrust;
use Mail;
use Vanguard\Http\Requests;
use Vanguard\Contrato;
use Vanguard\User;
use Vanguard\Role;
use Vanguard\Oficina;
use Vanguard\Categoria;
use Vanguard\Pagos_contrato;
use Vanguard\Upload_documento;
use Vanguard\Month;
use Vanguard\Adenda;
use PDF;
use Dompdf\Dompdf;
use Carbon\Carbon;
class ContratosController extends Controller
{
    public function index($value='')
    { 
      
      $oficinas=Oficina::get();
      
      $contratos=Contrato::get(); 

      $categoria=categoria::where('categoria','Consultor')->first()->id;
      
      $users=User::where('categoria_id',$categoria)->get();

      $contratos=Contrato::get();
      
      if (Entrust::hasRole('Administradora')) {
        $oficinas=$oficinas->where('id',auth()->user()->oficina->id);
        $contratos=$contratos->where('oficina_id',auth()->user()->oficina->id);        
      }
      return view('contratos.list',compact('users','oficinas','contratos','adendas'));
    }

    public function create(Request $request)
    {
      $user=User::find($request->id);
      $edit=false;      
      //$user->fecha_string=$this->fecha_string(date('Y-m-d'));
      if (Entrust::hasRole('Directora')) {
        $directora=auth()->user();
        
      }else{
        $directora=Role::where('name','Directora')->first()->users->where('status',1)->first();
      }
      
      
      return view('contratos.create',compact('user','edit','directora'));
    }
    public function store(Request $request)
    {
        function tiempo_contrato($fecha_inicio,$fecha_fin,$contrato)
        {          
          $tiempo_contrato=Carbon::parse($fecha_fin)->diffInDays(Carbon::parse($fecha_inicio));
          return $tiempo_contrato;
        }

        $validator = Validator::make($request->all(), [
             'file_documento.*' => 'max:10000'
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);                        
        }

        if ($request->contrato_id) {
          
          $contrato=Contrato::find($request->contrato_id);
          
          if ($contrato->status!=0) { 
            
            if ($request->cumplimiento || $request->cumplimiento!="") {
              
                $contrato->cumplimiento=$request->cumplimiento;
                $contrato->status=3;            
            
            }elseif ($contrato->aprobacion_directora && $contrato->aprobacion_coordinadora ){
              $contrato->cumplimiento=null;
              $contrato->status=1;               
            }                     
                   
          }else{
            $contrato->fill($request->all());
            $contrato->tiempo_contrato=tiempo_contrato($request->fecha_fin,$request->fecha_inicio,$contrato);
            $pagos=$contrato->pagos()->delete();
          }     
          
        }else{
          $contrato=(new Contrato)->fill($request->all());
          $contrato->tiempo_contrato=tiempo_contrato($request->fecha_fin,$request->fecha_inicio,$contrato);
        }               
                
        $contrato->save();
        
        $file=$request->file('file_documento');
        foreach ($file as  $key => $value) {          
            if ($value){
              
              $nombre_documento=uniqid().'.'.$value->getClientOriginalExtension();            
              
              Storage::disk('documentos')->put($nombre_documento,  \File::get($value));

              $documento=new Upload_documento;
              $documento->nombre=$request->nombre_documento[$key];
              $documento->documento=$nombre_documento;
              $documento->contrato()->associate($contrato->id);
              $documento->save();
            
            }
        }

        if($request->monto){
            foreach ($request->monto as $key => $monto) {
                if ($monto!="") {
                  $pagos= new Pagos_contrato;
                  $pagos->monto=$monto;
                  $pagos->monto_l=$request->monto_l[$key];
                  $pagos->monto_producto=$request->monto_producto[$key];          
                  $pagos->contrato()->associate($contrato->id);
                  $pagos->save();
                }              
            }
        }         

        return redirect()->route('contratos.list')
            ->withSuccess('Contrato generado con exito');  
    }

    public function edit($id)
    {
      $contrato=Contrato::find($id);

      $user=$contrato->user;
      $edit=true;
      //dd($contrato->actividades);
      $user->fecha_string=$this->fecha_string($contrato->created_at->format('Y-m-d'));
      
      if (Entrust::hasRole('Directora')) {
        $directora=auth()->user();
        # code...
      }else{
        $directora=Role::where('name','Directora')->first()->users->first();
      }
      return view('contratos.create',compact('user','edit','contrato','directora'));
    }
    
    public function view($id)
    {
        $contrato=Contrato::find($id);
        $user=$contrato->user;
        $edit=true;
        $view=true;
        if (Entrust::hasRole('Directora')) {
          $directora=auth()->user();
          # code...
        }else{
          $directora=Role::where('name','Directora')->first()->users->first();
        }
        return view('contratos.create',compact('user','edit','contrato','directora','view'));
    }
    
    public function delete($id)
    {
       $contrato=Contrato::find($id);
       $documentos=$contrato->documentos->pluck('documento')->toArray();
       
       foreach ($documentos as $documento) {
          if (file_exists(public_path() . '/documentos/'.$documento)) {
              Storage::disk('documentos')->delete($documento);
          }
       }      
       
       $contrato->delete();
       return redirect()->route('contratos.list')
            ->withSuccess('Contrato borrado con exito');
    }
    public function delete_documento($id)
    { 
      
      $documento=Upload_documento::find($id);
      $contrato=$documento->contrato;
      if (file_exists(public_path() . '/documentos/'.$documento->documento)) {
              Storage::disk('documentos')->delete($documento->documento);
      }
      $documento->delete();     
      
      
      return redirect()->route('contrato.edit',['id' => $contrato->id])
        ->withSuccess('Documento borrado con exito');
    }

    public function fecha_string($fecha)
    {
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha_string=date("d", strtotime($fecha))." de ".$meses[date("n", strtotime($fecha))-1]." del ".date("Y", strtotime($fecha));
        return $fecha_string;         
    }
    
    public function aprobacion($id)
    {

      $contrato=Contrato::find($id);              
      $id=auth()->user()->id;
      //dd($contrato->aprobacion_coordinadora);
      if (Entrust::hasRole(['Directora'])) {
          
          if (!$contrato->aprobacion_coordinadora) {
                return redirect()->route('contratos.list')
            ->withErrors("Este Contrato aun sigue en revision debe esperar a ser confirmado");    
          } 
          $contrato->aprobacion_directora=1;
          if ($contrato->aprobacion_directora) {
              $contrato->status=1;
          }
          $contrato->fecha_aprobacion_directora=date('Y-m-d');
          $contrato->directora()->associate($id);
          $mensaje="Aprobado el contrato: $contrato->consultoria";
      }
      elseif (Entrust::hasRole('Coordinadora')) {
          $contrato->aprobacion_coordinadora=1;
          $contrato->fecha_aprobacion_coordinadora=date('Y-m-d');
          $contrato->coordinadora()->associate($id);
          $mensaje="Confirmado el contrato: $contrato->consultoria";
      }         

      $contrato->save();
      return redirect()->route('contratos.list')
      ->withSuccess($mensaje); 
    }
    
    public function ajax_contrato($id)
    {
        $contrato=Contrato::find($id);
        echo json_encode($contrato); 
    }
    
    public function descargar_pdf($id)
    {
        ini_set('max_execution_time', 300);
        $contrato = Contrato::find($id);
        
        $user = $contrato->user;
        $extraeMesInicio = date('m', strtotime($contrato->fecha_inicio));
        $extraeMesFin = date('m', strtotime($contrato->fecha_fin));

        $mesInicio = $this->mes_nombre($extraeMesInicio);
        $mesFin = $this->mes_nombre($extraeMesFin);

        //dd($mes);
        
        if (Entrust::hasRole('Directora')) 
        {
          $directora=auth()->user();        
        }
        else
        {
          $directora=Role::where('name','Directora')->first()->users->first();
        }

        $directora->firma_directora="";
        if($contrato->aprobacion_directora==1 && $directora->firma){

            $directora->firma_directora="<img src='upload/users/$directora->firma'><br><br>";  
          
        } 
        $user->fecha_string=$this->fecha_string($contrato->created_at->format('Y-m-d'));
        $user->consultor=$user->sexo?'La consultora':'El consultor';          

        $contrato->alcance=str_replace("\r\n", '<br> ', $contrato->alcance);
        $contrato->actividades=str_replace("\r\n", '<br> ', $contrato->actividades);
        $contrato->metodologia=str_replace("\r\n", '<br> ', $contrato->metodologia);
        $contrato->objetivo=str_replace("\r\n", '<br> ', $contrato->objetivo);
        $contrato->productos=str_replace("\r\n", '<br> ', $contrato->productos);
        
        //
       
        $pdf= new Dompdf;
        $pdf->set_option("isPhpEnabled", true); 
        $pdf=PDF::loadView('pdf.pdf_contrato', compact('contrato', 'user', 'directora', 'mesInicio', 'mesFin','pdf'));   
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text('490', '740', "Pag {PAGE_NUM} de {PAGE_COUNT}", null, 12, array(0, 0, 0));
        return $pdf->download("Contrato $user->first_name-$user->last_name ".date("d-m-Y H:i:s").".pdf"); 
        //return view('pdf.pdf_contrato', compact('contrato', 'user', 'directora', 'mesInicio', 'mesFin'));
        /*return view('pdf.pdf_contrato', [
                    'contrato' => $contrato, 
                    'user' => $user, 
                    'directora' => $directora, 
                    'mesInicio' => $mesInicio, 
                    'mesFin' => $mesFin
                ] ); */
    }

    public function mes_nombre($extraeMes)
    {
        $mesNombre = Month::where('id', $extraeMes)->lists('month')->toArray();
        return $mesNombre;
    }
    
    
    public function email()
    {
      $contratos = Contrato::All();

      //$coord = User::select('first_name', 'email')->withRole('Coordinadora')->get();
      //dd($coord[0]['first_name']);

        foreach ($contratos as $contrato) {
            
            if ($contrato->fecha_fin == date('Y-m-d') && $contrato->status == 5) {
                
                $user = $contrato->user;

                $coord = User::select('first_name', 'email')->withRole('Coordinadora')->get();

                $data = array(
                    'n_contrato' => $contrato->n_contrato,
                    'fecha_fin' => $contrato->fecha_fin,
                    'nombre' => $user->first_name,
                    'apellido' => $user->last_name,
                    'status' => $contrato->status
                );

                Mail::send('emails.contratos.vence_contrato', $data, function ($message) use ($coord)
                {
                    $message->from('notificacion@weeffect-podeeir.org', "WE EFFECT");
                    $message->subject('Aviso de vencimiento de contrato');
                    $message->to($coord[0]['email'], $coord[0]['first_name']);
                });
            }
            elseif (date('Y-m', strtotime($contrato->fecha_fin)) == date('Y-m') && date("d", strtotime($contrato->fecha_fin)) - date('d') == 5 ) {
                
                $user = $contrato->user;

                $coord = User::select('first_name', 'email')->withRole('Coordinadora')->get();

                $data = array(
                    'n_contrato' => $contrato->n_contrato,
                    'fecha_fin' => $contrato->fecha_fin,
                    'nombre' => $user->first_name,
                    'apellido' => $user->last_name,
                    'sexo' => $user->sexo,
                    'status' => $contrato->status
                );

                Mail::send('emails.contratos.falta_cinco', $data, function ($message) use ($coord)
                {
                    $message->from('notificacion@weeffect-podeeir.org', "WE EFFECT");
                    $message->subject('Faltan 5 dias para que expire el contrato');
                    $message->to($coord[0]['email'], $coord[0]['first_name']);
                });
            }

            $this->info('Se enviaron los emails correspondientes');
        }
    }
}
