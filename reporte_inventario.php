<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion($datos){
	$resultado = array();
	$res = mysql_query("SELECT a.cve, a.nombre FROM engomados a inner join engomados_plazas b on a.cve = b.engomado WHERE b.entrega=1 AND b.plaza = '{$datos['cveplaza']}' ORDER BY a.nombre");
	while($row=mysql_fetch_array($res)){
		$resultado[$row['cve']] = array('nombre' => $row['nombre']);
	}
	
	$res = mysql_query("SELECT engomado, SUM(IF(fecha_compra < '{$datos['busquedafechaini']}', foliofin+1-folioini, 0)) as anteriores,
		SUM(IF(fecha_compra < '{$datos['busquedafechaini']}', (foliofin+1-folioini)*costo, 0)) as costo_anteriores,
		SUM(IF(fecha_compra >= '{$datos['busquedafechaini']}', foliofin+1-folioini, 0)) as periodo,
		SUM(IF(fecha_compra >= '{$datos['busquedafechaini']}', (foliofin+1-folioini)*costo, 0)) as costo_periodo
		FROM compra_certificados WHERE plaza='{$datos['cveplaza']}' AND estatus!='C' AND fecha_compra <= '{$datos['busquedafechafin']}' GROUP BY engomado");
	while($row = mysql_fetch_assoc($res)){
		$resultado[$row['engomado']]['inicial']+=$row['anteriores'];
		$resultado[$row['engomado']]['inicial_costo']+=$row['costo_anteriores'];
		$resultado[$row['engomado']]['compras']+=$row['periodo'];
		$resultado[$row['engomado']]['compras_costo']+=$row['costo_periodo'];
		$resultado[$row['engomado']]['final']+=$row['anteriores']+$row['periodo'];
		$resultado[$row['engomado']]['final_costo']+=$row['costo_anteriores']+$row['costo_periodo'];
	}

	$res = mysql_query("SELECT a.engomado, SUM(IF(a.fecha < '{$datos['busquedafechaini']}', 1, 0)) as anteriores,
		SUM(IF(a.fecha < '{$datos['busquedafechaini']}', costo, 0)) as costo_anteriores,
		SUM(IF(b.tipo_venta NOT IN (1,2) AND a.fecha >= '{$datos['busquedafechaini']}', 1, 0)) as periodo,
		SUM(IF(a.fecha >= '{$datos['busquedafechaini']}', costo, 0)) as costo_periodo,
		SUM(IF(a.fecha >= '{$datos['busquedafechaini']}' AND b.tipo_venta=1, 1, 0)) as intentos,
		SUM(IF(a.fecha >= '{$datos['busquedafechaini']}' AND b.tipo_venta=2, 1, 0)) as cortesias
		FROM certificados a INNER JOIN cobro_engomado b ON b.plaza = a.plaza AND b.cve = a.ticket 
		WHERE a.plaza='{$datos['cveplaza']}' AND a.estatus!='C' AND a.fecha <= '{$datos['busquedafechafin']}' GROUP BY a.engomado");
	while($row = mysql_fetch_assoc($res)){
		$resultado[$row['engomado']]['inicial']-=$row['anteriores'];
		$resultado[$row['engomado']]['inicial_costo']-=$row['costo_anteriores'];
		$resultado[$row['engomado']]['utilizados']+=$row['periodo'];
		$resultado[$row['engomado']]['utilizados_costo']+=$row['costo_periodo'];
		$resultado[$row['engomado']]['intentos']+=$row['intentos'];
		$resultado[$row['engomado']]['cortesias']+=$row['cortesias'];
		$resultado[$row['engomado']]['final']-=($row['anteriores']+$row['periodo']+$row['intentos']+$row['cortesias']);
		$resultado[$row['engomado']]['final_costo']-=($row['costo_anteriores']+$row['costo_periodo']);
	}
	
	$res = mysql_query("SELECT engomado, SUM(IF(fecha < '{$datos['busquedafechaini']}', 1, 0)) as anteriores,
		SUM(IF(fecha < '{$datos['busquedafechaini']}', costo, 0)) as costo_anteriores,
		SUM(IF(fecha >= '{$datos['busquedafechaini']}', 1, 0)) as periodo,
		SUM(IF(fecha >= '{$datos['busquedafechaini']}', costo, 0)) as costo_periodo
		FROM certificados_cancelados WHERE plaza='{$datos['cveplaza']}' AND estatus!='C' AND fecha <= '{$datos['busquedafechafin']}' GROUP BY engomado");
	while($row = mysql_fetch_assoc($res)){
		$resultado[$row['engomado']]['inicial']-=$row['anteriores'];
		$resultado[$row['engomado']]['inicial_costo']-=$row['costo_anteriores'];
		$resultado[$row['engomado']]['cancelados']+=$row['periodo'];
		$resultado[$row['engomado']]['utilizados_costo']+=$row['costo_periodo'];
		$resultado[$row['engomado']]['final']-=($row['anteriores']+$row['periodo']);
		$resultado[$row['engomado']]['final_costo']-=($row['costo_anteriores']+$row['costo_periodo']);
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
		  url: 'reporte_inventario.php',
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
	      <th scope="col" style="text-align: center;">Tipo Holograma</th>
	      <th scope="col" style="text-align: center;">Inventario Inicial</th>
	      <!--<th scope="col" style="text-align: center;">Costo Inventario Inicial</th>-->
		  <th scope="col" style="text-align: center;">Compras del Periodo</th> 
	      <!--<th scope="col" style="text-align: center;">Costo de Compras</th>-->
	      <th scope="col" style="text-align: center;">Certificados Utilizados</th>
	      <th scope="col" style="text-align: center;">Intentos</th>
	      <th scope="col" style="text-align: center;">Cortesias</th> 
	      <th scope="col" style="text-align: center;">Cancelados</th> 
	      <!--<th scope="col" style="text-align: center;">Costo Utilizados</th>--> 
	      <th scope="col" style="text-align: center;">Inventario Final</th> 
	      <!--<th scope="col" style="text-align: center;">Costo Inventario Final</th>--> 
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$totales = array();
		$i = 0;
		foreach($res as $row){
	?>
	    <tr>
	      <td align="left"><?php echo $row['nombre'];?></td>
	      <td align="right"><?php echo number_format($row['inicial'],0);?></td>
	      <!--<td align="right"><?php echo number_format($row['inicial_costo'],2);?></td>-->
	      <td align="right"><?php echo number_format($row['compras'],0);?></td>
	      <!--<td align="right"><?php echo number_format($row['compras_costo'],2);?></td>-->
	      <td align="right"><?php echo number_format($row['utilizados'],0);?></td>
	      <td align="right"><?php echo number_format($row['intentos'],0);?></td>
	      <td align="right"><?php echo number_format($row['cortesias'],0);?></td>
	      <td align="right"><?php echo number_format($row['cancelados'],0);?></td>
	      <!--<td align="right"><?php echo number_format($row['utilizados_costo'],2);?></td>-->
	      <td align="right"><?php echo number_format($row['final'],0);?></td>
	      <!--<td align="right"><?php echo number_format($row['final_costo'],2);?></td>-->
	    </tr>
	<?php
		$i++;
		$totales[0]+=$row['inicial'];
		//$totales[1]+=$row['inicial_costo'];
		$totales[2]+=$row['compras'];
		//$totales[3]+=$row['compras_costo'];
		$totales[4]+=$row['utilizados'];
		$totales[5]+=$row['intentos'];
		$totales[6]+=$row['cortesias'];
		$totales[7]+=$row['cancelados'];
		//$totales[8]+=$row['utilizados_costo'];
		$totales[9]+=$row['final'];
		//$totales[10]+=$row['final_costo'];
	}
	?>
		<tr>
			<th style="text-align: right;">Totales:</th>
			<th style="text-align: right;"><?php echo number_format($totales[0],0);?></th>
			<!--<th style="text-align: right;"><?php echo number_format($totales[1],2);?></th>-->
			<th style="text-align: right;"><?php echo number_format($totales[2],0);?></th>
			<!--<th style="text-align: right;"><?php echo number_format($totales[3],2);?></th>-->
			<th style="text-align: right;"><?php echo number_format($totales[4],0);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[5],0);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[6],0);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[7],0);?></th>
			<!--<th style="text-align: right;"><?php echo number_format($totales[8],2);?></th>-->
			<th style="text-align: right;"><?php echo number_format($totales[9],0);?></th>
			<!--<th style="text-align: right;"><?php echo number_format($totales[10],2);?></th>-->
		</tr>
	  </tbody>
	</table>
	

<?php
}

?>