@extends('layouts.app')

@section('page-title', 'Ajustes')

@section('content')

    <h1 class="page-header">Ajustes</h1>
    @include('partials.messages')    
    
    <form class="form_ajustes" action="{{url('/ajustes')}}" method="post">

      {!! csrf_field() !!}

      <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#planilla">Planilla</a></li>
          
          <li class=""><a data-toggle="tab" href="#oficina">Oficina</a></li>  

          <li class=""><a data-toggle="tab" href="#cargo">Cargo</a></li>

          <li class=""><a data-toggle="tab" href="#profesion">Profesión</a></li>

          <li class=""><a data-toggle="tab" href="#permisos">Permisos y Ausencias</a></li>   
          
          <li class=""><a data-toggle="tab" href="#vacaciones">Vacaciones</a></li>   

          <li class=""><a data-toggle="tab" href="#agregar">Agregar</a></li>    


        
      </ul>
      <div class="tab-content">
          <div id="planilla" class="tab-pane fade in active">

              <h4 class="">Visibilidad de Campos</h4>
              <table class="table ajustes_planillas_tabla table-hover table-bordered">
                <thead>
                  <tr>
                    <th>País</th>
                    <th>Fondo Pensión</th>      
                    <th>Impuesto renta</th>
                    <th>Seguridad Social</th>
                    <th>Préstamo</th>                                  
                    <th>Interés</th>                                
                    <th>Otras deducciones</th>        
                  </tr>
                </thead>

                <tbody>

                  @foreach( $paises as $pais)
                  <tr>
                    <td>{{$pais->pais}}</td>
                    <td class="text-center">
                      <div class="">
                        <input type="checkbox" name="pais[{{$pais->id}}][campo_deducciones][]" value="fondo_pension" 
                        @if( in_array('fondo_pension', explode(',', $pais->campo_deducciones)) ) checked @endif >
                        <label class="no-content"></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <div class="">
                        <input type="checkbox" name="pais[{{$pais->id}}][campo_deducciones][]" value="impuesto_renta"
                        @if( in_array('impuesto_renta', explode(',', $pais->campo_deducciones)) ) checked @endif >
                        <label class="no-content"></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <div class="">
                        <input type="checkbox" name="pais[{{$pais->id}}][campo_deducciones][]" value="seguridad_social"
                        @if( in_array('seguridad_social', explode(',', $pais->campo_deducciones)) ) checked @endif >
                        <label class="no-content"></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <div class="">
                        <input type="checkbox" name="pais[{{$pais->id}}][campo_deducciones][]" value="prestamo"
                        @if( in_array('prestamo', explode(',', $pais->campo_deducciones)) ) checked @endif >
                        <label class="no-content"></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <div class="">
                        <input type="checkbox" name="pais[{{$pais->id}}][campo_deducciones][]" value="interes"
                        @if( in_array('interes', explode(',', $pais->campo_deducciones)) ) checked @endif >
                        <label class="no-content"></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <div class="">
                        <input type="checkbox" name="pais[{{$pais->id}}][campo_deducciones][]" value="otras_deducciones"
                        @if( in_array('otras_deducciones', explode(',', $pais->campo_deducciones)) ) checked @endif >
                        <label class="no-content"></label>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <h4 class="">Porcentaje para cálculos</h4>
              <table class="table ajustes_planillas_tabla table-hover table-bordered">
                <thead>
                  <tr>
                    <th>País</th>
                             
                    <th>Moneda Símbolo</th>
                    <th>Moneda Nombre</th>
                    <th>Porcentaje Pensión</th>      
                    <th>Tipo de Valor Seguridad social</th>                                  
                    <th>Valor Seguridad social</th> 
                    <th>Tipo de Valor Seguridad social Patronal</th>                                  
                    <th>Valor Seguridad social Patronal</th>                                  
                            
                                 
                  </tr>
                </thead>

                <tbody>
                  @foreach($paises as $pais)
                  <tr>
                    <td><b>{{$pais->pais}}</b></td>
                    <td>
                      <input type="text" class="form-control" name="pais[{{$pais->id}}][moneda_simbolo]" 
                      value="{{$pais->moneda_simbolo}}">
                      </td>
                      
                      <td><input type="text" class="form-control" name="pais[{{$pais->id}}][moneda_nombre]" 
                        value="{{$pais->moneda_nombre}}">
                      </td>
                      <td>
                        <input type="number" step="0.01"  class="form-control" name="pais[{{$pais->id}}][porcentaje_pension]" 
                        value="{{$pais->porcentaje_pension}}">
                      </td>
                      <td>
                        <select class="form-control" name="pais[{{$pais->id}}][tipo_seguridad_social]">
                          <option value="valor" {{$pais->tipo_seguridad_social=="valor"? 'selected':''}}>Valor</option>
                          <option value="porcentaje" {{$pais->tipo_seguridad_social=="porcentaje"? 'selected':''}}>Porcentaje</option>
                        </select>
                      </td>
                      <td>
                        <input type="number" step="0.01"  class="form-control" name="pais[{{$pais->id}}][porcentaje_seguridad_social]" 
                        value="{{$pais->porcentaje_seguridad_social}}">
                      </td>
                       <td>
                        <select class="form-control" name="pais[{{$pais->id}}][tipo_seguridad_social_p]">
                          <option value="valor" {{$pais->tipo_seguridad_social_p=="valor"? 'selected':''}}>Valor</option>
                          <option value="porcentaje" {{$pais->tipo_seguridad_social_p=="porcentaje"? 'selected':''}}>Porcentaje</option>
                        </select>
                      </td>
                      <td>
                        <input type="number" step="0.01" class="form-control" name="pais[{{$pais->id}}][seguridad_social_p]" 
                        value="{{$pais->seguridad_social_p}}">
                      </td>
                      
                    </tr>


                    @endforeach      
                  </tbody>
              </table>

              <h4 class="">Nombre de Campos</h4>
              <table class="table table-hover table-bordered ajustes_planillas_tabla">
                  <thead>
                    <tr>
                      <th>Nombre de campos</th>
                      @foreach($paises as $pais)
                      <th>{{$pais->pais}}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>            
                    <tr>           
                      <td width="200">Salario base</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][salario_base]" value="{{$pais->campo->salario_base}}" >
                      </td>
                      @endforeach                      
                    </tr>
                    <tr>           
                      <td>Ajustes</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][ajustes]" value="{{$pais->campo->ajustes}}">
                      </td>
                      @endforeach  

                    </tr>
                    <tr>           
                      <td>Salario Total</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][total_salario]" value="{{$pais->campo->total_salario}}">
                      </td>
                      @endforeach                                       
                    </tr>
                    <tr>           
                      <td>Catorceavo mes</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][catorceavo]" value="{{$pais->campo->catorceavo}}">
                      </td>
                      @endforeach                                  
                    </tr>

                    <tr>           
                      <td>Préstamo</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][prestamo]" value="{{$pais->campo->prestamo}}">
                      </td>
                      @endforeach                      
                    </tr>
                    <tr>           
                      <td>Interes</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][interes]" value="{{$pais->campo->interes}}">
                      </td>
                      @endforeach

                    </tr>
                    <tr>           
                      <td>Otras deducciones</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][otras_deducciones]" value="{{$pais->campo->otras_deducciones}}">
                      </td>
                      @endforeach                      
                    </tr>
                    <tr>           
                      <td>Impuestos</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][impuestos]" value="{{$pais->campo->impuestos}}">
                      </td>
                      @endforeach                      
                    </tr>
                    <tr>           
                      <td>Total deducciones</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][total_deducciones]" value="{{$pais->campo->total_deducciones}}">
                      </td>
                      @endforeach                       
                    </tr>
                    <tr>           
                      <td>Seguridad Social</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][seguridad_social]" value="{{$pais->campo->seguridad_social}}">
                      </td>
                      @endforeach                      
                    </tr>
                    <tr>           
                      <td>Seguridad Social Patronal</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][seguridad_social_p]" value="{{$pais->campo->seguridad_social_patronal}}">
                      </td>
                      @endforeach                    
                    </tr>
                    <tr>
                      <td>Líquido a Recibir</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][liquido]" value="{{$pais->campo->liquido}}">
                      </td>
                      @endforeach                       
                    </tr>
                    <tr>
                      <td>Acumulado Aguinaldo</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][acumulado_aguinaldo]" value="{{$pais->campo->acumulado_aguinaldo}}">
                      </td>
                      @endforeach                       
                    </tr>
                    <tr>
                      <td>Acumulado indemnización</td>
                      @foreach($paises as $pais)
                      <td>
                        <input type="text" class="form-control input_inline" 
                        name="campo[{{$pais->id}}][acumulado_indemnizacion]" value="{{$pais->campo->acumulado_indemnizacion}}">
                      </td>
                      @endforeach                       
                    </tr> 

                  </tbody>
              </table>

              <div class=" row text-right">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn_color"> Guardar Ajustes </button>
                </div>
              </div>

          </div>
          
          <div id="oficina" class="tab-pane fade">              
             
              <table class="table ajustes_planillas_tabla table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Oficina</th>
                    <th>Central</th>                              
                    <th>Teléfono</th>
                    <th>NIT</th>
                    <th>Num. Patronal</th> 
                    <th>Dirección</th>                      
                  </tr>
                </thead>

                <tbody>
                  @foreach( $oficinas as $oficina)
                  <tr>
                    <td>{{$oficina->oficina}}</td>
                    <td align="center" valign="middle" >{{$oficina->central?'SI':'NO'}}</td>
                    <td>
                      <input type="text" class="form-control" name="oficina[{{$oficina->id}}][telf]" 
                      value="{{$oficina->telf}}">
                    </td>

                    <td>
                      <input type="text" class="form-control" name="oficina[{{$oficina->id}}][nit]" 
                      value="{{$oficina->nit}}">
                    </td>
                    <td>
                     <input type="text" class="form-control" name="oficina[{{$oficina->id}}][num_patronal]" 
                      value="{{$oficina->num_patronal}}">
                    </td>
                    <td>
                      <textarea type="text" class="form-control" name="oficina[{{$oficina->id}}][direccion]" 
                      >{{$oficina->direccion}} </textarea>
                    </td>                   
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <div class=" row text-right">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn_color"> Guardar Ajustes </button>
                </div>
              </div>
          </div>
          
          <div id="cargo" class="tab-pane fade">
             
            <div class="col-sm-6">
            <table class="table ajustes_planillas_tabla dataTables-cargo-profesiones table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Cargo</th>                          
                    <th>Opciones</th>                                       
                  </tr>
                </thead>
                <tbody>
                  @foreach( $cargos as $cargo)
                  <tr>                  
                    <td>                      
                      <input type="text" class="form-control" name="cargo[{{$cargo->id}}][cargo]" 
                      value="{{$cargo->cargo}}" {{$cargo->id==21 ? 'readonly':'' }}>
                    </td>
                    <td>                     
                    
                      <a href='{{ url("/delete/cargo/$cargo->id") }}' class="btn btn_color" 
                        title="Eliminar Cargo {{$cargo->cargo}}"
                            data-toggle="tooltip"
                            data-placement="top"
                            data-method="DELETE"
                            data-confirm-title="Eliminar Cargo {{$cargo->cargo}}"
                            data-confirm-text="Esta seguro que quiere eliminar este cargo"
                            data-confirm-delete="Borrar"
                            style="background: #F44336;" >
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>    
                    </td>                   
                  </tr>
                  @endforeach
                </tbody>
            </table>
            </div>

            <div class=" row text-right">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn_color"> Guardar Ajustes </button>
                </div>
            </div>
          </div>

          <div id="profesion" class="tab-pane fade">
            
            <div class="col-sm-6">
             <table class="table ajustes_planillas_tabla dataTables-cargo-profesiones table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Profesión</th>                          
                    <th>Opciones</th>                                       
                  </tr>
                </thead>
                <tbody>
                  @foreach( $profesiones as $profesion)
                  <tr>                  
                    <td>
                      <input type="text" class="form-control" name="profesion[{{$profesion->id}}][profesion]" 
                      value="{{$profesion->profesion}}" {{$profesion->id==21 ? 'readonly':'' }} >
                    </td>
                    <td>    
                      <a href='{{ url("/delete/profesion/$profesion->id") }}' class="btn btn_color" 
                        title="Eliminar Cargo {{$profesion->profesion}}"
                            data-toggle="tooltip"
                            data-placement="top"
                            data-method="DELETE"
                            data-confirm-title="Eliminar Cargo {{$profesion->profesion}}"
                            data-confirm-text="Esta seguro que quiere eliminar este profesion"
                            data-confirm-delete="Borrar"
                            style="background: #F44336;" >
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>    
                    </td>                   
                  </tr>
                  @endforeach
                </tbody>
              </table>
            
            </div>
            <div class=" row text-right">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn_color"> Guardar Ajustes </button>
                </div>
            </div>
          </div>

          <div id="permisos" class="tab-pane fade">
            
            <div class="col-sm-6">
               <h4 class="">Parámetros</h4>
              <table class="table ajustes_planillas_tabla table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Pais</th>
                             
                    <th>Nº de Horas máximas por permiso</th>
                    <th>Nº de Días máximos por permiso</th>                    
                                 
                  </tr>
                </thead>

                <tbody>
                  @foreach($paises as $pais)
                  <tr>
                    <td><b>{{$pais->pais}}</b></td>
                    <td>
                      <input type="text" class="form-control" name="pais[{{$pais->id}}][n_horas]" 
                      value="{{$pais->n_horas}}">
                    </td>

                    <td>
                      <input type="text" class="form-control" name="pais[{{$pais->id}}][n_dias]" 
                      value="{{$pais->n_dias}}">
                    </td>                
                      
                  </tr>


                    @endforeach      
                  </tbody>
              </table>
            </div>
            
            <div class="col-sm-6">
              <h4 class="">Motivos de Permiso</h4>
             <table class="table ajustes_planillas_tabla dataTables-cargo-profesiones table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Motivo del permiso</th>                          
                    <th>Opciones</th>                                       
                  </tr>
                </thead>
                <tbody>
                  @foreach( $motivos as $motivo)
                  <tr>                  
                    <td>
                      <input type="text" class="form-control" name="motivo_permiso[{{$motivo->id}}][motivo]" 
                      value="{{$motivo->motivo}}" >
                    </td>
                    <td>    
                      <a href='{{ url("/delete/motivo/$motivo->id") }}' class="btn btn_color" 
                        title="Eliminar Cargo {{$motivo->motivo}}"
                            data-toggle="tooltip"
                            data-placement="top"
                            data-method="DELETE"
                            data-confirm-title="Eliminar Cargo {{$motivo->motivo}}"
                            data-confirm-text="Esta seguro que quiere eliminar este motivo"
                            data-confirm-delete="Borrar"
                            style="background: #F44336;" >
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>    
                    </td>                   
                  </tr>
                  @endforeach
                </tbody>
              </table>
            
            </div>
            <div class=" row text-right">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn_color"> Guardar Ajustes </button>
                </div>
            </div>
          </div>
          <div id="vacaciones" class="tab-pane fade">
            
            <div class="col-sm-6">
               <h4 class="">Parámetros</h4>
              <table class="table ajustes_planillas_tabla table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Pais</th>                             
                    
                    <th>Nº de Días por Mes</th>                    
                                 
                  </tr>
                </thead>

                <tbody>
                  @foreach($paises as $pais)
                  <tr>
                    <td><b>{{$pais->pais}}</b></td>
                    <td>
                      <input type="text" class="form-control" name="pais[{{$pais->id}}][vacaciones]" 
                      value="{{$pais->vacaciones}}">
                    </td>                                 
                      
                  </tr>
                  @endforeach      
                  </tbody>
              </table>
            </div>           
            
            <div class=" row text-right">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn_color"> Guardar Ajustes </button>
                </div>
            </div>
          </div>
          </form>
          <div id="agregar" class="tab-pane fade">

             <div class="row">     
              
                  <form class="form_ajustes"  action="{{url('/create/cargo_profesion')}}" method="get">
                    <div class="form-group col-xs-12" style="  margin-bottom: 30px;" >
                          <input  type="text" required class="form-control input_inline" name="cargo">
                          <input type="hidden"  name="tipo" value="cargo">
                          <button type="submit" class="btn btn_color">Agregar Cargo</button>
                    </div>
                  </form>
                

              
                  <form class="form_ajustes" id="form_profesion" action="{{url('/create/cargo_profesion')}}" method="get">
                    <div class="form-group col-xs-12" style="  margin-bottom: 30px;" >
                          <input type="text" required class="form-control input_inline" name="profesion">
                          <input  type="hidden"  name="tipo" value="profesion">
                          <button type="submit" class="btn btn_color">Agregar Profesión</button>
                    </div>
                  </form>            

              
                  <form class="form_ajustes"  action="{{url('/create/motivo_permiso')}}" method="get">
                    <div class="form-group col-xs-12" style="  margin-bottom: 30px;" >
                          <input type="text" required class="form-control input_inline" name="motivo_permiso">
                          <input  type="hidden"  name="tipo" value="motivo_permiso">
                          <button type="submit" class="btn btn_color">Agregar Motivo de Permiso</button>
                    </div>
                  </form>
            </div>
           
          </div>

      </div>
      @role('Admin')
      <div class="row">
        <div class="col-xs-12">
          <form class="form_email" class="ajax_email_prueba">
          <input type="email" required class="form-control email_prueba" style="display: inline-block;width: auto;">
          
          <button type="submit" class=" email_btn btn btn_color"> Enviar correo </button>
          
          <label class="load_email_prueba"></label>
          </form>
        </div>
      </div>
      @endrole
            
        
      


@stop

@section('scripts')

<script> 

$(document).on('submit', '.form_email', function(event) {
    event.preventDefault();
    email=$('input.email_prueba').val();
    $('.load_email_prueba').html('<i class="glyphicon glyphicon-refresh"></i> Enviando');
    
    $('.form_email input,.email_btn').attr('disabled', true);
    
    $.ajax({
      url: '{{url("/email_prueba")}}',
      type:'POST',  
      dataType: 'json',
      data: {email: email},
    })
    .done(function(data) {
      $('.load_email_prueba').html('correo enviado');
    })
    .fail(function() {
      $('.load_email_prueba').html('correo fallido');
    })
    .always(function(data) {
      console.log(data)
      $('.form_email input,.email_btn').attr('disabled', false);
    });
    
}); 
$(document).on('submit', '.form_ajustes', function(event) {        
    $(this)
    .find(':input[type=submit]')
    .attr('disabled', true)
    .html('<i class="glyphicon glyphicon-refresh"></i> Guardando Informacion')
    .css('color', '#000');;
  }); 
//////////////////////////////////////////////////////////////////////////////////
  //salvador calculo acumulado aguinaldo
  
</script>
     
@stop