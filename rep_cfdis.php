<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');


function obtener_informacion($datos){
	$resultado = array();
	$res=mysql_query("SELECT cve, numero, nombre FROM plazas WHERE estatus!='I' ORDER BY lista");
	while($row=mysql_fetch_array($res)){
		$resultado[$row['cve']] = array('plaza' => $row['numero'].' '.$row['nombre']);
	}

	$res = mysql_query("SELECT plaza, COUNT(cve) FROM facturas WHERE fechatimbre BETWEEN '{$datos['busquedafechaini']} 00:00:00' AND '{$datos['busquedafechafin']} 23:59:59' AND respuesta1 != '' AND estatus!='C' GROUP BY plaza");
	while($row = mysql_fetch_array($res)){
		$resultado[$row['plaza']]['sistema']=$row[1];
	}

	$res = mysql_query("SELECT plaza, COUNT(cve) FROM notascredito WHERE fechatimbre BETWEEN '{$datos['busquedafechaini']} 00:00:00' AND '{$datos['busquedafechafin']} 23:59:59' AND respuesta1 != '' AND estatus!='C' GROUP BY plaza");
	while($row = mysql_fetch_array($res)){
		$resultado[$row['plaza']]['sistemanc']=$row[1];
	}

	$res = mysql_query("SELECT plaza, SUM(IF(tipo_comprobante!='E',1,0)), SUM(IF(tipo_comprobante='E',1,0)) FROM sat_xml WHERE fecha_timbrado BETWEEN '{$datos['busquedafechaini']} 00:00:00' AND '{$datos['busquedafechafin']} 23:59:59' GROUP BY plaza");
	while($row = mysql_fetch_array($res)){
		$resultado[$row['plaza']]['sat']=$row[1];
		$resultado[$row['plaza']]['satnc']=$row[2];
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
		  url: 'rep_cfdis.php',
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
	      <th scope="col" style="text-align: center;">Centro</th>
	      <th scope="col" style="text-align: center;">Facturas Sistema</th>
		  <th scope="col" style="text-align: center;">Facturas SAT</th> 
		  <th scope="col" style="text-align: center;">Notas de Credito Sistema</th>
		  <th scope="col" style="text-align: center;">Notas Credito SAT</th> 
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$totales = array();
		$i = 0;
		foreach($res as $idplaza => $row){
	?>
	    <tr>
	      <td align="left"><?php echo utf8_encode($row['plaza']);?></td>
	      <td align="right"><?php echo number_format($row['sistema'],0);?></td>
	      <td align="right"><?php echo number_format($row['sat'],0);?></td>
	      <td align="right"><?php echo number_format($row['sistemanc'],0);?></td>
	      <td align="right"><?php echo number_format($row['satnc'],0);?></td>
	    </tr>
	<?php
		$i++;
		$totales[0]+=$row['sistema'];
		$totales[1]+=$row['sat'];
		$totales[2]+=$row['sistemanc'];
		$totales[3]+=$row['satnc'];

	}
	?>
		<tr>
			<th style="text-align: left;"><?php echo $i;?> Registro(s)</th>
			<th style="text-align: right;"><?php echo number_format($totales[0],0);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[1],0);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[2],0);?></th>
			<th style="text-align: right;"><?php echo number_format($totales[3],0);?></th>
		</tr>
	  </tbody>
	</table>
	

<?php
}

?>