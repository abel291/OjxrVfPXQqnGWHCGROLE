@extends('layouts.app')

@section('page-title', 'Permisos')

@section('content')

<h1 class="page-header">Permisos</h1>

@include('partials.messages')


<div class="row" style="margin-bottom: 20px;">
    <div class="col-xs-12 text-right">

       

        @if(Entrust::hasRole('Administradora'))
        <form class="form-inline"  action="{{url('/create/permiso')}}" method="get">   
            <div class="form-group ">                   
                <select class="form-control" name="id" required>
                    <option></option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group ">
                <button type="submit" class="btn btn_color btn_azul">Crear Permiso</button>
            </div>
        </form>
        @else
        <a href="{{url('/create/permiso')}}"  class="btn btn_color btn_azul">Crear Permiso</a href="!#">
        @endif
    
    </div>

</div>


<table class="table dataTables-empleados table_planillas table-bordered table-hover">
    <thead>
        <tr>
            <th>No Empleado</th>
            <th>Nombre</th>         
            <th>País</th>                               
            <th>Tiempo</th>            
            <th>Fecha Solicitud</th>       
            <th>Status</th>
            <th>Opciones</th>       

        </tr>
    </thead>
    <tbody>

        @foreach($permisos as $permiso)
            <tr>
                <td>{{$permiso->user->n_contrato}}</td>
                <td>{{$permiso->user->first_name}} {{$permiso->user->last_name}}</td>
                <td>{{$permiso->oficina->pais->pais}}</td>           
                <td>{{$permiso->num_dh}} {{$permiso->dh}}</td>  
                <td>{{$permiso->created_at->format('d-m-Y')}} 
                    @if($permiso->created_at->format('d-m-Y')==date('d-m-Y')) 
                        <span class="label label-success "> Hoy</span>
                    @endif

                </td> 
                <td>
                    @if($permiso->aprobacion_coordinadora==0)                            
                        <span style="font-size:90%;font-weight:normal;" class="label label-warning ">Pendiente</span>          
                    @elseif($permiso->aprobacion_coordinadora==1)          
                        <span style="font-size:90%;font-weight:normal;" class="label label-success "><i class="fa fa-check"></i> Aprobado</span>
                    @elseif($permiso->aprobacion_coordinadora==2)
                        <span style="font-size:90%;font-weight:normal;" class="label label-danger "><i class="fa fa-close"></i> Rechazada</span>
                    @endif

                </td>
                <td>         
                    
                    <a href='{{url("/edit/permiso/$permiso->id")}}' class="btn btn_color">
                    @if(
                            $permiso->aprobacion_coordinadora!=0 ||
                            Entrust::hasRole(['Directora','Contralora']) && auth()->user()!=$permiso->user                            
                        )                        
                        Ver
                    @else                    
                        Editar
                    @endif

                    </a>
                    @if(
                    (   
                        Entrust::hasRole(['Directora','Contralora']) && auth()->user()->id==$permiso->user->id ||
                        Entrust::hasRole(['Administradora','Coordinadora'])) && $permiso->aprobacion_coordinadora==0 
                    )
                    <a href='{{ url("/delete/permiso/$permiso->id") }}'  class="btn btn_color btn_rojo" 
                        title="Eliminar Permiso "
                        data-toggle="tooltip"
                        data-placement="top"
                        data-method="DELETE"
                        data-confirm-title="Eliminar Permiso"
                        data-confirm-text="Esta seguro de querer eliminar este permiso"
                        data-confirm-delete="Borrar"
                         >
                        Borrar
                    </a>    
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    
    @stop
    @section('scripts')

    <script>


                /*$('.descargar_planilla').click(function(event) {
                    $(this).attr('disabled', true);

                    setInterval(function(){
                        $(this).removeAttr('disabled');
                    },1000)
                });*/

                $('.dataTables-empleados').DataTable({
                    
                    "info": false,
                    "pageLength": 15,
                    "language": {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ningún dato disponible en esta tabla",
                        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix":    "",
                        "sSearch":         "Buscar:",
                        "sUrl":            "",
                        "sInfoThousands":  ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "Último",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }                    
                    }
                });


            </script>
            @stop