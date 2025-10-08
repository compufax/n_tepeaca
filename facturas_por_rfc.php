<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');


function obtener_informacion($datos){
	
	$select = "SELECT b.rfc, MAX(b.nombre) as nombre, COUNT(a.cve) as cantidad FROM facturas a INNER JOIN clientes b ON b.cve = a.cliente WHERE a.estatus!='C'";
	if ($datos['busquedafechaini'] != '') {
		$select .= " AND a.fecha >= '{$datos['busquedafechaini']}'";
	}
	if ($datos['busquedafechafin'] != '') {
		$select .= " AND a.fecha <= '{$datos['busquedafechafin']}'";
	}
	if ($datos['busquedarfc'] != '') {
		$select .= " AND b.rfc = '{$datos['busquedarfc']}'";
	}
	$select .= " GROUP BY b.rfc ORDER BY MAX(b.nombre)";
	$res = mysql_query($select);
	
	return $res;
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
        	<label class="col-sm-2 col-form-label">RFC Cliente</label>
			<div class="col-sm-4">
            	<input type="text" class="form-control" id="busquedarfc" name="busquedarfc" placeholder="RFC">
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
		  url: 'facturas_por_rfc.php',
		  type: "POST",
		  data: {
			menu: $('#cvemenu').val(),
			cmd: 10,
			cveusuario: $('#cveusuario').val(),
			busquedafechaini: $('#busquedafechaini').val(),
			busquedafechafin: $('#busquedafechafin').val(),
			busquedarfc: $('#busquedarfc').val(),
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
	      <th scope="col" style="text-align: center;">Nombre</th>
	      <th scope="col" style="text-align: center;">RFC</th>
		  <th scope="col" style="text-align: center;">Facturas</th> 
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$totales = array();
		$i = 0;
		while($row = mysql_fetch_assoc($res)){
	?>
	    <tr>
	      <td align="left"><?php echo utf8_encode($row['nombre']);?></td>
	      <td align="center"><?php echo utf8_encode($row['rfc']);?></td>
	      <td align="right"><?php echo number_format($row['cantidad'],0);?></td>
	    </tr>
	<?php
		$i++;
		$totales[0]+=$row['cantidad'];

	}
	?>
		<tr>
			<th style="text-align: left;" colspan="2"><?php echo $i;?> Registro(s)</th>
			<th style="text-align: right;"><?php echo number_format($totales[0],0);?></th>
		</tr>
	  </tbody>
	</table>
	

<?php
}

?>