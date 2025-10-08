<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');
require_once('validarloging.php');

if($_POST['cmd']==0){
?>

<div class="row justify-content-center">
	<div class="col-xl-12 col-lg-12 col-md-12">
		<div class="form-group row">
			<label class="col-sm-3 col-form-label">C&oacute;digo de Facturaci&oacute;n:</label>
			<div class="col-sm-4">
            	<input type="text" class="form-control" id="busquedacodigo" name="busquedacodigo" placeholder="C&oacute;digo de Facturaci&oacute;n" value="">
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
		  url: 'estatus_ticket_facturacion.php',
		  type: "POST",
		  data: {
			menu: $('#cvemenu').val(),
			cmd: 10,
			cveusuario: $('#cveusuario').val(),
			busquedacodigo: $('#busquedacodigo').val(),
    		cvemenu: $('#cvemenu').val(),
    		cveplaza: $('#cveplaza').val()
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
	$res = mysql_query("SELECT a.plaza, a.cve as ticket, a.estatus, a.factura, a.notacredito FROM cobro_engomado a INNER JOIN claves_facturacion b ON a.plaza = b.plaza AND a.cve = b.ticket WHERE b.cve = '{$_POST['busquedacodigo']}'");
	if($row=mysql_fetch_array($res)){
		if($row['estatus']=='C'){
			echo '<h1>El ticket esta cancelado</h1>';
		}
		elseif($row['factura']==0 || $row['notacredito']>0){
			echo '<h1>El ticket esta pendiente de factura</h1>';
		}
		else{
		$res1=mysql_query("SELECT b.numero as plaza, a.serie, a.folio, c.nombre as nomcli FROM facturas a INNER JOIN plazas b ON b.cve = a.plaza INNER JOIN clientes c ON c.cve = a.cliente WHERE a.plaza={$row['plaza']} AND a.cve={$row['factura']}");
?>
	<table class="table">
	  <thead>
	    <tr>
	      <th scope="col" style="text-align: center;">Plaza</th>
	      <th scope="col" style="text-align: center;">Serie</th>
	      <th scope="col" style="text-align: center;">Folio</th>
		  <th scope="col" style="text-align: center;">Cliente</th> 
	    </tr>
	  </thead>
	  <tbody>
	<?php
		while($row1 = mysql_fetch_assoc($res1)){
	?>
	    <tr>
	      <td align="center"><?php echo utf8_encode($row1['plaza']);?></td>
	      <td align="center"><?php echo $row1['serie'];?></td>
	      <td align="center"><?php echo $row1['folio'];?></td>
		  <td align="left"><?php echo utf8_encode($row1['nomcli']);?></td>
	    </tr>
	<?php
	}
	?>
	  </tbody>
	</table>
	

<?php
		}
	}
	else{
		echo '<h3>No se encontro el c&oacute;digo de facturaci&oacute;n</h3>';
	}
}



?>