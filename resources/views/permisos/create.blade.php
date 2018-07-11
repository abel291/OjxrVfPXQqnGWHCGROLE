@extends('layouts.app')

@section('page-title', 'Crear Permiso')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Solicitud de permiso
           
            
        </h1>
    </div>
</div>

@include('partials.messages')

	<form class="form-horizontal permiso_form" action="{{url('/store/permiso')}}" method="post">
		{!! csrf_field() !!}
		<input type="hidden" name="user_id" value="{{$user->id}}">
		<input type="hidden" name="oficina_id" value="{{$user->oficina_id}}">
		@if($edit)
		<input type="hidden" name="permiso_id" value="{{$permiso->id}}">
		@endif
		
	<div class="row">
			
		<div class="form-group">
		    <label class="control-label col-sm-2">Nombre y Apellido:</label>
		    <div class="col-sm-4">
		      	<input type="text" id="a1" class="form-control" disabled name="nombre" value="{{$user->first_name}} {{$user->last_name}}" >
		    </div>
		</div>		

		<div class="form-group">
		    <label class="control-label col-sm-2">Nº contrato:</label>
		    <div class="col-sm-4">
		      	<input type="text" id="a3" class="form-control" disabled  value="{{$user->n_contrato}} " >
		    </div>
		</div>

		<div class="form-group">
		    <label class="control-label col-sm-2">Cargo:</label>
		    <div class="col-sm-4">
		      	<input type="text" id="a2" class="form-control" disabled value="{{$user->cargo->cargo}}" >
		    </div>
		</div>		

		<div class="form-group">
		    <label class="control-label col-sm-2">Oficina:</label>
		    <div class="col-sm-4">
		      	<input type="text" id="a4" class="form-control" disabled value="{{$user->oficina->oficina}}">
		    </div>
		</div>

		<div class="form-group">
		    <label class="control-label col-sm-2">Tipo de Permiso:</label>
		    <div class="col-sm-4" >
		      	<select class="form-control" name="tipo" required>		      		
		      		@if($edit)
		      		<option>{{$permiso->tipo}}</option>
		      		@else
		      		<option></option>
		      		@endif
		      		@foreach($motivo_permiso as $motivo)
		      		<option>{{$motivo->motivo}}</option>
		      		@endforeach
		      	</select>
		    </div>
		</div>

		<div class="form-group">
		    <label class="control-label col-sm-2">Motivo:</label>
		    <div class="col-sm-8">
		      	<textarea  class="form-control" rows="4" name="motivo" placeholder="Si se necesita aclarar detalles ">{{$edit? $permiso->motivo : '' }}</textarea>
		    </div>
		</div>
		<div class="form-group datepicker">
		    <label class="control-label col-sm-2" >Fecha Permiso:</label>
		    <label class="control-label col-xs-1" >Inicio:</label>
		    <div class="col-sm-3">
		      	<div class='input-group date' id='datetimepicker1'>
	                <input type='text' class="form-control" name="fecha_inicio" required 
	                value="{{$edit? $permiso->fecha_inicio : '' }}" />
	                <span class="input-group-addon">
	                    <span class="glyphicon glyphicon-calendar"></span>
	                </span>	                
	            </div>
		    </div>

			<label class="control-label col-xs-1">Fin:</label>
		    <div class="col-sm-3">
			    <div class='input-group date' id='datetimepicker2'>
	                <input type='text' class="form-control" name="fecha_fin" required 
	                value="{{$edit? $permiso->fecha_fin : '' }}" />
	                <span class="input-group-addon">
	                    <span class="glyphicon glyphicon-calendar"></span>
	                </span>		      	
			    </div>
			</div>		
		</div>
		<div class="form-group">
		    <label 
		    class="control-label col-sm-2"
		    title="
		    Maximo de horas: {{$user->oficina->pais->n_horas}} horas <br> 
		    Maximo de dias: {{$user->oficina->pais->n_dias}} dias "
			data-toggle="tooltip"
			data-html="true"
			data-placement="top">Tiempo de Permiso <i class="fa fa-question-circle" ></i></label>
		    
		    <div class="col-sm-2">
		      	<input name="num_dh" type="number" min="0" class="form-control"  required placeholder="Nº de horas o dias" value="{{$edit? $permiso->num_dh : '' }}">
		    </div>

		    <div class="col-sm-2">
		      	<label class="radio-inline"><input type="radio"   name="dh"{{$edit && $permiso->dh=="Horas"?'checked':''}} value="Horas">Horas</label>
				<label class="radio-inline"><input type="radio" {{!$edit?'checked':''}} name="dh"{{$edit && $permiso->dh=="Dias"?'checked':''}} value="Dias">Días</label>
		    </div>
		    
		</div>

		<div class="col-md-12 text-right">

	        @if(	
	        		(Entrust::hasRole(['Administradora','Coordinadora']) && $edit&& $aprobacion_coordinadora==0) || 
	        		(Entrust::hasRole(['Directora','Contralora']) && auth()->user()->id==$user->id && $aprobacion_coordinadora==0)
	        		 || !$edit
	        		
	        	)	
		        <button type="submit" class="btn btn_color" >
		           {{$edit?'Guardar Cambios':' Crear permiso'}}
		        </button>
	        @endif
	        @if(Entrust::hasRole('Coordinadora') && $edit && !$aprobacion_coordinadora)
	        <a href='{{url("/aprobacion/permiso/$permiso->id?aprobacion=1")}}' class="btn btn_color aprobar">
				<i class="fa fa-check-square"></i> Aprobar Permiso
			</a> 
			<a href='{{url("/aprobacion/permiso/$permiso->id?aprobacion=2")}}' class="btn btn_color btn_rojo aprobar">
				 Rechazar
			</a> 
	        @endrole
    	</div>
	</form>


@stop

@section('scripts')
{!! HTML::script('assets/js/moment.min.js') !!}
{!! HTML::script('assets/js/moment_es.js') !!}

{!! HTML::script('assets/js/bootstrap-datepicker.min.js') !!}
{!! HTML::script('assets/js/bootstrap-datepicker.es.min.js') !!}


<script type="text/javascript">
	@if(Entrust::hasRole(['Directora','Contralora']) && auth()->user()->id!=$user->id || $aprobacion_coordinadora!=0)
	$('.permiso_form input,.permiso_form textarea,.permiso_form select').attr('disabled', true);
	@endif
	
	$('#datetimepicker1 input , #datetimepicker2 input')
	.datepicker({
        format: 'dd-mm-yyyy',
        language:'es',	
    });

    $(document).on('submit', '.permiso_form', function(event) {	
			event.preventDefault();	
			//calculo_totales_inputs();			
			$(this)
			.find(':input[type=submit]')
		    .attr('disabled', true)
		    .html('<i class="glyphicon glyphicon-refresh"></i> Guardando datos')
		    .css('color', '#000')
		    ;
		    this.submit();		    
		});

	$(document).on('click', 'a.aprobar', function(event) {	
			//event.preventDefault();	
			//calculo_totales_inputs();			
			$(this)			
		    .attr('disabled', true)
		    .html('<i class="glyphicon glyphicon-refresh"></i> Procesando')
		    .css('color', '#000')
		    ;		    	    
		});	
</script>
@stop
@section('styles')
    {!! HTML::style('assets/css/bootstrap-datepicker.min.css') !!}
    {!! HTML::style('assets/plugins/croppie/croppie.css') !!}
@stop