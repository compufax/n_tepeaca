<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion($datos){
	$rechazos = 19;
	$_POST['fecha_ini'] = $datos['busquedafechaini'];
	$_POST['fecha_fin'] = $datos['busquedafechafin'];
	$resultado = array();
	$array_plazas=array();
	$resP=mysql_query("SELECT a.* FROM plazas a INNER JOIN datosempresas b ON a.cve = b.plaza WHERE a.estatus!='I' ORDER BY a.lista");
	while($rowP=mysql_fetch_array($resP)){
		$k=$rowP['cve'];
		$renglon = array();
		$renglon['plaza'] = $rowP['numero'].' '.$rowP['nombre'];
		$select= " SELECT cve,fecha,hora,estatus
				   FROM cobro_engomado as a WHERE a.plaza='".$k."' AND a.fecha='".$_POST['fecha_ini']."' order by cve asc ";
				   //FROM cobro_engomado as a WHERE a.plaza='".$k."' AND a.fecha>='".$_POST['fecha_ini']."' AND a.fecha<='".$_POST['fecha_fin']."' ";
		$res=mysql_query($select) or die(mysql_error());
		$row=mysql_fetch_array($res);
		
		
		$renglon['cve'] = $row[0];
		$renglon['fecha'] = $row[1];
		$renglon['hora'] = $row[2];
		if($row[3]=="A")$row[3]="Activo";
		if($row[3]=="C")$row[3]="Cancelado";
		$renglon['estatus'] = $row[3];
		
		
		$resultado[] = $renglon;
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
			<label class="col-sm-2 col-form-label" style="display:none">Fecha Fin</label>
			<div class="col-sm-4" style="display:none">
            	<input type="date"  class="form-control" id="busquedafechafin" name="busquedafechafin" placeholder="Fecha Fin" value="<?php echo date('Y-m-d');?>">
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
		  url: 'inicio_produccion.php',
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
	      <th scope="col" style="text-align: center;">Centro</th>
	      <th scope="col" style="text-align: center;">Folio</th>
	      <th scope="col" style="text-align: center;">Fecha</th>
		  <th scope="col" style="text-align: center;">Hora</th> 
	      <th scope="col" style="text-align: center;">Estatus</th>
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
	      <td align="center"><?php echo $row['cve'];?></td>
	      <td align="center"><?php echo $row['fecha'];?></td>
	      <td align="center"><?php echo $row['hora'];?></td>
	      <td align="center"><?php echo $row['estatus'];?></td>
	      
	    </tr>
	<?php
		$i++;
		
	}
	?>
		
	  </tbody>
	</table>
	

<?php
}

?>