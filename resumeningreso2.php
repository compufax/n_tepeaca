<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion($datos){
	$resultado = array();
	$fecha = $datos['busquedafechaini'];
	while($fecha<=$datos['busquedafechafin']){
		$resultado[$fecha] = array('fecha' => $fecha);
		$fecha=date( "Y-m-d" , strtotime ( "+1 day" , strtotime($fecha) ) );
	}
	$res = mysql_query("SELECT fecha, SUM(IF(estatus='A' AND tipo_venta IN (0,3) AND tipo_pago = 1, monto, 0)) as efectivo,
		SUM(IF(estatus='A' AND tipo_venta IN (0,3) AND tipo_pago = 5, monto, 0)) as t_credito,
		SUM(IF(estatus='A' AND tipo_venta IN (0,3) AND tipo_pago = 7, monto, 0)) as t_debito,
		SUM(copias) as copias,
		SUM(IF(estatus='A' and tipo_venta=0 and tipo_pago = 2, monto, 0)) as credito
		FROM cobro_engomado WHERE plaza='{$datos['cveplaza']}' AND fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' GROUP BY fecha");
	while($row = mysql_fetch_assoc($res)){
		$resultado[$row['fecha']]['efectivo'] = $row['efectivo'];
		$resultado[$row['fecha']]['t_credito'] = $row['t_credito'];
		$resultado[$row['fecha']]['t_debito'] = $row['t_debito'];
		$resultado[$row['fecha']]['copias'] = $row['copias'];
		$resultado[$row['fecha']]['credito'] = $row['credito'];
		$resultado[$row['fecha']]['cometra'] += $row['efectivo'];
		$resultado[$row['fecha']]['bancos'] += $row['efectivo']+$row['t_credito']+$row['t_debito'];
		$resultado[$row['fecha']]['total_venta'] += $row['efectivo']+$row['t_credito']+$row['t_debito']+$row['credito'];
	}

	$res = mysql_query("SELECT fecha, SUM(IF(tipo_pago = 6 AND forma_pago = 1, monto, 0)) as efectivo_pa,
		SUM(IF(tipo_pago = 6 AND forma_pago != 1, monto, 0)) as banco_pa,
		SUM(IF(tipo_pago = 2 AND forma_pago != 1, monto, 0)) as banco_rc,
		SUM(IF(tipo_pago = 2 AND forma_pago = 1, monto, 0)) as efectivo_rc FROM pagos_caja WHERE plaza='{$datos['cveplaza']}' AND fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' AND estatus!='C' AND tipo_pago IN (2,6) GROUP BY fecha");
	while($row = mysql_fetch_assoc($res)){
		$resultado[$row['fecha']]['efectivo_pa'] = $row['efectivo_pa'];
		$resultado[$row['fecha']]['banco_pa'] = $row['banco_pa'];
		$resultado[$row['fecha']]['banco_rc'] = $row['banco_rc'];
		$resultado[$row['fecha']]['efectivo_rc'] = $row['efectivo_rc'];
		$resultado[$row['fecha']]['cometra'] += $row['efectivo_pa'];
		$resultado[$row['fecha']]['bancos'] += $row['efectivo_pa']+$row['banco_pa']+$row['banco_rc'];
		$resultado[$row['fecha']]['total_venta'] += $row['efectivo_pa']+$row['banco_pa'];
	}

	$res = mysql_query("SELECT fecha, SUM(monto) as gastos FROM recibos_salida WHERE plaza='{$datos['cveplaza']}' AND fecha BETWEEN '{$datos['busquedafechaini']}' AND '{$datos['busquedafechafin']}' AND estatus='A' GROUP BY fecha");
	while($row = mysql_fetch_assoc($res)){
		$resultado[$row['fecha']]['gastos'] = $row['gastos'];
		$resultado[$row['fecha']]['total_venta'] -= $row['gastos'];
	}
	
	return $resultado;
}
require_once('validarloging.php');

if($_POST['cmd']==0){
?>

<div class="row justify-content-center">
	<div class="col-xl-12 col-lg-12 col-md-12">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Fecha Inicio</label>
			<div class="col-sm-4">
            	<input type="date" class="form-control" id="busquedafechaini" name="busquedafechaini" placeholder="Fecha Inicio" value="<?php echo date('Y-m-d');?>">
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
		  url: 'resumeningreso2.php',
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
	$colspan = 9;
?>
	<table class="table">
	  <thead>
	    <tr>
	      <th scope="col" style="text-align: center;">Fecha</th>
	      <th scope="col" style="text-align: center;">Efectivo</th>
	      <th scope="col" style="text-align: center;">Copias</th>
		  <th scope="col" style="text-align: center;">Pagos Anticipados<br>Efectivo</th> 
	      <th scope="col" style="text-align: center;">Pagos Anticipados<br>Bancos</th> 
	      <th scope="col" style="text-align: center;">Efectivo</th> 
	      <th scope="col" style="text-align: center;">Recuperaci&oacute;n de Cr&eacute;dito<br>Banco</th> 
	      <th scope="col" style="text-align: center;">Tarjeta<br>Cr&eacute;dito</th> 
	      <th scope="col" style="text-align: center;">Tarjeta<br>Debito</th> 
	      <th scope="col" style="text-align: center;">Bancos</th> 
	      <th scope="col" style="text-align: center;">Creditos</th> 
	      <th scope="col" style="text-align: center;">Gastos</th>
	      <th scope="col" style="text-align: center;">Total Venta</th>  
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$totales = array();
		$i = 0;
		foreach($res as $row){
	?>
	    <tr>
	      <td align="center"><?php echo $row['fecha'];?></td>
	      <td align="right"><?php echo number_format($row['efectivo'],2);?></td>
	      <td align="right"><?php echo number_format($row['copias'],2);?></td>
	      <td align="right"><?php echo number_format($row['efectivo_pa'],2);?></td>
	      <td align="right"><?php echo number_format($row['banco_pa'],2);?></td>
	      <td align="right"><?php echo number_format($row['cometra'],2);?></td>
	      <td align="right"><?php echo number_format($row['banco_rc'],2);?></td>
	      <td align="right"><?php echo number_format($row['t_credito'],2);?></td>
	      <td align="right"><?php echo number_format($row['t_debito'],2);?></td>
	      <td align="right"><?php echo number_format($row['bancos'],2);?></td>
	      <td align="right"><?php echo number_format($row['credito'],2);?></td>
	      <td align="right"><?php echo number_format($row['gastos'],2);?></td>
	      <td align="right"><?php echo number_format($row['total_venta'],2);?></td>
	    </tr>
	<?php
		$i++;
		$totales[0]+=$row['efectivo'];
		$totales[1]+=$row['copias'];
		$totales[2]+=$row['efectivo_pa'];
		$totales[3]+=$row['banco_pa'];
		$totales[4]+=$row['cometra'];
		$totales[5]+=$row['banco_rc'];
		$totales[6]+=$row['t_credito'];
		$totales[7]+=$row['t_debito'];
		$totales[8]+=$row['bancos'];
		$totales[9]+=$row['credito'];
		$totales[10]+=$row['gastos'];
		$totales[11]+=$row['total_venta'];
	}
	?>
		<tr>
			<th style="text-align: left;"><?php echo $i;?> Registro(s)</th>
			<th style="text-align: right;"><?php echo number_format($totales[0],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[1],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[2],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[3],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[4],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[5],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[6],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[7],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[8],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[9],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[10],2);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[11],2);?></th>
		</tr>
	  </tbody>
	</table>
	

<?php
}

?>