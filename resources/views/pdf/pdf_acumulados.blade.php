	@include('pdf.pdf_encabezado')
	
	<h2>Acumulados</h2>
	<table class="datos_em" style="page-break-after:always;">
		<tr>
			<td  colspan="2"></td>			
			<td class="titulo_td" align="center" colspan="2">
				ACUMULADOS
			</td>		

		</tr>
		<tr class="titulo_tr" >
			<td style="width: 20px;"  >Nº</td>
			<td style="width: 130px;" >Nombres y Apellidos</td>							

			<td>{{$pais->campo->acumulado_aguinaldo}}</td>

			<td>{{$pais->campo->acumulado_indemnizacion}}</td>					
		</tr>		

		@foreach($planilla->empleados as $empleado)
		<tr class="empleados" >
			<td>{{$empleado->id}}</td>
			<td>{{$empleado->nombre}}</td>

			<td>{{number_format($empleado->acumulado->aguinaldo,2)}}</td>
			<td>{{number_format($empleado->acumulado->indemnizacion,2)}}</td>			
	
		</tr>
		@endforeach

		<tr class="empleados totales" >
			<td  colspan="2" style="text-align: right;"><b>TOTAL</b></td>						

			<td>{{number_format($planilla->acumulados->sum('aguinaldo'),2)}}</td>
			<td>{{number_format($planilla->acumulados->sum('indemnizacion'),2)}}</td>

		</tr>
	</table>
