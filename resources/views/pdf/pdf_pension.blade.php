
@include('pdf.pdf_encabezado')
<h2>Pensión</h2>
<table class="datos_em" style="page-break-after:always;" >
	<tr class="titulo_tr" >
		<td style="width: 20px;"  >Nº</td>
		<td style="width: 130px;" >Nomrbes y Apellidos</td>		
		<td rowspan="1" width="11">{{$campo->total_salario}}</td>
		<td rowspan="1" width="22">TOTAL PENSIÓN</td>
	</tr>

	@foreach($planilla->empleados as $empleado)
	<tr class="empleados" >
		<td>{{$empleado->id}}</td>
		<td>{{$empleado->nombre}}</td>
		<td>{{number_format($empleado->total_salario,2)}}</td>				
		<td>{{number_format($empleado->total_pension,2)}}</td>		
	</tr>
	@endforeach

	<tr class="empleados totales">
		<td colspan="2" style="text-align:right;"> TOTALES</td>
		<td>{{number_format($planilla->empleados->sum('salario_base'),2)}}</td>
		<td>{{number_format($planilla->empleados->sum('total_pension'),2)}}</td>
	</tr>
</table>		

