<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion($datos){
	$_POST['fecha_ini'] = $datos['busquedafechaini'];
	$_POST['fecha_fin'] = $datos['busquedafechafin'];
	$resultado = array();
	$array_plazas=array();
	$res=mysql_query("SELECT a.* FROM plazas a INNER JOIN datosempresas b ON a.cve = b.plaza WHERE a.estatus!='I' ORDER BY a.lista");
	while($row=mysql_fetch_array($res)){
		$k = $row['cve'];
		$renglon = array();
		$renglon['plaza'] = $row['numero'].' '.$row['nombre'];
		$renglon['plaza_cve'] = $row['cve'];
		// $select= " SELECT count(a.cve) as t_vendidos,a.*,b.cve as certificado, b.certificado as holograma,b.engomado as engomado_entrega, CONCAT(b.fecha,' ',b.hora) as fechaentrega, TIMEDIFF(IFNULL(CONCAT(b.fecha,' ',b.hora),NOW()),CONCAT(a.fecha,' ',a.hora)) as diferencia FROM cobro_engomado a LEFT JOIN certificados b ON a.plaza=b.plaza AND a.cve=b.ticket AND b.estatus!='C' LEFT JOIN depositantes c ON c.cve = a.depositante AND c.plaza = a.plaza 
		// WHERE a.fecha BETWEEN '".$_POST['fecha_ini']."' AND '".$_POST['fecha_fin']."' and  a.plaza='".$k."' AND a.estatus!='C'";
		// $res1=mysql_query($select) or die(mysql_error());
		// $roww1=mysql_fetch_array($res1);
		// $renglon['t_vendidos']=$roww1['t_vendidos'];

		// $select2= " SELECT count(a.cve) as t_importe,a.*,b.cve as certificado, b.certificado as holograma,b.engomado as engomado_entrega, CONCAT(b.fecha,' ',b.hora) as fechaentrega, TIMEDIFF(IFNULL(CONCAT(b.fecha,' ',b.hora),NOW()),CONCAT(a.fecha,' ',a.hora)) as diferencia FROM cobro_engomado a LEFT JOIN certificados b ON a.plaza=b.plaza AND a.cve=b.ticket AND b.estatus!='C' LEFT JOIN depositantes c ON c.cve = a.depositante AND c.plaza = a.plaza WHERE a.fecha BETWEEN '".$_POST['fecha_ini']."' AND '".$_POST['fecha_fin']."' and a.plaza='".$k."' AND a.estatus!='C' and a.tipo_pago not in(2,5,6,7)";
		// $res2=mysql_query($select2) or die(mysql_error());
		// $roww2=mysql_fetch_array($res2);
		// $renglon['t_importe']=$roww2['t_importe'];

		// $select3= " SELECT count(a.cve) as t_intento,a.*,b.cve as certificado, b.certificado as holograma,b.engomado as engomado_entrega, CONCAT(b.fecha,' ',b.hora) as fechaentrega, TIMEDIFF(IFNULL(CONCAT(b.fecha,' ',b.hora),NOW()),CONCAT(a.fecha,' ',a.hora)) as diferencia FROM cobro_engomado a LEFT JOIN certificados b ON a.plaza=b.plaza AND a.cve=b.ticket AND b.estatus!='C' LEFT JOIN depositantes c ON c.cve = a.depositante AND c.plaza = a.plaza WHERE a.fecha BETWEEN '".$_POST['fecha_ini']."' AND '".$_POST['fecha_fin']."' AND a.plaza='".$k."' AND a.estatus!='C' and  a.tipo_venta='1'";
		// $res3=mysql_query($select3) or die(mysql_error());
		// $roww3=mysql_fetch_array($res3);
		// $renglon['t_intento']=$roww3['t_intento'];

		// $select4= " SELECT count(a.cve) as t_cortecia,a.*,b.cve as certificado, b.certificado as holograma,b.engomado as engomado_entrega, CONCAT(b.fecha,' ',b.hora) as fechaentrega, TIMEDIFF(IFNULL(CONCAT(b.fecha,' ',b.hora),NOW()),CONCAT(a.fecha,' ',a.hora)) as diferencia FROM cobro_engomado a LEFT JOIN certificados b ON a.plaza=b.plaza AND a.cve=b.ticket AND b.estatus!='C' LEFT JOIN depositantes c ON c.cve = a.depositante AND c.plaza = a.plaza WHERE a.fecha BETWEEN '".$_POST['fecha_ini']."' AND '".$_POST['fecha_fin']."' AND a.plaza='".$k."' AND a.estatus!='C' and  a.tipo_venta='2'";
		// $res4=mysql_query($select4) or die(mysql_error());
		// $roww4=mysql_fetch_array($res4);
		// $renglon['t_cortecia']=$roww4['t_cortecia'];

		// $select7= " SELECT count(a.cve) as t_anticipado,a.*,b.cve as certificado, b.certificado as holograma,b.engomado as engomado_entrega, CONCAT(b.fecha,' ',b.hora) as fechaentrega, TIMEDIFF(IFNULL(CONCAT(b.fecha,' ',b.hora),NOW()),CONCAT(a.fecha,' ',a.hora)) as diferencia FROM cobro_engomado a LEFT JOIN certificados b ON a.plaza=b.plaza AND a.cve=b.ticket AND b.estatus!='C' LEFT JOIN depositantes c ON c.cve = a.depositante AND c.plaza = a.plaza WHERE a.fecha BETWEEN '".$_POST['fecha_ini']."' AND '".$_POST['fecha_fin']."' AND a.plaza='".$k."' AND a.tipo_pago='6'";
		// $res7=mysql_query($select7) or die(mysql_error());
		// $roww7=mysql_fetch_array($res7);
		// $renglon['t_anticipado']=$roww7['t_anticipado'];

		// $select5= " SELECT count(a.cve) as t_entregado,a.*, b.tipo_venta, b.tipo_pago, d.nombre as nomdepositante, b.engomado as engomadoticket, b.tipo_combustible, b.factura FROM certificados a INNER JOIN cobro_engomado b ON b.plaza = a.plaza AND b.cve = a.ticket LEFT JOIN depositantes d ON d.plaza = b.plaza AND d.cve = b.depositante WHERE a.plaza='".$k."' AND a.fecha BETWEEN '".$_POST['fecha_ini']."' AND '".$_POST['fecha_fin']."' AND a.estatus='A'";
		// $res5=mysql_query($select5) or die(mysql_error());
		// $roww5=mysql_fetch_array($res5);
		// $renglon['t_entregado']=$roww5['t_entregado'];

		// $select6= " SELECT count(cve) as t_cancel FROM certificados_cancelados WHERE plaza='".$k."' AND fecha BETWEEN '".$_POST['fecha_ini']."' AND '".$_POST['fecha_fin']."' AND engomado IN (19,2,3,5,1) ";
		// $res6=mysql_query($select6) or die(mysql_error());
		// $roww6=mysql_fetch_array($res6);
		// $renglon['t_cancel']=$roww6['t_cancel'];

		$resultado[] = $renglon;
	}
	return $resultado;
}
require_once('validarloging.php');

