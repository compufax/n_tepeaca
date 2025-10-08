<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion($datos){
	$filtros = "";
	if ($datos['busquedafechaini'] != ''){
		$filtros .= " AND fecha>='{$datos['busquedafechaini']}'";
	}
	if ($datos['busquedafechafin'] != ''){
		$filtros .= " AND fecha<='{$datos['busquedafechafin']}'";
	}

	if ($datos['busquedausuario'] != ''){
		$filtros .= " AND usuario='{$datos['busquedausuario']}'";
	}

	$res = mysql_query("SELECT SUM(IF(estatus!='C' AND tipo_venta=0 AND tipo_pago=6, 1, 0)) as cantidad_pagos_anticipados,
							   SUM(IF(estatus!='C' AND tipo_venta=1, 1, 0)) as cantidad_intentos,
							   SUM(IF(estatus!='C' AND tipo_venta=2, 1, 0)) as cantidad_cortesias,
							   SUM(IF(estatus!='C' AND tipo_venta=0 AND tipo_pago=1, 1, 0)) as cantidad_efectivo,
							   SUM(IF(estatus!='C' AND tipo_venta=0 AND tipo_pago=1, monto, 0)) as importe_efectivo,
							   SUM(IF(estatus!='C' AND tipo_venta=0 AND tipo_pago=7, 1, 0)) as cantidad_tarjeta_debito,
							   SUM(IF(estatus!='C' AND tipo_venta=0 AND tipo_pago=7, monto, 0)) as importe_tarjeta_debito,
							   SUM(IF(estatus!='C' AND tipo_venta=0 AND tipo_pago=5, 1, 0)) as cantidad_tarjeta_credito,
							   SUM(IF(estatus!='C' AND tipo_venta=0 AND tipo_pago=5, monto, 0)) as importe_tarjeta_credito,
							   SUM(IF(tipo_pago NOT IN (5, 7), copias, 0)) as cantidad_copias_efectivo,
							   SUM(IF(tipo_pago NOT IN (5, 7), copias * costo_copias, 0)) as importe_copias_efectivo,
							   SUM(IF(tipo_pago IN (5, 7), copias, 0)) as cantidad_copias_banco,
							   SUM(IF(tipo_pago IN (5, 7), copias * costo_copias, 0)) as importe_copias_banco
						FROM cobro_engomado
						WHERE plaza = {$datos['cveplaza']}{$filtros}");
	$row = mysql_fetch_assoc($res);

	$resultado = $row;

	$res = mysql_query("SELECT SUM(IF(forma_pago = 1, verificaciones, 0)) as cantidad_efectivo_pagos_anticipados,
		                       SUM(IF(forma_pago = 1, monto, 0)) as importe_efectivo_pagos_anticipados,
		                       SUM(IF(forma_pago != 1, verificaciones, 0)) as cantidad_bancos_pagos_anticipados,
		                       SUM(IF(forma_pago != 1, monto, 0)) as importe_bancos_pagos_anticipados
		                FROM pagos_caja
		                WHERE plaza = {$datos['cveplaza']} AND estatus!='C'{$filtros}");
    $row = mysql_fetch_assoc($res);

	$resultado = array_merge($resultado, $row);

	$res = mysql_query("SELECT SUM(IF(usado=0 AND tipo=0,1,0)) as cantidad_vales_sin_usar,
		                       SUM(IF(usado=0 AND tipo=1,1,0)) as cantidad_cortesias_sin_usar
		                FROM vales_pago_anticipado
		                WHERE plaza = {$datos['cveplaza']} AND estatus!='C'");
	$row = mysql_fetch_assoc($res);
	$resultado = array_merge($resultado, $row);

	return $resultado;
}

function obtener_informacion_vales($datos){
	$filtros = "";

	$res = mysql_query("SELECT a.nombre as depositante, IFNULL(b.vales,0) as vales, IFNULL(b.cortesias,0) as cortesias FROM depositantes a INNER JOIN (SELECT depositante, SUM(IF(tipo=0,1,0)) as vales, SUM(IF(tipo=1,1,0)) as cortesias FROM vales_pago_anticipado WHERE plaza={$datos['cveplaza']} AND estatus!='C' AND usado=0{$filtros} GROUP BY depositante) b ON a.cve = b.depositante WHERE a.plaza={$datos['cveplaza']} ORDER BY a.nombre");
	return $res;
}
require_once('validarloging.php');
$res1 = mysql_query("SELECT cve, nombre FROM cat_entidades ORDER BY nombre");
	while($row1=mysql_fetch_array($res1)){
		$array_entidad[$row1['cve']]=$row1['nombre'];
	}

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
        	<label class="col-sm-2 col-form-label">Usuario</label>
			<div class="col-sm-4">
            	<select name="busquedausuario" id="busquedausuario" class="form-control" data-container="body" data-live-search="true" title="Usuario" data-hide-disabled="true" data-actions-box="true" data-virtual-scroll="false"><option value="">Todos</option>
            	<?php
            	$res1 = mysql_query("SELECT b.cve, b.usuario FROM (SELECT usuario FROM cobro_engomado WHERE plaza='{$_POST['cveplaza']}' GROUP BY usuario) a INNER JOIN usuarios b ON b.cve = a.usuario ORDER BY b.usuario");
				while($row1=mysql_fetch_array($res1)){
					echo '<option value="'.$row1['cve'].'">'.$row1['usuario'].'</option>';
				}
				?>
            	</select>
            	<script>
					$("#busquedausuario").selectpicker();	
				</script>
        	</div>
        </div>
        <div class="form-group row">
        	<div class="col-sm-12" align="center">
	        	<button id="btnbuscar" type="button" class="btn btn-primary" onClick="buscar();">
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
		  url: 'reporte_ventas_periodo.php',
		  type: "POST",
		  data: {
			menu: $('#cvemenu').val(),
			cmd: 10,
			busquedafechaini: $('#busquedafechaini').val(),
			busquedafechafin: $('#busquedafechafin').val(),
			busquedausuario: $('#busquedausuario').val(),
    		cvemenu: $('#cvemenu').val(),
    		cveplaza: $('#cveplaza').val(),
    		cveusuario: $('#cveusuario').val(),
		  },
			success: function(data) {
				$('#resultadocorte').html(data);
			}
		});
	}

	function vales_sin_usar(){
		$.ajax({
		  url: 'reporte_ventas_periodo.php',
		  type: "POST",
		  data: {
			menu: $('#cvemenu').val(),
			cmd: 20,
			busquedafechaini: $('#busquedafechaini').val(),
			busquedafechafin: $('#busquedafechafin').val(),
    		cvemenu: $('#cvemenu').val(),
    		cveplaza: $('#cveplaza').val(),
    		cveusuario: $('#cveusuario').val(),
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
	$row = obtener_informacion($_POST);
?>
<table class="table">
	<thead>
	    <tr>
	      <th scope="col" style="text-align: center;">Tipo</th>
	      <th scope="col" style="text-align: center;">Cantidad</th>
	      <th scope="col" style="text-align: center;">Importe</th>
	  </tr>
	</thead>
	<tbody>
		<tr><td>Canje de Pagos Anticipados</td><td align="right"><?php echo number_format($row['cantidad_pagos_anticipados'],0);?></td><td>&nbsp;</td></tr>
		<tr><td>Intentos</td><td align="right"><?php echo number_format($row['cantidad_intentos'],0);?></td><td>&nbsp;</td></tr>
		<tr><td>Cortesias</td><td align="right"><?php echo number_format($row['cantidad_cortesias'],0);?></td><td>&nbsp;</td></tr>
		<tr><th>Total</th><th style="text-align: right;"><?php echo number_format($row['cantidad_pagos_anticipados']+$row['cantidad_intentos']+$row['cantidad_cortesias'],0);?></th><td>&nbsp;</td></tr>
		<tr><td colspan="3">&nbsp;</td></tr>

		<tr><td>Efectivo</td><td align="right"><?php echo number_format($row['cantidad_efectivo'],0);?></td><td align="right"><?php echo number_format($row['importe_efectivo'],2);?></td></tr>
		<tr><td>Tarjeta de Debito</td><td align="right"><?php echo number_format($row['cantidad_tarjeta_debito'],0);?></td><td align="right"><?php echo number_format($row['importe_tarjeta_debito'],2);?></td></tr>
		<tr><td>Tarjeta de Credito</td><td align="right"><?php echo number_format($row['cantidad_tarjeta_credito'],0);?></td><td align="right"><?php echo number_format($row['importe_tarjeta_credito'],2);?></td></tr>
		<tr><th>Total</th><th style="text-align: right;"><?php echo number_format($row['cantidad_efectivo']+$row['cantidad_tarjeta_debito']+$row['cantidad_tarjeta_credito'],0);?></th><th style="text-align: right;"><?php echo number_format($row['importe_efectivo']+$row['importe_tarjeta_debito']+$row['importe_tarjeta_credito'],2);?></th></tr>
		<tr><td colspan="3">&nbsp;</td></tr>

		<tr><td>Compras Vales Anticipados Efectivo</td><td align="right"><?php echo number_format($row['cantidad_efectivo_pagos_anticipados'],0);?></td><td align="right"><?php echo number_format($row['importe_efectivo_pagos_anticipados'],2);?></td></tr>
		<tr><td>Compras Vales Anticipados Bancos</td><td align="right"><?php echo number_format($row['cantidad_bancos_pagos_anticipados'],0);?></td><td align="right"><?php echo number_format($row['importe_bancos_pagos_anticipados'],2);?></td></tr>
		<tr><th>Total</th><th style="text-align: right;"><?php echo number_format($row['cantidad_efectivo_pagos_anticipados']+$row['cantidad_bancos_pagos_anticipados'],0);?></td><th style="text-align: right;"><?php echo number_format($row['importe_efectivo_pagos_anticipados']+$row['importe_bancos_pagos_anticipados'],2);?></th></tr>
		<tr><td colspan="3">&nbsp;</td></tr>

		<tr><th>Total Verificacion Pagadas</th><th style="text-align: right;"><?php echo number_format($row['cantidad_efectivo']+$row['cantidad_tarjeta_debito']+$row['cantidad_tarjeta_credito']+$row['cantidad_efectivo_pagos_anticipados']+$row['cantidad_bancos_pagos_anticipados'],0);?></th><th style="text-align: right;"><?php echo number_format($row['importe_efectivo']+$row['importe_tarjeta_debito']+$row['importe_tarjeta_credito']+$row['importe_efectivo_pagos_anticipados']+$row['importe_bancos_pagos_anticipados'],2);?></th></tr>
		<tr><td colspan="3">&nbsp;</td></tr>

		<tr><td>Vales de Cortesia Sin Usar Actuales</td><td align="right"><a href="#" onClick="vales_sin_usar()"><?php echo number_format($row['cantidad_cortesias_sin_usar'],0);?></a></td><td>&nbsp;</td></tr>
		<tr><td>Vales de Pago Anticipado Sin Usar Actuales</td><td align="right"><a href="#" onClick="vales_sin_usar()"><?php echo number_format($row['cantidad_vales_sin_usar'],0);?></a></td><td>&nbsp;</td></tr>
		<tr><td colspan="3">&nbsp;</td></tr>

		<tr><td>Copias Efectivo</td><td align="right"><?php echo number_format($row['cantidad_copias_efectivo'],0);?></td><td align="right"><?php echo number_format($row['importe_copias_efectivo'],2);?></td></tr>
		<tr><td>Copias Bancos</td><td align="right"><?php echo number_format($row['cantidad_copias_banco'],0);?></td><td align="right"><?php echo number_format($row['importe_copias_banco'],2);?></td></tr>
		<tr><th>Total</th><th style="text-align: right;"><?php echo number_format($row['cantidad_copias_efectivo']+$row['cantidad_copias_banco'],0);?></th><th style="text-align: right;"><?php echo number_format($row['importe_copias_efectivo']+$row['importe_copias_banco'],2);?></th></tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr><th>Total Ingreso</th><th style="text-align: right;"><?php echo number_format($row['cantidad_efectivo']+$row['cantidad_tarjeta_debito']+$row['cantidad_tarjeta_credito']+$row['cantidad_efectivo_pagos_anticipados']+$row['cantidad_bancos_pagos_anticipados']+$row['cantidad_copias_efectivo']+$row['cantidad_copias_banco'],0);?></th><th style="text-align: right;"><?php echo number_format($row['importe_efectivo']+$row['importe_tarjeta_debito']+$row['importe_tarjeta_credito']+$row['importe_efectivo_pagos_anticipados']+$row['importe_bancos_pagos_anticipados']+$row['importe_copias_efectivo']+$row['importe_copias_banco'],2);?></th></tr>
	</tbody>
</table>
<?php
}

if ($_POST['cmd']==20) {
?>
	<h3>Vales sin usar</h3>
	<table class="table">
	  <thead>
	    <tr>
	      <th scope="col" style="text-align: center;">Depositante</th>
	      <th scope="col" style="text-align: center;">Pago Anticipado</th>
	      <th scope="col" style="text-align: center;">Cortesia</th>
	    </tr>
	  </thead>
	  <tbody>
<?php
	$totales = array(0,0);
	$i=0;
	$res = obtener_informacion_vales($_POST);

	while($row = mysql_fetch_assoc($res)){
?>
		<tr>
			<td align="left"><?php echo utf8_encode($row['depositante']);?></td>
		    <td align="right"><?php echo number_format($row['vales'],0);?></td>
		    <td align="right"><?php echo number_format($row['cortesias'],0);?></td>
		</tr>
<?php
		$totales[0] += $row['vales'];
		$totales[1] += $row['cortesias'];
		$i++;
	}
?>
		<tr>
			<th style="text-align: left;"><?php echo $i;?> Registro(s)</th>
			<th style="text-align: right;"><?php echo number_format($totales[0],0);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[1],0);?></th>
		</tr>
	  </tbody>
	</table>

<?php
}
?>