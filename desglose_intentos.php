<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion($datos){
	$select .= "SELECT IFNULL(c.nombre, 'SIN ENTREGA') as nomcertificado, COUNT(a.cve) as cantidad
	FROM cobro_engomado a 
	LEFT JOIN certificados b ON a.plaza = b.plaza AND a.cve = b.ticket AND b.estatus!='C' 
	LEFT JOIN engomados c ON c.cve = b.engomado 
	WHERE a.plaza = {$datos['cveplaza']} AND a.tipo_venta = 1 AND a.estatus NOT IN ('C', 'D')";
	if ($datos['busquedafechaini'] != ''){
		$select .= " AND a.fecha>='{$datos['busquedafechaini']}'";
	}
	if ($datos['busquedafechafin'] != ''){
		$select .= " AND a.fecha<='{$datos['busquedafechafin']}'";
	}
	
	$select.=" GROUP BY nomcertificado ORDER BY nomcertificado";
	$res = mysql_query($select);
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
	        	&nbsp;&nbsp;
	        	<button type="button" class="btn btn-primary" onClick="atcr('desglose_intentos.php','_blank',100,0);">
	            	Excel
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
		  url: 'desglose_intentos.php',
		  type: "POST",
		  data: {
			menu: $('#cvemenu').val(),
			cmd: 10,
			busquedafechaini: $('#busquedafechaini').val(),
			busquedafechafin: $('#busquedafechafin').val(),
    		cvemenu: $('#cvemenu').val(),
    		cveplaza: $('#cveplaza').val(),
    		cveusuario: $('#cveusuario').val()
		  },
			success: function(data) {
				$('#resultadocorte').html(data);
				$('#totalintentos').html($('#totallistado').html());
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
	<h3>Total de Intentos: <span id="totalintentos"></span></h3>
	<table class="table">
	  <thead>
	    <tr>
	      <th scope="col" style="text-align: center;">Tipo de Certificado</th>
	      <th scope="col" style="text-align: center;">Cantidad</th>
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$i = 0;
		while($row = mysql_fetch_assoc($res)){
	?>
	    <tr>
	      <td align="left"><?php echo $row['nomcertificado'];?></td>
	      <td align="right"><?php echo number_format($row['cantidad'],0);?></td>
	    </tr>
	<?php
		$i+=$row['cantidad'];
	}
	?>
		<tr>
			<th style="text-align: right;">Total:</th>
			<th style="text-align: right;" id="totallistado"><?php echo number_format($i,0);?></th>
		</tr>
	  </tbody>
	</table>
	

<?php
}

if($_POST['cmd']==100){
	require_once('PHPExcel/Classes/PHPExcel.php');
	include 'PHPExcel/Classes/PHPExcel/Writer/Excel2007.php'; 
	$objPHPExcel = new PHPExcel(); 
	$filename = "desgloseintentos.xlsx"; 
	$res = obtener_informacion($_POST);
	$tmonto = 0;
	header('Content-Type: application/vnd.ms-excel'); 
	header('Content-Disposition: attachment;filename="' . $filename . '"'); 
	header('Cache-Control: max-age=0'); 
	$F=$objPHPExcel->getActiveSheet(); 
	$Line = 1;
	$F->setCellValue('A'.$Line, 'Tipo de Certificado'); 
	$F->setCellValue('B'.$Line, 'Cantidad'); 
	$total = 0;

	while($row=mysql_fetch_assoc($res)){//extract each record 
	    ++$Line; 
	    $F->setCellValue('A'.$Line, $row['nomcertificado']); 
		$F->setCellValue('B'.$Line, $row['cantidad']); 
		$total+=$row['cantidad'];		
	} 
	++$Line;
	$F->setCellValue('A'.$Line, 'TOTAL:'); 
	$F->setCellValue('B'.$Line, number_format($total,0)); 
	// Redirect output to a client’s web browser (Excel5) 
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
	header('Content-Disposition: attachment;filename="'.$filename.'"'); 
	header('Cache-Control: max-age=0'); 

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
	$objWriter->save('php://output'); 
	exit; 

}

?>