<!--<div class="panel panel-default">
    <div class="panel-heading">@lang('app.user_details')</div>
    <div class="panel-body">
        
    </div>
</div>-->
<div class="row">
    <div class="col-xs-12">

        <div class="form-group col-sm-4">
            <label for="oficina_id">Oficina</label>           
            
            <select required name="oficina_id" class="form-control">                
                @if(Entrust::hasRole(['Administradora','Coordinadora','Directora']))
                <option value="{{auth()->user()->oficina->id}}" >{{auth()->user()->oficina->oficina}}</option>
                @else
                    @foreach(Vanguard\Oficina::get() as $oficina)
                    <option value="{{$oficina->id}}" 
                        @if($edit)
                        {{ $user->oficina_id==$oficina->id ? "selected" : ''}}
                        @endif
                    >{{$oficina->oficina}}</option>
                    @endforeach
                @endif
            </select>
           
           
        </div>

        <div class="form-group col-sm-4">
            <label  required for="status">Estatus</label>
            <select name="status" class="form-control">
                <option value="1" @if($edit && $user->status==1) selected @endif>Activo</option>
                <option value="0" @if($edit && $user->status==0) selected @endif>Inactivo</option>
            </select>
           
        </div>

        <div class="form-group col-sm-4">
            <label required for="">@lang('app.role') </label>
            <select name="role" class="form-control">
                @if($edit && Entrust::hasRole(['Administradora','Coordinadora','Directora']))
                    <option value="{{$user->roles->first()->id}}">{{$user->roles->first()->name}}</option>                   
                @elseif(Entrust::hasRole(['Administradora','Coordinadora','Directora']))
                    <option value="2">Colega</option>

                @else
                    @foreach($roles as $id => $role)
                        <option value="{{$id}}" @if($edit && $user->roles->first()->id==$id ) selected @endif >{{$role}}</option>
                    @endforeach
                @endif                
            </select>
        </div> 
            
    </div>


    <div class="col-xs-12">
        <div class="form-group col-sm-4">
            <label class="">Nombres</label>            
            <input type="text" class="form-control" required name="first_name" value="{{ $edit ? $user->first_name : '' }}">
        </div>

        <div class="form-group col-sm-4">
            <label class="">Apellidos</label>            
            <input type="text" class="form-control" required name="last_name" value="{{ $edit ? $user->last_name : '' }}">
        </div>

        <div class="form-group col-sm-4">
            <label># No contrato</label>
            <input type="text" class="form-control" required name="n_contrato" value="{{ $edit ? $user->n_contrato : '' }}">           
        </div>        
    </div>

    <div class="col-xs-12">
        <div class="form-group col-sm-4 ">
            <label >Tipo de Documento</label>

            <select name="tipo_documento_id" required class="form-control">
                @foreach(Vanguard\Tipo_documento::get() as $tipo_documento)
                <option value="{{$tipo_documento->id}}"
                    @if($edit){{$user->tipo_documento_id==$tipo_documento->id ? "selected" : ''}}  @endif
                    >{{$tipo_documento->tipo_documento}}
                </option> 
                @endforeach
            </select>            
        </div>
        <div class="form-group col-sm-4">
            <label >Documento</label> 
            <input type="text" class="form-control" required name="documento" value="{{ $edit ? $user->documento : '' }}">  
        </div>
        <div class="form-group col-sm-4">
            <label >Cargo</label>
            <select required name="cargo_id" class="form-control">                
                <option value="21">Sin Cargo</option>
                @foreach(Vanguard\Cargo::orderBy('cargo','asc')->get() as $cargo)
                <option value="{{$cargo->id}}" 
                    @if($edit)
                    {{ $user->cargo_id==$cargo->id ? "selected" : ''}}
                    @endif
                >{{$cargo->cargo}}
                </option>
                @endforeach
               
            </select>
                                        
        </div>
    </div>

    <div class="col-xs-12">
        <div class="form-group col-sm-4">
            <label >Fecha de inicio</label> 
            <div class="form-group">
                <div class='input-group date'>
                    <input type='text' name="fecha_inicio" id='fecha_inicio' value="{{ $edit ? $user->fecha_inicio : '' }}" class="form-control" />
                    <span class="input-group-addon" style="cursor: default;">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>           
        </div>        
        <div class="form-group col-sm-4">
            <label >Fecha de Finalización</label>
            <div class="form-group">
                <div class='input-group date'>
                    <input type='text' name="fecha_finalizacion" id='fecha_finalizacion' value="{{ $edit ? $user->fecha_finalizacion : '' }}" class="form-control" />
                    <span class="input-group-addon" style="cursor: default;">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div> 
                       
        </div>
        <div class="form-group col-sm-4">
            <label >Profesión </label>
            <select required name="profesion_id" class="form-control">                
                <option value="21">Sin Profesion</option>
                @foreach(Vanguard\Profesion::orderBy('profesion','asc')->get() as $profesion)
                <option value="{{$profesion->id}}" 
                    @if($edit)
                    {{ $user->profesion_id==$profesion->id ? "selected" : ''}}
                    @endif
                >{{$profesion->profesion}}
                </option>
                @endforeach
               
            </select>
                                     
        </div> 
    </div>
    <div class="col-xs-12">
        <div class="form-group col-sm-4">
            <label >No. Afiliación </label> 
                <input type="text" class="form-control" required name="n_afiliacion" 
                value="{{$edit ? $user->n_afiliacion : '' }}">
        </div>
    </div>
    <div class="col-xs-12">        

        <div class="form-group col-sm-4">
            <label >Identificación Tributaria </label> 
                <input type="text" class="form-control" required name="n_identificacion_tributaria" 
                value="{{$edit ? $user->n_identificacion_tributaria : '' }}">
        </div>

        <div class="form-group col-sm-4">
            <label >Régimen  Tributario</label>
                <input type="text" class="form-control" required name="regimen_tributario" value="{{ $edit ? $user->regimen_tributario : '' }}">
        </div>
        <div class="form-group col-sm-4">
            <label >Tipo de Empleado</label>
            <select name="categoria_id" required class="form-control">
                @foreach(Vanguard\Categoria::get() as $categoria)
                <option value="{{$categoria->id}}" 
                    @if($edit)
                    {{$user->categoria_id==$categoria->id ? "selected" : ''}} 
                    @endif
                    >{{$categoria->categoria}}</option>
                @endforeach
            </select>
        </div>                 
    </div>

    <div class="col-xs-12">
        <div class="form-group col-sm-4">
            <label >Teléfono</label>
            <input type="text" class="form-control" required name="phone" value="{{ $edit ? $user->phone : '' }}">       
        </div>

        <div class="form-group col-sm-4">
            <label >Skype</label>
            <input type="text" class="form-control" required name="skype" value="{{ $edit ? $user->skype : '' }}">           
        </div>

        <div class="form-group col-sm-4">
            <label >Celular</label>
            <input type="text" class="form-control" required name="cellphone" value="{{ $edit ? $user->cellphone : '' }}">           
        </div>
    </div>

    <div class="col-xs-12">
        <div class="form-group col-sm-4">
            <label > Sexo </label>
            <select class="form-control" required name="sexo">
                <option value="1"  @if($edit) {{$user->sexo==1 ? 'selected' : ''}} @endif  >Mujer</option> 
                <option value="0"  @if($edit) {{$user->sexo==0 ? 'selected' : ''}} @endif  >Hombre</option> 
            </select>        
        </div>  
        <div class="form-group col-sm-4">
            <label > Fecha de Nacimiento </label>
            <div class="form-group">
                <div class='input-group date'>
                    <input type='text' name="birthday" required id='birthday' value="{{ $edit ? $user->birthday : '' }}" class="form-control" />
                    <span class="input-group-addon" style="cursor: default;">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>       
        </div>                   
    </div>

    <div class="col-xs-12">

        <div class="form-group col-sm-4">
            <label >Contacto de emergencia</label>       
            <input type="text" class="form-control" required name="contacto_emergencia" value="{{ $edit ? $user->contacto_emergencia : '' }}">
        </div>

        <div class="form-group col-sm-4">
            <label >Telef. Contacto Emergencia </label>     
            <input type="text" class="form-control" required name="tlf_contacto_emergencia" value="{{ $edit ? $user->tlf_contacto_emergencia : '' }}">
        </div>

        <div class="form-group col-sm-4">
            <label >Tipo de Sangre </label>           
            <input type="text" class="form-control" required name="tipo_sangre" value="{{ $edit ? $user->tipo_sangre : '' }}">
        </div>
    </div>  
    <div class="col-xs-12">
        <div class="form-group col-sm-4">
            <label>Salario Base ({{ auth()->user()->oficina->pais->moneda_simbolo }})</label>            
            <input type="text" class="form-control" required name="salario_base" 
            value="{{ $edit ? $user->salario_base : '' }}">
        </div> 
        <div class="form-group col-sm-4">
            <label>Vacaciones acumuladas </label>            
            <input type="text" class="form-control" required name="acumulado_vacaciones" 
            value="{{ $edit ? $user->acumulado_vacaciones : '' }}">
        </div>   
    </div> 
     <div class="col-xs-12">
        <div class="form-group col-sm-8">
            <label class="">Dirección</label>            
            <input type="text" class="form-control" required name="address" value="{{ $edit ? $user->address : '' }}">
        </div>   
    </div> 

    @if ($edit)
    <div class="col-md-12 text-right">
        <button type="submit" class="btn btn_color" id="update-details-btn">
            <i class="fa fa-refresh"></i>
            Guardar datos del Colega
        </button>
    </div>
    @endif
</div>