<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion($datos){
	$resultados = array();
	$res=mysql_query("SELECT cve, numero, nombre FROM plazas WHERE estatus!='I' ORDER BY lista");
	while($row=mysql_fetch_array($res)){
		$resultados[$row['cve']] = array('plaza' => $row['numero'].' '.$row['nombre']);
	}

     $res = mysql_query("SELECT plaza, SUM(IF(tipo_venta=0 AND tipo_pago=1 AND estatus!='C', monto, 0)) as efectivo, SUM(IF(tipo_venta=0 AND tipo_pago=5 AND estatus!='C', monto, 0)) as t_credito, SUM(IF(tipo_venta=0 AND tipo_pago=7 AND estatus!='C', monto, 0)) as t_debito, SUM(IF(estatus!='C', copias*costo_copias, 0)) as copias, SUM(IF(tipo_venta=0 AND tipo_pago=2 AND estatus!='C', monto, 0)) as credito, SUM(IF(tipo_venta=2 AND estatus!='C', 1, 0)) as cortesias, SUM(IF(tipo_venta=3 AND tipo_pago=1 AND estatus!='C', monto, 0)) as reposicion_efectivo, SUM(IF(tipo_venta=3 AND tipo_pago IN (5, 7) AND estatus!='C', monto, 0)) as reposicion_tb, SUM(IF(costo_especial=1 AND estatus!='C',1,0)) as medio_pago FROM cobro_engomado WHERE fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' GROUP BY plaza") or die(mysql_error());
     while($row = mysql_fetch_assoc($res)){
     	$resultados[$row['plaza']]['ventas_efectivo']=$row['efectivo'];
     	$resultados[$row['plaza']]['ventas_t_credito']=$row['t_credito'];
     	$resultados[$row['plaza']]['ventas_t_debito']=$row['t_debito'];
     	$resultados[$row['plaza']]['copias']=$row['copias'];
     	$resultados[$row['plaza']]['credito']=$row['credito'];
     	$resultados[$row['plaza']]['cortesias']=$row['cortesias'];
     	$resultados[$row['plaza']]['reposicion_efectivo']=$row['reposicion_efectivo'];
     	$resultados[$row['plaza']]['reposicion_tb']=$row['reposicion_tb'];
     	$resultados[$row['plaza']]['medio_pago']=$row['medio_pago'];
     }
     $res = mysql_query("SELECT a.plaza, SUM(IF(b.tipo_venta=0 AND b.tipo_pago=1, a.devolucion, 0)) as efectivo, SUM(IF(b.tipo_venta=0 AND b.tipo_pago = 5, a.devolucion, 0)) as t_credito, SUM(IF(b.tipo_venta=0 AND b.tipo_pago = 7, a.devolucion, 0)) as t_debito FROM devolucion_certificado a INNER JOIN cobro_engomado b ON b.plaza = a.plaza AND b.cve = a.ticket WHERE a.estatus!='C' AND a.fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' GROUP BY a.plaza");
     while($row = mysql_fetch_assoc($res)){
     	$resultados[$row['plaza']]['ventas_t_credito']-=$row['t_credito'];
     	$resultados[$row['plaza']]['ventas_t_debito']-=$row['t_debito'];
     	$resultados[$row['plaza']]['ventas_efectivo']-=$row['efectivo'];
     }
     $res = mysql_query("SELECT plaza, SUM(IF(tipo_pago=2 AND forma_pago IN (2,3,4), monto, 0)) as rec_bancos, SUM(IF(tipo_pago=2 AND forma_pago=5, monto, 0)) as rec_tb, SUM(IF(tipo_pago=2 AND forma_pago = 1, monto, 0)) as rec_efectivo, SUM(IF(tipo_pago=13 AND forma_pago IN (2,3,4), monto, 0)) as rec_pa_bancos, SUM(IF(tipo_pago=13 AND forma_pago=5, monto, 0)) as rec_pa_tb, SUM(IF(tipo_pago=13 AND forma_pago = 1, monto, 0)) as rec_pa_efectivo, SUM(IF(tipo_pago=6 AND forma_pago IN (2,3,4), monto, 0)) as pa_bancos, SUM(IF(tipo_pago=6 AND forma_pago = 1, monto, 0)) as pa_efectivo, SUM(IF(tipo_pago=6 AND forma_pago = 5, monto, 0)) as pa_tb, SUM(IF(forma_pago=9, monto, 0)) as pa_credito FROM pagos_caja WHERE estatus!='C' AND fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' GROUP BY plaza") or die(mysql_error());
      while($row = mysql_fetch_assoc($res)){
     	$resultados[$row['plaza']]['rec_bancos']=$row['rec_bancos'];
     	$resultados[$row['plaza']]['rec_tb']=$row['rec_tb'];
     	$resultados[$row['plaza']]['rec_efectivo']=$row['rec_efectivo'];
     	$resultados[$row['plaza']]['pa_transferencias']=$row['pa_bancos'];
     	$resultados[$row['plaza']]['rec_pa_tb']=$row['rec_pa_tb'];
     	$resultados[$row['plaza']]['rec_pa_efectivo']=$row['rec_pa_efectivo'];
     	$resultados[$row['plaza']]['rec_pa_bancos']=$row['rec_pa_bancos'];
     	$resultados[$row['plaza']]['pa_tb']=$row['pa_tb'];
     	$resultados[$row['plaza']]['pa_efectivo']=$row['pa_efectivo'];
     	$resultados[$row['plaza']]['pa_credito']=$row['pa_credito'];
     }
	$res=mysql_query("SELECT plaza, SUM(monto) as gastos FROM recibos_salidav WHERE fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' AND estatus='A' GROUP BY plaza");
	while($row=mysql_fetch_array($res)){
		$resultados[$row['plaza']]['salidas']=$row['gastos'];
	}

	/*$res=mysql_query("SELECT plaza, SUM(monto) as gastos FROM venta_servicios WHERE fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' AND estatus='A' GROUP BY plaza");
	while($row=mysql_fetch_array($res)){
		$resultados[$row['plaza']]['servicios']=$row['gastos'];
	}*/

	
	return $resultados;
}