if($_POST['cmd']==0){
?>

<div class="row justify-content-center">
	<div class="col-xl-12 col-lg-12 col-md-12">
		<div class="form-group row" style="display:none">
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
        	<div class="col-sm-12" align="left">
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
		  url: 'existencia_timbres.php',
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
	      <th scope="col" style="text-align: center;">Plaza</th>
	      <th scope="col" style="text-align: center;">Existencia</th>
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$totales = array();
		$i = 0;
		foreach($res as $row){
	?>
	    <tr>
	      <td align="left"><?php echo $row['plaza'];?></td>
	      <td align="right"><?php echo existencia_timbres($row['plaza_cve']); ?></td>
	     
	    </tr>
	<?php
		$i++;
		// $totales[0]+=$row['t_vendidos'];
		// $totales[1]+=$row['t_importe'];
		// $totales[2]+=$row['t_intento'];
		// $totales[3]+=$row['t_cortecia'];
		// $totales[4]+=$row['t_anticipado'];
		// $totales[5]+=$row['t_entregado'];
		// $totales[6]+=$row['t_cancel'];
	}
	?>
		
		<tr>
			<th style="text-align: left;"><?php echo $i;?> Registro(s)</th>
		</tr>
	  </tbody>
	</table>
	

<?php
}

?>