function obtener_informacion2($datos){
	$resultados = array();
	$res=mysql_query("SELECT cve, numero, nombre FROM plazas WHERE estatus!='I' ORDER BY lista");
	while($row=mysql_fetch_array($res)){
		$resultados[$row['cve']] = array('plaza' => $row['numero'].' '.$row['nombre']);
	}

    $res=mysql_query("SELECT a.plaza, sum(if(a.engomado=19,1,0)) as t_rechazos,sum(if(a.engomado=3,1,0)) as t_doble_cero,sum(if(a.engomado=2,1,0)) as t_cero,sum(if(a.engomado=5,1,0)) as t_uno,sum(if(a.engomado=1,1,0)) as t_dos, sum(if(a.engomado=21,1,0)) as t_taxi, sum(if(a.engomado=22,1,0)) as t_privado, sum(if(a.engomado=23,1,0)) as t_exento
		FROM certificados a 
		WHERE a.fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' AND a.estatus!='C' GROUP BY a.plaza");
	
	while($row = mysql_fetch_assoc($res)){
		foreach($row as $campo => $valor){
			if ($campo != 'plaza'){
				$resultados[$row['plaza']][$campo] = $valor;
			}
		}
	}

	
	return $resultados;
}
require_once('validarloging.php');

if($_POST['cmd']==0){
?>

<div class="row justify-content-center">
	<div class="col-xl-12 col-lg-12 col-md-12">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Fecha Inicio</label>
			<div class="col-sm-4">
            	<input type="date" class="form-control" id="busquedafechaini" name="busquedafechaini" placeholder="Fecha Inicio" value="<?php echo date('Y-m');?>-01">
        	</div>
			<label class="col-sm-2 col-form-label">Fecha Fin</label>
			<div class="col-sm-4">
            	<input type="date" class="form-control" id="busquedafechafin" name="busquedafechafin" placeholder="Fecha Fin" value="<?php echo date('Y-m-d');?>">
        	</div>
        </div>
        
        <div class="form-group row">
        	<div class="col-sm-12" align="center">
	        	<button type="button" class="btn btn-primary" onClick="buscar();">
	            	Buscar
	        	</button>
        	</div>
        </div>
    </div>
</div>
<div class="row" id="resultadocorte">
	
</div>
<script>
	function buscar(){
		$.ajax({
		  url: 'resumeningreso2xplaza.php',
		  type: "POST",
		  data: {
			menu: $('#cvemenu').val(),
			cmd: 10,
			cveusuario: $('#cveusuario').val(),
			busquedafechaini: $('#busquedafechaini').val(),
			busquedafechafin: $('#busquedafechafin').val(),
    		cvemenu: $('#cvemenu').val(),
    		cveplaza: $('#cveplaza').val(),
    		cveusuario: $('#cveusuario').val()
		  },
			success: function(data) {
				$('#resultadocorte').html(data);
			}
		});
	}
</script>
<?php
}

if($_POST['cmd']==10){
	$res = obtener_informacion($_POST);
	$colspan = 1;
?>
	<table class="table">
	  <thead>
	  	<tr>
	     <th scope="col" style="text-align: center;">&nbsp;</th>
	     <th scope="col" style="text-align: center;" colspan="3">Ventas y Devoluciones</th>
	     <th scope="col" style="text-align: center;" colspan="3">Recuperaci&oacute;n de Cr&eacute;ditos</th>
	     <th scope="col" style="text-align: center;" colspan="3">Recuperaci&oacute;n de Vales a Cr&eacute;dito</th>
	     <th scope="col" style="text-align: center;" colspan="2">Cr&eacute;ditos</th>
	     <th scope="col" style="text-align: center;" colspan="3">Vales de Pago Anticipado</th>
	     <th scope="col" style="text-align: center;" colspan="2">Reposiciones</th>
	     <th scope="col" style="text-align: center;">&nbsp;</th>
	     <th scope="col" style="text-align: center;" colspan="5">Resumen</th>
	     <th scope="col" style="text-align: center;">&nbsp;</th>
	     <th scope="col" style="text-align: center;">&nbsp;</th>
	     <th scope="col" style="text-align: center;">&nbsp;</th>
	     </tr>
	     <tr>
	     <th scope="col" style="text-align: center;"h>Plaza</th>
	     <th scope="col" style="text-align: center;">Ventas Efectivo (Ya contando devoluciones)</th>
	     <th scope="col" style="text-align: center;">Tarjeta de Credito (Ya contando devoluciones)</th>
	     <th scope="col" style="text-align: center;">Tarjeta de Debito (Ya contando devoluciones)</th>
	     <th scope="col" style="text-align: center;">Recuperaci&oacute;n de Cr&eacute;ditos Banco</th>
	     <th scope="col" style="text-align: center;">Recuperaci&oacute;n de Cr&eacute;ditos Efectivo</th>
	     <th scope="col" style="text-align: center;">Recuperaci&oacute;n de Cr&eacute;ditos Tarjetas Bancarias</th>
	     <th scope="col" style="text-align: center;">Recuperaci&oacute;n de Cr&eacute;ditos Banco</th>
	     <th scope="col" style="text-align: center;">Recuperaci&oacute;n de Cr&eacute;ditos Efectivo</th>
	     <th scope="col" style="text-align: center;">Recuperaci&oacute;n de Cr&eacute;ditos Tarjetas Bancarias</th>
	     <th scope="col" style="text-align: center;">Creditos</th>
	     <th scope="col" style="text-align: center;">Vales de Credito</th>
	     <th scope="col" style="text-align: center;">Vales de Pago Anticipado Transferencias</th>
	     <th scope="col" style="text-align: center;">Vales de Pago Anticipado Tarjetas Bancarias</th>
	     <th scope="col" style="text-align: center;">Vales de Pago Anticipado Efectivo</th>
	     <th scope="col" style="text-align: center;">Reposiciones Efectivo</th>
	     <th scope="col" style="text-align: center;">Reposiciones Tarjeta Bancaria</th>
	     <th scope="col" style="text-align: center;">Servicios</th>
	     <th scope="col" style="text-align: center;">Recibos de Salida(Gastos)</th>
	     <th scope="col" style="text-align: center;">Copias</th>
	     <th scope="col" style="text-align: center;">Total de Efectivo</th>
	     <th scope="col" style="text-align: center;">Venta Efectiva(Bancos Total)</th>
	     <th scope="col" style="text-align: center;">Bancos</th>
	     <th scope="col" style="text-align: center;">&nbsp;</th>
	     <th scope="col" style="text-align: center;">Cortesias Generadas</th>
	     <th scope="col" style="text-align: center;">Medios Pagos Generados en el dia</th></tr>
	  </thead>
	  <tbody>
	<?php
		$totales = array();
		$i = 0;
		foreach($res as $idplaza => $row){
			echo '<tr>';
		    echo '<td align="left">'.utf8_encode($row['plaza']).'</td>';
			echo '<td align="right">'.number_format($row['ventas_efectivo'],2).'</td>';
			echo '<td align="right">'.number_format($row['ventas_t_credito'],2).'</td>';
			echo '<td align="right">'.number_format($row['ventas_t_debito'],2).'</td>';
			echo '<td align="right">'.number_format($row['rec_bancos'],2).'</td>';
			echo '<td align="right">'.number_format($row['rec_efectivo'],2).'</td>';
			echo '<td align="right">'.number_format($row['rec_tb'],2).'</td>';
			echo '<td align="right">'.number_format($row['rec_pa_bancos'],2).'</td>';
			echo '<td align="right">'.number_format($row['rec_pa_efectivo'],2).'</td>';
			echo '<td align="right">'.number_format($row['rec_pa_tb'],2).'</td>';
			echo '<td align="right">'.number_format($row['credito'],2).'</td>';
			echo '<td align="right">'.number_format($row['pa_credito'],2).'</td>';
			echo '<td align="right">'.number_format($row['pa_transferencias'],2).'</td>';
			echo '<td align="right">'.number_format($row['pa_tb'],2).'</td>';
			echo '<td align="right">'.number_format($row['pa_efectivo'],2).'</td>';
			echo '<td align="right">'.number_format($row['reposicion_efectivo'],2).'</td>';
			echo '<td align="right">'.number_format($row['reposicion_tb'],2).'</td>';
			echo '<td align="right">'.number_format($row['servicios'],2).'</td>';
			echo '<td align="right">'.number_format($row['salidas'],2).'</td>';
			echo '<td align="right">'.number_format($row['copias'],2).'</td>';
			$total_efectivo = $row['ventas_efectivo'] + $row['rec_efectivo']+$row['rec_pa_efectivo']+$row['pa_efectivo']+$row['reposicion_efectivo']+$row['copias']-$row['salidas'];
			echo '<td align="right">'.number_format($total_efectivo,2).'</td>';
			$total_venta = $total_efectivo + $row['salidas'] + $row['ventas_t_credito'] + $row['ventas_t_debito'] + $row['rec_bancos'] + $row['rec_tb'] + $row['rec_pa_bancos'] + $row['rec_pa_tb']+$row['pa_transferencias']+$row['pa_tb']+$row['reposicion_tb'];
			echo '<td align="right">'.number_format($total_venta,2).'</td>';
			$bancos_total = $total_venta - $row['salidas'];
			echo '<td align="right">'.number_format($bancos_total,2).'</td>';
			echo '<td>&nbsp;</td>';
			echo '<td align="right">'.number_format($row['cortesias'],0).'</td>';
			echo '<td align="right">'.number_format($row['medio_pago'],0).'</td>';
			echo '</tr>';
			$c=0;
			$array_totales[$c]+=$row['ventas_efectivo'];$c++;
			$array_totales[$c]+=$row['ventas_t_credito'];$c++;
			$array_totales[$c]+=$row['ventas_t_debito'];$c++;
			$array_totales[$c]+=$row['rec_bancos'];$c++;
			$array_totales[$c]+=$row['rec_efectivo'];$c++;
			$array_totales[$c]+=$row['rec_tb'];$c++;
			$array_totales[$c]+=$row['rec_pa_bancos'];$c++;
			$array_totales[$c]+=$row['rec_pa_efectivo'];$c++;
			$array_totales[$c]+=$row['rec_pa_tb'];$c++;
			$array_totales[$c]+=$row['credito'];$c++;
			$array_totales[$c]+=$row['pa_credito'];$c++;
			$array_totales[$c]+=$row['pa_transferencias'];$c++;
			$array_totales[$c]+=$row['pa_tb'];$c++;
			$array_totales[$c]+=$row['pa_efectivo'];$c++;
			$array_totales[$c]+=$row['reposicion_efectivo'];$c++;
			$array_totales[$c]+=$row['reposicion_tb'];$c++;
			$array_totales[$c]+=$row['servicios'];$c++;
			$array_totales[$c]+=$row['salidas'];$c++;
			$array_totales[$c]+=$row['copias'];$c++;
			$array_totales[$c]+=$total_efectivo;$c++;
			$array_totales[$c]+=$total_venta;$c++;
			$array_totales[$c]+=$bancos_total;$c++;
			$array_totales[$c]+=$row['cortesias'];$c++;
			$array_totales[$c]+=$row['medio_pago'];$c++;
		}
	$total_totales = count($array_totales);
	echo'<tr>
	     <th style="text-align:right;"></th>';
	foreach($array_totales as $k=>$v){
		if($k==($total_totales-2))
			echo '<th>&nbsp;</th><th style="text-align:right;">'.number_format($v,0);
		elseif($k==($total_totales-1))
			echo '<th style="text-align:right;">'.number_format($v,0);
		else
			echo '<th style="text-align:right;">'.number_format($v,2);
		echo '</th>';
	}
	echo '</table><br>';
	$res = obtener_informacion2($_POST);
	echo'<table width="100%" border="0">
	     <tr>
		 <th scope="col" style="text-align: center;">Plaza</th>
		 <th scope="col" style="text-align: center;">Rechazo</th>
		 <th scope="col" style="text-align: center;">Verificacion 00</th>
		 <th scope="col" style="text-align: center;">Verificacion 0</th>
		 <th scope="col" style="text-align: center;">Verificacion 1</th>
		 <th scope="col" style="text-align: center;">Verificacion 2</th>
		 <th scope="col" style="text-align: center;">Taxis Revistas</th>
		 <th scope="col" style="text-align: center;">Transportes Privados</th>
		 <th scope="col" style="text-align: center;">Verificacion Exento</th>
		 </tr>';
	foreach($res as $row){
		echo '<tr>';
		echo'<td align="left">'.utf8_encode($row['plaza']).'</td>';
		echo'<td align="center">'.$row['t_rechazos'].'</td>';
		echo'<td align="center">'.$row['t_doble_cero'].'</td>';
		echo'<td align="center">'.$row['t_cero'].'</td>';
		echo'<td align="center">'.$row['t_uno'].'</td>';
		echo'<td align="center">'.$row['t_dos'].'</td>';
		echo'<td align="center">'.$row['t_taxi'].'</td>';
		echo'<td align="center">'.$row['t_privado'].'</td>';
		echo'<td align="center">'.$row['t_exento'].'</td>';
		echo'</tr>';
		$tt1+=$row['t_rechazos'];
		$tt2+=$row['t_doble_cero'];
		$tt3+=$row['t_cero'];
		$tt4+=$row['t_uno'];
		$tt5+=$row['t_dos'];
		$tt6+=$row['t_taxi'];
		$tt7+=$row['t_privado'];
		$tt8+=$row['t_exento'];
	}
	echo'<tr>
	     <th style="text-align:center;"></th>
		 <th style="text-align:center;">'.number_format($tt1).'</th>
		 <th style="text-align:center;">'.number_format($tt2).'</th>
		 <th style="text-align:center;">'.number_format($tt3).'</th>
		 <th style="text-align:center;">'.number_format($tt4).'</th>
		 <th style="text-align:center;">'.number_format($tt5).'</th>
		 <th style="text-align:center;">'.number_format($tt6).'</th>
		 <th style="text-align:center;">'.number_format($tt7).'</th>
		 <th style="text-align:center;">'.number_format($tt8).'</th>
		 </tr>';
	
	echo'</table>';
}


?>